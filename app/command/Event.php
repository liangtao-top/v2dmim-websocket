<?php
// +----------------------------------------------------------------------
// | CodeEngine
// +----------------------------------------------------------------------
// | Copyright 艾邦
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: TaoGe <liangtao.gz@foxmail.com>
// +----------------------------------------------------------------------
// | Version: 2.0 2020-01-19 13:36
// +----------------------------------------------------------------------

namespace app\command;

use app\command\Result as R;
use app\dao\DeviceDao;
use app\dao\OnlineDao;
use app\model\DeviceModel;
use app\struct\Device;
use app\validate\Server as Validate;
use com\log\Log;
use com\sign\TokenSig;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server as Ws;
use Throwable;

class Event
{

    /**
     * AccessToken鉴权
     * @param Request  $request
     * @param Response $response
     * @return bool
     * @author       TaoGe <liangtao.gz@foxmail.com>
     * @date         2020/9/10 13:48
     * @noinspection PhpParameterByRefIsNotUsedAsReferenceInspection
     */
    public static function auth(Request &$request, Response &$response): bool
    {
        if (!isset($request->get['access_token']) || empty($request->get['access_token'])) {
            Log::error("fd：$request->fd 缺少access_token参数");
            $response->end();
            return false;
        }
        $token = $request->get['access_token'];
        if (!TokenSig::verify($token)) {
            Log::error("fd：$request->fd AccessToken 鉴权失败");
            return false;
        }
        DeviceDao::set(new DeviceModel([
                                           'fd'     => $request->fd,
                                           'ip'     => $request->server['remote_addr'],
                                           'device' => new Device(TokenSig::getDevice($token)),
                                       ]));
        return true;
    }

    /**
     * request
     * @param Request  $request
     * @param Response $response
     * @author TaoGe <liangtao.gz@foxmail.com>
     * @date   2020/10/31 13:36
     */
    public static function request(Request &$request, Response &$response)
    {
        // URL 路由
        $uri        = explode('/', trim($request->server['request_uri'], '/'));
        $controller = $uri[0] ?? null;
        $controller = $controller ?: 'Index';
        $controller = '\app\controller\\' . parse_name($controller, 1);
        $action     = parse_name(($uri[1] ?? 'index') ?: 'index', 1, false);
        // 根据 $controller, $action 映射到不同的控制器类和方法
        if (!class_exists($controller)) {
            $response->end(error('Non-existent: ' . $controller . '::class'));
            return;
        }
        $class = new $controller;
        if (!method_exists($class, $action)) {
            $response->end(error('Non-existent: ' . $controller . '::' . $action));
            return;
        }
        try {
            $result = $class->$action($request, $response);
            $response->end($result);
        } catch (Throwable $e) {
            Log::error($e->__toString());
            $response->status(500);
            $response->end(error('Server exception!'));
        }
    }

    /**
     * message
     * @param \Swoole\WebSocket\Server $ws
     * @param \Swoole\WebSocket\Frame  $frame
     * @author TaoGe <liangtao.gz@foxmail.com>
     * @date   2021/6/2 11:48
     */
    public static function message(Ws &$ws, Frame &$frame): void
    {
        if (empty($frame->data) || !is_json($frame->data)) {
            Log::error('数据格式异常');
            $ws->push($frame->fd, R::e(\com\event\Event::EXCEPTION_WRONG_FORMAT, $frame->data));
            return;
        }
        $params   = json_decode($frame->data, true);
        $validate = new Validate;
        if (!$validate->check($params)) {
            Log::error((string)$validate->getError());
            $ws->push($frame->fd, R::n((string)$validate->getError(), '', ['state' => $params['state']]));
            return;
        }
        $online = OnlineDao::get($frame->fd);
        if ($online) {
            $params['uuid'] = $online->getUserId();
        }
        $dispatch = Route::toDispatch($params, $online);
//        Log::info('Dispatch(' . $dispatch . ')');
        if (!Route::exists($dispatch)) {
            $controller = '\app\controller\\' . $dispatch->getController();
            // 根据 $controller, $action 映射到不同的控制器类和方法
            if (!class_exists($controller)) {
                $msg = 'Non-existent: ' . $controller . '::class';
                Log::error($msg);
                $ws->push($frame->fd, R::n($msg, '', ['state' => $params['state']]));
                return;
            }
            $class = new $controller;
            if (!method_exists($class, $dispatch->getAction())) {
                $msg = 'Non-existent: ' . $controller . '::' . $dispatch->getAction();
                Log::error($msg);
                $ws->push($frame->fd, R::n($msg, '', ['state' => $params['state']]));
                return;
            }
            try {
                $online = OnlineDao::get($frame->fd);
                if ($online) {
                    $params['uuid'] = $online->getUserId();
                }
                $result = $class->{$dispatch->getAction()}($params, $ws, $frame);
                if (!$result) {
                    Log::error((string)$class->getError());
                    $data = R::n($class->getError(), '', ['state' => $params['state']]);
                } else {
                    $data = R::y($class->getResult(), 'success', ['state' => $params['state']]);
                }
            } catch (Throwable $e) {
                $data = R::n($e->getMessage(), '', ['state' => $params['state']]);
                Log::error($e->__toString());
            }
            $ws->push($frame->fd, $data);
            return;
        }
        Route::dispatch($dispatch);
    }

    /**
     * task
     * @param \Swoole\WebSocket\Server $ws
     * @param                          $task_id
     * @param                          $from_id
     * @param                          $data
     * @return mixed
     * @author       TaoGe <liangtao.gz@foxmail.com>
     * @date         2021/5/28 15:44
     * @noinspection DuplicatedCode
     */
    public static function task(Ws &$ws, $task_id, $from_id, $data): mixed
    {
        unset($task_id);
        unset($from_id);
        if (empty($data) || !is_array($data) || !isset($data['class']) || !isset($data['method'])) {
            Log::error('数据异常');
            return false;
        }
        $class  = $data['class'];
        $method = $data['method'] . 'Before';
        if (!class_exists($class)) {
            Log::warning('Non-existent: ' . $class . '::class');
            return false;
        }
        $controller = new $class;
        if (!method_exists($controller, $method)) {
            Log::warning('Non-existent: ' . $class . '::' . $method);
            return false;
        }
        try {
            if (isset($data['arg'])) {
                $arg = $data['arg'];
                if (!is_array($arg)) {
                    return $controller->$method($ws, $arg);
                }
                array_unshift($arg, $ws);
                return call_user_func_array([$controller, $method], $arg);
            }
            return $controller->$method($ws);
        } catch (Throwable $e) {
            Log::error($e->__toString());
            return false;
        }
    }

    /**
     * finish
     * @param \Swoole\WebSocket\Server $ws
     * @param                          $task_id
     * @param                          $data
     * @return mixed
     * @author       TaoGe <liangtao.gz@foxmail.com>
     * @date         2021/5/28 15:45
     * @noinspection DuplicatedCode
     */
    public static function finish(Ws &$ws, $task_id, $data): mixed
    {
        unset($task_id);
        if (empty($data) || !is_array($data) || !isset($data['class']) || !isset($data['method'])) {
            Log::error('数据异常');
            return false;
        }
        $class  = $data['class'];
        $method = $data['method'] . 'After';
        if (!class_exists($class)) {
            Log::warning('Non-existent: ' . $class . '::class');
            return false;
        }
        $controller = new $class;
        if (!method_exists($controller, $method)) {
            Log::warning('Non-existent: ' . $class . '::' . $method);
            return false;
        }
        try {
            if (isset($data['arg'])) {
                $arg = $data['arg'];
                if (!is_array($arg)) {
                    return $controller->$method($ws, $arg);
                }
                array_unshift($arg, $ws);
                return call_user_func_array([$controller, $method], $arg);
            }
            return $controller->$method($ws);
        } catch (Throwable $e) {
            Log::error($e->__toString());
            return false;
        }
    }

    /**
     * WebSocket连接关闭事件
     * @param Ws  $ws
     * @param int $fd
     * @author       TaoGe <liangtao.gz@foxmail.com>
     * @date         2019-11-29 20:36
     * @noinspection PhpUnusedParameterInspection
     */
    public static function close(Ws &$ws, int $fd)
    {
        DeviceDao::del($fd);
        $online = OnlineDao::get($fd);
        if ($online) OnlineDao::del($online);
    }
}

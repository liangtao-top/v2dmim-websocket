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
// | Version: 2.0 2019-11-26 14:21
// +----------------------------------------------------------------------
namespace app\command;

use com\ip\LocalIP;
use com\pool\redis\MemberPool;
use com\pool\redis\online\OnlinePool;
use com\pool\redis\route\business\DownPool;
use com\pool\redis\route\business\RegisterPool;
use DateTime;
use app\command\Result as R;
use com\log\Log;
use Swoole\Server\Task;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server as Ws;
use Swoole\Http\{Request, Response};

/**
 * Class Server
 * @package app\controller
 */
class Server
{
    // webSocket实例化配置
    private array $_config;

    // webSocket服务实例
    private Ws $_webSocket;

    /**
     * Server constructor.
     */
    public function __construct()
    {
        $this->_config = config();
        $web_socket_instance_config = $this->_config ['swoole'] ?? [];
        Log::info('WebSocket service start time ' . (new DateTime)->format('Y-m-d H:i:s'));
        Log::info('CPU number: ' . swoole_cpu_num());
        foreach (swoole_get_local_ip() as $key => $ip) {
            Log::info("$key  $ip");
            LocalIP::instance()->setIp($ip);
        }
        Log::info('PHP version: ' . PHP_VERSION_ID);
        Log::info('SWOOLE version: ' . SWOOLE_VERSION);
        Log::info('current process PID：' . posix_getpid());
        Log::info('Reactor number of Threads：' . $web_socket_instance_config['reactor_num']);
        Log::info('Worker number of processes：' . $web_socket_instance_config['worker_num']);
        Log::info('TaskWorker number of processes：' . $web_socket_instance_config['task_worker_num']);
        Log::info('maximum number of connections：' . $web_socket_instance_config['max_connection']);
        Log::info('process daemon：' . ($web_socket_instance_config['daemonize'] ? 'true' : 'false'));
        Log::info('heartbeat check interval：' . $web_socket_instance_config['heartbeat_check_interval'] . 's');
        Log::info('heartbeat idle time：' . $web_socket_instance_config['heartbeat_idle_time'] . 's');
        if ($this->_config['websocket']['protocol'] === 'wss') {
            $this->_webSocket = new Ws("0.0.0.0", $this->_config['websocket']['port'], SWOOLE_PROCESS, SWOOLE_SOCK_TCP | SWOOLE_SSL);
        } else {
            $this->_webSocket = new Ws("0.0.0.0", $this->_config['websocket']['port'], SWOOLE_PROCESS, SWOOLE_SOCK_TCP);
        }
        Log::info("{$this->_config['websocket']['protocol']}://0.0.0.0:{$this->_config['websocket']['port']}");
        $this->_webSocket->set($web_socket_instance_config);
        $process = (new Process($this->_webSocket))->start();
        $this->_webSocket->addProcess($process);
    }

    /**
     * start
     * @author TaoGe <liangtao.gz@foxmail.com>
     * @date   2021/5/28 11:08
     */
    public function start()
    {
        // 事件绑定
        $this->bindEvent();
        $this->_webSocket->start();
    }

    /**
     * 绑定事件
     * @author TaoGe <liangtao.gz@foxmail.com>
     * @date   2020/10/30 10:56
     */
    public function bindEvent()
    {
        // 启动后在主进程（master）的主线程回调此函数
        $this->_webSocket->on('start', [$this, 'onStart']);
        // 当管理进程启动时触发此事件
        $this->_webSocket->on('managerStart', [$this, 'onManagerStart']);
        // 此事件在 Worker 进程 / Task 进程 启动时发生，这里创建的对象可以在进程生命周期内使用
        $this->_webSocket->on('managerStop', [$this, 'onManagerStop']);
        // 此事件在 Worker 进程 / Task 进程 启动时发生，这里创建的对象可以在进程生命周期内使用
        $this->_webSocket->on('workerStart', [$this, 'onWorkerStart']);
        // 此事件在 Worker 进程终止时发生。在此函数中可以回收 Worker 进程申请的各类资源。
        $this->_webSocket->on('workerStop', [$this, 'onWorkerStop']);
        // 服务器会进行 handshake 握手的过程
        $this->_webSocket->on('handshake', [$this, 'onHandshake']);
        // 监听WebSocket连接打开事件
        $this->_webSocket->on('open', [$this, 'onOpen']);
        // 有新的连接进入时，在 worker 进程中回调。
        $this->_webSocket->on('connect', [$this, 'onConnect']);
        // 监听Request消息事件
        $this->_webSocket->on('request', [$this, 'onRequest']);
        // 监听WebSocket消息事件
        $this->_webSocket->on('message', [$this, 'onMessage']);
        // 处理异步任务(此回调函数在task进程中执行)
        $this->_webSocket->on('task', [$this, 'onTask']);
        // 处理异步任务的结果(此回调函数在worker进程中执行)
        $this->_webSocket->on('finish', [$this, 'onFinish']);
        // 监听WebSocket连接关闭事件
        $this->_webSocket->on('close', [$this, 'onClose']);
        // 事件在 Server 正常结束时发生
        $this->_webSocket->on('shutdown', [$this, 'onShutdown']);
    }

    /**
     * 启动后在主进程（master）的主线程回调此函数
     * onStart 回调中，仅允许 echo、打印 Log、修改进程名称。不得执行其他操作 (不能调用 server 相关函数等操作，因为服务尚未就绪)。
     * onWorkerStart 和 onStart 回调是在不同进程中并行执行的，不存在先后顺序
     * @param Ws $ws
     * @author       TaoGe <liangtao.gz@foxmail.com>
     * @date         2020/10/30 9:54
     * @noinspection PhpUnusedParameterInspection
     */
    public function onStart(Ws $ws)
    {
        $process_name = "php-swoole: master process";
        swoole_set_process_name($process_name);
        Log::info($process_name . ' started.');
    }

    /**
     * 当管理进程启动时触发此事件
     * @param Ws $ws
     * @author       TaoGe <liangtao.gz@foxmail.com>
     * @date         2020/10/30 10:19
     * @noinspection PhpUnusedParameterInspection
     */
    public function onManagerStart(Ws $ws)
    {
        $process_name = "php-swoole: manager";
        swoole_set_process_name($process_name);
        Log::info($process_name . ' started.');
    }

    /**
     * 当管理进程停止时触发此事件
     * @param Ws $ws
     * @author       TaoGe <liangtao.gz@foxmail.com>
     * @date         2021/4/8 14:04
     * @noinspection PhpUnusedParameterInspection
     */
    public function onManagerStop(Ws $ws)
    {
        Log::info("php-swoole: manager stop.");
    }

    /**
     * 此事件在 Worker 进程 / Task 进程 启动时发生，这里创建的对象可以在进程生命周期内使用
     * @param Ws $ws
     * @param int $worker_id
     * @author TaoGe <liangtao.gz@foxmail.com>
     * @date   2020/10/30 9:56
     */
    public function onWorkerStart(Ws $ws, int $worker_id)
    {
        if ($worker_id >= $ws->setting['worker_num']) {
            $process_name = "php-swoole: task-worker-$worker_id";
        } else {
            $process_name = "php-swoole: worker-$worker_id";
        }
        swoole_set_process_name($process_name);
        Log::info($process_name . ' started.');
        // Redis 连接池初始化
        OnlinePool::instance();
        RegisterPool::instance();
        DownPool::instance();
        MemberPool::instance();
    }

    /**
     * 此事件在 Worker 进程终止时发生。在此函数中可以回收 Worker 进程申请的各类资源。
     * @param Ws $ws
     * @param int $worker_id
     * @author TaoGe <liangtao.gz@foxmail.com>
     * @date   2020/10/31 12:07
     */
    public function onWorkerStop(Ws $ws, int $worker_id)
    {
        if ($worker_id >= $ws->setting['worker_num']) {
            $process_name = "php-swoole: task_worker $worker_id";
        } else {
            $process_name = "php-swoole: worker $worker_id process";
        }
        Log::info($process_name . ' process stop.');
    }

    /**
     * WebSocket 建立连接后进行握手
     * 设置 onHandShake 回调函数后不会再触发 onOpen 事件，需要应用代码自行处理
     * @param Request $request
     * @param Response $response
     * @return false
     * @author       TaoGe <liangtao.gz@foxmail.com>
     * @date         2020/10/30 10:31
     */
    public function onHandshake(Request $request, Response $response): bool
    {
        Log::info("fd：$request->fd websocket握手连接算法验证");
        $secWebSocketKey = $request->header['sec-websocket-key'];
        $patten = '#^[+/0-9A-Za-z]{21}[AQgw]==$#';
        if (0 === preg_match($patten, $secWebSocketKey) || 16 !== strlen(base64_decode($secWebSocketKey))) {
            $response->end();
            return false;
        }
        $key = base64_encode(
            sha1(
                $request->header['sec-websocket-key'] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11',
                true
            )
        );
        $headers = [
            'Upgrade' => 'websocket',
            'Connection' => 'Upgrade',
            'Sec-WebSocket-Accept' => $key,
            'Sec-WebSocket-Version' => '13',
        ];
        Log::info("fd：$request->fd websocket握手连接鉴权验证");
        if (!Event::auth($request, $response)) {
            return false;
        }
        foreach ($headers as $key => $val) {
            $response->header($key, $val);
        }
        $response->status(101);
        $response->end();
        /**
         * 设置 onHandShake 回调函数后不会再触发 onOpen 事件，需要应用代码自行处理，可以使用 $server->defer 调用 onOpen 逻辑
         * @noinspection PhpUndefinedMethodInspection
         */
        $this->_webSocket->defer(function () use ($request) {
            $this->onOpen($this->_webSocket, $request);
        });
        return true;
    }

    /**
     * 当 WebSocket 客户端与服务器建立连接并完成握手后会回调此函数。
     * @param Ws $ws
     * @param Request $request
     * @author TaoGe <liangtao.gz@foxmail.com>
     * @date   2020/10/30 10:32
     */
    public function onOpen(Ws $ws, Request $request)
    {
        Log::info("fd：$request->fd 连接成功");
        $ws->push($request->fd, R::e(\com\event\Event::onConnectSuccess));
    }

    /**
     * 有新的连接进入时，在 worker 进程中回调。
     * @param Ws $ws
     * @param int $fd
     * @param int $reactorId
     * @author       TaoGe <liangtao.gz@foxmail.com>
     * @date         2020/10/31 12:28
     */
    public function onConnect(Ws $ws, int $fd, int $reactorId)
    {
        Log::info('当前服务器共有连接：' . count($ws->connections));
        Log::info("新的连接 fd: $fd reactorId: $reactorId");
    }

    /**
     * HTTP服务
     * @param Request $request
     * @param Response $response
     * @author TaoGe <liangtao.gz@foxmail.com>
     * @date   2020/10/30 10:34
     */
    public function onRequest(Request $request, Response $response)
    {
        // 支持跨域
        $response->header('Access-Control-Allow-Origin', '*');

        // OPTIONS返回
        if ($request->server['request_method'] == 'OPTIONS') {
            $response->status(http_response_code());
            $response->end();
            return;
        }
        // Chrome浏览器访问服务器，会产生额外的一次请求
        if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
            $response->header('Content-Type', 'image/x-icon');
            $response->end(file_get_contents(SERVICE_PATH . DS . 'favicon.ico'));
            return;
        }
        Log::info("{$request->server['server_protocol']} {$request->server['request_method']} from uri:{$request->server['request_uri']} ip:{$request->server['remote_addr']} fd:$request->fd ");
        Event::request($request, $response);
        $this->_webSocket->close($response->fd);
    }

    /**
     * 当服务器收到来自客户端的数据帧时会回调此函数。
     * @param Ws $ws
     * @param Frame $frame
     * @author       TaoGe <liangtao.gz@foxmail.com>
     * @date         2020/10/30 10:32
     */
    public function onMessage(Ws $ws, Frame $frame)
    {
        if ($frame->data === 'ping') {
            $ws->push($frame->fd, 'pong');
            return;
        }
        $use = microtime(true);
        Log::info(sprintf("Message fd[%s]，OpCode：%s，Finish：%s", $frame->fd, $frame->opcode, $frame->finish));
        Event::message($ws, $frame);
        Log::info('Finish use ' . ((microtime(true) - $use) * 1000) . ' ms');
    }

    /**
     * 在 task 进程内被调用。
     * @param Ws $ws
     * @param Task $task
     * @author TaoGe <liangtao.gz@foxmail.com>
     * @date   2020/11/25 17:44
     */
    public function onTask(Ws $ws, Task $task)
    {
        $use = microtime(true);
        Log::task("New AsyncTask[id=$task->id]");
        $task_id = $task->id;
        $from_id = $task->worker_id;
        $data = $task->data;
        $result = Event::task($ws, $task_id, $from_id, $data);
        if ($result) {
            if (isset($data['arg']) && is_array($data['arg'])) {
                array_push($data['arg'], $result);
            } else {
                $data['arg'] = $result;
            }
        }
        $data['use_task_time'] = $use;
        //返回任务执行的结果
        $task->finish($data);
    }

    /**
     * 此回调函数在 worker 进程被调用，当 worker 进程投递的任务在 task 进程中完成时， task 进程会通过 Swoole\Server->finish() 方法将任务处理的结果发送给 worker 进程
     * @param Ws $ws
     * @param int $task_id
     * @param mixed $data
     * @author TaoGe <liangtao.gz@foxmail.com>
     * @date   2020/10/30 10:14
     */
    public function onFinish(Ws $ws, int $task_id, mixed $data)
    {
        Event::finish($ws, $task_id, $data);
        Log::task('AsyncTask[$task_id] finish use ' . ((microtime(true) - $data['use_task_time']) * 1000) . ' ms');
    }

    /**
     * TCP 客户端连接关闭后，在 worker 进程中回调此函数。
     * @param Ws $ws
     * @param int $fd
     * @param int $reactorId
     * @author TaoGe <liangtao.gz@foxmail.com>
     * @date   2020/10/30 10:06
     */
    public function onClose(Ws $ws, int $fd, int $reactorId)
    {
        if ($ws->isEstablished($fd)) {
            Event::close($ws, $fd);
        }
        Log::info("Close fd: $fd Reactor线程ID: $reactorId");
    }

    /**
     * 此事件在 Server 正常结束时发生
     * 强制 kill 进程不会回调 onShutdown，如 kill -9
     * 需要使用 kill -15 来发送 SIGTREM 信号到主进程才能按照正常的流程终止
     * 在命令行中使用 Ctrl+C 中断程序会立即停止，底层不会回调 onShutdown
     * @param Ws $ws
     * @author       TaoGe <liangtao.gz@foxmail.com>
     * @date         2020/10/30 9:51
     * @noinspection PhpUnusedParameterInspection
     */
    public function onShutdown(Ws $ws)
    {
        Log::info("php-swoole: master process stop.");
    }
}

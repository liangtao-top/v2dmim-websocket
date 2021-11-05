<?php
declare(strict_types=1);
// +----------------------------------------------------------------------
// | CodeEngine
// +----------------------------------------------------------------------
// | Copyright 艾邦
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: TaoGe <liangtao.gz@foxmail.com>
// +----------------------------------------------------------------------
// | Version: 2.0 2021/6/1 14:03
// +----------------------------------------------------------------------

namespace app\command;

use app\dao\route\business\DownDao;
use app\dao\route\business\RegisterDao;
use app\model\OnlineModel;
use com\log\Log;
use com\request\Request;
use JetBrains\PhpStorm\Pure;

class Route
{

    /**
     * 路由白名单
     * @var array
     */
    private static array $whiteList = [
        'User'   => ['login', 'logout'],
        'Server' => ['getServerTime']
    ];

    /**
     * 路由派发
     * @param Request $dispatch
     * @author TaoGe <liangtao.gz@foxmail.com>
     * @date   2021/6/1 17:59
     */
    public static function dispatch(Request $dispatch): void
    {
        $server = RegisterDao::optimization();
        if (is_null($server)) {
            Log::error('无可用的业务服务器资源');
            return;
        }
        Log::info('dispatch business ' . $server->getIp() . ' ' . $dispatch->getController() . '.' . $dispatch->getAction());
        DownDao::save($server->getIp(), $dispatch);
    }

    /**
     * exists
     * @param Request $dispatch
     * @return bool
     * @author TaoGe <liangtao.gz@foxmail.com>
     * @date   2021/6/2 11:44
     */
    #[Pure] public static function exists(Request $dispatch): bool
    {
        foreach (self::$whiteList as $key => $value) {
            if ($key === $dispatch->getController()) {
                if (is_string($value)) {
                    $value = [$value];
                }
                if (in_array($dispatch->getAction(), $value)) {
                    return false;
                }
            }
        }
        return true;
    }


    /**
     * toDispatch
     * @param array                       $params
     * @param \app\model\OnlineModel|null $online
     * @return Request
     * @author TaoGe <liangtao.gz@foxmail.com>
     * @date   2021/6/2 11:44
     */
    public static function toDispatch(array $params, ?OnlineModel $online): Request
    {
        $temp       = explode('.', $params['route']);
        $action     = isset($temp[1]) ? parse_name($temp[1], 1, false) : 'index';
        $controller = parse_name($temp[0], 1, true);
        unset($temp);
        return new Request([
                               'userId'     => $params['uuid'] ?? '',
                               'action'     => $action,
                               'controller' => $controller,
                               'data'       => $params['data'] ?? [],
                               'state'      => $params['state'] ?? '',
                               'timestamp'  => $params['timestamp'] ?? 0,
                               'online'     => $online
                           ]);
    }

}

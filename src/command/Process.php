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
// | Version: 2.0 2021/6/2 17:15
// +----------------------------------------------------------------------

namespace V2dmIM\WebSocket\command;

use Exception;
use V2dmIM\Core\etcd\Register;
use V2dmIM\Core\etcd\Schema;
use V2dmIM\Core\utils\ip\LocalIP;
use V2dmIM\Core\utils\log\Log;

/**
 * 独立进程
 * @package app\command
 */
class Process
{

    /**
     * 服务注册
     * @return \Swoole\Process
     * @author TaoGe <liangtao.gz@foxmail.com>
     * @date   2021/11/11 15:26
     */
    public static function register(): \Swoole\Process
    {
        return new \Swoole\Process(function () {
            $ip = LocalIP::instance()->getIp();
            Log::success("service register ip: $ip");
            $register = new Register('etcd:2379', 'v3beta');
//            \Swoole\Process::signal(SIGTERM, function () use (&$register, $ip) {
//                Log::warning("service unregister ip: $ip");
//                $register->unregister();    // 收到停止信号后，注销服务
//            });
            try {
                $register->register(Schema::GATEWAY(), $ip, 9502, 3);
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        }, false, 2, true);
    }

}

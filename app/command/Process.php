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

namespace app\command;

use com\ip\LocalIP;
use com\log\Log;
use com\pool\redis\route\business\UpPool;
use com\response\Response;
use com\response\Type;
use RedisException;
use Swoole\WebSocket\Server;

/**
 * 独立进程 - 监听业务服务器发送过来的消息
 * @package app\command
 */
class Process
{

    /**
     * redis->brpop 阻塞时长
     */
    const TIMEOUT = 1;

    private Server $server;

    private \Swoole\Process $process;

    public function __construct(Server &$server)
    {
        $this->server  = &$server;
        $this->process = new \Swoole\Process(function () {
            $ip    = LocalIP::instance()->getIp();
            $pool  = UpPool::instance();
            $redis = $pool->get();
            static $running = true;
            \Swoole\Process::signal(SIGTERM, function () use (&$running) {
                $running = false;
            });
            while ($running) {
                try {
                    $value = $redis->brpop($ip, self::TIMEOUT);
                } catch (RedisException $e) {
                    Log::error($e->getMessage());
                }
                if (!empty($value)) {
                    $this->handle(unserialize($value[1]));
                }
            }
        }, false, 2, true);
    }

    public function start(): \Swoole\Process
    {
        return $this->process;
    }

    private function handle(Response $response): void
    {
        $send_data = null;
        if ($response->getType()->equals(new Type(Type::ASK))) {
            $send_data = $response->getAsk();
        } else if ($response->getType()->equals(new Type(Type::NOTIFY))) {
            $send_data = $response->getNotify();
        }
        if (!is_null($send_data)) {
            if (!$this->server->exist($response->getFd())) {
                Log::error('检测 fd:' . $response->getFd() . ' 对应的连接不存在');
                return;
            }
            if (!$this->server->isEstablished($response->getFd())) {
                Log::error('检测 fd:' . $response->getFd() . ' 不是有效的 WebSocket 连接');
                return;
            }
            $this->server->push($response->getFd(), $send_data->toJson());
        }
    }
}

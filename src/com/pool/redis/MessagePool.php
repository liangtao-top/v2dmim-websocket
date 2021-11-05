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
// | Version: 2.0 2021/4/8 13:55
// +----------------------------------------------------------------------

namespace com\pool\redis;

use com\config\Config;
use Swoole\Database\RedisPool;
use Swoole\Database\RedisConfig;

class MessagePool
{
    //创建静态私有的变量保存该类对象
    static private ?RedisPool $instance = null;

    //防止使用new直接创建对象
    private function __construct()
    {
    }

    //防止使用clone克隆对象
    private function __clone()
    {
    }

    static public function instance(): RedisPool
    {
        //判断$instance是否是Singleton的对象，不是则创建
        if (is_null(self::$instance)) {
            $config         = Config::instance()->redis['connections']['message'];
            self::$instance = new RedisPool((new RedisConfig)->withHost($config['host'])
                                                             ->withPort($config['port'])
                                                             ->withTimeout($config['timeout'])
                                                             ->withReserved($config['reserved'])
                                                             ->withRetryInterval($config['retry_interval'])
                                                             ->withAuth($config['auth'])
                                                             ->withDbIndex($config['db_index'])
                                                             ->withReadTimeout($config['read_timeout'])
                , $config['pool_size']
            );
        }
        return self::$instance;
    }
}

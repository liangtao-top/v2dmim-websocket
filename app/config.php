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
// | Version: 2.0 2021/5/27 17:39
// +----------------------------------------------------------------------

$cpu_num = function_exists("swoole_cpu_num") ? swoole_cpu_num() : 4;

return [
    'debug'     => true,
    'log'       => true,
    'websocket' => [
        'port'     => 9502,
        'protocol' => 'ws',
    ],
    'swoole'  => [
        'daemonize'                => 0, // 进程守护
        'heartbeat_check_interval' => 120, // 每5秒侦测一次心跳
        'heartbeat_idle_time'      => 400, // 一个TCP连接如果在10秒内未向服务器端发送数据，将会被切断
        'reactor_num'              => $cpu_num, // Reactor线程数,默认会启用CPU核数相同的数量,建议设置为CPU核数的1-4倍
        'worker_num'               => $cpu_num, // Worker进程数,这里设置为CPU核数的1-4倍最合理
        'task_worker_num'          => $cpu_num, // 设置异步任务的工作进程数量
        'max_connection'           => 20000, // 最大允许的连接数
        'reload_async'             => true, // 设置异步重启开关 设置为 true 时，将启用异步安全重启特性，Worker 进程会等待异步事件完成后再退出
        'enable_coroutine'         => true, // 内置协程
        'task_enable_coroutine'    => true, // 开启 Task 协程支持
        'open_redis_protocol'      => false, // 启用后会解析 Redis 协议，worker 进程 onReceive 每次会返回一个完整的 Redis 数据包。建议直接使用 Redis\Server
        'open_mqtt_protocol'       => false, // 启用后会解析 MQTT 包头，worker 进程 onReceive 每次会返回一个完整的 MQTT 数据包。
        'open_http_protocol'       => false, // 启用 HTTP 协议处理，Swoole\Http\Server 会自动启用此选项。设置为 false 表示关闭 HTTP 协议处理。
        'open_websocket_protocol'  => true, // 设置使得这个端口WebSocket协议
        'pid_file'                 => DOCKER_PATH . DS . 'gateway' . DS . 'service.pid', // 进程ID
        'log_file'                 => DOCKER_PATH . DS . 'gateway' . DS . 'logs' . DS . 'error.log', // 日志路径
        'log_rotation'             => defined('SWOOLE_LOG_ROTATION_HOURLY') ? SWOOLE_LOG_ROTATION_HOURLY : 3, // 日志分割
        'open_http2_protocol'      => true,
        'ssl_key_file'             => DOCKER_PATH . DS . 'http' . DS . 'nginx' . DS . 'conf.d' . DS . 'cert' . DS . 'privkey.key',
        'ssl_cert_file'            => DOCKER_PATH . DS . 'http' . DS . 'nginx' . DS . 'conf.d' . DS . 'cert' . DS . 'fullchain.pem',
    ],
    'database'  => [
        // 默认数据连接标识
        'default'     => 'history',
        // 数据库连接信息
        'connections' => [
            'history' => [
                // 数据库类型
                'type'     => 'mysql',
                // 主机地址
                'hostname' => 'localhost-storage-mysql-history',
                // 用户名
                'username' => 'root',
                // 密码
                'password' => 'dnoVEX8Lp1uVmOum',
                // 端口
                'hostport' => '3306',
                // 数据库连接参数
                'params'   => [],
                // 数据库名
                'database' => 'im-std-db',
                // 数据库编码默认采用utf8
                'charset'  => 'utf8mb4',
                // 数据库表前缀
                'prefix'   => 'v2dmim_',
                // 数据库调试模式
                'debug'    => true,
            ],
            'http'    => [
                // 数据库类型
                'type'     => 'mysql',
                // 主机地址
                'hostname' => 'localhost-storage-mysql-http',
                // 用户名
                'username' => 'root',
                // 密码
                'password' => 'pEPCYAl99IE7iLve',
                // 端口
                'hostport' => '3306',
                // 数据库连接参数
                'params'   => [],
                // 数据库名
                'database' => 'im-std-db',
                // 数据库编码默认采用utf8
                'charset'  => 'utf8mb4',
                // 数据库表前缀
                'prefix'   => 'v2dmim_',
                // 数据库调试模式
                'debug'    => true,
            ],
        ],
    ],
    'redis'     => [
        'expire'      => 7,
        // redis 连接信息
        'connections' => [
            // 路由中心
            'route'        => [
                // 业务服务器
                'business' => [
                    // 服务注册中心
                    'register' => [
                        'host'           => 'localhost-route',
                        'port'           => 6379,
                        'timeout'        => 1,
                        'reserved'       => '',
                        'retry_interval' => 1,
                        'read_timeout'   => 10,
                        'auth'           => '9avytRxTuBZrXcvm',
                        'db_index'       => 0,
                        'pool_size'      => 64,
                    ],
                    // 消息上行队列
                    'up'       => [
                        'host'           => 'localhost-route',
                        'port'           => 6379,
                        'timeout'        => 1,
                        'reserved'       => '',
                        'retry_interval' => 1,
                        'read_timeout'   => 10,
                        'auth'           => '9avytRxTuBZrXcvm',
                        'db_index'       => 1,
                        'pool_size'      => 64,
                    ],
                    // 消息下行队列
                    'down'     => [
                        'host'           => 'localhost-route',
                        'port'           => 6379,
                        'timeout'        => 1, // 连接超时
                        'reserved'       => '',
                        'retry_interval' => 1,
                        'read_timeout'   => 10,
                        'auth'           => '9avytRxTuBZrXcvm',
                        'db_index'       => 2,
                        'pool_size'      => 64,
                    ],
                ],
                // 推送服务器
                'push'     => [
                    // 服务注册中心
                    'register' => [
                        'host'           => 'localhost-route',
                        'port'           => 6379,
                        'timeout'        => 1,
                        'reserved'       => '',
                        'retry_interval' => 1,
                        'read_timeout'   => 10,
                        'auth'           => '9avytRxTuBZrXcvm',
                        'db_index'       => 3,
                        'pool_size'      => 64,
                    ],
                    // 消息下行队列
                    'down'     => [
                        'host'           => 'localhost-route',
                        'port'           => 6379,
                        'timeout'        => 1,
                        'reserved'       => '',
                        'retry_interval' => 1,
                        'read_timeout'   => 10,
                        'auth'           => '9avytRxTuBZrXcvm',
                        'db_index'       => 4,
                        'pool_size'      => 64,
                    ],
                ],
            ],
            // 用户在线状态
            'online'       => [
                'online' => [
                    'host'           => 'localhost-online',
                    'port'           => 6379,
                    'timeout'        => 1,
                    'reserved'       => '',
                    'retry_interval' => 1,
                    'read_timeout'   => 10,
                    'auth'           => '9avytRxTuBZrXcvm',
                    'db_index'       => 0,
                    'pool_size'      => 64,
                ],
                'device' => [
                    'host'           => 'localhost-online',
                    'port'           => 6379,
                    'timeout'        => 1,
                    'reserved'       => '',
                    'retry_interval' => 1,
                    'read_timeout'   => 10,
                    'auth'           => '9avytRxTuBZrXcvm',
                    'db_index'       => 1,
                    'pool_size'      => 64,
                ],
            ],
            // 用户资料存储
            'member'       => [
                'host'           => 'localhost-storage-redis-member',
                'port'           => 6379,
                'timeout'        => 1,
                'reserved'       => '',
                'retry_interval' => 1,
                'read_timeout'   => 10,
                'auth'           => '0bi8v0Nz2KWhrM0t',
                'db_index'       => 0,
                'pool_size'      => 64,
            ],
            // 好友关系链
            'friend'       => [
                // 好友信息
                'friend'             => [
                    'host'           => 'localhost-storage-redis-friend',
                    'port'           => 6379,
                    'timeout'        => 1,
                    'reserved'       => '',
                    'retry_interval' => 1,
                    'read_timeout'   => 10,
                    'auth'           => '0bi8v0Nz2KWhrM0t',
                    'db_index'       => 0,
                    'pool_size'      => 64,
                ],
                // 好友申请
                'friend_application' => [
                    'host'           => 'localhost-storage-redis-friend',
                    'port'           => 6379,
                    'timeout'        => 1,
                    'reserved'       => '',
                    'retry_interval' => 1,
                    'read_timeout'   => 10,
                    'auth'           => '0bi8v0Nz2KWhrM0t',
                    'db_index'       => 1,
                    'pool_size'      => 64,
                ],
            ],
            // 群组
            'group'        => [
                'host'           => 'localhost-storage-redis-group',
                'port'           => 6379,
                'timeout'        => 1, // 连接超时
                'reserved'       => '',
                'retry_interval' => 1,
                'read_timeout'   => 10,
                'auth'           => '0bi8v0Nz2KWhrM0t',
                'db_index'       => 0,
                'pool_size'      => 64,
            ],
            // 消息
            'message'      => [
                'host'           => 'localhost-storage-redis-message',
                'port'           => 6379,
                'timeout'        => 1, // 连接超时
                'reserved'       => '',
                'retry_interval' => 1,
                'read_timeout'   => 0.0,
                'auth'           => '0bi8v0Nz2KWhrM0t',
                'db_index'       => 0,
                'pool_size'      => 64,
            ],
            // 会话
            'conversation' => [
                'host'           => 'localhost-storage-redis-conversation',
                'port'           => 6379,
                'timeout'        => 1, // 连接超时
                'reserved'       => '',
                'retry_interval' => 1,
                'read_timeout'   => 10,
                'auth'           => '8bt8vmQz2KWhrGh7',
                'db_index'       => 0,
                'pool_size'      => 64,
            ],
            // 消息模型
            'timeline'     => [
                'host'           => 'localhost-storage-redis-timeline',
                'port'           => 6379,
                'timeout'        => 1,
                'reserved'       => '',
                'retry_interval' => 1,
                'read_timeout'   => 10,
                'auth'           => '0bi8v0Nz2KWhrM0t',
                'db_index'       => 0,
                'pool_size'      => 64,
            ],
        ]
    ],
];

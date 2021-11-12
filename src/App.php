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
// | Version: 2.0 2021/4/23 10:50
// +----------------------------------------------------------------------

namespace V2dmIM\WebSocket;

use V2dmIM\WebSocket\command\Server;

class App
{

    /**
     * run
     * @author TaoGe <liangtao.gz@foxmail.com>
     * @date   2020-01-19 13:58
     */
    public static function run(): void
    {
        (new Server)->start();
    }

}

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
// | Version: 2.0 2021/6/3 9:53
// +----------------------------------------------------------------------

namespace dao\route\business;

use common\Dao;
use com\request\Request;
use com\pool\redis\route\business\DownPool;

/**
 * 消息下发通道 - 业务服务器
 * @package app\dao
 */
class DownDao extends Dao
{

    public static function save(string $key, Request $request): void
    {
        $redis = DownPool::instance()->get();
        $redis->lpush($key, serialize($request));
        DownPool::instance()->put($redis);
    }

}

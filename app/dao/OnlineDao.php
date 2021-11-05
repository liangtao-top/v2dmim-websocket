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
// | Version: 2.0 2021/5/28 12:12
// +----------------------------------------------------------------------

namespace app\dao;

use app\common\Dao;
use app\model\OnlineModel;
use com\pool\redis\online\OnlinePool;

class OnlineDao extends Dao
{

    public static function set(OnlineModel $model): void
    {
        $redis = OnlinePool::instance()->get();
        $redis->set("fd:{$model->getFd()}", serialize($model));
        $redis->sAdd("user:{$model->getUserId()}", $model->getFd());
        OnlinePool::instance()->put($redis);
    }

    public static function get(int $fd): ?OnlineModel
    {
        $redis = OnlinePool::instance()->get();
        if (!$redis->exists("fd:{$fd}")) {
            OnlinePool::instance()->put($redis);
            return null;
        }
        $item = $redis->get("fd:{$fd}");
        OnlinePool::instance()->put($redis);
        return unserialize($item);
    }

    public static function del(OnlineModel $model): void
    {
        $redis = OnlinePool::instance()->get();
        $redis->del("fd:{$model->getFd()}");
        $redis->sRem("user:{$model->getUserId()}", $model->getFd());
        OnlinePool::instance()->put($redis);
    }

    public static function all(string $userID): array
    {
        $redis = OnlinePool::instance()->get();
        $fds   = $redis->sMembers("user:{$userID}");
        $list  = [];
        foreach ($fds as $fd) {
            if ($redis->exists("fd:{$fd}")) {
                $item   = $redis->get("fd:{$fd}");
                $list[] = unserialize($item);
            } else {
                $list[] = null;
            }
        }
        OnlinePool::instance()->put($redis);
        return $list;
    }

    public static function exist(string $userID): bool
    {
        $redis  = OnlinePool::instance()->get();
        $result = $redis->exists("user:{$userID}");
        OnlinePool::instance()->put($redis);
        return $result;
    }

}

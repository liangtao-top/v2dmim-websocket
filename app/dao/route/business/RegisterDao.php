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

namespace app\dao\route\business;

use app\common\Dao;
use app\model\ServerModel;
use com\pool\redis\route\business\RegisterPool;
use com\traits\crud\Min;

/**
 * Class RouteDao
 * @package app\dao
 */
class RegisterDao extends Dao
{

    use Min;

    public static function set(ServerModel $model): void
    {
        self::minSet(RegisterPool::instance(), $model->getIp(), $model);
    }

    public static function get(string $key): ?ServerModel
    {
        return self::minGet(RegisterPool::instance(), $key);
    }

    public static function del(string $key): void
    {
        self::minDel(RegisterPool::instance(), $key);
    }

    public static function all(): array
    {
        return self::minAll(RegisterPool::instance());
    }

    public static function len(): int
    {
        return self::minlen(RegisterPool::instance());
    }

    public static function isset(string $key): bool
    {
        return self::minExi(RegisterPool::instance(), $key);
    }

    public static function clean(){
         self::minCle(RegisterPool::instance());
    }

    public static function inc(string $key): void
    {
        $redis = RegisterPool::instance()->get();
        if ($redis->exists($key)) {
            $server = unserialize($redis->get($key));
            $server->setLoad($server->getLoad() + 1);
            $redis->set($key, serialize($server));
        }
        RegisterPool::instance()->put($redis);
    }

    public static function dec(string $key): void
    {
        $redis = RegisterPool::instance()->get();
        if ($redis->exists($key)) {
            $server = unserialize($redis->get($key));
            $server->setLoad($server->getLoad() - 1);
            $redis->set($key, serialize($server));
        }
        RegisterPool::instance()->put($redis);
    }

    public static function optimization(): ?ServerModel
    {
        $pool  = RegisterPool::instance();
        $redis = $pool->get();
        $list  = $redis->keys('*');
        if (empty($list)) {
            $pool->put($redis);
            return null;
        }
        $result = null;
        foreach ($list as $key) {
            $item = $redis->get($key);
            if ($item === false) {
                continue;
            }
            $value = unserialize($redis->get($key));
            if (is_null($result)) {
                $result = $value;
            } elseif ($value->getLoad() > $result->getLoad()) {
                $result = $value;
            }
        }
        $pool->put($redis);
        return $result;
    }

}

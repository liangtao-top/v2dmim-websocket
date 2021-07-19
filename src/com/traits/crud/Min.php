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
// | Version: 2.0 2021/6/19 15:12
// +----------------------------------------------------------------------

namespace com\traits\crud;

use Swoole\Database\RedisPool;

trait Min
{

    protected static function minSet(RedisPool $pool, string $key, mixed $value): void
    {
        $redis = $pool->get();
        $redis->set($key, serialize($value));
        $pool->put($redis);
    }

    protected static function minGet(RedisPool $pool, string $key)
    {
        $redis = $pool->get();
        $value = $redis->get($key);
        $pool->put($redis);
        if ($value === false) {
            return null;
        }
        return unserialize($value);
    }

    protected static function minDel(RedisPool $pool, string $key): void
    {
        $redis = $pool->get();
        $redis->del($key);
        $pool->put($redis);
    }

    protected static function minExi(RedisPool $pool, string $key): bool
    {
        $redis = $pool->get();
        $value = $redis->exists($key);
        $pool->put($redis);
        return (bool)$value;
    }

    protected static function minLen(RedisPool $pool): int
    {
        $redis = $pool->get();
        $value = $redis->keys('*');
        $pool->put($redis);
        return count($value);
    }

    protected static function minAll(RedisPool $pool): array
    {
        $redis  = $pool->get();
        $value  = $redis->keys('*');
        $result = [];
        foreach ($value as $key) {
            $item = $redis->get($key);
            if ($item === false) {
                continue;
            }
            $result[] = unserialize($item);
        }
        $pool->put($redis);
        return $result;
    }

    protected static function minCle(RedisPool $pool): void
    {
        $redis = $pool->get();
        $redis->flushAll();
        $pool->put($redis);
    }
}

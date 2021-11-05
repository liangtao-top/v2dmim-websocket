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
// | Version: 2.0 2021/6/19 14:25
// +----------------------------------------------------------------------

namespace com\traits\crud;

use Swoole\Database\RedisPool;

trait STD
{

    private static string $AUTO_INCREMENT = 'AUTO_INCREMENT';

    private static string $INDEX = 'INDEX';

    private static string $TABLE = 'TABLE';

    protected static function stdSet(RedisPool $pool, string $user_id, string $key, mixed $value): mixed
    {
        $redis = $pool->get();
        if (is_null($value->getId())) {
            $value->setId($redis->incr($user_id . ':' . self::$AUTO_INCREMENT));
        }
        $redis->zAdd($user_id . ':' . self::$INDEX, $value->getId(), $user_id . ':' . self::$TABLE . ':' . $key);
        $redis->set($user_id . ':' . self::$TABLE . ':' . $key, serialize($value));
        $pool->put($redis);
        return $value;
    }

    protected static function stdDel(RedisPool $pool, string $user_id, string $key): void
    {
        $redis = $pool->get();
        $redis->zRem($user_id . ':' . self::$INDEX, $user_id . ':' . self::$TABLE . ':' . $key);
        $redis->del($user_id . ':' . self::$TABLE . ':' . $key);
        $pool->put($redis);
    }

    protected static function stdGet(RedisPool $pool, string $user_id, string $key): mixed
    {
        $redis = $pool->get();
        $value = $redis->get($user_id . ':' . self::$TABLE . ':' . $key);
        $pool->put($redis);
        if ($value === false) {
            return null;
        }
        return unserialize($value);
    }

    protected static function stdAll(RedisPool $pool, string $user_id, int $start = 0, int $stop = null): array
    {
        $redis = $pool->get();
        $end   = is_null($stop) ? -1 : $start + ($stop - 1);
        $index = $redis->zRange($user_id . ':' . self::$INDEX, $start, $end);
        $list  = [];
        foreach ($index as $key) {
            $item = $redis->get($key);
            if ($item !== false) {
                $list[] = unserialize($item);
            } else {
                $list[] = null;
            }
        }
        $pool->put($redis);
        return $list;
    }

    protected static function stdLen(RedisPool $pool, string $user_id): int
    {
        $redis = $pool->get();
        if (!$redis->exists($user_id . ':' . self::$INDEX)) {
            $pool->put($redis);
            return 0;
        }
        $len = $redis->zCard($user_id . ':' . self::$INDEX);
        $pool->put($redis);
        return $len;
    }

    protected static function stdExi(RedisPool $pool, string $user_id, string $key): bool
    {
        $redis = $pool->get();
        $value = $redis->exists($user_id . ':' . self::$TABLE . ':' . $key);
        $pool->put($redis);
        return (boolean)$value;
    }

    protected static function stdNex(RedisPool $pool, string $user_id): int
    {
        $redis = $pool->get();
        $incr  = $redis->incr($user_id . ':' . self::$AUTO_INCREMENT);
        $pool->put($redis);
        return $incr;
    }

    protected static function stdCle(RedisPool $pool, string $user_id): void
    {
        $redis = $pool->get();
        $value = $redis->keys($user_id . '*');
        if (!empty($value) && is_array($value)) {
            $redis->del($value);
        }
        $pool->put($redis);
    }

}

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
// | Version: 2.0 2021/5/27 15:35
// +----------------------------------------------------------------------

namespace app\dao;

use app\common\Dao;
use app\model\ProfileModel as Model;
use app\struct\AllowType;
use app\struct\Gender;
use com\pool\redis\MemberPool as Pool;
use com\traits\crud\STD;
use think\facade\Db;

class ProfileDao extends Dao
{

    use STD;

    public static function set(Model $model): Model
    {
        return self::stdSet(Pool::instance(), 'all', $model->getUserId(), $model);
    }

    public static function del(string $userId): void
    {
        self::stdDel(Pool::instance(), 'all', $userId);
    }

    public static function get(string $userId): ?Model
    {
        $result = self::stdGet(Pool::instance(), 'all', $userId);
        if (!is_null($result)) {
            return $result;
        }
        $find = Db::name('member_profile')->where('user_id', $userId)->find();
        if (is_null($find)) {
            return null;
        }
        $find['face_url']   = $find['avatar'] > 0 ? 'https://im-std.docker.abontest.com:8443/api/downloadPicture/id/' . $find['avatar'] : '';
        $find['gender']     = new Gender($find['gender']);
        $find['allow_type'] = new AllowType($find['allow_type']);
        return self::set(new Model($find));
    }

    public static function all(int $start = 0, int $stop = null): array
    {
        return self::stdAll(Pool::instance(), 'all', $start, $stop);
    }

    public static function len(): int
    {
        return self::stdLen(Pool::instance(), 'all');
    }

    public static function isset(string $userId): bool
    {
        $result = self::stdExi(Pool::instance(), 'all', $userId);
        if ($result) {
            return true;
        }
        $find = Db::name('member_profile')->where('user_id', $userId)->find();
        if (is_null($find)) {
            return false;
        }
        $find['gender']     = new Gender($find['gender']);
        $find['allow_type'] = new AllowType($find['allow_type']);
        self::set(new Model($find));
        return true;
    }

    public static function clear(): void
    {
        self::stdCle(Pool::instance(), 'all');
    }

}

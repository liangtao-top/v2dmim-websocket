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
use app\model\DeviceModel;
use com\pool\redis\online\DevicePool as Pool;
use com\traits\crud\Min;

class DeviceDao extends Dao
{

    use Min;

    public static function set(DeviceModel $model): void
    {
        self::minSet(Pool::instance(), (string)$model->getFd(), $model);
    }

    public static function del(int $fd): void
    {
        self::minDel(Pool::instance(), (string)$fd);
    }

    public static function get(int $fd): ?DeviceModel
    {
        return self::minGet(Pool::instance(), (string)$fd);
    }

    public static function all(): array
    {
        return self::minAll(Pool::instance());
    }

    public static function len(): int
    {
        return self::minLen(Pool::instance());
    }

    public static function isset(int $fd): bool
    {
        return self::minExi(Pool::instance(), (string)$fd);
    }

}

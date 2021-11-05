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
// | Version: 2.0 2021/6/19 13:55
// +----------------------------------------------------------------------

namespace com\traits;

trait Singleton
{

    private static ?self $instance = null;

    public static function instance(): static
    {
        if (is_null(self::$instance)) {
            self::$instance = new static;
        }
        return self::$instance;
    }

}

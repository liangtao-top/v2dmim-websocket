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
// | Version: 2.0 2021/5/28 9:36
// +----------------------------------------------------------------------

use com\config\Config;

/**
 * config
 * @param string $name
 * @return mixed
 * @author TaoGe <liangtao.gz@foxmail.com>
 * @date   2021/5/28 12:02
 */
function config(string $name = ''): mixed
{
    if (empty($name)) {
        return Config::instance()->getData();
    } else {
        return Config::instance()->$name;
    }
}

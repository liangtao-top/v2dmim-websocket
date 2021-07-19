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
// | Version: 2.0 2021/6/17 15:06
// +----------------------------------------------------------------------

namespace app\controller;

use app\common\Base;

/**
 * Class Server
 * @package app\controller
 */
class Server extends Base
{

    /**
     * 获取服务器时间
     * @return bool
     * @author TaoGe <liangtao.gz@foxmail.com>
     * @date   2021/6/17 15:07
     */
    public function getServerTime(): bool
    {
        $this->setResult(time());
        return true;
    }

}

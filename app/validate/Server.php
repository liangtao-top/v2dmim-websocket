<?php
// +----------------------------------------------------------------------
// | CodeEngine
// +----------------------------------------------------------------------
// | Copyright 艾邦
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: TaoGe <liangtao.gz@foxmail.com>
// +----------------------------------------------------------------------
// | Version: 2.0 2019-11-27 22:18
// +----------------------------------------------------------------------
namespace app\validate;

use app\common\Validate;

/**
 * Class Server
 * @package app\validate
 */
class Server extends Validate
{

    protected array $rule = [
        'route' => 'require',
        'state' => 'require|length:32',
    ];

}

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
// | Version: 2.0 2021/5/27 17:49
// +----------------------------------------------------------------------

use app\App;

// 设置时区亚洲/上海
date_default_timezone_set('Asia/Shanghai');

// 定义根目录
define('ROOT_PATH', dirname(__DIR__, 2));

// 定义版本
const V2DMIM_VERSION = '1.0.0';

// 定义docker路径
const DOCKER_PATH = ROOT_PATH . DS . 'docker';

// 定义服务路径
const SERVICE_PATH = ROOT_PATH . DS . 'src';

// 定义定义应用目录
const APP_PATH = SERVICE_PATH . DS . 'app';

const DS = DIRECTORY_SEPARATOR;

// 载入助手函数
require 'helper.php';

// 应用入口
App::run();

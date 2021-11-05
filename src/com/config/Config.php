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
// | Version: 2.0 2021/4/23 12:03
// +----------------------------------------------------------------------

namespace com\config;

/**
 * Class Config
 * @property array database
 * @property array redis
 * @package app
 */
class Config
{
    //声明一个静态属性来存放实例
    private static ?self $instance = null;

    //声明一个数组 用于存放读取来的数据库类信息
    private array $data;

    //首先将类的构造函数和克隆方法写死
    private function __construct()
    {
        //将配置数组赋给成员变量
        $this->data = include(APP_PATH . DS . 'config.php');
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    private function __clone()
    {
    }

    //写一个静态方法来声明并判断实例，存在则返回已存在的实例，不存在则实例化新的，保证实例对象的唯一性
    public static function instance(): static
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    //使用魔术方法读取data中的信息
    public function __get($key)
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        } else {
            return null;
        }
    }

    //使用魔术方法 在运行期动态增加或改变配置选项
    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }

}

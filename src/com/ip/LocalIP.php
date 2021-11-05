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
// | Version: 2.0 2021/5/31 17:52
// +----------------------------------------------------------------------

namespace com\ip;


class LocalIP
{
    private string $ip;

    //创建静态私有的变量保存该类对象
    static private ?self $instance = null;

    //防止使用new直接创建对象
    private function __construct()
    {
    }

    //防止使用clone克隆对象

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    private function __clone()
    {
    }

    static public function instance(): static
    {
        //判断$instance是否是Singleton的对象，不是则创建
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }


}

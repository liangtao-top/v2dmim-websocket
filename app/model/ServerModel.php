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
// | Version: 2.0 2021/6/1 15:54
// +----------------------------------------------------------------------

namespace app\model;

use app\common\Model;

/**
 * Class Server
 * @package app\route
 */
class ServerModel extends Model
{
    /**
     * 服务器IP
     * @var string
     */
    private string $ip;

    /**
     * 负载
     * @var int
     */
    private int $load;

    /**
     * 注册时间
     * @var int
     */
    private int $registerTime;

    /**
     * @return int
     */
    public function getRegisterTime(): int
    {
        return $this->registerTime;
    }

    /**
     * @param int $registerTime
     */
    public function setRegisterTime(int $registerTime): void
    {
        $this->registerTime = $registerTime;
    }

    /**
     * @return int
     */
    public function getLoad(): int
    {
        return $this->load;
    }

    /**
     * @param int $load
     */
    public function setLoad(int $load): void
    {
        $this->load = $load;
    }

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
}

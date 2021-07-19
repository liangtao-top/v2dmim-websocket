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
// | Version: 2.0 2021/6/2 14:25
// +----------------------------------------------------------------------

namespace com\request;

use com\struct\Struct;
use app\model\OnlineModel;

class Request extends Struct
{

    /**
     * 用户ID
     * @var string
     */
    private string $userId;

    /**
     *  控制器
     * @var string
     */
    private string $controller;

    /**
     * 操作
     * @var string
     */
    private string $action;

    /**
     * 参数
     * @var array
     */
    private array $data;

    /**
     * @var string
     */
    private string $state;

    /**
     * 时间戳
     * @var int
     */
    private int $timestamp;

    /**
     * 在线信息
     * @var \app\model\OnlineModel|null
     */
    private ?OnlineModel $online;


    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     */
    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * @param string $controller
     */
    public function setController(string $controller): void
    {
        $this->controller = $controller;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction(string $action): void
    {
        $this->action = $action;
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

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState(string $state): void
    {
        $this->state = $state;
    }

    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @param int $timestamp
     */
    public function setTimestamp(int $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return \app\model\OnlineModel|null
     */
    public function getOnline(): ?OnlineModel
    {
        return $this->online;
    }

    /**
     * @param \app\model\OnlineModel|null $online
     */
    public function setOnline(?OnlineModel $online): void
    {
        $this->online = $online;
    }
}

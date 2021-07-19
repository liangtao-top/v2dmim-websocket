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
// | Version: 2.0 2021/6/2 14:12
// +----------------------------------------------------------------------

namespace com\response;

use com\struct\Struct;

class Response extends Struct
{
    /**
     * 连接ID
     * @var int
     */
    private int $fd;

    /**
     * 类型
     * @var \com\response\Type
     */
    private Type $type;

    /**
     * 应答报文
     * @var \com\response\Ask|null
     */
    private ?Ask $ask = null;

    /**
     * 通知报文
     * @var \com\response\Notify|null
     */
    private ?Notify $notify = null;

    /**
     * @return int
     */
    public function getFd(): int
    {
        return $this->fd;
    }

    /**
     * @param int $fd
     */
    public function setFd(int $fd): void
    {
        $this->fd = $fd;
    }

    /**
     * @return \com\response\Type
     */
    public function getType(): Type
    {
        return $this->type;
    }

    /**
     * @param \com\response\Type $type
     */
    public function setType(Type $type): void
    {
        $this->type = $type;
    }

    /**
     * @return \com\response\Ask|null
     */
    public function getAsk(): ?Ask
    {
        return $this->ask;
    }

    /**
     * @param \com\response\Ask|null $ask
     */
    public function setAsk(?Ask $ask): void
    {
        $this->ask = $ask;
    }

    /**
     * @return \com\response\Notify|null
     */
    public function getNotify(): ?Notify
    {
        return $this->notify;
    }

    /**
     * @param \com\response\Notify|null $notify
     */
    public function setNotify(?Notify $notify): void
    {
        $this->notify = $notify;
    }

}

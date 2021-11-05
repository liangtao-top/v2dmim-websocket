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
// | Version: 2.0 2021/6/3 13:42
// +----------------------------------------------------------------------

namespace com\response;

use com\event\Event;
use com\struct\Struct;

/**
 * 通知报文
 * @package com\response
 */
class Notify extends Struct
{

    private Event $event;

    private mixed $data;

    private int $sequence;

    /**
     * @return \com\event\Event
     */
    public function getEvent(): Event
    {
        return $this->event;
    }

    /**
     * @param \com\event\Event $event
     */
    public function setEvent(Event $event): void
    {
        $this->event = $event;
    }

    /**
     * @return mixed
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData(mixed $data): void
    {
        $this->data = $data;
    }

    /**
     * @return int
     */
    public function getSequence(): int
    {
        return $this->sequence;
    }

    /**
     * @param int $sequence
     */
    public function setSequence(int $sequence): void
    {
        $this->sequence = $sequence;
    }
}

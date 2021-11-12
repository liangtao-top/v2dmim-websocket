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
// | Date: 2021/6/2 16:19
// +----------------------------------------------------------------------
namespace V2dmIM\WebSocket\command;

/**
 * Class Result
 * @package com\response
 */
class Result
{

    /**
     * 服务主动推送事件
     * @param string $event
     * @param mixed  $data
     * @return bool|string
     * @author TaoGe <liangtao.gz@foxmail.com>
     * @date   2021/4/19 10:43
     */
    public static function e(string $event, mixed $data = ''): bool|string
    {
        return json_encode(['event' => $event, 'data' => $data], JSON_UNESCAPED_UNICODE);
    }

    /**
     * 返回成功信息
     * @param mixed  $data
     * @param string $msg
     * @param array  $append
     * @return false|string
     * @author TaoGe <liangtao.gz@foxmail.com>
     * @date   2019-11-28 11:46
     */
    public static function y(mixed $data, string $msg = 'success', array $append = []): bool|string
    {
        $result = [
            'code' => 1,
            'msg'  => $msg,
            'data' => $data,
            'time' => time(),
        ];
        return json_encode(array_merge($result, $append), JSON_UNESCAPED_UNICODE);
    }

    /**
     * 返回失败信息
     * @param string $msg
     * @param mixed  $data
     * @param array  $append
     * @return false|string
     * @author TaoGe <liangtao.gz@foxmail.com>
     * @date   2019-11-28 11:46
     */
    public static function n(string $msg = 'fail', mixed $data = '', array $append = []): bool|string
    {
        $result = [
            'code' => 0,
            'msg'  => $msg,
            'data' => $data,
            'time' => time(),
        ];
        return json_encode(array_merge($result, $append), JSON_UNESCAPED_UNICODE);
    }

}

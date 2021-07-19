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
// | Date: 2021/5/31 14:25
// +----------------------------------------------------------------------

namespace app\controller;

use app\common\Base;
use app\dao\DeviceDao;
use app\dao\OnlineDao;
use app\service\UserService;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server as Ws;

/**
 * Class User
 * @package app\controller
 */
class User extends Base
{

    /**
     * 用户登录
     * @param array                    $param
     * @param \Swoole\WebSocket\Server $ws
     * @param \Swoole\WebSocket\Frame  $frame
     * @return bool
     * @author TaoGe <liangtao.gz@foxmail.com>
     * @date   2021/6/1 12:01
     */
    public function login(array $param, Ws &$ws, Frame &$frame): bool
    {
        $data     = $param['data'] ?? [];
        $validate = new \app\validate\User();
        $result   = $validate->scene(__FUNCTION__)->check($data);
        if (!$result) {
            $this->setError($validate->getError());
            return false;
        }
        $device = DeviceDao::get($frame->fd);
        if (isset($data['device_token']) && !empty($data['device_token'])) {
            $device->setDeviceToken($data['device_token']);
            DeviceDao::set($device);
        }
        $service = new UserService;
        $result  = $service->login($data['uuid'], $data['sign'], $device);
        if (!$result) {
            $this->setError($service->getError());
            return false;
        }
        $this->setResult($service->getResult());
        return true;
    }

    /**
     * 退出登录
     * @param array                    $param
     * @param \Swoole\WebSocket\Server $ws
     * @param \Swoole\WebSocket\Frame  $frame
     * @return bool
     * @author TaoGe <liangtao.gz@foxmail.com>
     * @date   2021/6/1 12:01
     */
    public function logout(array $param, Ws &$ws, Frame &$frame): bool
    {
        $online = OnlineDao::get($frame->fd);
        if ($online) {
            OnlineDao::del($online);
        }
        return true;
    }

}

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
// | Version: 2.0 2021/5/31 14:28
// +----------------------------------------------------------------------

namespace app\service;

use app\common\Service;
use app\dao\OnlineDao;
use app\dao\ProfileDao;
use app\model\DeviceModel;
use app\model\OnlineModel;
use com\ip\LocalIP;
use com\sign\UserSig;

/**
 * Class User
 * @package app\service
 */
class UserService extends Service
{

    public function login(string $uuid, string $sign, DeviceModel $device): bool
    {
        $init_time   = 0;
        $expire_time = 0;
        $error_msg   = '';
        $res         = (new UserSig)->verifySig($sign, $uuid, $init_time, $expire_time, $error_msg);
        if (!$res) {
            $this->setError($error_msg);
            return false;
        }
        $find = ProfileDao::get($uuid);
        if (is_null($find)) {
            $this->setError('用户资料不存在。');
            return false;
        }
        $time = time();
        OnlineDao::set(new OnlineModel([
                                            'userId'         => $uuid,
                                            'fd'             => $device->getFd(),
                                            'gatewayIp'      => LocalIP::instance()->getIp(),
                                            'device'         => $device->getDevice(),
                                            'loginIp'        => $device->getIp(),
                                            'loginTime'      => $time,
                                            'lastActiveTime' => $time,
                                        ]));
        $this->setResult($find->toArray());
        return true;
    }

}

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
// | Version: 2.0 2021/4/17 17:56
// +----------------------------------------------------------------------

namespace com\event;

use com\enum\Enum;

class Event extends Enum
{

    // SDK 已经成功连接到云服务器
    const onConnectSuccess = 'onConnectSuccess';

    // 当前用户被踢下线，此时可以 UI 提示用户，并再次调用 V2DMIM 的 login() 函数重新登录。
    const onKickedOffline = 'onKickedOffline';

    // 在线时票据过期：此时您需要生成新的 userSig 并再次调用 V2DMIM 的 login() 函数重新登录。
    const onUserSigExpired = 'onUserSigExpired';

    // 登录用户的资料发生了更新
    const onSelfInfoUpdated = 'onSelfInfoUpdated';

    // 同步服务器会话开始，SDK 会在登录成功或者断网重连后自动同步服务器会话，您可以监听这个事件做一些 UI 进度展示操作。
    const onSyncServerStart = 'onSyncServerStart';

    // 同步服务器会话完成，如果会话有变更，会通过 onNewConversation | onConversationChanged 回调告知客户
    const onSyncServerFinish = 'onSyncServerFinish';

    // 同步服务器会话失败
    const onSyncServerFailed = 'onSyncServerFailed';

    // 有新的会话（比如收到一个新同事发来的单聊消息、或者被拉入了一个新的群组中），可以根据会话的 lastMessage -> timestamp 重新对会话列表做排序
    const onNewConversation = 'onNewConversation';

    // 某些会话的关键信息发生变化（未读计数发生变化、最后一条消息被更新等等），可以根据会话的 lastMessage -> timestamp 重新对会话列表做排序
    const onConversationChanged = 'onConversationChanged';

    // 会话未读总数变更通知
    const onTotalUnreadMessageCountChanged = 'onTotalUnreadMessageCountChanged';

    // 收到新消息
    //
    // 参数
    // msg	消息
    const onRecvNewMessage = 'onRecvNewMessage';

    // 收到 C2C 消息已读回执
    //
    // 参数
    // receiptList	已读回执列表
    const onRecvC2CReadReceipt = 'onRecvC2CReadReceipt';

    // 收到消息撤回的通知
    //
    // 参数
    // msgId	消息唯一标识
    const onRecvMessageRevoked = 'onRecvMessageRevoked';

    // 好友申请新增通知，两种情况会收到这个回调：
    //
    // 自己申请加别人好友
    // 别人申请加自己好友
    const onFriendApplicationListAdded = 'onFriendApplicationListAdded';

    // 好友申请删除通知，四种情况会收到这个回调
    //
    // 调用 deleteFriendApplication 主动删除好友申请
    // 调用 refuseFriendApplication 拒绝好友申请
    // 调用 acceptFriendApplication 同意好友申请且同意类型为FRIEND_ACCEPT_AGREE 时
    // 申请加别人好友被拒绝
    const onFriendApplicationListDeleted = 'onFriendApplicationListDeleted';

    // 好友申请已读通知，如果调用 setFriendApplicationRead 设置好友申请列表已读，会收到这个回调（主要用于多端同步）
    const onFriendApplicationListRead = 'onFriendApplicationListRead';

    // 好友新增通知
    const onFriendListAdded = 'onFriendListAdded';

    // 好友删除通知，，两种情况会收到这个回调：
    //
    // 自己删除好友（单向和双向删除都会收到回调）
    // 好友把自己删除（双向删除会收到）
    const onFriendListDeleted = 'onFriendListDeleted';

    // 黑名单新增通知
    const onBlackListAdd = 'onBlackListAdd';

    // 黑名单删除通知
    const onBlackListDeleted = 'onBlackListDeleted';

    // 好友资料更新通知
    const onFriendInfoChanged = 'onFriendInfoChanged';

    // 有用户加入群（全员能够收到）
    //
    // 参数
    // groupId	群 ID
    // memberList	加入的成员
    const onMemberEnter = 'onMemberEnter';

    // 有用户离开群（全员能够收到）
    //
    // 参数
    // groupId	群 ID
    // member	离开的成员
    const onMemberLeave = 'onMemberLeave';

    // 某些人被拉入某群（全员能够收到）
    //
    // 参数
    // groupId	群 ID
    // opUser	处理人
    // memberList	被拉进群成员
    const onMemberInvited = 'onMemberInvited';

    // 某些人被踢出某群（全员能够收到）
    //
    // 参数
    // groupId	群 ID
    // opUser	处理人
    // memberList	被踢成员
    const onMemberKicked = 'onMemberKicked';

    // 群成员信息被修改，仅支持禁言通知（全员能收到）。
    //
    // 注意
    // 会议群（Meeting）和直播群（AVChatRoom）默认无此回调
    // 参数
    // groupId	群 ID
    // memberChangeInfoList	被修改的群成员信息
    const onMemberInfoChanged = 'onMemberInfoChanged';

    // 创建群（主要用于多端同步）
    //
    // 参数
    // groupId	群 ID
    const onGroupCreated = 'onGroupCreated';

    // 群被解散了（全员能收到）
    //
    // 参数
    // groupId	群 ID
    // opUser	处理人
    const onGroupDismissed = 'onGroupDismissed';

    // 群被回收（全员能收到）
    //
    // 参数
    // groupId	群 ID
    // opUser
    const onGroupRecycled = 'onGroupRecycled';

    // 群信息被修改（全员能收到）
    //
    // 参数
    // changeInfos	修改的群信息
    const onGroupInfoChanged = 'onGroupInfoChanged';

    // 有新的加群请求（只有群主或管理员会收到）
    //
    // 参数
    // groupId	群 ID
    // member	申请人
    // opReason	申请原因
    const onReceiveJoinApplication = 'onReceiveJoinApplication';

    // 加群请求已经被群主或管理员处理了（只有申请人能够收到）
    //
    // 参数
    // groupId	群 ID
    // opUser	处理人
    // isAgreeJoin	是否同意加群
    // opReason	处理原因
    const onApplicationProcessed = 'onApplicationProcessed';

    // 指定管理员身份
    //
    // 参数
    // groupId	群 ID
    // opUser	处理人
    // memberList	被处理的群成员
    const onGrantAdministrator = 'onGrantAdministrator';

    // 取消管理员身份
    //
    // 参数
    // groupId	群 ID
    // opUser	处理人
    // memberList	被处理的群成员
    const onRevokeAdministrator = 'onRevokeAdministrator';

    // 主动退出群组（主要用于多端同步，直播群（AVChatRoom）不支持）
    //
    // 参数
    // groupId	群 ID
    const onQuitFromGroup = '';

    // 收到 RESTAPI 下发的自定义系统消息
    //
    // 参数
    // groupId	群 ID
    // customData	自定义数据
    const onReceiveRESTCustomData = 'onReceiveRESTCustomData';

    // 收到群属性更新的回调
    //
    // 参数
    // groupId	群 ID
    // groupAttributeMap	群的所有属性
    const onGroupAttributeChanged = 'onGroupAttributeChanged';

}

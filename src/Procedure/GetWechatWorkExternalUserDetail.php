<?php

namespace WechatWorkExternalContactBundle\Procedure;

use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use WechatWorkExternalContactBundle\Event\GetExternalUserDetailEvent;
use WechatWorkExternalContactBundle\Repository\ExternalUserRepository;

/**
 * @see https://developer.work.weixin.qq.com/document/path/94315
 * @see https://developer.work.weixin.qq.com/document/path/91799
 */
#[MethodTag(name: '企业微信')]
#[MethodDoc(summary: '获取企微外部联系人详情')]
#[MethodExpose(method: 'GetWechatWorkExternalUserDetail')]
class GetWechatWorkExternalUserDetail extends BaseProcedure
{
    #[MethodParam(description: '进入会话的场景值，例如 single_kf_tools')]
    public string $entry;

    #[MethodParam(description: '分享Ticket')]
    public string $shareTicket = '';

    #[MethodParam(description: '外部联系人ID')]
    public string $externalUserId;

    public function __construct(
        private readonly ExternalUserRepository $externalUserRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {}

    public function execute(): array
    {
        $externalUser = $this->externalUserRepository->findOneBy([
            'externalUserId' => $this->externalUserId,
        ]);
        if ($externalUser === null) {
            throw new ApiException('找不到指定外部用户');
        }

        // mysql> select * from wechat_work_external_user where external_user_id = 'wm2A3kDQAAlO4wgTIEEfM7qC0VDAO3Hg'\G
        // *************************** 1. row ***************************
        //                   id: 1
        //              corp_id: 1
        //             nickname: Better
        //     external_user_id: wm2A3kDQAAlO4wgTIEEfM7qC0VDAO3Hg
        //             union_id: oHnWtwlt4nGjRT1i1XfTqFKO4fEc
        //               avatar: http://wx.qlogo.cn/mmhead/PiajxSqBRaELhmIA2Evnd7zibfM7zldFpByZcxeqjuDpQNOQj31uy0RA/0
        //               gender: 1
        // enter_session_context: {"scene": "default"}
        //               remark: NULL
        //                 tags: []
        //          create_time: 2023-02-11 12:04:13
        //          update_time: 2023-03-22 17:18:59

        // 这里分发一个事件出去，是为了方便后续我们给第三方系统补充信息
        $event = new GetExternalUserDetailEvent();
        $event->setExternalUser($externalUser);
        $event->setResult($externalUser->retrieveApiArray());
        $this->eventDispatcher->dispatch($event);

        return $event->getResult();
    }
}

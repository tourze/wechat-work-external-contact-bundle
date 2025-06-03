<?php

namespace WechatWorkExternalContactBundle\EventSubscriber;

use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Tourze\WechatWorkContracts\UserLoaderInterface;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Service\WorkService;
use WechatWorkExternalContactBundle\Entity\ExternalServiceRelation;
use WechatWorkExternalContactBundle\Entity\ExternalUser;
use WechatWorkExternalContactBundle\Repository\ExternalServiceRelationRepository;
use WechatWorkExternalContactBundle\Repository\ExternalUserRepository;
use WechatWorkExternalContactBundle\Request\GetExternalContactRequest;
use WechatWorkServerBundle\Event\WechatWorkServerMessageRequestEvent;
use WechatWorkStaffBundle\Entity\User;

/**
 * 外部联系人相关的处理逻辑
 *
 * @see https://developer.work.weixin.qq.com/document/path/92277
 */
class ExternalUserSubscriber
{
    public function __construct(
        private readonly ExternalUserRepository $externalUserRepository,
        private readonly WorkService $workService,
        private readonly UserLoaderInterface $userLoader,
        private readonly ExternalServiceRelationRepository $externalServiceRelationRepository,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[AsEventListener(priority: 99)] // 这里的同步，优先级高点比较好
    public function onServerMessageRequest(WechatWorkServerMessageRequestEvent $event): void
    {
        $message = $event->getMessage()->getRawData();
        $this->logger->debug('尝试处理外部联系人相关逻辑', [
            'event' => $event,
            'message' => $message,
        ]);
        if (!isset($message['ExternalUserID'])) {
            return;
        }

        // 查找对应的服务人员信息
        $user = $this->userLoader->loadUserByUserIdAndCorp($message['UserID'], $event->getMessage()->getCorp());
        if (!$user) {
            // 从逻辑上来讲，到这里的数据，UserID都应该存在的
            $user = new User();
            $user->setCorp($event->getMessage()->getCorp());
            $user->setAgent($event->getMessage()->getAgent());
            $user->setUserId($message['UserID']);
            $user->setName($message['UserID']);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        // 先保存最基础的外部联系人信息
        $externalUser = $this->externalUserRepository->findOneBy([
            'corp' => $event->getMessage()->getCorp(),
            'externalUserId' => $message['ExternalUserID'],
        ]);
        if (!$externalUser) {
            $externalUser = new ExternalUser();
            $externalUser->setCorp($event->getMessage()->getCorp());
            $externalUser->setExternalUserId($message['ExternalUserID']);
            $this->entityManager->persist($externalUser);
            $this->entityManager->flush();
        }

        // 查找关系
        $relation = $this->externalServiceRelationRepository->findOneBy([
            'user' => $user,
            'externalUser' => $externalUser,
        ]);
        if (!$relation) {
            $relation = new ExternalServiceRelation();
            $relation->setUser($user);
            $relation->setExternalUser($externalUser);
            $relation->setCorp($user->getCorp());
        }

        $changeType = $message['ChangeType'] ?? '';
        // 根据不同的时间，更新不同的字段喔
        if ('add_external_contact' === $changeType) {
            $relation->setAddExternalContactTime(Carbon::createFromTimestamp($message['CreateTime'], date_default_timezone_get()));
        }
        if ('add_half_external_contact' === $changeType) {
            $relation->setAddHalfExternalContactTime(Carbon::createFromTimestamp($message['CreateTime'], date_default_timezone_get()));
        }
        if ('del_follow_user' === $changeType) {
            $relation->setDelFollowUserTime(Carbon::createFromTimestamp($message['CreateTime'], date_default_timezone_get()));
        }
        if ('del_external_contact' === $changeType) {
            $relation->setDelExternalContactTime(Carbon::createFromTimestamp($message['CreateTime'], date_default_timezone_get()));
        }
        $this->entityManager->persist($externalUser);
        $this->entityManager->persist($relation);
        $this->entityManager->flush();

        // 最后，更新一次消费者详情
        if ('del_external_contact' === $changeType) {
            return;
        }
        $this->fetchExternalUserDetail($externalUser, $event->getMessage()->getAgent());
    }

    private function fetchExternalUserDetail(ExternalUser $externalUser, Agent $agent): void
    {
        // 查找和保存其他用户信息
        $request = new GetExternalContactRequest();
        $request->setExternalUserId($externalUser->getExternalUserId());
        $request->setAgent($agent);
        $response = $this->workService->request($request);

        if (isset($response['external_contact'])) {
            $externalUser->setRawData($response['external_contact']);
            if (isset($response['external_contact']['unionid'])) {
                $externalUser->setUnionId($response['external_contact']['unionid']);
            }
            if (isset($response['external_contact']['avatar'])) {
                $externalUser->setAvatar($response['external_contact']['avatar']);
            }
            if (isset($response['external_contact']['name'])) {
                $externalUser->setNickname($response['external_contact']['name']);
            }
            $this->entityManager->persist($externalUser);
            $this->entityManager->flush();
        }
    }
}

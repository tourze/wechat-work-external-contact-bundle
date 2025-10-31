<?php

namespace WechatWorkExternalContactBundle\EventSubscriber;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Tourze\WechatWorkContracts\AgentInterface;
use Tourze\WechatWorkContracts\UserInterface;
use Tourze\WechatWorkContracts\UserLoaderInterface;
use WechatWorkBundle\Service\WorkServiceInterface;
use WechatWorkExternalContactBundle\Entity\ExternalServiceRelation;
use WechatWorkExternalContactBundle\Entity\ExternalUser;
use WechatWorkExternalContactBundle\Exception\ExternalContactAgentException;
use WechatWorkExternalContactBundle\Exception\ExternalContactCorpException;
use WechatWorkExternalContactBundle\Exception\ExternalContactUserException;
use WechatWorkExternalContactBundle\Repository\ExternalServiceRelationRepository;
use WechatWorkExternalContactBundle\Repository\ExternalUserRepository;
use WechatWorkExternalContactBundle\Request\GetExternalContactRequest;
use WechatWorkServerBundle\Event\WechatWorkServerMessageRequestEvent;

/**
 * 外部联系人相关的处理逻辑
 *
 * @see https://developer.work.weixin.qq.com/document/path/92277
 */
#[WithMonologChannel(channel: 'wechat_work_external_contact')]
class ExternalUserSubscriber
{
    public function __construct(
        private readonly ExternalUserRepository $externalUserRepository,
        private readonly WorkServiceInterface $workService,
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

        $user = $this->getOrCreateUser($event, $message);
        $externalUser = $this->getOrCreateExternalUser($event, $message);
        $relation = $this->updateServiceRelation($user, $externalUser, $event, $message);

        $this->entityManager->persist($externalUser);
        $this->entityManager->persist($relation);
        $this->entityManager->flush();

        $this->updateExternalUserDetailsIfNeeded($externalUser, $event, $message);
    }

    /**
     * @param array<string, mixed> $message
     */
    private function getOrCreateUser(WechatWorkServerMessageRequestEvent $event, array $message): object
    {
        $corp = $event->getMessage()->getCorp();

        if (null === $corp) {
            throw new ExternalContactCorpException('Corp cannot be null when creating user');
        }

        // 确保 UserID 键存在且为字符串类型
        assert(isset($message['UserID']));
        assert(is_string($message['UserID']));

        $user = $this->userLoader->loadUserByUserIdAndCorp($message['UserID'], $corp);
        $agent = $event->getMessage()->getAgent();

        if (null === $agent) {
            throw new ExternalContactAgentException('Agent cannot be null when creating user');
        }

        if (null === $user) {
            $user = $this->userLoader->createUser(
                $corp,
                $agent,
                $message['UserID'],
                $message['UserID'],
            );
        }

        return $user;
    }

    /**
     * @param array<string, mixed> $message
     */
    private function getOrCreateExternalUser(WechatWorkServerMessageRequestEvent $event, array $message): ExternalUser
    {
        // 确保 ExternalUserID 键存在且为字符串类型
        assert(isset($message['ExternalUserID']));
        assert(is_string($message['ExternalUserID']));

        $externalUser = $this->externalUserRepository->findOneBy([
            'corp' => $event->getMessage()->getCorp(),
            'externalUserId' => $message['ExternalUserID'],
        ]);

        if (null === $externalUser) {
            $externalUser = new ExternalUser();
            $externalUser->setCorp($event->getMessage()->getCorp());
            $externalUser->setExternalUserId($message['ExternalUserID']);
            $this->entityManager->persist($externalUser);
            $this->entityManager->flush();
        }

        return $externalUser;
    }

    /**
     * @param array<string, mixed> $message
     */
    private function updateServiceRelation(object $user, ExternalUser $externalUser, WechatWorkServerMessageRequestEvent $event, array $message): ExternalServiceRelation
    {
        $relation = $this->externalServiceRelationRepository->findOneBy([
            'user' => $user,
            'externalUser' => $externalUser,
        ]);

        if (null === $relation) {
            $relation = new ExternalServiceRelation();
            if ($user instanceof UserInterface) {
                $relation->setUser($user);
            } else {
                throw new ExternalContactUserException('User must implement UserInterface');
            }
            $relation->setExternalUser($externalUser);
            $relation->setCorp($event->getMessage()->getCorp());
        }

        $this->updateRelationTimestamps($relation, $message);

        return $relation;
    }

    /**
     * @param array<string, mixed> $message
     */
    private function updateRelationTimestamps(ExternalServiceRelation $relation, array $message): void
    {
        $changeType = $message['ChangeType'] ?? '';

        // 确保 CreateTime 键存在且为数值类型
        assert(isset($message['CreateTime']));
        assert(is_numeric($message['CreateTime']));

        $timestamp = CarbonImmutable::createFromTimestamp((int) $message['CreateTime'], date_default_timezone_get());

        match ($changeType) {
            'add_external_contact' => $relation->setAddExternalContactTime($timestamp),
            'add_half_external_contact' => $relation->setAddHalfExternalContactTime($timestamp),
            'del_follow_user' => $relation->setDelFollowUserTime($timestamp),
            'del_external_contact' => $relation->setDelExternalContactTime($timestamp),
            default => null,
        };
    }

    /**
     * @param array<string, mixed> $message
     */
    private function updateExternalUserDetailsIfNeeded(ExternalUser $externalUser, WechatWorkServerMessageRequestEvent $event, array $message): void
    {
        $changeType = $message['ChangeType'] ?? '';

        if ('del_external_contact' === $changeType) {
            return;
        }

        $agent = $event->getMessage()->getAgent();
        if (null === $agent) {
            throw new ExternalContactAgentException('Agent cannot be null when fetching external user detail');
        }
        $this->fetchExternalUserDetail($externalUser, $agent);
    }

    private function fetchExternalUserDetail(ExternalUser $externalUser, AgentInterface $agent): void
    {
        // 查找和保存其他用户信息
        $request = new GetExternalContactRequest();
        $externalUserId = $externalUser->getExternalUserId();
        if (null === $externalUserId) {
            throw new ExternalContactUserException('External user ID cannot be null');
        }
        $request->setExternalUserId($externalUserId);
        $request->setAgent($agent);
        $response = $this->workService->request($request);

        // 确保 response 是数组类型且包含 external_contact 键
        if (is_array($response) && isset($response['external_contact']) && is_array($response['external_contact'])) {
            // 类型转换：确保传递给 setRawData 的是符合 array<string, mixed>|null 类型的数据
            $externalContactData = $response['external_contact'];
            $typedExternalContactData = $this->ensureStringKeys($externalContactData);
            $externalUser->setRawData($typedExternalContactData);

            $this->updateExternalUserFromContactData($externalUser, $typedExternalContactData);

            $this->entityManager->persist($externalUser);
            $this->entityManager->flush();
        }
    }

    /**
     * 确保数组的键都是字符串类型
     *
     * @param array<mixed, mixed> $data
     * @return array<string, mixed>
     */
    private function ensureStringKeys(array $data): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            if (is_string($key)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * 从外部联系人数据更新用户信息
     *
     * @param array<string, mixed> $contactData
     */
    private function updateExternalUserFromContactData(ExternalUser $externalUser, array $contactData): void
    {
        // 使用空合并操作符确保数组安全访问，并进行类型检查
        $unionId = $contactData['unionid'] ?? null;
        if (null !== $unionId && is_string($unionId)) {
            $externalUser->setUnionId($unionId);
        }

        $avatar = $contactData['avatar'] ?? null;
        if (null !== $avatar && is_string($avatar)) {
            $externalUser->setAvatar($avatar);
        }

        $name = $contactData['name'] ?? null;
        if (null !== $name && is_string($name)) {
            $externalUser->setNickname($name);
        }
    }
}

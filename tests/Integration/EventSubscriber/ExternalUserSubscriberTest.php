<?php

namespace WechatWorkExternalContactBundle\Tests\Integration\EventSubscriber;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Tourze\WechatWorkContracts\AgentInterface;
use Tourze\WechatWorkContracts\CorpInterface;
use Tourze\WechatWorkContracts\UserInterface;
use Tourze\WechatWorkContracts\UserLoaderInterface;
use WechatWorkBundle\Service\WorkService;
use WechatWorkExternalContactBundle\Entity\ExternalServiceRelation;
use WechatWorkExternalContactBundle\Entity\ExternalUser;
use WechatWorkExternalContactBundle\EventSubscriber\ExternalUserSubscriber;
use WechatWorkExternalContactBundle\Repository\ExternalServiceRelationRepository;
use WechatWorkExternalContactBundle\Repository\ExternalUserRepository;
use WechatWorkExternalContactBundle\Request\GetExternalContactRequest;
use WechatWorkServerBundle\Event\WechatWorkServerMessageRequestEvent;
use WechatWorkServerBundle\Entity\ServerMessage;

/**
 * ExternalUserSubscriber 集成测试
 *
 * 测试外部联系人订阅器的所有功能
 */
class ExternalUserSubscriberTest extends TestCase
{
    private ExternalUserSubscriber $subscriber;
    private MockObject|ExternalUserRepository $externalUserRepository;
    private MockObject|WorkService $workService;
    private MockObject|UserLoaderInterface $userLoader;
    private MockObject|ExternalServiceRelationRepository $externalServiceRelationRepository;
    private MockObject|LoggerInterface $logger;
    private MockObject|EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->externalUserRepository = $this->createMock(ExternalUserRepository::class);
        $this->workService = $this->createMock(WorkService::class);
        $this->userLoader = $this->createMock(UserLoaderInterface::class);
        $this->externalServiceRelationRepository = $this->createMock(ExternalServiceRelationRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->subscriber = new ExternalUserSubscriber(
            $this->externalUserRepository,
            $this->workService,
            $this->userLoader,
            $this->externalServiceRelationRepository,
            $this->logger,
            $this->entityManager
        );
    }

    public function test_onServerMessageRequest_withoutExternalUserId_returnsEarly(): void
    {
        $message = new ServerMessage();
        $rawData = ['UserID' => 'user123'];
        $message->setRawData($rawData);

        $event = new WechatWorkServerMessageRequestEvent();
        $event->setMessage($message);

        $this->logger->expects($this->once())
            ->method('debug')
            ->with('尝试处理外部联系人相关逻辑', $this->anything());

        $this->externalUserRepository->expects($this->never())
            ->method('findOneBy');

        $this->subscriber->onServerMessageRequest($event);
    }

    public function test_onServerMessageRequest_withExternalUserId_createsNewExternalUser(): void
    {
        $corp = $this->createMock(CorpInterface::class);
        $agent = $this->createMock(AgentInterface::class);
        $user = $this->createMock(UserInterface::class);

        $message = new ServerMessage();
        $rawData = [
            'UserID' => 'user123',
            'ExternalUserID' => 'ext_user456',
            'ChangeType' => 'add_external_contact',
            'CreateTime' => 1705300200,
        ];
        $message->setRawData($rawData);
        $message->setCorp($corp);
        $message->setAgent($agent);

        $event = new WechatWorkServerMessageRequestEvent();
        $event->setMessage($message);

        $this->userLoader->expects($this->once())
            ->method('loadUserByUserIdAndCorp')
            ->with('user123', $corp)
            ->willReturn($user);

        $this->externalUserRepository->expects($this->once())
            ->method('findOneBy')
            ->with([
                'corp' => $corp,
                'externalUserId' => 'ext_user456',
            ])
            ->willReturn(null);

        $this->externalServiceRelationRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $capturedExternalUser = null;
        $capturedRelation = null;

        $this->entityManager->expects($this->atLeast(3))
            ->method('persist')
            ->willReturnCallback(function ($entity) use (&$capturedExternalUser, &$capturedRelation) {
                if ($entity instanceof ExternalUser) {
                    $capturedExternalUser = $entity;
                } elseif ($entity instanceof ExternalServiceRelation) {
                    $capturedRelation = $entity;
                }
            });

        $this->entityManager->expects($this->atLeast(2))
            ->method('flush');

        $this->workService->expects($this->once())
            ->method('request')
            ->willReturn([
                'external_contact' => [
                    'unionid' => 'union789',
                    'avatar' => 'https://example.com/avatar.jpg',
                    'name' => '张三',
                ],
            ]);

        $this->subscriber->onServerMessageRequest($event);

        $this->assertNotNull($capturedExternalUser);
        $this->assertSame($corp, $capturedExternalUser->getCorp());
        $this->assertSame('ext_user456', $capturedExternalUser->getExternalUserId());
        $this->assertSame('union789', $capturedExternalUser->getUnionId());
        $this->assertSame('https://example.com/avatar.jpg', $capturedExternalUser->getAvatar());
        $this->assertSame('张三', $capturedExternalUser->getNickname());

        $this->assertNotNull($capturedRelation);
        $this->assertSame($user, $capturedRelation->getUser());
        $this->assertSame($capturedExternalUser, $capturedRelation->getExternalUser());
        $this->assertSame($corp, $capturedRelation->getCorp());
        $this->assertInstanceOf(CarbonImmutable::class, $capturedRelation->getAddExternalContactTime());
    }

    public function test_onServerMessageRequest_withExistingExternalUser_updatesRelation(): void
    {
        $corp = $this->createMock(CorpInterface::class);
        $agent = $this->createMock(AgentInterface::class);
        $user = $this->createMock(UserInterface::class);
        $externalUser = new ExternalUser();
        $externalUser->setCorp($corp);
        $externalUser->setExternalUserId('ext_user456');

        $message = new ServerMessage();
        $rawData = [
            'UserID' => 'user123',
            'ExternalUserID' => 'ext_user456',
            'ChangeType' => 'add_half_external_contact',
            'CreateTime' => 1705300200,
        ];
        $message->setRawData($rawData);
        $message->setCorp($corp);
        $message->setAgent($agent);

        $event = new WechatWorkServerMessageRequestEvent();
        $event->setMessage($message);

        $this->userLoader->expects($this->once())
            ->method('loadUserByUserIdAndCorp')
            ->with('user123', $corp)
            ->willReturn($user);

        $this->externalUserRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($externalUser);

        $relation = new ExternalServiceRelation();
        $relation->setUser($user);
        $relation->setExternalUser($externalUser);
        $relation->setCorp($corp);

        $this->externalServiceRelationRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($relation);

        $this->entityManager->expects($this->atLeast(2))
            ->method('persist');

        $this->entityManager->expects($this->atLeast(1))
            ->method('flush');

        $this->workService->expects($this->once())
            ->method('request')
            ->willReturn(['external_contact' => []]);

        $this->subscriber->onServerMessageRequest($event);

        $this->assertInstanceOf(CarbonImmutable::class, $relation->getAddHalfExternalContactTime());
    }

    public function test_onServerMessageRequest_withDelExternalContact_skipsDetailFetch(): void
    {
        $corp = $this->createMock(CorpInterface::class);
        $agent = $this->createMock(AgentInterface::class);
        $user = $this->createMock(UserInterface::class);
        $externalUser = new ExternalUser();
        $relation = new ExternalServiceRelation();

        $message = new ServerMessage();
        $rawData = [
            'UserID' => 'user123',
            'ExternalUserID' => 'ext_user456',
            'ChangeType' => 'del_external_contact',
            'CreateTime' => 1705300200,
        ];
        $message->setRawData($rawData);
        $message->setCorp($corp);
        $message->setAgent($agent);

        $event = new WechatWorkServerMessageRequestEvent();
        $event->setMessage($message);

        $this->userLoader->expects($this->once())
            ->method('loadUserByUserIdAndCorp')
            ->willReturn($user);

        $this->externalUserRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($externalUser);

        $this->externalServiceRelationRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($relation);

        $this->entityManager->expects($this->exactly(2))
            ->method('persist');

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->workService->expects($this->never())
            ->method('request');

        $this->subscriber->onServerMessageRequest($event);

        $this->assertInstanceOf(CarbonImmutable::class, $relation->getDelExternalContactTime());
    }

    public function test_onServerMessageRequest_withDelFollowUser_updatesTime(): void
    {
        $corp = $this->createMock(CorpInterface::class);
        $agent = $this->createMock(AgentInterface::class);
        $user = $this->createMock(UserInterface::class);
        $externalUser = new ExternalUser();
        $externalUser->setExternalUserId('ext_user456');
        $relation = new ExternalServiceRelation();

        $message = new ServerMessage();
        $rawData = [
            'UserID' => 'user123',
            'ExternalUserID' => 'ext_user456',
            'ChangeType' => 'del_follow_user',
            'CreateTime' => 1705300200,
        ];
        $message->setRawData($rawData);
        $message->setCorp($corp);
        $message->setAgent($agent);

        $event = new WechatWorkServerMessageRequestEvent();
        $event->setMessage($message);

        $this->userLoader->expects($this->once())
            ->method('loadUserByUserIdAndCorp')
            ->willReturn($user);

        $this->externalUserRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($externalUser);

        $this->externalServiceRelationRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($relation);

        $this->workService->expects($this->once())
            ->method('request')
            ->willReturn(['external_contact' => []]);

        $this->subscriber->onServerMessageRequest($event);

        $this->assertInstanceOf(CarbonImmutable::class, $relation->getDelFollowUserTime());
    }

    public function test_onServerMessageRequest_withNullUser_createsNewUser(): void
    {
        $corp = $this->createMock(CorpInterface::class);
        $agent = $this->createMock(AgentInterface::class);
        $newUser = $this->createMock(UserInterface::class);

        $message = new ServerMessage();
        $rawData = [
            'UserID' => 'user123',
            'ExternalUserID' => 'ext_user456',
            'ChangeType' => 'add_external_contact',
            'CreateTime' => 1705300200,
        ];
        $message->setRawData($rawData);
        $message->setCorp($corp);
        $message->setAgent($agent);

        $event = new WechatWorkServerMessageRequestEvent();
        $event->setMessage($message);

        $this->userLoader->expects($this->once())
            ->method('loadUserByUserIdAndCorp')
            ->with('user123', $corp)
            ->willReturn(null);

        $this->userLoader->expects($this->once())
            ->method('createUser')
            ->with($corp, $agent, 'user123', 'user123')
            ->willReturn($newUser);

        $this->externalUserRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $this->externalServiceRelationRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $this->workService->expects($this->once())
            ->method('request')
            ->willReturn(['external_contact' => []]);

        $this->subscriber->onServerMessageRequest($event);
    }

    public function test_onServerMessageRequest_withoutChangeType_doesNotUpdateTimes(): void
    {
        $corp = $this->createMock(CorpInterface::class);
        $agent = $this->createMock(AgentInterface::class);
        $user = $this->createMock(UserInterface::class);
        $externalUser = new ExternalUser();
        $externalUser->setExternalUserId('ext_user456');
        $relation = new ExternalServiceRelation();

        $message = new ServerMessage();
        $rawData = [
            'UserID' => 'user123',
            'ExternalUserID' => 'ext_user456',
            'CreateTime' => 1705300200,
        ];
        $message->setRawData($rawData);
        $message->setCorp($corp);
        $message->setAgent($agent);

        $event = new WechatWorkServerMessageRequestEvent();
        $event->setMessage($message);

        $this->userLoader->expects($this->once())
            ->method('loadUserByUserIdAndCorp')
            ->willReturn($user);

        $this->externalUserRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($externalUser);

        $this->externalServiceRelationRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($relation);

        $this->workService->expects($this->once())
            ->method('request')
            ->willReturn(['external_contact' => []]);

        $this->subscriber->onServerMessageRequest($event);

        $this->assertNull($relation->getAddExternalContactTime());
        $this->assertNull($relation->getAddHalfExternalContactTime());
        $this->assertNull($relation->getDelFollowUserTime());
        $this->assertNull($relation->getDelExternalContactTime());
    }

    public function test_fetchExternalUserDetail_withoutExternalContactData(): void
    {
        $corp = $this->createMock(CorpInterface::class);
        $agent = $this->createMock(AgentInterface::class);
        $user = $this->createMock(UserInterface::class);
        $externalUser = new ExternalUser();
        $externalUser->setExternalUserId('ext_user456');

        $message = new ServerMessage();
        $rawData = [
            'UserID' => 'user123',
            'ExternalUserID' => 'ext_user456',
            'ChangeType' => 'add_external_contact',
            'CreateTime' => 1705300200,
        ];
        $message->setRawData($rawData);
        $message->setCorp($corp);
        $message->setAgent($agent);

        $event = new WechatWorkServerMessageRequestEvent();
        $event->setMessage($message);

        $this->userLoader->expects($this->once())
            ->method('loadUserByUserIdAndCorp')
            ->willReturn($user);

        $this->externalUserRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($externalUser);

        $this->externalServiceRelationRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $this->workService->expects($this->once())
            ->method('request')
            ->with($this->callback(function (GetExternalContactRequest $request) {
                return $request->getExternalUserId() === 'ext_user456';
            }))
            ->willReturn([]);

        $this->entityManager->expects($this->exactly(2))
            ->method('persist');

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->subscriber->onServerMessageRequest($event);

        $this->assertNull($externalUser->getUnionId());
        $this->assertNull($externalUser->getAvatar());
        $this->assertNull($externalUser->getNickname());
    }

    public function test_fetchExternalUserDetail_withPartialData(): void
    {
        $corp = $this->createMock(CorpInterface::class);
        $agent = $this->createMock(AgentInterface::class);
        $user = $this->createMock(UserInterface::class);
        $externalUser = new ExternalUser();
        $externalUser->setExternalUserId('ext_user456');

        $message = new ServerMessage();
        $rawData = [
            'UserID' => 'user123',
            'ExternalUserID' => 'ext_user456',
            'ChangeType' => 'add_external_contact',
            'CreateTime' => 1705300200,
        ];
        $message->setRawData($rawData);
        $message->setCorp($corp);
        $message->setAgent($agent);

        $event = new WechatWorkServerMessageRequestEvent();
        $event->setMessage($message);

        $this->userLoader->expects($this->once())
            ->method('loadUserByUserIdAndCorp')
            ->willReturn($user);

        $this->externalUserRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($externalUser);

        $this->externalServiceRelationRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $this->workService->expects($this->once())
            ->method('request')
            ->willReturn([
                'external_contact' => [
                    'name' => '李四',
                ],
            ]);

        $this->subscriber->onServerMessageRequest($event);

        $this->assertNull($externalUser->getUnionId());
        $this->assertNull($externalUser->getAvatar());
        $this->assertSame('李四', $externalUser->getNickname());
    }
}
<?php

namespace WechatWorkExternalContactBundle\Tests\MessageHandler;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tourze\WechatWorkContracts\UserInterface;
use Tourze\WechatWorkContracts\UserLoaderInterface;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkExternalContactBundle\Entity\ExternalServiceRelation;
use WechatWorkExternalContactBundle\Entity\ExternalUser;
use WechatWorkExternalContactBundle\Message\SaveExternalContactListItemMessage;
use WechatWorkExternalContactBundle\MessageHandler\SaveExternalContactListItemHandler;
use WechatWorkExternalContactBundle\Repository\ExternalServiceRelationRepository;
use WechatWorkExternalContactBundle\Repository\ExternalUserRepository;

/**
 * 测试用的UserInterface实现
 */
class TestUser implements UserInterface
{
    public function __construct(
        private readonly string $userId,
        private readonly ?string $name = null
    ) {
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}

/**
 * SaveExternalContactListItemHandler测试
 * 
 * 测试关注点：
 * - 消息处理逻辑
 * - 外部用户创建和更新
 * - 关系管理
 * - 数据持久化
 */
class SaveExternalContactListItemHandlerTest extends TestCase
{
    private ExternalUserRepository&MockObject $externalUserRepository;
    private UserLoaderInterface&MockObject $userLoader;
    private ExternalServiceRelationRepository&MockObject $externalServiceRelationRepository;
    private AgentRepository&MockObject $agentRepository;
    private EntityManagerInterface&MockObject $entityManager;
    private SaveExternalContactListItemHandler $handler;

    protected function setUp(): void
    {
        $this->externalUserRepository = $this->createMock(ExternalUserRepository::class);
        $this->userLoader = $this->createMock(UserLoaderInterface::class);
        $this->externalServiceRelationRepository = $this->createMock(ExternalServiceRelationRepository::class);
        $this->agentRepository = $this->createMock(AgentRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->handler = new SaveExternalContactListItemHandler(
            $this->externalUserRepository,
            $this->userLoader,
            $this->externalServiceRelationRepository,
            $this->agentRepository,
            $this->entityManager
        );
    }

    public function testHandlerCreation(): void
    {
        // 测试处理器创建
        $this->assertInstanceOf(SaveExternalContactListItemHandler::class, $this->handler);
    }

    public function testInvokeWithNonExistentAgent(): void
    {
        // 测试代理不存在的情况
        $message = new SaveExternalContactListItemMessage();
        $message->setAgentId('non_existent_agent');
        $message->setItem(['external_userid' => 'ext_user_123']);

        $this->agentRepository->expects($this->once())
            ->method('find')
            ->with('non_existent_agent')
            ->willReturn(null);

        // 代理不存在时应该直接返回，不进行任何操作
        $this->externalUserRepository->expects($this->never())
            ->method('findOneBy');

        $this->entityManager->expects($this->never())
            ->method('persist');

        $this->handler->__invoke($message);
    }

    public function testInvokeWithNewExternalUser(): void
    {
        // 测试创建新外部用户
        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);

        $item = [
            'external_userid' => 'ext_user_new',
            'name' => '新用户',
            'is_customer' => true,
            'tmp_openid' => 'tmp_123',
            'add_time' => '2022-01-01 10:00:00'
        ];

        $message = new SaveExternalContactListItemMessage();
        $message->setAgentId('agent_123');
        $message->setItem($item);

        $this->agentRepository->expects($this->once())
            ->method('find')
            ->with('agent_123')
            ->willReturn($agent);

        $this->externalUserRepository->expects($this->exactly(2))
            ->method('findOneBy')
            ->willReturn(null);

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($externalUser) use ($corp, $item) {
                return $externalUser instanceof ExternalUser
                    && $externalUser->getCorp() === $corp
                    && $externalUser->getExternalUserId() === $item['external_userid']
                    && $externalUser->getNickname() === $item['name']
                    && $externalUser->isCustomer() === true
                    && $externalUser->getTmpOpenId() === $item['tmp_openid'];
            }));

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->handler->__invoke($message);
    }

    public function testInvokeWithExistingExternalUser(): void
    {
        // 测试更新现有外部用户
        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);

        $existingUser = new ExternalUser();
        $existingUser->setCorp($corp);
        $existingUser->setExternalUserId('ext_user_existing');

        $item = [
            'external_userid' => 'ext_user_existing',
            'name' => '更新用户',
            'is_customer' => false,
            'add_time' => '2022-01-02 15:30:00'
        ];

        $message = new SaveExternalContactListItemMessage();
        $message->setAgentId('agent_456');
        $message->setItem($item);

        $this->agentRepository->expects($this->once())
            ->method('find')
            ->with('agent_456')
            ->willReturn($agent);

        $this->externalUserRepository->expects($this->once())
            ->method('findOneBy')
            ->with([
                'externalUserId' => 'ext_user_existing',
                'corp' => $corp,
            ])
            ->willReturn($existingUser);

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($existingUser);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->handler->__invoke($message);
    }

    public function testInvokeWithFollowUserRelation(): void
    {
        // 测试跟进用户关系
        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);

        $user = new TestUser('follow_user_123', 'Follow User');

        $item = [
            'external_userid' => 'ext_user_follow',
            'name' => '跟进用户',
            'follow_userid' => 'follow_user_123',
            'add_time' => '2022-01-03 10:15:00'
        ];

        $message = new SaveExternalContactListItemMessage();
        $message->setAgentId('agent_789');
        $message->setItem($item);

        $this->agentRepository->expects($this->once())
            ->method('find')
            ->with('agent_789')
            ->willReturn($agent);

        $this->externalUserRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $this->userLoader->expects($this->once())
            ->method('loadUserByUserIdAndCorp')
            ->with('follow_user_123', $corp)
            ->willReturn($user);

        $this->externalServiceRelationRepository->expects($this->once())
            ->method('findOneBy')
            ->with($this->callback(function ($criteria) use ($user) {
                return isset($criteria['user']) && $criteria['user'] === $user 
                    && isset($criteria['externalUser']) && $criteria['externalUser'] instanceof ExternalUser;
            }))
            ->willReturn(null);

        $this->entityManager->expects($this->exactly(2))
            ->method('persist');

        $this->entityManager->expects($this->exactly(2))
            ->method('flush');

        $this->handler->__invoke($message);
    }

    public function testInvokeWithExistingRelation(): void
    {
        // 测试更新现有关系
        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);

        $externalUser = new ExternalUser();
        $externalUser->setCorp($corp);

        $user = new TestUser('follow_user_456', 'Relation User');

        $existingRelation = new ExternalServiceRelation();
        $existingRelation->setUser($user);
        $existingRelation->setExternalUser($externalUser);

        $item = [
            'external_userid' => 'ext_user_relation',
            'name' => '关系用户',
            'follow_userid' => 'follow_user_456',
            'add_time' => '2022-01-04 14:20:00'
        ];

        $message = new SaveExternalContactListItemMessage();
        $message->setAgentId('agent_012');
        $message->setItem($item);

        $this->agentRepository->expects($this->once())
            ->method('find')
            ->with('agent_012')
            ->willReturn($agent);

        $this->externalUserRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $this->userLoader->expects($this->once())
            ->method('loadUserByUserIdAndCorp')
            ->with('follow_user_456', $corp)
            ->willReturn($user);

        $this->externalServiceRelationRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($existingRelation);

        $this->entityManager->expects($this->exactly(2))
            ->method('persist');

        $this->entityManager->expects($this->exactly(2))
            ->method('flush');

        $this->handler->__invoke($message);
    }

    public function testInvokeWithUserCreation(): void
    {
        // 测试创建新用户
        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);

        $newUser = new TestUser('new_follow_user', 'New User');

        $item = [
            'external_userid' => 'ext_user_create',
            'name' => '创建用户',
            'follow_userid' => 'new_follow_user',
            'add_time' => '2022-01-05 11:45:00'
        ];

        $message = new SaveExternalContactListItemMessage();
        $message->setAgentId('agent_345');
        $message->setItem($item);

        $this->agentRepository->expects($this->once())
            ->method('find')
            ->with('agent_345')
            ->willReturn($agent);

        $this->externalUserRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $this->userLoader->expects($this->once())
            ->method('loadUserByUserIdAndCorp')
            ->with('new_follow_user', $corp)
            ->willReturn(null);

        $this->userLoader->expects($this->once())
            ->method('createUser')
            ->with($corp, $agent, 'new_follow_user', 'new_follow_user')
            ->willReturn($newUser);

        $this->externalServiceRelationRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $this->entityManager->expects($this->exactly(2))
            ->method('persist');

        $this->entityManager->expects($this->exactly(2))
            ->method('flush');

        $this->handler->__invoke($message);
    }

    public function testInvokeWithTmpOpenIdLookup(): void
    {
        // 测试通过临时OpenID查找用户
        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);

        $existingUser = new ExternalUser();
        $existingUser->setCorp($corp);
        $existingUser->setTmpOpenId('tmp_openid_123');

        $item = [
            'external_userid' => 'ext_user_tmp',
            'tmp_openid' => 'tmp_openid_123',
            'name' => '临时用户'
        ];

        $message = new SaveExternalContactListItemMessage();
        $message->setAgentId('agent_678');
        $message->setItem($item);

        $this->agentRepository->expects($this->once())
            ->method('find')
            ->with('agent_678')
            ->willReturn($agent);

        $this->externalUserRepository->expects($this->exactly(2))
            ->method('findOneBy')
            ->willReturnOnConsecutiveCalls(null, $existingUser);

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($existingUser);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->handler->__invoke($message);
    }

    public function testInvokeWithRawDataPreservation(): void
    {
        // 测试原始数据保留
        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);

        $existingUser = new ExternalUser();
        $existingUser->setCorp($corp);
        $existingUser->setRawData(['existing' => 'data']);

        $item = [
            'external_userid' => 'ext_user_raw',
            'name' => '原始数据用户',
            'new_field' => 'new_value'
        ];

        $message = new SaveExternalContactListItemMessage();
        $message->setAgentId('agent_901');
        $message->setItem($item);

        $this->agentRepository->expects($this->once())
            ->method('find')
            ->with('agent_901')
            ->willReturn($agent);

        $this->externalUserRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($existingUser);

        // 验证原始数据不会被覆盖
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($user) {
                return $user instanceof ExternalUser 
                    && $user->getRawData() === ['existing' => 'data'];
            }));

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->handler->__invoke($message);
    }

    public function testInvokeWithCompleteItemData(): void
    {
        // 测试包含完整数据的项目
        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);

        $user = new TestUser('complete_follow_user', 'Complete User');

        $item = [
            'external_userid' => 'ext_user_complete',
            'name' => '完整数据用户',
            'avatar' => 'https://example.com/avatar.jpg',
            'type' => 1,
            'gender' => 1,
            'unionid' => 'union_id_123',
            'follow_userid' => 'complete_follow_user',
            'remark' => '测试备注',
            'description' => '测试描述',
            'remark_corp_name' => '企业备注名',
            'remark_mobiles' => ['13800138000'],
            'add_time' => '2022-01-06 16:30:00',
            'state' => 'active'
        ];

        $message = new SaveExternalContactListItemMessage();
        $message->setAgentId('agent_complete');
        $message->setItem($item);

        $this->agentRepository->expects($this->once())
            ->method('find')
            ->with('agent_complete')
            ->willReturn($agent);

        $this->externalUserRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $this->userLoader->expects($this->once())
            ->method('loadUserByUserIdAndCorp')
            ->with('complete_follow_user', $corp)
            ->willReturn($user);

        $this->externalServiceRelationRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $this->entityManager->expects($this->exactly(2))
            ->method('persist');

        $this->entityManager->expects($this->exactly(2))
            ->method('flush');

        $this->handler->__invoke($message);
    }
} 
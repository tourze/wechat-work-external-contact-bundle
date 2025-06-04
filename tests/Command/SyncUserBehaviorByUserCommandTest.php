<?php

namespace WechatWorkExternalContactBundle\Tests\Command;

use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\WechatWorkContracts\UserInterface;
use Tourze\WechatWorkContracts\UserLoaderInterface;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Service\WorkService;
use WechatWorkExternalContactBundle\Command\SyncUserBehaviorByUserCommand;
use WechatWorkExternalContactBundle\Entity\UserBehaviorDataByUser;
use WechatWorkExternalContactBundle\Repository\UserBehaviorDataByUserRepository;

class SyncUserBehaviorByUserCommandTest extends TestCase
{
    private AgentRepository&MockObject $agentRepository;
    private WorkService&MockObject $workService;
    private UserLoaderInterface&MockObject $userLoader;
    private UserBehaviorDataByUserRepository&MockObject $dataByUserRepository;
    private EntityManagerInterface&MockObject $entityManager;
    private SyncUserBehaviorByUserCommand $command;

    protected function setUp(): void
    {
        $this->agentRepository = $this->createMock(AgentRepository::class);
        $this->workService = $this->createMock(WorkService::class);
        $this->userLoader = $this->createMock(UserLoaderInterface::class);
        $this->dataByUserRepository = $this->createMock(UserBehaviorDataByUserRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->command = new SyncUserBehaviorByUserCommand(
            $this->agentRepository,
            $this->workService,
            $this->userLoader,
            $this->dataByUserRepository,
            $this->entityManager
        );
    }

    public function testExecuteWithNoAgents(): void
    {
        // 准备数据
        $this->agentRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // 执行测试
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('execute');
        $method->setAccessible(true);
        $result = $method->invoke($this->command, $input, $output);

        // 验证结果
        $this->assertEquals(0, $result);
    }

    public function testExecuteWithAgentButNoFollowUsers(): void
    {
        // 准备数据
        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);

        $this->agentRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$agent]);

        $this->workService->expects($this->once())
            ->method('request')
            ->willReturn([]); // 没有 follow_user 字段

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // 执行测试
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('execute');
        $method->setAccessible(true);
        $result = $method->invoke($this->command, $input, $output);

        // 验证结果
        $this->assertEquals(0, $result);
    }

    public function testExecuteWithFollowUserButUserNotFound(): void
    {
        // 准备数据
        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);

        $this->agentRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$agent]);

        $this->workService->expects($this->once())
            ->method('request')
            ->willReturn(['follow_user' => ['user123']]);

        $this->userLoader->expects($this->once())
            ->method('loadUserByUserIdAndCorp')
            ->with('user123', $corp)
            ->willReturn(null);

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // 执行测试
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('execute');
        $method->setAccessible(true);
        $result = $method->invoke($this->command, $input, $output);

        // 验证结果
        $this->assertEquals(0, $result);
    }

    public function testExecuteWithNewBehaviorData(): void
    {
        // 准备数据
        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);

        $user = $this->createMock(UserInterface::class);
        $user->method('getUserId')->willReturn('user123');

        $behaviorResponse = [
            'behavior_data' => [
                [
                    'stat_time' => 1640995200, // 2022-01-01 00:00:00
                    'new_apply_cnt' => 5,
                    'new_contact_cnt' => 3,
                    'chat_cnt' => 10,
                    'message_cnt' => 25,
                    'reply_percentage' => 85.5,
                    'avg_reply_time' => 120,
                    'negative_feedback_cnt' => 1
                ]
            ]
        ];

        $this->agentRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$agent]);

        $this->workService->expects($this->exactly(2))
            ->method('request')
            ->willReturnOnConsecutiveCalls(
                ['follow_user' => ['user123']], // GetFollowUserListRequest
                $behaviorResponse // GetUserBehaviorDataRequest
            );

        $this->userLoader->expects($this->once())
            ->method('loadUserByUserIdAndCorp')
            ->with('user123', $corp)
            ->willReturn($user);

        $this->dataByUserRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function (UserBehaviorDataByUser $data) use ($user) {
                return $data->getUser() === $user
                    && $data->getDate()->format('Y-m-d') === '2022-01-01'
                    && $data->getNewApplyCount() === 5
                    && $data->getNewContactCount() === 3
                    && $data->getChatCount() === 10
                    && $data->getMessageCount() === 25
                    && $data->getReplyPercentage() === 85.5
                    && $data->getAvgReplyTime() === 120
                    && $data->getNegativeFeedbackCount() === 1;
            }));

        $this->entityManager->expects($this->once())
            ->method('flush');

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // 执行测试
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('execute');
        $method->setAccessible(true);
        $result = $method->invoke($this->command, $input, $output);

        // 验证结果
        $this->assertEquals(0, $result);
    }

    public function testExecuteWithExistingBehaviorDataUpdate(): void
    {
        // 准备数据
        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);

        $user = $this->createMock(UserInterface::class);
        $user->method('getUserId')->willReturn('user123');

        $existingData = new UserBehaviorDataByUser();
        $existingData->setDate(Carbon::createFromTimestamp(1640995200)->startOfDay());

        $behaviorResponse = [
            'behavior_data' => [
                [
                    'stat_time' => 1640995200,
                    'new_apply_cnt' => 8,
                    'new_contact_cnt' => 6,
                    'chat_cnt' => 15,
                    'message_cnt' => 40,
                    'reply_percentage' => 90.0,
                    'avg_reply_time' => 100,
                    'negative_feedback_cnt' => 0
                ]
            ]
        ];

        $this->agentRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$agent]);

        $this->workService->expects($this->exactly(2))
            ->method('request')
            ->willReturnOnConsecutiveCalls(
                ['follow_user' => ['user123']],
                $behaviorResponse
            );

        $this->userLoader->expects($this->once())
            ->method('loadUserByUserIdAndCorp')
            ->willReturn($user);

        $this->dataByUserRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($existingData);

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($existingData);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // 执行测试
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('execute');
        $method->setAccessible(true);
        $result = $method->invoke($this->command, $input, $output);

        // 验证结果
        $this->assertEquals(0, $result);
        $this->assertEquals(8, $existingData->getNewApplyCount());
        $this->assertEquals(6, $existingData->getNewContactCount());
        $this->assertEquals(15, $existingData->getChatCount());
        $this->assertEquals(40, $existingData->getMessageCount());
        $this->assertEquals(90.0, $existingData->getReplyPercentage());
        $this->assertEquals(100, $existingData->getAvgReplyTime());
        $this->assertEquals(0, $existingData->getNegativeFeedbackCount());
    }

    public function testExecuteWithMultipleBehaviorData(): void
    {
        // 准备数据
        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);

        $user = $this->createMock(UserInterface::class);
        $user->method('getUserId')->willReturn('user123');

        $behaviorResponse = [
            'behavior_data' => [
                [
                    'stat_time' => 1640995200, // 2022-01-01
                    'new_apply_cnt' => 5,
                    'new_contact_cnt' => 3,
                    'chat_cnt' => 10,
                    'message_cnt' => 25,
                    'reply_percentage' => 85.5,
                    'avg_reply_time' => 120,
                    'negative_feedback_cnt' => 1
                ],
                [
                    'stat_time' => 1641081600, // 2022-01-02
                    'new_apply_cnt' => 3,
                    'new_contact_cnt' => 2,
                    'chat_cnt' => 8,
                    'message_cnt' => 20,
                    'reply_percentage' => 80.0,
                    'avg_reply_time' => 150,
                    'negative_feedback_cnt' => 0
                ]
            ]
        ];

        $this->agentRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$agent]);

        $this->workService->expects($this->exactly(2))
            ->method('request')
            ->willReturnOnConsecutiveCalls(
                ['follow_user' => ['user123']],
                $behaviorResponse
            );

        $this->userLoader->expects($this->once())
            ->method('loadUserByUserIdAndCorp')
            ->willReturn($user);

        $this->dataByUserRepository->expects($this->exactly(2))
            ->method('findOneBy')
            ->willReturn(null);

        $this->entityManager->expects($this->exactly(2))
            ->method('persist');

        $this->entityManager->expects($this->exactly(2))
            ->method('flush');

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // 执行测试
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('execute');
        $method->setAccessible(true);
        $result = $method->invoke($this->command, $input, $output);

        // 验证结果
        $this->assertEquals(0, $result);
    }

    public function testExecuteWithMultipleUsersAndAgents(): void
    {
        // 准备数据
        $corp1 = new Corp();
        $corp2 = new Corp();
        $agent1 = new Agent();
        $agent1->setCorp($corp1);
        $agent2 = new Agent();
        $agent2->setCorp($corp2);

        $user1 = $this->createMock(UserInterface::class);
        $user1->method('getUserId')->willReturn('user1');
        $user2 = $this->createMock(UserInterface::class);
        $user2->method('getUserId')->willReturn('user2');

        $this->agentRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$agent1, $agent2]);

        $this->workService->expects($this->exactly(5))
            ->method('request')
            ->willReturnOnConsecutiveCalls(
                ['follow_user' => ['user1', 'user2']], // Agent1 GetFollowUserListRequest
                ['behavior_data' => [['stat_time' => 1640995200, 'new_apply_cnt' => 1, 'new_contact_cnt' => 1, 'chat_cnt' => 1, 'message_cnt' => 1, 'reply_percentage' => 50.0, 'avg_reply_time' => 100, 'negative_feedback_cnt' => 0]]], // User1 GetUserBehaviorDataRequest
                ['behavior_data' => [['stat_time' => 1640995200, 'new_apply_cnt' => 2, 'new_contact_cnt' => 2, 'chat_cnt' => 2, 'message_cnt' => 2, 'reply_percentage' => 60.0, 'avg_reply_time' => 110, 'negative_feedback_cnt' => 0]]], // User2 GetUserBehaviorDataRequest
                ['follow_user' => ['user2']], // Agent2 GetFollowUserListRequest
                ['behavior_data' => [['stat_time' => 1640995200, 'new_apply_cnt' => 3, 'new_contact_cnt' => 3, 'chat_cnt' => 3, 'message_cnt' => 3, 'reply_percentage' => 70.0, 'avg_reply_time' => 120, 'negative_feedback_cnt' => 0]]] // User2 for Agent2 GetUserBehaviorDataRequest
            );

        $this->userLoader->expects($this->exactly(3))
            ->method('loadUserByUserIdAndCorp')
            ->willReturnMap([
                ['user1', $corp1, $user1],
                ['user2', $corp1, $user2],
                ['user2', $corp2, $user2]
            ]);

        $this->dataByUserRepository->expects($this->exactly(3))
            ->method('findOneBy')
            ->willReturn(null);

        $this->entityManager->expects($this->exactly(3))
            ->method('persist');

        $this->entityManager->expects($this->exactly(3))
            ->method('flush');

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // 执行测试
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('execute');
        $method->setAccessible(true);
        $result = $method->invoke($this->command, $input, $output);

        // 验证结果
        $this->assertEquals(0, $result);
    }

    public function testTimeRangeCalculation(): void
    {
        // 测试时间范围计算逻辑
        Carbon::setTestNow(Carbon::create(2022, 1, 8, 10, 0, 0)); // 设置测试时间为2022-01-08 10:00:00

        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);

        $user = $this->createMock(UserInterface::class);
        $user->method('getUserId')->willReturn('user123');

        $this->agentRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$agent]);

        $this->workService->expects($this->exactly(2))
            ->method('request')
            ->with($this->callback(function ($request) {
                if (method_exists($request, 'getStartTime') && method_exists($request, 'getEndTime')) {
                    $startTime = $request->getStartTime();
                    $endTime = $request->getEndTime();
                    
                    // 验证时间范围：endTime 应该是今天，startTime 应该是一周前
                    return $startTime->format('Y-m-d') === '2022-01-01' // 一周前
                        && $endTime->format('Y-m-d') === '2022-01-08'; // 今天
                }
                return true;
            }))
            ->willReturnOnConsecutiveCalls(
                ['follow_user' => ['user123']],
                ['behavior_data' => []]
            );

        $this->userLoader->expects($this->once())
            ->method('loadUserByUserIdAndCorp')
            ->willReturn($user);

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // 执行测试
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('execute');
        $method->setAccessible(true);
        $result = $method->invoke($this->command, $input, $output);

        // 验证结果
        $this->assertEquals(0, $result);

        // 重置测试时间
        Carbon::setTestNow();
    }

    public function testCommandMetadata(): void
    {
        // 验证命令名称
        $this->assertEquals('wechat-work:SyncUserBehaviorByUserCommand', $this->command->getName());
        
        // 验证命令描述
        $this->assertEquals('获取「联系客户统计」数据-单用户的数据', $this->command->getDescription());
        
        // 验证构造函数依赖
        $reflection = new \ReflectionClass($this->command);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();
        
        $this->assertCount(5, $parameters);
        $this->assertEquals('agentRepository', $parameters[0]->getName());
        $this->assertEquals('workService', $parameters[1]->getName());
        $this->assertEquals('userLoader', $parameters[2]->getName());
        $this->assertEquals('dataByUserRepository', $parameters[3]->getName());
        $this->assertEquals('entityManager', $parameters[4]->getName());
    }
} 
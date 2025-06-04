<?php

namespace WechatWorkExternalContactBundle\Tests\Command;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Service\WorkService;
use WechatWorkExternalContactBundle\Command\SyncContactWaysCommand;
use WechatWorkExternalContactBundle\Entity\ContactWay;
use WechatWorkExternalContactBundle\Repository\ContactWayRepository;

/**
 * 同步联系方式命令测试
 * 
 * 测试关注点：
 * - 联系方式同步逻辑
 * - API调用和响应处理
 * - 数据持久化
 * - 分页处理
 * - 异常处理
 */
class SyncContactWaysCommandTest extends TestCase
{
    private AgentRepository&MockObject $agentRepository;
    private ContactWayRepository&MockObject $contactWayRepository;
    private WorkService&MockObject $workService;
    private EntityManagerInterface&MockObject $entityManager;
    private SyncContactWaysCommand $command;

    protected function setUp(): void
    {
        $this->agentRepository = $this->createMock(AgentRepository::class);
        $this->contactWayRepository = $this->createMock(ContactWayRepository::class);
        $this->workService = $this->createMock(WorkService::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->command = new SyncContactWaysCommand(
            $this->agentRepository,
            $this->contactWayRepository,
            $this->workService,
            $this->entityManager
        );
    }

    public function testExecuteWithNoAgents(): void
    {
        // 安排：没有代理的情况
        $this->agentRepository
            ->expects(self::once())
            ->method('findAll')
            ->willReturn([]);

        // 不应该调用工作服务
        $this->workService->expects(self::never())->method('request');
        
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // 执行
        $result = $this->executeCommand($input, $output);

        // 断言
        self::assertSame(Command::SUCCESS, $result);
    }

    public function testExecuteWithAgentButNoContactWays(): void
    {
        // 安排：有代理但无联系方式的情况
        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);

        $this->agentRepository
            ->expects(self::once())
            ->method('findAll')
            ->willReturn([$agent]);

        // 模拟空的联系方式列表响应
        $this->workService
            ->expects(self::once())
            ->method('request')
            ->willReturn([
                'contact_way' => [],
                'next_cursor' => null
            ]);

        // 不应该调用持久化
        $this->entityManager->expects(self::never())->method('persist');
        $this->entityManager->expects(self::never())->method('flush');

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // 执行
        $result = $this->executeCommand($input, $output);

        // 断言
        self::assertSame(Command::SUCCESS, $result);
    }

    public function testExecuteWithNewContactWay(): void
    {
        // 安排：有新联系方式的情况
        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);

        $this->agentRepository
            ->expects(self::once())
            ->method('findAll')
            ->willReturn([$agent]);

        // 模拟联系方式不存在
        $this->contactWayRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['configId' => 'config_123'])
            ->willReturn(null);

        // 模拟API响应
        $this->workService
            ->expects(self::exactly(2))
            ->method('request')
            ->willReturnOnConsecutiveCalls(
                // 列表响应
                [
                    'contact_way' => [
                        ['config_id' => 'config_123']
                    ],
                    'next_cursor' => null
                ],
                // 详情响应
                [
                    'contact_way' => [
                        'type' => 1,
                        'scene' => 2,
                        'style' => 1,
                        'remark' => '测试备注',
                        'skip_verify' => true,
                        'state' => 'active',
                        'qr_code' => 'https://example.com/qr.png',
                        'user' => ['user1', 'user2'],
                        'party' => [1, 2],
                        'is_temp' => false,
                        'expires_in' => 86400,
                        'chat_expires_in' => 3600,
                        'unionid' => 'union123',
                        'conclusions' => ['结论1', '结论2']
                    ]
                ]
            );

        // 应该调用持久化
        $this->entityManager
            ->expects(self::once())
            ->method('persist')
            ->with(self::isInstanceOf(ContactWay::class));

        $this->entityManager
            ->expects(self::once())
            ->method('flush');

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // 执行
        $result = $this->executeCommand($input, $output);

        // 断言
        self::assertSame(Command::SUCCESS, $result);
    }

    public function testExecuteWithExistingContactWay(): void
    {
        // 安排：联系方式已存在的情况
        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);

        $existingContactWay = new ContactWay();

        $this->agentRepository
            ->expects(self::once())
            ->method('findAll')
            ->willReturn([$agent]);

        // 模拟联系方式已存在
        $this->contactWayRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['configId' => 'config_123'])
            ->willReturn($existingContactWay);

        // 只调用一次列表API，不调用详情API
        $this->workService
            ->expects(self::once())
            ->method('request')
            ->willReturn([
                'contact_way' => [
                    ['config_id' => 'config_123']
                ],
                'next_cursor' => null
            ]);

        // 不应该调用持久化
        $this->entityManager->expects(self::never())->method('persist');
        $this->entityManager->expects(self::never())->method('flush');

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // 执行
        $result = $this->executeCommand($input, $output);

        // 断言
        self::assertSame(Command::SUCCESS, $result);
    }

    public function testExecuteWithPagination(): void
    {
        // 安排：分页处理的情况
        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);

        $this->agentRepository
            ->expects(self::once())
            ->method('findAll')
            ->willReturn([$agent]);

        // 模拟联系方式不存在
        $this->contactWayRepository
            ->expects(self::exactly(2))
            ->method('findOneBy')
            ->willReturn(null);

        // 模拟分页API响应
        $this->workService
            ->expects(self::exactly(4))
            ->method('request')
            ->willReturnOnConsecutiveCalls(
                // 第一页列表响应
                [
                    'contact_way' => [
                        ['config_id' => 'config_1']
                    ],
                    'next_cursor' => 'cursor_123'
                ],
                // 第一个详情响应
                [
                    'contact_way' => [
                        'type' => 1,
                        'scene' => 2,
                        'style' => 1,
                        'remark' => '测试备注1',
                        'skip_verify' => true,
                        'state' => 'active',
                        'qr_code' => 'https://example.com/qr1.png',
                        'user' => ['user1'],
                        'party' => [1]
                    ]
                ],
                // 第二页列表响应
                [
                    'contact_way' => [
                        ['config_id' => 'config_2']
                    ],
                    'next_cursor' => null
                ],
                // 第二个详情响应
                [
                    'contact_way' => [
                        'type' => 2,
                        'scene' => 1,
                        'style' => 2,
                        'remark' => '测试备注2',
                        'skip_verify' => false,
                        'state' => 'inactive',
                        'qr_code' => 'https://example.com/qr2.png',
                        'user' => ['user2'],
                        'party' => [2]
                    ]
                ]
            );

        // 应该调用两次持久化
        $this->entityManager
            ->expects(self::exactly(2))
            ->method('persist');

        $this->entityManager
            ->expects(self::exactly(2))
            ->method('flush');

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // 执行
        $result = $this->executeCommand($input, $output);

        // 断言
        self::assertSame(Command::SUCCESS, $result);
    }

    public function testCommandNameAndDescription(): void
    {
        // 测试命令名称和描述
        self::assertSame('wechat-work:sync-contact-way', $this->command->getName());
        self::assertSame('同步获取联系我的方式', $this->command->getDescription());
    }

    public function testCommandConstructorDependencies(): void
    {
        // 测试构造函数依赖
        $reflection = new \ReflectionClass($this->command);
        $constructor = $reflection->getConstructor();
        
        self::assertNotNull($constructor);
        $parameters = $constructor->getParameters();
        self::assertCount(4, $parameters);
        
        self::assertSame('agentRepository', $parameters[0]->getName());
        self::assertSame('contactWayRepository', $parameters[1]->getName());
        self::assertSame('workService', $parameters[2]->getName());
        self::assertSame('entityManager', $parameters[3]->getName());
    }

    /**
     * 执行命令的受保护方法
     */
    private function executeCommand(MockObject $input, MockObject $output): int
    {
        $reflection = new ReflectionMethod($this->command, 'execute');
        $reflection->setAccessible(true);
        return $reflection->invoke($this->command, $input, $output);
    }
} 
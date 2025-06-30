<?php

namespace WechatWorkExternalContactBundle\Tests\Command;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Service\WorkService;
use WechatWorkExternalContactBundle\Command\SyncExternalContactListCommand;
use WechatWorkExternalContactBundle\Message\SaveExternalContactListItemMessage;

/**
 * 同步外部联系人列表命令测试
 *
 * 测试关注点：
 * - 联系人列表同步逻辑
 * - 消息派发机制
 * - 分页处理
 * - API调用和响应处理
 */
class SyncExternalContactListCommandTest extends TestCase
{
    private AgentRepository&MockObject $agentRepository;
    private WorkService&MockObject $workService;
    private MessageBusInterface&MockObject $messageBus;
    private SyncExternalContactListCommand $command;

    protected function setUp(): void
    {
        $this->agentRepository = $this->createMock(AgentRepository::class);
        $this->workService = $this->createMock(WorkService::class);
        $this->messageBus = $this->createMock(MessageBusInterface::class);

        $this->command = new SyncExternalContactListCommand(
            $this->agentRepository,
            $this->workService,
            $this->messageBus
        );
    }

    public function testExecuteWithNoAgents(): void
    {
        // 安排：没有代理的情况
        $this->agentRepository
            ->expects(self::once())
            ->method('findAll')
            ->willReturn([]);

        // 不应该调用工作服务和消息总线
        $this->workService->expects(self::never())->method('request');
        $this->messageBus->expects(self::never())->method('dispatch');
        
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // 执行
        $result = $this->executeCommand($input, $output);

        // 断言
        self::assertSame(Command::SUCCESS, $result);
    }

    public function testExecuteWithAgentButNoContacts(): void
    {
        // 安排：有代理但无联系人的情况
        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);

        $this->agentRepository
            ->expects(self::once())
            ->method('findAll')
            ->willReturn([$agent]);

        // 模拟空的联系人列表响应
        $this->workService
            ->expects(self::once())
            ->method('request')
            ->willReturn([
                'next_cursor' => null
            ]);

        // 不应该派发消息
        $this->messageBus->expects(self::never())->method('dispatch');

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // 执行
        $result = $this->executeCommand($input, $output);

        // 断言
        self::assertSame(Command::SUCCESS, $result);
    }

    public function testExecuteWithSingleContact(): void
    {
        // 安排：单个联系人的情况
        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);

        $this->agentRepository
            ->expects(self::once())
            ->method('findAll')
            ->willReturn([$agent]);

        // 模拟API响应
        $this->workService
            ->expects(self::once())
            ->method('request')
            ->willReturn([
                'info_list' => [
                    [
                        'userid' => 'user123',
                        'name' => '测试用户',
                        'avatar' => 'https://example.com/avatar.png'
                    ]
                ],
                'next_cursor' => null
            ]);

        // 应该派发一次消息
        $this->messageBus
            ->expects(self::once())
            ->method('dispatch')
            ->with(self::isInstanceOf(SaveExternalContactListItemMessage::class))
            ->willReturn(new Envelope(new SaveExternalContactListItemMessage()));

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // 执行
        $result = $this->executeCommand($input, $output);

        // 断言
        self::assertSame(Command::SUCCESS, $result);
    }

    public function testExecuteWithMultipleContacts(): void
    {
        // 安排：多个联系人的情况
        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);

        $this->agentRepository
            ->expects(self::once())
            ->method('findAll')
            ->willReturn([$agent]);

        // 模拟包含多个联系人的响应
        $this->workService
            ->expects(self::once())
            ->method('request')
            ->willReturn([
                'info_list' => [
                    [
                        'userid' => 'user123',
                        'name' => '测试用户1',
                        'avatar' => 'https://example.com/avatar1.png'
                    ],
                    [
                        'userid' => 'user456',
                        'name' => '测试用户2',
                        'avatar' => 'https://example.com/avatar2.png'
                    ],
                    [
                        'userid' => 'user789',
                        'name' => '测试用户3',
                        'avatar' => 'https://example.com/avatar3.png'
                    ]
                ],
                'next_cursor' => null
            ]);

        // 应该派发三次消息
        $this->messageBus
            ->expects(self::exactly(3))
            ->method('dispatch')
            ->with(self::isInstanceOf(SaveExternalContactListItemMessage::class))
            ->willReturn(new Envelope(new SaveExternalContactListItemMessage()));

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

        // 模拟分页API响应
        $this->workService
            ->expects(self::exactly(2))
            ->method('request')
            ->willReturnOnConsecutiveCalls(
                // 第一页响应
                [
                    'info_list' => [
                        [
                            'userid' => 'user123',
                            'name' => '测试用户1',
                            'avatar' => 'https://example.com/avatar1.png'
                        ]
                    ],
                    'next_cursor' => 'cursor_123'
                ],
                // 第二页响应
                [
                    'info_list' => [
                        [
                            'userid' => 'user456',
                            'name' => '测试用户2',
                            'avatar' => 'https://example.com/avatar2.png'
                        ]
                    ],
                    'next_cursor' => null
                ]
            );

        // 应该派发两次消息
        $this->messageBus
            ->expects(self::exactly(2))
            ->method('dispatch')
            ->with(self::isInstanceOf(SaveExternalContactListItemMessage::class))
            ->willReturn(new Envelope(new SaveExternalContactListItemMessage()));

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // 执行
        $result = $this->executeCommand($input, $output);

        // 断言
        self::assertSame(Command::SUCCESS, $result);
    }

    public function testExecuteWithMultipleAgents(): void
    {
        // 安排：多个代理的情况
        $corp = new Corp();
        
        $agent1 = new Agent();
        $agent1->setCorp($corp);
        
        $agent2 = new Agent();
        $agent2->setCorp($corp);

        $this->agentRepository
            ->expects(self::once())
            ->method('findAll')
            ->willReturn([$agent1, $agent2]);

        // 模拟两个代理的响应
        $this->workService
            ->expects(self::exactly(2))
            ->method('request')
            ->willReturnOnConsecutiveCalls(
                // 第一个代理响应
                [
                    'info_list' => [
                        [
                            'userid' => 'user123',
                            'name' => '代理1用户',
                            'avatar' => 'https://example.com/avatar1.png'
                        ]
                    ],
                    'next_cursor' => null
                ],
                // 第二个代理响应
                [
                    'info_list' => [
                        [
                            'userid' => 'user456',
                            'name' => '代理2用户',
                            'avatar' => 'https://example.com/avatar2.png'
                        ]
                    ],
                    'next_cursor' => null
                ]
            );

        // 应该派发两次消息
        $this->messageBus
            ->expects(self::exactly(2))
            ->method('dispatch')
            ->with(self::isInstanceOf(SaveExternalContactListItemMessage::class))
            ->willReturn(new Envelope(new SaveExternalContactListItemMessage()));

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
        self::assertSame('wechat-work:sync-external-contact-list', $this->command->getName());
        self::assertSame('同步获取已服务的外部联系人', $this->command->getDescription());
    }

    public function testCommandConstructorDependencies(): void
    {
        // 测试构造函数依赖
        $reflection = new \ReflectionClass($this->command);
        $constructor = $reflection->getConstructor();
        
        self::assertNotNull($constructor);
        $parameters = $constructor->getParameters();
        self::assertCount(3, $parameters);
        
        self::assertSame('agentRepository', $parameters[0]->getName());
        self::assertSame('workService', $parameters[1]->getName());
        self::assertSame('messageBus', $parameters[2]->getName());
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
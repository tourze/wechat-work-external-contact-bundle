<?php

namespace WechatWorkExternalContactBundle\Tests\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use League\Flysystem\FilesystemOperator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use ReflectionMethod;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use WechatWorkExternalContactBundle\Command\CheckUserAvatarCommand;
use WechatWorkExternalContactBundle\Entity\ExternalUser;
use WechatWorkExternalContactBundle\Repository\ExternalUserRepository;

/**
 * 检查用户头像命令测试
 *
 * 测试关注点：
 * - 头像URL检查和过滤逻辑
 * - HTTP请求处理和错误响应
 * - 实体持久化
 * - 异常处理和容错
 */
class CheckUserAvatarCommandTest extends TestCase
{
    private ExternalUserRepository&MockObject $externalUserRepository;
    private HttpClientInterface&MockObject $httpClient;
    private FilesystemOperator&MockObject $mountManager;
    private LoggerInterface&MockObject $logger;
    private EntityManagerInterface&MockObject $entityManager;
    private CheckUserAvatarCommand $command;

    protected function setUp(): void
    {
        $this->externalUserRepository = $this->createMock(ExternalUserRepository::class);
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->mountManager = $this->createMock(FilesystemOperator::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->command = new CheckUserAvatarCommand(
            $this->externalUserRepository,
            $this->httpClient,
            $this->mountManager,
            $this->logger,
            $this->entityManager
        );
    }

    public function testExecuteWithNoUsers(): void
    {
        // 安排：创建空的查询结果
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(Query::class);
        $expr = $this->createMock(\Doctrine\ORM\Query\Expr::class);
        $comparison = $this->createMock(\Doctrine\ORM\Query\Expr\Comparison::class);
        $literal = $this->createMock(\Doctrine\ORM\Query\Expr\Literal::class);
        $orX = $this->createMock(\Doctrine\ORM\Query\Expr\Orx::class);

        $this->externalUserRepository
            ->expects(self::once())
            ->method('createQueryBuilder')
            ->with('u')
            ->willReturn($queryBuilder);

        $queryBuilder
            ->expects(self::once())
            ->method('setMaxResults')
            ->with(100)
            ->willReturnSelf();

        $queryBuilder
            ->expects(self::atLeastOnce())
            ->method('expr')
            ->willReturn($expr);

        $expr
            ->expects(self::atLeastOnce())
            ->method('like')
            ->willReturn($comparison);

        $expr
            ->expects(self::atLeastOnce())
            ->method('literal')
            ->willReturn($literal);

        $expr
            ->expects(self::once())
            ->method('orX')
            ->willReturn($orX);

        $queryBuilder
            ->expects(self::once())
            ->method('where')
            ->willReturnSelf();

        $queryBuilder
            ->expects(self::once())
            ->method('andWhere')
            ->willReturnSelf();

        $queryBuilder
            ->expects(self::once())
            ->method('getQuery')
            ->willReturn($query);

        $query
            ->expects(self::once())
            ->method('toIterable')
            ->willReturn([]);

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // 执行
        $result = $this->executeCommand($input, $output);

        // 断言
        self::assertSame(Command::SUCCESS, $result);
    }

    public function testExecuteWithUsersHavingEmptyAvatar(): void
    {
        // 安排：用户头像为空的情况
        $user = new ExternalUser();
        $user->setAvatar('');

        $this->setupQueryBuilderMock([$user]);

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // 不应该调用HTTP客户端，因为头像为空
        $this->httpClient->expects(self::never())->method('request');

        // 执行
        $result = $this->executeCommand($input, $output);

        // 断言
        self::assertSame(Command::SUCCESS, $result);
    }

    public function testExecuteWithValidAvatarUrl(): void
    {
        // 安排：有效的头像URL
        $user = new ExternalUser();
        $user->setAvatar('https://thirdwx.qlogo.cn/mmopen/vi_32/test.png');

        $this->setupQueryBuilderMock([$user]);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getHeaders')->willReturn([]);
        $response->method('getContent')->willReturn('fake-image-content');

        $this->httpClient
            ->expects(self::once())
            ->method('request')
            ->with('GET', 'https://thirdwx.qlogo.cn/mmopen/vi_32/test.png')
            ->willReturn($response);

        // 修复：正确模拟方法调用而不是设置属性
        $this->mountManager
            ->expects(self::once())
            ->method('write')
            ->with(self::stringContains('.png'), 'fake-image-content');

        $this->logger
            ->expects(self::once())
            ->method('info')
            ->with(
                '保存企业微信外部用户头像',
                self::callback(function ($context) use ($user) {
                    return $context['user'] === $user 
                        && is_string($context['new'])
                        && str_starts_with($context['new'], 'https://cdn.example.com/')
                        && str_ends_with($context['new'], '.png');
                })
            );

        $this->entityManager
            ->expects(self::once())
            ->method('persist')
            ->with($user);

        $this->entityManager
            ->expects(self::once())
            ->method('flush');

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // 执行
        $result = $this->executeCommand($input, $output);

        // 断言
        self::assertSame(Command::SUCCESS, $result);
        self::assertStringStartsWith('https://cdn.example.com/', $user->getAvatar());
        self::assertStringEndsWith('.png', $user->getAvatar());
    }

    public function testExecuteWithErrorResponse(): void
    {
        // 安排：微信返回错误响应
        $_ENV['DEFAULT_USER_AVATAR_URL'] = 'https://example.com/default-avatar.png';

        $user = new ExternalUser();
        $user->setAvatar('https://wx.qlogo.cn/mmopen/invalid.png');

        $this->setupQueryBuilderMock([$user]);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getHeaders')->willReturn([
            'x-errno' => ['6101'],
            'x-info' => ['notexist:-6101']
        ]);

        $this->httpClient
            ->expects(self::once())
            ->method('request')
            ->willReturn($response);

        $this->logger
            ->expects(self::once())
            ->method('info')
            ->with('保存企业微信外部用户头像', [
                'user' => $user,
                'new' => 'https://example.com/default-avatar.png',
            ]);

        $this->entityManager
            ->expects(self::once())
            ->method('persist')
            ->with($user);

        $this->entityManager
            ->expects(self::once())
            ->method('flush');

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // 执行
        $result = $this->executeCommand($input, $output);

        // 断言
        self::assertSame(Command::SUCCESS, $result);
        self::assertSame('https://example.com/default-avatar.png', $user->getAvatar());

        unset($_ENV['DEFAULT_USER_AVATAR_URL']);
    }

    public function testExecuteWithHttpException(): void
    {
        // 安排：HTTP请求抛出异常
        $user = new ExternalUser();
        $user->setAvatar('https://thirdwx.qlogo.cn/error.png');

        $this->setupQueryBuilderMock([$user]);

        $this->httpClient
            ->expects(self::once())
            ->method('request')
            ->willThrowException(new \Exception('Network error'));

        // 异常情况下不应该调用持久化
        $this->entityManager->expects(self::never())->method('persist');
        $this->entityManager->expects(self::never())->method('flush');

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $output
            ->expects(self::once())
            ->method('writeln')
            ->with('Network error');

        // 执行
        $result = $this->executeCommand($input, $output);

        // 断言：即使有异常，命令也应该成功返回
        self::assertSame(Command::SUCCESS, $result);
    }

    public function testExecuteWithMultipleUsers(): void
    {
        // 安排：测试多个用户处理
        $user1 = new ExternalUser();
        $user1->setAvatar('https://thirdwx.qlogo.cn/user1.png');

        $user2 = new ExternalUser();
        $user2->setAvatar(''); // 空头像，应该跳过

        $user3 = new ExternalUser();
        $user3->setAvatar('https://wx.qlogo.cn/mmopen/user3.png');

        $this->setupQueryBuilderMock([$user1, $user2, $user3]);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getHeaders')->willReturn([]);
        $response->method('getContent')->willReturn('image-content');

        // 只应该为有头像的用户发起HTTP请求
        $this->httpClient
            ->expects(self::exactly(2))
            ->method('request')
            ->willReturn($response);

        // 修复：正确模拟方法调用而不是设置属性
        $this->mountManager
            ->expects(self::exactly(2))
            ->method('write')
            ->with(self::stringContains('.png'), 'image-content');

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

    public function testExecuteWithSpecificNotExistError(): void
    {
        // 安排：特定的不存在错误
        $_ENV['DEFAULT_USER_AVATAR_URL'] = 'https://example.com/default.png';

        $user = new ExternalUser();
        $user->setAvatar('https://thirdwx.qlogo.cn/notexist.png');

        $this->setupQueryBuilderMock([$user]);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getHeaders')->willReturn([
            'x-info' => ['notexist:-6101']
        ]);

        $this->httpClient
            ->expects(self::once())
            ->method('request')
            ->willReturn($response);

        $this->entityManager
            ->expects(self::once())
            ->method('persist')
            ->with($user);

        $this->entityManager
            ->expects(self::once())
            ->method('flush');

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // 执行
        $result = $this->executeCommand($input, $output);

        // 断言
        self::assertSame(Command::SUCCESS, $result);
        self::assertSame('https://example.com/default.png', $user->getAvatar());

        unset($_ENV['DEFAULT_USER_AVATAR_URL']);
    }

    public function testCommandNameAndDescription(): void
    {
        // 测试命令名称和描述
        self::assertSame('wechat-work:external-contact:check-user-avatar', $this->command->getName());
        self::assertSame('检查用户头像并保存', $this->command->getDescription());
    }

    public function testCommandConstructorDependencies(): void
    {
        // 测试构造函数依赖
        $reflection = new \ReflectionClass($this->command);
        $constructor = $reflection->getConstructor();
        
        self::assertNotNull($constructor);
        $parameters = $constructor->getParameters();
        self::assertCount(5, $parameters);
        
        self::assertSame('externalUserRepository', $parameters[0]->getName());
        self::assertSame('httpClient', $parameters[1]->getName());
        self::assertSame('mountManager', $parameters[2]->getName());
        self::assertSame('logger', $parameters[3]->getName());
        self::assertSame('entityManager', $parameters[4]->getName());
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

    /**
     * 设置查询构建器模拟
     */
    private function setupQueryBuilderMock(array $users): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(Query::class);
        $expr = $this->createMock(\Doctrine\ORM\Query\Expr::class);
        $comparison = $this->createMock(\Doctrine\ORM\Query\Expr\Comparison::class);
        $literal = $this->createMock(\Doctrine\ORM\Query\Expr\Literal::class);
        $orX = $this->createMock(\Doctrine\ORM\Query\Expr\Orx::class);

        $this->externalUserRepository
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);

        $queryBuilder
            ->method('setMaxResults')
            ->willReturnSelf();

        $queryBuilder
            ->method('expr')
            ->willReturn($expr);

        $expr
            ->method('like')
            ->willReturn($comparison);

        $expr
            ->method('literal')
            ->willReturn($literal);

        $expr
            ->method('orX')
            ->willReturn($orX);

        $queryBuilder
            ->method('where')
            ->willReturnSelf();

        $queryBuilder
            ->method('andWhere')
            ->willReturnSelf();

        $queryBuilder
            ->method('getQuery')
            ->willReturn($query);

        $query
            ->method('toIterable')
            ->willReturn($users);
    }
} 
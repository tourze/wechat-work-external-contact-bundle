<?php

namespace WechatWorkExternalContactBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;
use WechatWorkExternalContactBundle\Command\CheckUserAvatarCommand;

/**
 * @internal
 */
#[CoversClass(CheckUserAvatarCommand::class)]
#[RunTestsInSeparateProcesses]
final class CheckUserAvatarCommandTest extends AbstractCommandTestCase
{
    private ?CommandTester $commandTester = null;

    protected function onSetUp(): void
    {
        // 此测试不需要特殊的设置
    }

    protected function getCommandTester(): CommandTester
    {
        if (null === $this->commandTester) {
            $command = self::getContainer()->get(CheckUserAvatarCommand::class);
            self::assertInstanceOf(CheckUserAvatarCommand::class, $command);
            $application = new Application();
            $application->add($command);
            $this->commandTester = new CommandTester($command);
        }

        return $this->commandTester;
    }

    #[Test]
    public function testCommandName(): void
    {
        $this->assertSame('wechat-work:external-contact:check-user-avatar', CheckUserAvatarCommand::NAME);
    }

    #[Test]
    public function testCommandExecuteSuccessfully(): void
    {
        $commandTester = $this->getCommandTester();
        $exitCode = $commandTester->execute([]);

        $this->assertEquals(0, $exitCode);
    }

    #[Test]
    public function testCommandHasCorrectConfiguration(): void
    {
        $command = self::getContainer()->get(CheckUserAvatarCommand::class);
        self::assertInstanceOf(CheckUserAvatarCommand::class, $command);

        $this->assertEquals('wechat-work:external-contact:check-user-avatar', $command->getName());
        $this->assertEquals('检查用户头像并保存', $command->getDescription());
    }
}

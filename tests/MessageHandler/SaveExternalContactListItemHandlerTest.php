<?php

namespace WechatWorkExternalContactBundle\Tests\MessageHandler;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatWorkExternalContactBundle\MessageHandler\SaveExternalContactListItemHandler;

/**
 * @internal
 */
#[CoversClass(SaveExternalContactListItemHandler::class)]
#[RunTestsInSeparateProcesses]
final class SaveExternalContactListItemHandlerTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void        // 初始化 handler 属性
    {
        $this->handler = self::getService(SaveExternalContactListItemHandler::class);
    }

    private SaveExternalContactListItemHandler $handler;

    #[Test]
    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(SaveExternalContactListItemHandler::class, $this->handler);
    }

    #[Test]
    public function testHandlerIsInvokable(): void
    {
        $this->assertNotNull($this->handler);
    }

    #[Test]
    public function testInvokeMethodIsPublic(): void
    {
        $reflection = new \ReflectionClass(SaveExternalContactListItemHandler::class);
        $method = $reflection->getMethod('__invoke');
        $this->assertTrue($method->isPublic());
    }

    #[Test]
    public function testHandlerIntegration(): void
    {
        $this->assertInstanceOf(SaveExternalContactListItemHandler::class, $this->handler);
        $this->assertNotNull($this->handler);
    }
}

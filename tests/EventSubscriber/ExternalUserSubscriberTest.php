<?php

namespace WechatWorkExternalContactBundle\Tests\EventSubscriber;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitSymfonyKernelTest\AbstractEventSubscriberTestCase;
use WechatWorkExternalContactBundle\EventSubscriber\ExternalUserSubscriber;

/**
 * @internal
 */
#[CoversClass(ExternalUserSubscriber::class)]
#[RunTestsInSeparateProcesses]
final class ExternalUserSubscriberTest extends AbstractEventSubscriberTestCase
{
    private ExternalUserSubscriber $subscriber;

    protected function onSetUp(): void
    {
        $this->subscriber = self::getService(ExternalUserSubscriber::class);
    }

    #[Test]
    public function testSubscriberCanBeInstantiated(): void
    {
        $this->assertInstanceOf(ExternalUserSubscriber::class, $this->subscriber);
    }

    #[Test]
    public function testOnServerMessageRequestMethodIsPublic(): void
    {
        $reflection = new \ReflectionClass(ExternalUserSubscriber::class);
        $method = $reflection->getMethod('onServerMessageRequest');
        $this->assertTrue($method->isPublic());
    }

    #[Test]
    public function testSubscriberIntegration(): void
    {
        $this->assertInstanceOf(ExternalUserSubscriber::class, $this->subscriber);
    }
}

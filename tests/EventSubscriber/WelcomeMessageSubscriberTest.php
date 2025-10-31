<?php

namespace WechatWorkExternalContactBundle\Tests\EventSubscriber;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitSymfonyKernelTest\AbstractEventSubscriberTestCase;
use WechatWorkExternalContactBundle\EventSubscriber\WelcomeMessageSubscriber;

/**
 * @internal
 */
#[CoversClass(WelcomeMessageSubscriber::class)]
#[RunTestsInSeparateProcesses]
final class WelcomeMessageSubscriberTest extends AbstractEventSubscriberTestCase
{
    private WelcomeMessageSubscriber $subscriber;

    protected function onSetUp(): void
    {
        $this->subscriber = self::getService(WelcomeMessageSubscriber::class);
    }

    #[Test]
    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(WelcomeMessageSubscriber::class, $this->subscriber);
    }

    #[Test]
    public function testOnServerMessageRequestMethodIsPublic(): void
    {
        $reflection = new \ReflectionClass(WelcomeMessageSubscriber::class);
        $method = $reflection->getMethod('onServerMessageRequest');
        $this->assertTrue($method->isPublic());
    }

    #[Test]
    public function testSubscriberIntegration(): void
    {
        $this->assertInstanceOf(WelcomeMessageSubscriber::class, $this->subscriber);
    }
}

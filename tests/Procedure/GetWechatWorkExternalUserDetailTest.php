<?php

declare(strict_types=1);

namespace WechatWorkExternalContactBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;
use WechatWorkExternalContactBundle\Event\GetExternalUserDetailEvent;
use WechatWorkExternalContactBundle\Procedure\GetWechatWorkExternalUserDetail;
use WechatWorkExternalContactBundle\Repository\ExternalUserRepository;

/**
 * @internal
 */
#[CoversClass(GetWechatWorkExternalUserDetail::class)]
#[RunTestsInSeparateProcesses]
final class GetWechatWorkExternalUserDetailTest extends AbstractProcedureTestCase
{
    private GetWechatWorkExternalUserDetail $procedure;

    protected function onSetUp(): void
    {
        $this->procedure = self::getService(GetWechatWorkExternalUserDetail::class);
    }

    #[Test]
    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(GetWechatWorkExternalUserDetail::class, $this->procedure);
    }

    #[Test]
    public function testExecuteMethod(): void
    {
        $reflection = new \ReflectionClass(GetWechatWorkExternalUserDetail::class);
        $method = $reflection->getMethod('execute');
        $this->assertTrue($method->isPublic());
    }

    #[Test]
    public function testProcedureIntegration(): void
    {
        $this->assertInstanceOf(GetWechatWorkExternalUserDetail::class, $this->procedure);
    }
}

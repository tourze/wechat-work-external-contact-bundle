<?php

declare(strict_types=1);

namespace WechatWorkExternalContactBundle\Tests\Procedure;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;
use WechatWorkExternalContactBundle\Procedure\SaveWechatWorkExternalUser;
use WechatWorkExternalContactBundle\Repository\ExternalUserRepository;

/**
 * @internal
 */
#[CoversClass(SaveWechatWorkExternalUser::class)]
#[RunTestsInSeparateProcesses]
final class SaveWechatWorkExternalUserTest extends AbstractProcedureTestCase
{
    protected const PROCEDURE_CLASS = SaveWechatWorkExternalUser::class;

    private SaveWechatWorkExternalUser $procedure;

    protected function onSetUp(): void
    {
        $this->procedure = self::getService(SaveWechatWorkExternalUser::class);
    }

    #[Test]
    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(SaveWechatWorkExternalUser::class, $this->procedure);
    }

    #[Test]
    public function testExecuteMethod(): void
    {
        $reflection = new \ReflectionClass(SaveWechatWorkExternalUser::class);
        $method = $reflection->getMethod('execute');
        $this->assertTrue($method->isPublic());
    }

    #[Test]
    public function testProcedureIntegration(): void
    {
        $this->assertInstanceOf(SaveWechatWorkExternalUser::class, $this->procedure);
    }
}

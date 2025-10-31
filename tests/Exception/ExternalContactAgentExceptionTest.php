<?php

namespace WechatWorkExternalContactBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use WechatWorkExternalContactBundle\Exception\ExternalContactAgentException;

/**
 * @internal
 */
#[CoversClass(ExternalContactAgentException::class)]
final class ExternalContactAgentExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionCanBeThrown(): void
    {
        $this->expectException(ExternalContactAgentException::class);
        $this->expectExceptionMessage('Test exception');

        throw new ExternalContactAgentException('Test exception');
    }

    public function testExceptionIsRuntimeException(): void
    {
        $exception = new ExternalContactAgentException('Test message');
        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    public function testExceptionWithCode(): void
    {
        $exception = new ExternalContactAgentException('Test message', 500);
        $this->assertEquals('Test message', $exception->getMessage());
        $this->assertEquals(500, $exception->getCode());
    }

    public function testExceptionWithPrevious(): void
    {
        $previous = new \Exception('Previous exception');
        $exception = new ExternalContactAgentException('Test message', 0, $previous);

        $this->assertEquals('Test message', $exception->getMessage());
        $this->assertSame($previous, $exception->getPrevious());
    }
}

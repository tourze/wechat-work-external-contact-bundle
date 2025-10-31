<?php

namespace WechatWorkExternalContactBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use WechatWorkExternalContactBundle\Exception\ExternalContactUserException;

/**
 * @internal
 */
#[CoversClass(ExternalContactUserException::class)]
final class ExternalContactUserExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionCanBeThrown(): void
    {
        $this->expectException(ExternalContactUserException::class);
        $this->expectExceptionMessage('Test exception');

        throw new ExternalContactUserException('Test exception');
    }

    public function testExceptionIsRuntimeException(): void
    {
        $exception = new ExternalContactUserException('Test message');
        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    public function testExceptionWithCode(): void
    {
        $exception = new ExternalContactUserException('Test message', 500);
        $this->assertEquals('Test message', $exception->getMessage());
        $this->assertEquals(500, $exception->getCode());
    }

    public function testExceptionWithPrevious(): void
    {
        $previous = new \Exception('Previous exception');
        $exception = new ExternalContactUserException('Test message', 0, $previous);

        $this->assertEquals('Test message', $exception->getMessage());
        $this->assertSame($previous, $exception->getPrevious());
    }
}

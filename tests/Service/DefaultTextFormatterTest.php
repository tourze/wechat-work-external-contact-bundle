<?php

namespace WechatWorkExternalContactBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\TextManageBundle\Service\TextFormatter;
use WechatWorkExternalContactBundle\Service\DefaultTextFormatter;

/**
 * @internal
 */
#[CoversClass(DefaultTextFormatter::class)]
#[RunTestsInSeparateProcesses]
final class DefaultTextFormatterTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {        // 此测试不需要特殊的设置
    }

    #[Test]
    public function testImplementsTextFormatterInterface(): void
    {
        $formatter = self::getService(DefaultTextFormatter::class);
        $this->assertInstanceOf(TextFormatter::class, $formatter);
    }

    #[Test]
    public function testFormatTextReturnsOriginalText(): void
    {
        $formatter = self::getService(DefaultTextFormatter::class);
        $text = 'Hello, World!';

        $result = $formatter->formatText($text);

        $this->assertEquals($text, $result);
    }

    #[Test]
    public function testFormatTextWithParametersIgnoresParams(): void
    {
        $formatter = self::getService(DefaultTextFormatter::class);
        $text = 'Hello, World!';
        $params = ['name' => 'John', 'age' => 25];

        $result = $formatter->formatText($text, $params);

        $this->assertEquals($text, $result);
    }

    #[Test]
    public function testFormatTextWithEmptyString(): void
    {
        $formatter = self::getService(DefaultTextFormatter::class);
        $result = $formatter->formatText('');
        $this->assertEquals('', $result);
    }

    #[Test]
    public function testFormatTextWithSpecialCharacters(): void
    {
        $formatter = self::getService(DefaultTextFormatter::class);
        $text = 'Hello\nWorld\t!@#$%^&*()';

        $result = $formatter->formatText($text);

        $this->assertEquals($text, $result);
    }
}

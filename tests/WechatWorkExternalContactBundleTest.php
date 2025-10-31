<?php

declare(strict_types=1);

namespace WechatWorkExternalContactBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;
use WechatWorkExternalContactBundle\WechatWorkExternalContactBundle;

/**
 * @internal
 */
#[CoversClass(WechatWorkExternalContactBundle::class)]
#[RunTestsInSeparateProcesses]
final class WechatWorkExternalContactBundleTest extends AbstractBundleTestCase
{
}

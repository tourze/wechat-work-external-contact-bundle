<?php

declare(strict_types=1);

namespace WechatWorkExternalContactBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;
use WechatWorkExternalContactBundle\DependencyInjection\WechatWorkExternalContactExtension;

/**
 * @internal
 */
#[CoversClass(WechatWorkExternalContactExtension::class)]
final class WechatWorkExternalContactExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
}

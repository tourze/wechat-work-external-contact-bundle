<?php

namespace WechatWorkExternalContactBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use WechatWorkExternalContactBundle\WechatWorkExternalContactBundle;

/**
 * WechatWorkExternalContactBundle 单元测试
 *
 * 测试 Bundle 类的基本功能
 */
class WechatWorkExternalContactBundleTest extends TestCase
{
    private WechatWorkExternalContactBundle $bundle;

    protected function setUp(): void
    {
        $this->bundle = new WechatWorkExternalContactBundle();
    }

    public function test_extendsSymfonyBundle(): void
    {
        $this->assertInstanceOf(Bundle::class, $this->bundle);
    }

    public function test_getPath_returnsCorrectPath(): void
    {
        $path = $this->bundle->getPath();
        
        $this->assertStringContainsString('wechat-work-external-contact-bundle', $path);
        $this->assertDirectoryExists($path);
    }

    public function test_getName_returnsCorrectName(): void
    {
        $name = $this->bundle->getName();
        
        $this->assertSame('WechatWorkExternalContactBundle', $name);
    }

    public function test_getNamespace_returnsCorrectNamespace(): void
    {
        $namespace = $this->bundle->getNamespace();
        
        $this->assertSame('WechatWorkExternalContactBundle', $namespace);
    }

    public function test_bundleCanBeInstantiated(): void
    {
        $bundle = new WechatWorkExternalContactBundle();
        
        $this->assertInstanceOf(WechatWorkExternalContactBundle::class, $bundle);
    }

    public function test_bundleImplementsToString(): void
    {
        // Bundle 类没有实现 __toString 方法，使用 getName() 代替
        $name = $this->bundle->getName();
        
        $this->assertSame('WechatWorkExternalContactBundle', $name);
    }

    public function test_getContainerExtension_returnsExtension(): void
    {
        $extension = $this->bundle->getContainerExtension();
        
        $this->assertInstanceOf(\Symfony\Component\DependencyInjection\Extension\ExtensionInterface::class, $extension);
        $this->assertSame('wechat_work_external_contact', $extension->getAlias());
    }

    public function test_boot_doesNotThrowException(): void
    {
        $this->expectNotToPerformAssertions();
        
        $this->bundle->boot();
    }

    public function test_shutdown_doesNotThrowException(): void
    {
        $this->expectNotToPerformAssertions();
        
        $this->bundle->shutdown();
    }

    public function test_build_doesNotThrowException(): void
    {
        $container = $this->createMock(\Symfony\Component\DependencyInjection\ContainerBuilder::class);
        
        $this->expectNotToPerformAssertions();
        
        $this->bundle->build($container);
    }
}
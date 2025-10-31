<?php

namespace WechatWorkExternalContactBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\RoutingAutoLoaderBundle\Service\RoutingAutoLoaderInterface;
use WechatWorkExternalContactBundle\Service\AttributeControllerLoader;

/**
 * @internal
 */
#[CoversClass(AttributeControllerLoader::class)]
#[RunTestsInSeparateProcesses]
final class AttributeControllerLoaderTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 此测试不需要特殊的设置
    }

    public function testServiceCanBeInstantiated(): void
    {
        $service = self::getService(AttributeControllerLoader::class);
        $this->assertInstanceOf(AttributeControllerLoader::class, $service);
    }

    public function testLoadReturnsRouteCollection(): void
    {
        $service = self::getService(AttributeControllerLoader::class);

        $result = $service->load('resource');
        $this->assertInstanceOf(RouteCollection::class, $result);
    }

    public function testAutoloadReturnsRouteCollection(): void
    {
        $service = self::getService(AttributeControllerLoader::class);

        $result = $service->autoload();
        $this->assertInstanceOf(RouteCollection::class, $result);
    }

    public function testSupportsReturnsFalse(): void
    {
        $service = self::getService(AttributeControllerLoader::class);
        $this->assertFalse($service->supports('resource'));
    }

    public function testImplementsRequiredInterfaces(): void
    {
        $reflection = new \ReflectionClass(AttributeControllerLoader::class);
        $this->assertTrue($reflection->implementsInterface(RoutingAutoLoaderInterface::class));
        $this->assertTrue($reflection->isSubclassOf(Loader::class));
    }

    public function testLoadCallsAutoload(): void
    {
        $service = self::getService(AttributeControllerLoader::class);

        $loadResult = $service->load('resource');
        $autoloadResult = $service->autoload();
        $this->assertEquals($autoloadResult->all(), $loadResult->all());
    }
}

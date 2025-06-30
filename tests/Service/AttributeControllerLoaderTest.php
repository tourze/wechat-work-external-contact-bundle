<?php

namespace WechatWorkExternalContactBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RouteCollection;
use WechatWorkExternalContactBundle\Service\AttributeControllerLoader;

class AttributeControllerLoaderTest extends TestCase
{
    private AttributeControllerLoader $loader;

    protected function setUp(): void
    {
        $this->loader = new AttributeControllerLoader();
    }

    public function testLoad(): void
    {
        $result = $this->loader->load('resource');
        
        $this->assertInstanceOf(RouteCollection::class, $result);
    }

    public function testSupports(): void
    {
        $this->assertFalse($this->loader->supports('resource'));
        $this->assertFalse($this->loader->supports('resource', 'type'));
    }

    public function testAutoload(): void
    {
        $collection = $this->loader->autoload();
        
        $this->assertInstanceOf(RouteCollection::class, $collection);
        
        // 验证是否加载了控制器路由
        $routes = $collection->all();
        $this->assertNotEmpty($routes);
        
        // 验证是否包含预期的路由
        $routePaths = array_map(fn($route) => $route->getPath(), $routes);
        $this->assertContains('/wechat/work/test/get_external_contact_list', $routePaths);
        $this->assertContains('/wechat/work/test/send_welcome_msg', $routePaths);
    }
}
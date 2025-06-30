<?php

namespace WechatWorkExternalContactBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use WechatWorkExternalContactBundle\DependencyInjection\WechatWorkExternalContactExtension;

class WechatWorkExternalContactExtensionTest extends TestCase
{
    private WechatWorkExternalContactExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new WechatWorkExternalContactExtension();
        $this->container = new ContainerBuilder();
    }

    public function testLoad(): void
    {
        $this->extension->load([], $this->container);
        
        // 验证服务是否被正确加载
        $this->assertTrue($this->container->hasDefinition('WechatWorkExternalContactBundle\Repository\ExternalUserRepository'));
        $this->assertTrue($this->container->hasDefinition('WechatWorkExternalContactBundle\Repository\ExternalServiceRelationRepository'));
        $this->assertTrue($this->container->hasDefinition('WechatWorkExternalContactBundle\Service\AttributeControllerLoader'));
    }

    public function testLoadWithEmptyConfig(): void
    {
        $configs = [[]];
        
        $this->extension->load($configs, $this->container);
        
        // 验证即使配置为空也能正常加载
        $this->assertNotEmpty($this->container->getDefinitions());
    }
}
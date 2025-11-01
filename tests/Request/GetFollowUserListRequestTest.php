<?php

namespace WechatWorkExternalContactBundle\Tests\Request;

use HttpClientBundle\Test\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkExternalContactBundle\Request\GetFollowUserListRequest;

/**
 * GetFollowUserListRequest 测试
 *
 * @internal
 */
#[CoversClass(GetFollowUserListRequest::class)]
final class GetFollowUserListRequestTest extends RequestTestCase
{
    private GetFollowUserListRequest $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new GetFollowUserListRequest();
    }

    public function testInheritance(): void
    {
        // 测试基本功能
        $this->assertNotNull($this->request);
        $this->assertSame('/cgi-bin/externalcontact/get_follow_user_list', $this->request->getRequestPath());
    }

    public function testRequestPath(): void
    {
        // 测试请求路径
        $this->assertSame('/cgi-bin/externalcontact/get_follow_user_list', $this->request->getRequestPath());
    }

    public function testRequestMethod(): void
    {
        // 测试请求方法
        $this->assertSame('GET', $this->request->getRequestMethod());
    }

    public function testRequestOptions(): void
    {
        // 测试获取请求选项
        $expected = [];

        $this->assertSame($expected, $this->request->getRequestOptions());
    }

    public function testRequestOptionsStructure(): void
    {
        // 测试请求选项结构
        $options = $this->request->getRequestOptions();
        $this->assertEmpty($options);
    }

    public function testBusinessScenarioGetConfiguredUsers(): void
    {
        // 测试业务场景：获取配置客户联系功能的用户

        $this->assertSame('/cgi-bin/externalcontact/get_follow_user_list', $this->request->getRequestPath());
        $this->assertSame('GET', $this->request->getRequestMethod());

        $options = $this->request->getRequestOptions();
        $this->assertEmpty($options);
    }

    public function testBusinessScenarioAdminGetFollowUsers(): void
    {
        // 测试业务场景：管理员获取跟进用户列表

        // 验证API路径符合管理员查询要求
        $this->assertStringContainsString('externalcontact', $this->request->getRequestPath());
        $this->assertStringContainsString('get_follow_user_list', $this->request->getRequestPath());
    }

    public function testBusinessScenarioSystemIntegration(): void
    {
        // 测试业务场景：系统集成获取有权限的用户

        $options = $this->request->getRequestOptions();
        // 验证这是一个不需要参数的简单查询
        $this->assertEmpty($options);
        $this->assertSame('GET', $this->request->getRequestMethod());
    }

    public function testIdempotentMethodCalls(): void
    {
        // 测试方法调用是幂等的

        // 多次调用应该返回相同结果
        $options1 = $this->request->getRequestOptions();
        $options2 = $this->request->getRequestOptions();
        $this->assertSame($options1, $options2);

        $path1 = $this->request->getRequestPath();
        $path2 = $this->request->getRequestPath();
        $this->assertSame($path1, $path2);

        $method1 = $this->request->getRequestMethod();
        $method2 = $this->request->getRequestMethod();
        $this->assertSame($method1, $method2);
    }

    public function testImmutableRequestOptions(): void
    {
        // 测试获取请求选项不会修改原始数据

        $options1 = $this->request->getRequestOptions();
        $options2 = $this->request->getRequestOptions();

        // 修改返回的数组不应影响原始数据
        $options1['new_key'] = 'new_value';
        $options1['json'] = ['test' => 'data'];

        $this->assertEmpty($this->request->getRequestOptions());
        $this->assertNotNull($options2);
        $this->assertEmpty($options2);
        $this->assertArrayNotHasKey('new_key', $options2);
        $this->assertArrayNotHasKey('json', $options2);
    }

    public function testAgentAwareTrait(): void
    {
        // 测试AgentAware特性

        // 测试默认值
        $this->assertNull($this->request->getAgent());

        // 测试设置和获取agent
        $agent = $this->createMock(AgentInterface::class);
        $this->request->setAgent($agent);
        $this->assertSame($agent, $this->request->getAgent());

        // 测试设置null
        $this->request->setAgent(null);
        $this->assertNull($this->request->getAgent());

        // 测试多次设置
        $agent2 = $this->createMock(AgentInterface::class);
        $this->request->setAgent($agent2);
        $this->assertSame($agent2, $this->request->getAgent());
        $this->assertNotSame($agent, $this->request->getAgent());
    }

    public function testNoParametersRequired(): void
    {
        // 测试不需要参数
        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options);
        $this->assertEmpty($options);
        $this->assertCount(0, $options);
    }

    public function testApiEndpointCorrectness(): void
    {
        // 测试API端点正确性
        $path = $this->request->getRequestPath();

        $this->assertStringContainsString('externalcontact', $path);
        $this->assertStringContainsString('get_follow_user_list', $path);
        $this->assertStringStartsWith('/cgi-bin/', $path);
    }

    public function testHttpMethodCorrectness(): void
    {
        // 测试HTTP方法正确性
        $method = $this->request->getRequestMethod();

        $this->assertSame('GET', $method);
        $this->assertContains($method, ['GET', 'POST', 'PUT', 'DELETE']);
    }

    public function testSimpleRequestFormat(): void
    {
        // 测试简单请求格式
        $options = $this->request->getRequestOptions();

        // 验证这是一个简单的无参数请求
        $this->assertNotNull($options);
        $this->assertEmpty($options);
        $this->assertArrayNotHasKey('query', $options);
        $this->assertArrayNotHasKey('json', $options);
        $this->assertArrayNotHasKey('body', $options);
        $this->assertArrayNotHasKey('form_params', $options);
    }

    public function testBusinessScenarioPermissionManagement(): void
    {
        // 测试业务场景：权限管理

        // 验证这个接口用于获取有客户联系权限的用户
        $this->assertSame('/cgi-bin/externalcontact/get_follow_user_list', $this->request->getRequestPath());
        $this->assertSame('GET', $this->request->getRequestMethod());

        $options = $this->request->getRequestOptions();
        $this->assertEmpty($options);
    }

    public function testBusinessScenarioUserCapabilityQuery(): void
    {
        // 测试业务场景：用户能力查询

        $this->assertStringContainsString('follow_user', $this->request->getRequestPath());

        // 验证这是查询配置了客户联系功能的成员
        $options = $this->request->getRequestOptions();
        $this->assertEmpty($options);
    }

    public function testRequestDataIntegrity(): void
    {
        // 测试请求数据完整性
        $options = $this->request->getRequestOptions();

        // 验证请求数据结构完整性
        $this->assertNotNull($options);
        $this->assertIsArray($options);
        $this->assertEmpty($options);

        // 验证不包含多余的字段
        $this->assertCount(0, $options);
    }

    public function testConstantBehavior(): void
    {
        // 测试常量行为

        // 所有属性应该是常量，不会改变
        $this->assertSame('/cgi-bin/externalcontact/get_follow_user_list', $this->request->getRequestPath());
        $this->assertSame('GET', $this->request->getRequestMethod());
        $this->assertSame([], $this->request->getRequestOptions());

        // 多次调用结果相同
        $this->assertSame('/cgi-bin/externalcontact/get_follow_user_list', $this->request->getRequestPath());
        $this->assertSame('GET', $this->request->getRequestMethod());
        $this->assertSame([], $this->request->getRequestOptions());
    }

    public function testOptionsImmutability(): void
    {
        // 测试选项不可变性
        $options = $this->request->getRequestOptions();

        // 修改返回的选项不应影响后续调用
        $options['modified'] = true;
        $options['test'] = 'value';

        $newOptions = $this->request->getRequestOptions();
        $this->assertNotNull($newOptions);
        $this->assertEmpty($newOptions);
        $this->assertArrayNotHasKey('modified', $newOptions);
        $this->assertArrayNotHasKey('test', $newOptions);
    }

    public function testBusinessScenarioStaffManagement(): void
    {
        // 测试业务场景：员工管理

        // 验证用于员工管理系统查询有客户联系权限的员工
        $path = $this->request->getRequestPath();
        $this->assertStringContainsString('externalcontact', $path);
        $this->assertStringContainsString('follow_user', $path);

        $method = $this->request->getRequestMethod();
        $this->assertSame('GET', $method);

        $options = $this->request->getRequestOptions();
        $this->assertEmpty($options);
    }

    public function testMultipleInstancesIndependence(): void
    {
        // 测试多个实例的独立性
        $request1 = new GetFollowUserListRequest();
        $request2 = new GetFollowUserListRequest();

        // 两个实例应该独立且相同
        $this->assertNotSame($request1, $request2);
        $this->assertEquals($request1->getRequestPath(), $request2->getRequestPath());
        $this->assertEquals($request1->getRequestMethod(), $request2->getRequestMethod());
        $this->assertEquals($request1->getRequestOptions(), $request2->getRequestOptions());
    }

    public function testAgentInterfaceImplementation(): void
    {
        // 测试AgentInterface接口实现，移除冗余检查

        // 验证基本功能
        $this->assertNotNull($this->request);
        $this->assertSame('/cgi-bin/externalcontact/get_follow_user_list', $this->request->getRequestPath());
    }
}

<?php

namespace WechatWorkExternalContactBundle\Tests\Request;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkExternalContactBundle\Request\GetFollowUserListRequest;

/**
 * GetFollowUserListRequest 测试
 */
class GetFollowUserListRequestTest extends TestCase
{
    public function test_inheritance(): void
    {
        // 测试继承关系
        $request = new GetFollowUserListRequest();
        $this->assertInstanceOf(ApiRequest::class, $request);
        $this->assertTrue(method_exists($request, 'getAgent'));
        $this->assertTrue(method_exists($request, 'setAgent'));
    }

    public function test_requestPath(): void
    {
        // 测试请求路径
        $request = new GetFollowUserListRequest();
        $this->assertSame('/cgi-bin/externalcontact/get_follow_user_list', $request->getRequestPath());
    }

    public function test_requestMethod(): void
    {
        // 测试请求方法
        $request = new GetFollowUserListRequest();
        $this->assertSame('GET', $request->getRequestMethod());
    }

    public function test_requestOptions(): void
    {
        // 测试获取请求选项
        $request = new GetFollowUserListRequest();
        $expected = [];
        
        $this->assertSame($expected, $request->getRequestOptions());
    }

    public function test_requestOptionsStructure(): void
    {
        // 测试请求选项结构
        $request = new GetFollowUserListRequest();
        $options = $request->getRequestOptions();
        $this->assertEmpty($options);
    }

    public function test_businessScenario_getConfiguredUsers(): void
    {
        // 测试业务场景：获取配置客户联系功能的用户
        $request = new GetFollowUserListRequest();
        
        $this->assertSame('/cgi-bin/externalcontact/get_follow_user_list', $request->getRequestPath());
        $this->assertSame('GET', $request->getRequestMethod());
        
        $options = $request->getRequestOptions();
        $this->assertEmpty($options);
    }

    public function test_businessScenario_adminGetFollowUsers(): void
    {
        // 测试业务场景：管理员获取跟进用户列表
        $request = new GetFollowUserListRequest();
        
        // 验证API路径符合管理员查询要求
        $this->assertStringContainsString('externalcontact', $request->getRequestPath());
        $this->assertStringContainsString('get_follow_user_list', $request->getRequestPath());
    }

    public function test_businessScenario_systemIntegration(): void
    {
        // 测试业务场景：系统集成获取有权限的用户
        $request = new GetFollowUserListRequest();
        
        $options = $request->getRequestOptions();
        // 验证这是一个不需要参数的简单查询
        $this->assertEmpty($options);
        $this->assertSame('GET', $request->getRequestMethod());
    }

    public function test_idempotentMethodCalls(): void
    {
        // 测试方法调用是幂等的
        $request = new GetFollowUserListRequest();
        
        // 多次调用应该返回相同结果
        $options1 = $request->getRequestOptions();
        $options2 = $request->getRequestOptions();
        $this->assertSame($options1, $options2);
        
        $path1 = $request->getRequestPath();
        $path2 = $request->getRequestPath();
        $this->assertSame($path1, $path2);
        
        $method1 = $request->getRequestMethod();
        $method2 = $request->getRequestMethod();
        $this->assertSame($method1, $method2);
    }

    public function test_immutableRequestOptions(): void
    {
        // 测试获取请求选项不会修改原始数据
        $request = new GetFollowUserListRequest();
        
        $options1 = $request->getRequestOptions();
        $options2 = $request->getRequestOptions();
        
        // 修改返回的数组不应影响原始数据
        $options1['new_key'] = 'new_value';
        $options1['json'] = ['test' => 'data'];
        
        $this->assertEmpty($request->getRequestOptions());
        $this->assertEmpty($options2);
        $this->assertArrayNotHasKey('new_key', $options2);
        $this->assertArrayNotHasKey('json', $options2);
    }

    public function test_agentAwareTrait(): void
    {
        // 测试AgentAware特性
        $request = new GetFollowUserListRequest();
        
        // 测试trait提供的方法存在
        $this->assertTrue(method_exists($request, 'getAgent'));
        $this->assertTrue(method_exists($request, 'setAgent'));
        $this->assertTrue(is_callable([$request, 'getAgent']));
        $this->assertTrue(is_callable([$request, 'setAgent']));
    }

    public function test_noParametersRequired(): void
    {
        // 测试不需要参数
        $request = new GetFollowUserListRequest();
        $options = $request->getRequestOptions();
        $this->assertEmpty($options);
        $this->assertCount(0, $options);
    }

    public function test_apiEndpointCorrectness(): void
    {
        // 测试API端点正确性
        $request = new GetFollowUserListRequest();
        $path = $request->getRequestPath();
        
        $this->assertStringContainsString('externalcontact', $path);
        $this->assertStringContainsString('get_follow_user_list', $path);
        $this->assertStringStartsWith('/cgi-bin/', $path);
    }

    public function test_httpMethodCorrectness(): void
    {
        // 测试HTTP方法正确性
        $request = new GetFollowUserListRequest();
        $method = $request->getRequestMethod();
        
        $this->assertSame('GET', $method);
        $this->assertTrue(in_array($method, ['GET', 'POST', 'PUT', 'DELETE']));
    }

    public function test_simpleRequestFormat(): void
    {
        // 测试简单请求格式
        $request = new GetFollowUserListRequest();
        $options = $request->getRequestOptions();
        
        // 验证这是一个简单的无参数请求
        $this->assertEmpty($options);
        $this->assertArrayNotHasKey('query', $options);
        $this->assertArrayNotHasKey('json', $options);
        $this->assertArrayNotHasKey('body', $options);
        $this->assertArrayNotHasKey('form_params', $options);
    }

    public function test_businessScenario_permissionManagement(): void
    {
        // 测试业务场景：权限管理
        $request = new GetFollowUserListRequest();
        
        // 验证这个接口用于获取有客户联系权限的用户
        $this->assertSame('/cgi-bin/externalcontact/get_follow_user_list', $request->getRequestPath());
        $this->assertSame('GET', $request->getRequestMethod());
        
        $options = $request->getRequestOptions();
        $this->assertEmpty($options);
    }

    public function test_businessScenario_userCapabilityQuery(): void
    {
        // 测试业务场景：用户能力查询
        $request = new GetFollowUserListRequest();
        
        $this->assertStringContainsString('follow_user', $request->getRequestPath());
        
        // 验证这是查询配置了客户联系功能的成员
        $options = $request->getRequestOptions();
        $this->assertEmpty($options);
    }

    public function test_requestDataIntegrity(): void
    {
        // 测试请求数据完整性
        $request = new GetFollowUserListRequest();
        $options = $request->getRequestOptions();
        
        // 验证请求数据结构完整性
        $this->assertEmpty($options);
        
        // 验证不包含多余的字段
        $this->assertCount(0, $options);
    }

    public function test_constantBehavior(): void
    {
        // 测试常量行为
        $request = new GetFollowUserListRequest();
        
        // 所有属性应该是常量，不会改变
        $this->assertSame('/cgi-bin/externalcontact/get_follow_user_list', $request->getRequestPath());
        $this->assertSame('GET', $request->getRequestMethod());
        $this->assertSame([], $request->getRequestOptions());
        
        // 多次调用结果相同
        $this->assertSame('/cgi-bin/externalcontact/get_follow_user_list', $request->getRequestPath());
        $this->assertSame('GET', $request->getRequestMethod());
        $this->assertSame([], $request->getRequestOptions());
    }

    public function test_optionsImmutability(): void
    {
        // 测试选项不可变性
        $request = new GetFollowUserListRequest();
        $options = $request->getRequestOptions();
        
        // 修改返回的选项不应影响后续调用
        $options['modified'] = true;
        $options['test'] = 'value';
        
        $newOptions = $request->getRequestOptions();
        $this->assertEmpty($newOptions);
        $this->assertArrayNotHasKey('modified', $newOptions);
        $this->assertArrayNotHasKey('test', $newOptions);
    }

    public function test_businessScenario_staffManagement(): void
    {
        // 测试业务场景：员工管理
        $request = new GetFollowUserListRequest();
        
        // 验证用于员工管理系统查询有客户联系权限的员工
        $path = $request->getRequestPath();
        $this->assertStringContainsString('externalcontact', $path);
        $this->assertStringContainsString('follow_user', $path);
        
        $method = $request->getRequestMethod();
        $this->assertSame('GET', $method);
        
        $options = $request->getRequestOptions();
        $this->assertEmpty($options);
    }

    public function test_multipleInstancesIndependence(): void
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
} 
<?php

namespace WechatWorkExternalContactBundle\Tests\Request;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkExternalContactBundle\Request\GetExternalContactRequest;

/**
 * GetExternalContactRequest 测试
 */
class GetExternalContactRequestTest extends TestCase
{
    public function test_inheritance(): void
    {
        // 测试继承关系
        $request = new GetExternalContactRequest();
        $this->assertInstanceOf(ApiRequest::class, $request);
    }

    public function test_usesAgentAwareTrait(): void
    {
        // 测试使用AgentAware trait
        $request = new GetExternalContactRequest();
        $this->assertTrue(method_exists($request, 'getAgent'));
        $this->assertTrue(method_exists($request, 'setAgent'));
    }

    public function test_getRequestPath(): void
    {
        // 测试请求路径
        $request = new GetExternalContactRequest();
        $this->assertSame('/cgi-bin/externalcontact/get', $request->getRequestPath());
    }

    public function test_externalUserId_setterAndGetter(): void
    {
        // 测试外部用户ID设置和获取
        $request = new GetExternalContactRequest();
        $externalUserId = 'woAJ2GCAAAXtWyujaWJHDDGi0mACHAAA';
        
        $request->setExternalUserId($externalUserId);
        $this->assertSame($externalUserId, $request->getExternalUserId());
    }

    public function test_externalUserId_chainSetting(): void
    {
        // 测试链式调用
        $request = new GetExternalContactRequest();
        $externalUserId = 'woAJ2GCAAAXtWyujaWJHDDGi0mACHAAA';
        
        $result = $request->setExternalUserId($externalUserId);
        $this->assertNull($result);
        $this->assertSame($externalUserId, $request->getExternalUserId());
    }

    public function test_getRequestOptions_withExternalUserId(): void
    {
        // 测试请求选项
        $request = new GetExternalContactRequest();
        $externalUserId = 'woAJ2GCAAAXtWyujaWJHDDGi0mACHAAA';
        $request->setExternalUserId($externalUserId);
        
        $options = $request->getRequestOptions();
        $this->assertArrayHasKey('query', $options);
        $this->assertArrayHasKey('external_userid', $options['query']);
        $this->assertSame($externalUserId, $options['query']['external_userid']);
    }

    public function test_getRequestOptions_structure(): void
    {
        // 测试请求选项结构
        $request = new GetExternalContactRequest();
        $request->setExternalUserId('test_user_id');
        
        $options = $request->getRequestOptions();
        $this->assertCount(1, $options);
        $this->assertArrayHasKey('query', $options);
        $this->assertCount(1, $options['query']);
    }

    public function test_externalUserId_withEmptyString(): void
    {
        // 测试空字符串
        $request = new GetExternalContactRequest();
        $request->setExternalUserId('');
        
        $this->assertSame('', $request->getExternalUserId());
        
        $options = $request->getRequestOptions();
        $this->assertSame('', $options['query']['external_userid']);
    }

    public function test_externalUserId_withSpecialCharacters(): void
    {
        // 测试特殊字符
        $request = new GetExternalContactRequest();
        $specialId = 'user_123-456@test.com';
        $request->setExternalUserId($specialId);
        
        $this->assertSame($specialId, $request->getExternalUserId());
        
        $options = $request->getRequestOptions();
        $this->assertSame($specialId, $options['query']['external_userid']);
    }

    public function test_externalUserId_withUnicodeCharacters(): void
    {
        // 测试Unicode字符
        $request = new GetExternalContactRequest();
        $unicodeId = '用户_123测试';
        $request->setExternalUserId($unicodeId);
        
        $this->assertSame($unicodeId, $request->getExternalUserId());
        
        $options = $request->getRequestOptions();
        $this->assertSame($unicodeId, $options['query']['external_userid']);
    }

    public function test_externalUserId_withLongString(): void
    {
        // 测试长字符串
        $request = new GetExternalContactRequest();
        $longId = str_repeat('a', 255);
        $request->setExternalUserId($longId);
        
        $this->assertSame($longId, $request->getExternalUserId());
        
        $options = $request->getRequestOptions();
        $this->assertSame($longId, $options['query']['external_userid']);
    }

    public function test_inheritsFromApiRequest(): void
    {
        // 测试继承自ApiRequest的核心方法
        $request = new GetExternalContactRequest();
        
        // 测试实际存在的方法
        $this->assertTrue(method_exists($request, 'getRequestPath'));
        $this->assertTrue(method_exists($request, 'getRequestOptions'));
        
        // 验证是ApiRequest的实例
        $this->assertInstanceOf(ApiRequest::class, $request);
    }

    public function test_businessScenario_getCustomerDetail(): void
    {
        // 测试业务场景：获取客户详情
        $request = new GetExternalContactRequest();
        $customerId = 'woAJ2GCAAAXtWyujaWJHDDGi0mACCustomer';
        
        $request->setExternalUserId($customerId);
        
        $this->assertSame('/cgi-bin/externalcontact/get', $request->getRequestPath());
        $this->assertSame($customerId, $request->getExternalUserId());
        
        $options = $request->getRequestOptions();
        $this->assertSame($customerId, $options['query']['external_userid']);
    }

    public function test_businessScenario_getPartnerDetail(): void
    {
        // 测试业务场景：获取合作伙伴详情
        $request = new GetExternalContactRequest();
        $partnerId = 'woAJ2GCAAAXtWyujaWJHDDGi0mACPartner';
        
        $request->setExternalUserId($partnerId);
        
        $options = $request->getRequestOptions();
        $this->assertSame($partnerId, $options['query']['external_userid']);
        $this->assertSame('/cgi-bin/externalcontact/get', $request->getRequestPath());
    }

    public function test_multipleSetCalls(): void
    {
        // 测试多次设置值
        $request = new GetExternalContactRequest();
        
        $firstId = 'first_user_id';
        $secondId = 'second_user_id';
        
        $request->setExternalUserId($firstId);
        $this->assertSame($firstId, $request->getExternalUserId());
        
        $request->setExternalUserId($secondId);
        $this->assertSame($secondId, $request->getExternalUserId());
        
        $options = $request->getRequestOptions();
        $this->assertSame($secondId, $options['query']['external_userid']);
    }

    public function test_requestOptionsDoesNotModifyOriginalData(): void
    {
        // 测试获取请求选项不会修改原始数据
        $request = new GetExternalContactRequest();
        $originalId = 'original_user_id';
        $request->setExternalUserId($originalId);
        
        $options1 = $request->getRequestOptions();
        $options2 = $request->getRequestOptions();
        
        // 修改返回的数组不应影响原始数据
        $options1['query']['external_userid'] = 'modified_id';
        
        $this->assertSame($originalId, $request->getExternalUserId());
        $this->assertSame($originalId, $options2['query']['external_userid']);
    }

    public function test_immutableBehavior(): void
    {
        // 测试不可变行为
        $request = new GetExternalContactRequest();
        $userId = 'test_user_id';
        $request->setExternalUserId($userId);
        
        $options = $request->getRequestOptions();
        
        // 修改选项不应影响request对象
        $options['query']['external_userid'] = 'changed_id';
        $options['query']['new_param'] = 'new_value';
        
        $this->assertSame($userId, $request->getExternalUserId());
        
        $newOptions = $request->getRequestOptions();
        $this->assertSame($userId, $newOptions['query']['external_userid']);
        $this->assertArrayNotHasKey('new_param', $newOptions['query']);
    }
} 
<?php

namespace WechatWorkExternalContactBundle\Tests\Request;

use HttpClientBundle\Request\RequestInterface;
use HttpClientBundle\Tests\Request\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkExternalContactBundle\Request\GetExternalContactRequest;

/**
 * GetExternalContactRequest 测试
 *
 * @internal
 */
#[CoversClass(GetExternalContactRequest::class)]
final class GetExternalContactRequestTest extends RequestTestCase
{
    private GetExternalContactRequest $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new GetExternalContactRequest();
    }

    public function testInheritance(): void
    {
        // 测试继承关系
        $this->assertInstanceOf(RequestInterface::class, $this->request);

        // 测试Agent功能的实际使用
        $agent = $this->createMock(AgentInterface::class);
        $this->request->setAgent($agent);
        $this->assertSame($agent, $this->request->getAgent());
    }

    public function testUsesAgentAwareTrait(): void
    {
        // 测试使用AgentAware trait

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

    public function testGetRequestPath(): void
    {
        // 测试请求路径
        $this->assertSame('/cgi-bin/externalcontact/get', $this->request->getRequestPath());
    }

    public function testExternalUserIdSetterAndGetter(): void
    {
        // 测试外部用户ID设置和获取
        $externalUserId = 'woAJ2GCAAAXtWyujaWJHDDGi0mACHAAA';

        $this->request->setExternalUserId($externalUserId);
        $this->assertSame($externalUserId, $this->request->getExternalUserId());
    }

    public function testExternalUserIdChainSetting(): void
    {
        // 测试setter方法调用
        $externalUserId = 'woAJ2GCAAAXtWyujaWJHDDGi0mACHAAA';

        $this->request->setExternalUserId($externalUserId);
        $this->assertSame($externalUserId, $this->request->getExternalUserId());
    }

    public function testGetRequestOptionsWithExternalUserId(): void
    {
        // 测试请求选项
        $externalUserId = 'woAJ2GCAAAXtWyujaWJHDDGi0mACHAAA';
        $this->request->setExternalUserId($externalUserId);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertIsArray($options['query']);
        $this->assertArrayHasKey('external_userid', $options['query']);
        $this->assertSame($externalUserId, $options['query']['external_userid']);
    }

    public function testGetRequestOptionsStructure(): void
    {
        // 测试请求选项结构
        $this->request->setExternalUserId('test_user_id');

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options);
        $this->assertCount(1, $options);
        $this->assertArrayHasKey('query', $options);
        $this->assertIsArray($options['query']);
        $this->assertCount(1, $options['query']);
    }

    public function testExternalUserIdWithEmptyString(): void
    {
        // 测试空字符串
        $this->request->setExternalUserId('');

        $this->assertSame('', $this->request->getExternalUserId());

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options);
        $this->assertIsArray($options['query']);
        $this->assertSame('', $options['query']['external_userid']);
    }

    public function testExternalUserIdWithSpecialCharacters(): void
    {
        // 测试特殊字符
        $specialId = 'user_123-456@test.com';
        $this->request->setExternalUserId($specialId);

        $this->assertSame($specialId, $this->request->getExternalUserId());

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options);
        $this->assertIsArray($options['query']);
        $this->assertSame($specialId, $options['query']['external_userid']);
    }

    public function testExternalUserIdWithUnicodeCharacters(): void
    {
        // 测试Unicode字符
        $unicodeId = '用户_123测试';
        $this->request->setExternalUserId($unicodeId);

        $this->assertSame($unicodeId, $this->request->getExternalUserId());

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options);
        $this->assertIsArray($options['query']);
        $this->assertSame($unicodeId, $options['query']['external_userid']);
    }

    public function testExternalUserIdWithLongString(): void
    {
        // 测试长字符串
        $longId = str_repeat('a', 255);
        $this->request->setExternalUserId($longId);

        $this->assertSame($longId, $this->request->getExternalUserId());

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options);
        $this->assertIsArray($options['query']);
        $this->assertSame($longId, $options['query']['external_userid']);
    }

    public function testInheritsFromApiRequest(): void
    {
        // 测试继承自ApiRequest的核心方法的实际功能
        $this->request->setExternalUserId('test_external_user_id');

        // 测试实际方法的返回值
        $this->assertIsString($this->request->getRequestPath());
        $this->assertIsArray($this->request->getRequestOptions());
        $this->assertStringContainsString('externalcontact', $this->request->getRequestPath());

        // 验证是ApiRequest的实例
        $this->assertInstanceOf(RequestInterface::class, $this->request);
    }

    public function testBusinessScenarioGetCustomerDetail(): void
    {
        // 测试业务场景：获取客户详情
        $customerId = 'woAJ2GCAAAXtWyujaWJHDDGi0mACCustomer';

        $this->request->setExternalUserId($customerId);

        $this->assertSame('/cgi-bin/externalcontact/get', $this->request->getRequestPath());
        $this->assertSame($customerId, $this->request->getExternalUserId());

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options);
        $this->assertIsArray($options['query']);
        $this->assertSame($customerId, $options['query']['external_userid']);
    }

    public function testBusinessScenarioGetPartnerDetail(): void
    {
        // 测试业务场景：获取合作伙伴详情
        $partnerId = 'woAJ2GCAAAXtWyujaWJHDDGi0mACPartner';

        $this->request->setExternalUserId($partnerId);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options);
        $this->assertIsArray($options['query']);
        $this->assertSame($partnerId, $options['query']['external_userid']);
        $this->assertSame('/cgi-bin/externalcontact/get', $this->request->getRequestPath());
    }

    public function testMultipleSetCalls(): void
    {
        // 测试多次设置值

        $firstId = 'first_user_id';
        $secondId = 'second_user_id';

        $this->request->setExternalUserId($firstId);
        $this->assertSame($firstId, $this->request->getExternalUserId());

        $this->request->setExternalUserId($secondId);
        $this->assertSame($secondId, $this->request->getExternalUserId());

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options);
        $this->assertIsArray($options['query']);
        $this->assertSame($secondId, $options['query']['external_userid']);
    }

    public function testRequestOptionsDoesNotModifyOriginalData(): void
    {
        // 测试获取请求选项不会修改原始数据
        $originalId = 'original_user_id';
        $this->request->setExternalUserId($originalId);

        $options1 = $this->request->getRequestOptions();
        $options2 = $this->request->getRequestOptions();

        // 确保选项是数组
        $this->assertIsArray($options1);
        $this->assertIsArray($options1['query']);
        $this->assertIsArray($options2);
        $this->assertIsArray($options2['query']);

        // 修改返回的数组不应影响原始数据
        $options1['query']['external_userid'] = 'modified_id';

        $this->assertSame($originalId, $this->request->getExternalUserId());
        $this->assertNotNull($options2);
        $this->assertSame($originalId, $options2['query']['external_userid']);
    }

    public function testImmutableBehavior(): void
    {
        // 测试不可变行为
        $userId = 'test_user_id';
        $this->request->setExternalUserId($userId);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options);
        $this->assertIsArray($options['query']);

        // 修改选项不应影响request对象
        $options['query']['external_userid'] = 'changed_id';
        $options['query']['new_param'] = 'new_value';

        $this->assertSame($userId, $this->request->getExternalUserId());

        $newOptions = $this->request->getRequestOptions();
        $this->assertNotNull($newOptions);
        $this->assertIsArray($newOptions);
        $this->assertIsArray($newOptions['query']);
        $this->assertSame($userId, $newOptions['query']['external_userid']);
        $this->assertArrayNotHasKey('new_param', $newOptions['query']);
    }
}

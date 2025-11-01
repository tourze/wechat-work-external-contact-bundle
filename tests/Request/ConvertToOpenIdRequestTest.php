<?php

namespace WechatWorkExternalContactBundle\Tests\Request;

use HttpClientBundle\Request\RequestInterface;
use HttpClientBundle\Test\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkExternalContactBundle\Request\ConvertToOpenIdRequest;

/**
 * ConvertToOpenIdRequest 测试
 *
 * @internal
 */
#[CoversClass(ConvertToOpenIdRequest::class)]
final class ConvertToOpenIdRequestTest extends RequestTestCase
{
    private ConvertToOpenIdRequest $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new ConvertToOpenIdRequest();
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

    public function testExternalUserIdSetterAndGetter(): void
    {
        // 测试外部用户ID设置和获取
        $externalUserId = 'wxExternalUserId123';

        $this->request->setExternalUserId($externalUserId);
        $this->assertSame($externalUserId, $this->request->getExternalUserId());
    }

    public function testRequestPath(): void
    {
        // 测试请求路径
        $this->assertSame('/cgi-bin/externalcontact/convert_to_openid', $this->request->getRequestPath());
    }

    public function testRequestOptions(): void
    {
        // 测试获取请求选项
        $externalUserId = 'wxExternalUser456';

        $this->request->setExternalUserId($externalUserId);

        $expected = [
            'json' => [
                'external_userid' => $externalUserId,
            ],
        ];

        $this->assertSame($expected, $this->request->getRequestOptions());
    }

    public function testRequestOptionsStructure(): void
    {
        // 测试请求选项结构
        $this->request->setExternalUserId('test_external_user');

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $json = $options['json'];
        $this->assertIsArray($json);
        $this->assertArrayHasKey('external_userid', $json);
        $this->assertCount(1, $json);
    }

    public function testBusinessScenarioWechatPaymentIntegration(): void
    {
        // 测试业务场景：微信支付集成
        $wechatExternalUserId = 'wx_external_customer_12345';

        $this->request->setExternalUserId($wechatExternalUserId);

        $this->assertSame($wechatExternalUserId, $this->request->getExternalUserId());

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $json = $options['json'];
        $this->assertIsArray($json);
        $this->assertArrayHasKey('external_userid', $json);
        $this->assertSame($wechatExternalUserId, $json['external_userid']);

        // 验证用于支付相关接口的转换
        $this->assertSame('/cgi-bin/externalcontact/convert_to_openid', $this->request->getRequestPath());
    }

    public function testBusinessScenarioMiniProgramIntegration(): void
    {
        // 测试业务场景：小程序集成
        $miniProgramUserId = 'wx_miniprogram_user_789';

        $this->request->setExternalUserId($miniProgramUserId);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $json = $options['json'];
        $this->assertIsArray($json);
        $this->assertArrayHasKey('external_userid', $json);
        $this->assertSame($miniProgramUserId, $json['external_userid']);
    }

    public function testBusinessScenarioThirdPartyServiceIntegration(): void
    {
        // 测试业务场景：第三方服务集成
        $thirdPartyUserId = 'wx_third_party_user_abc';

        $this->request->setExternalUserId($thirdPartyUserId);

        $this->assertSame($thirdPartyUserId, $this->request->getExternalUserId());

        // 验证API路径符合第三方服务要求
        $this->assertStringContainsString('convert_to_openid', $this->request->getRequestPath());
    }

    public function testSpecialCharactersInExternalUserId(): void
    {
        // 测试外部用户ID中的特殊字符
        $specialExternalUserId = 'wx_user-123_test@domain.com';

        $this->request->setExternalUserId($specialExternalUserId);

        $this->assertSame($specialExternalUserId, $this->request->getExternalUserId());

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $json = $options['json'];
        $this->assertIsArray($json);
        $this->assertArrayHasKey('external_userid', $json);
        $this->assertSame($specialExternalUserId, $json['external_userid']);
    }

    public function testLongExternalUserId(): void
    {
        // 测试长外部用户ID
        $longExternalUserId = 'wx_' . str_repeat('a', 100);

        $this->request->setExternalUserId($longExternalUserId);

        $this->assertSame($longExternalUserId, $this->request->getExternalUserId());
    }

    public function testUnicodeCharacters(): void
    {
        // 测试Unicode字符
        $unicodeExternalUserId = 'wx_外部用户_123_测试';

        $this->request->setExternalUserId($unicodeExternalUserId);

        $this->assertSame($unicodeExternalUserId, $this->request->getExternalUserId());

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $json = $options['json'];
        $this->assertIsArray($json);
        $this->assertArrayHasKey('external_userid', $json);
        $this->assertSame($unicodeExternalUserId, $json['external_userid']);
    }

    public function testMultipleSetOperations(): void
    {
        // 测试多次设置值

        $firstExternalUserId = 'wx_first_external_user';
        $secondExternalUserId = 'wx_second_external_user';

        $this->request->setExternalUserId($firstExternalUserId);
        $this->assertSame($firstExternalUserId, $this->request->getExternalUserId());

        $this->request->setExternalUserId($secondExternalUserId);
        $this->assertSame($secondExternalUserId, $this->request->getExternalUserId());

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $json = $options['json'];
        $this->assertIsArray($json);
        $this->assertArrayHasKey('external_userid', $json);
        $this->assertSame($secondExternalUserId, $json['external_userid']);
    }

    public function testIdempotentMethodCalls(): void
    {
        // 测试方法调用是幂等的
        $externalUserId = 'wx_idempotent_external_user';

        $this->request->setExternalUserId($externalUserId);

        // 多次调用应该返回相同结果
        $this->assertSame($externalUserId, $this->request->getExternalUserId());
        $this->assertSame($externalUserId, $this->request->getExternalUserId());

        $options1 = $this->request->getRequestOptions();
        $options2 = $this->request->getRequestOptions();
        $this->assertSame($options1, $options2);

        $path1 = $this->request->getRequestPath();
        $path2 = $this->request->getRequestPath();
        $this->assertSame($path1, $path2);
    }

    public function testImmutableRequestOptions(): void
    {
        // 测试获取请求选项不会修改原始数据
        $originalExternalUserId = 'wx_original_external_user';

        $this->request->setExternalUserId($originalExternalUserId);

        $options1 = $this->request->getRequestOptions();
        $options2 = $this->request->getRequestOptions();

        // 修改返回的数组不应影响原始数据
        $this->assertIsArray($options1);
        $this->assertArrayHasKey('json', $options1);
        $this->assertIsArray($options1['json']);
        $options1['json']['external_userid'] = 'wx_modified_external_user';
        $options1['json']['new_field'] = 'new_value';
        $options1['new_key'] = 'new_value';

        $this->assertSame($originalExternalUserId, $this->request->getExternalUserId());
        $this->assertNotNull($options2);
        $this->assertArrayHasKey('json', $options2);
        $json2 = $options2['json'];
        $this->assertIsArray($json2);
        $this->assertArrayHasKey('external_userid', $json2);
        $this->assertSame($originalExternalUserId, $json2['external_userid']);
        $this->assertArrayNotHasKey('new_field', $json2);
        $this->assertArrayNotHasKey('new_key', $options2);
    }

    public function testAgentAwareTrait(): void
    {
        // 测试AgentAware特性

        // 测试trait提供的功能
        // 测试默认值
        $this->assertNull($this->request->getAgent());

        // 测试设置和获取agent
        $agent = $this->createMock(AgentInterface::class);
        $this->request->setAgent($agent);
        $this->assertSame($agent, $this->request->getAgent());

        // 测试设置 null
        $this->request->setAgent(null);
        $this->assertNull($this->request->getAgent());
    }

    public function testEmptyStringValue(): void
    {
        // 测试空字符串值
        $this->request->setExternalUserId('');

        $this->assertSame('', $this->request->getExternalUserId());

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $json = $options['json'];
        $this->assertIsArray($json);
        $this->assertArrayHasKey('external_userid', $json);
        $this->assertSame('', $json['external_userid']);
    }

    public function testWechatExternalUserIdFormats(): void
    {
        // 测试微信外部用户ID格式
        $formats = [
            'wx_basic_format',
            'wxExternalUserId123456',
            'wx-external-user-789',
            'wx_user_2024_v1',
        ];

        foreach ($formats as $format) {
            $this->request->setExternalUserId($format);
            $this->assertSame($format, $this->request->getExternalUserId());

            $options = $this->request->getRequestOptions();
            $this->assertNotNull($options);
            $this->assertArrayHasKey('json', $options);
            $json = $options['json'];
            $this->assertIsArray($json);
            $this->assertArrayHasKey('external_userid', $json);
            $this->assertSame($format, $json['external_userid']);
        }
    }

    public function testApiPathCorrectness(): void
    {
        // 测试API路径正确性
        $path = $this->request->getRequestPath();

        $this->assertStringContainsString('externalcontact', $path);
        $this->assertStringContainsString('convert_to_openid', $path);
        $this->assertStringStartsWith('/cgi-bin/', $path);
    }

    public function testRequestDataIntegrity(): void
    {
        // 测试请求数据完整性
        $externalUserId = 'wx_integrity_test_user';

        $this->request->setExternalUserId($externalUserId);

        $options = $this->request->getRequestOptions();

        // 验证请求数据结构完整性
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $json = $options['json'];
        $this->assertIsArray($json);
        $this->assertArrayHasKey('external_userid', $json);
        $this->assertSame($externalUserId, $json['external_userid']);

        // 验证只包含必要的字段
        $this->assertCount(1, $options);
        $this->assertCount(1, $json);
    }

    public function testAgentInterfaceImplementation(): void
    {
        // 测试AgentInterface接口实现的完整功能

        // 测试初始状态
        $this->assertNull($this->request->getAgent());

        // 测试设置和获取不同类型的agent
        $agent1 = $this->createMock(AgentInterface::class);
        $agent2 = $this->createMock(AgentInterface::class);

        $this->request->setAgent($agent1);
        $this->assertSame($agent1, $this->request->getAgent());

        $this->request->setAgent($agent2);
        $this->assertSame($agent2, $this->request->getAgent());

        $this->request->setAgent(null);
        $this->assertNull($this->request->getAgent());
    }
}

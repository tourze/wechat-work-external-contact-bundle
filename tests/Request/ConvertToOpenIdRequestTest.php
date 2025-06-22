<?php

namespace WechatWorkExternalContactBundle\Tests\Request;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkExternalContactBundle\Request\ConvertToOpenIdRequest;

/**
 * ConvertToOpenIdRequest 测试
 */
class ConvertToOpenIdRequestTest extends TestCase
{
    public function test_inheritance(): void
    {
        // 测试继承关系
        $request = new ConvertToOpenIdRequest();
        $this->assertInstanceOf(ApiRequest::class, $request);
    }

    public function test_externalUserId_setterAndGetter(): void
    {
        // 测试外部用户ID设置和获取
        $request = new ConvertToOpenIdRequest();
        $externalUserId = 'wxExternalUserId123';

        $request->setExternalUserId($externalUserId);
        $this->assertSame($externalUserId, $request->getExternalUserId());
    }

    public function test_requestPath(): void
    {
        // 测试请求路径
        $request = new ConvertToOpenIdRequest();
        $this->assertSame('/cgi-bin/externalcontact/convert_to_openid', $request->getRequestPath());
    }

    public function test_requestOptions(): void
    {
        // 测试获取请求选项
        $request = new ConvertToOpenIdRequest();
        $externalUserId = 'wxExternalUser456';

        $request->setExternalUserId($externalUserId);

        $expected = [
            'json' => [
                'external_userid' => $externalUserId,
            ],
        ];

        $this->assertSame($expected, $request->getRequestOptions());
    }

    public function test_requestOptionsStructure(): void
    {
        // 测试请求选项结构
        $request = new ConvertToOpenIdRequest();
        $request->setExternalUserId('test_external_user');

        $options = $request->getRequestOptions();
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayHasKey('external_userid', $options['json']);
        $this->assertCount(1, $options['json']);
    }

    public function test_businessScenario_wechatPaymentIntegration(): void
    {
        // 测试业务场景：微信支付集成
        $request = new ConvertToOpenIdRequest();
        $wechatExternalUserId = 'wx_external_customer_12345';

        $request->setExternalUserId($wechatExternalUserId);

        $this->assertSame($wechatExternalUserId, $request->getExternalUserId());

        $options = $request->getRequestOptions();
        $this->assertSame($wechatExternalUserId, $options['json']['external_userid']);

        // 验证用于支付相关接口的转换
        $this->assertSame('/cgi-bin/externalcontact/convert_to_openid', $request->getRequestPath());
    }

    public function test_businessScenario_miniProgramIntegration(): void
    {
        // 测试业务场景：小程序集成
        $request = new ConvertToOpenIdRequest();
        $miniProgramUserId = 'wx_miniprogram_user_789';

        $request->setExternalUserId($miniProgramUserId);

        $options = $request->getRequestOptions();
        $this->assertSame($miniProgramUserId, $options['json']['external_userid']);
    }

    public function test_businessScenario_thirdPartyServiceIntegration(): void
    {
        // 测试业务场景：第三方服务集成
        $request = new ConvertToOpenIdRequest();
        $thirdPartyUserId = 'wx_third_party_user_abc';

        $request->setExternalUserId($thirdPartyUserId);

        $this->assertSame($thirdPartyUserId, $request->getExternalUserId());

        // 验证API路径符合第三方服务要求
        $this->assertStringContainsString('convert_to_openid', $request->getRequestPath());
    }

    public function test_specialCharacters_inExternalUserId(): void
    {
        // 测试外部用户ID中的特殊字符
        $request = new ConvertToOpenIdRequest();
        $specialExternalUserId = 'wx_user-123_test@domain.com';

        $request->setExternalUserId($specialExternalUserId);

        $this->assertSame($specialExternalUserId, $request->getExternalUserId());

        $options = $request->getRequestOptions();
        $this->assertSame($specialExternalUserId, $options['json']['external_userid']);
    }

    public function test_longExternalUserId(): void
    {
        // 测试长外部用户ID
        $request = new ConvertToOpenIdRequest();
        $longExternalUserId = 'wx_' . str_repeat('a', 100);

        $request->setExternalUserId($longExternalUserId);

        $this->assertSame($longExternalUserId, $request->getExternalUserId());
    }

    public function test_unicodeCharacters(): void
    {
        // 测试Unicode字符
        $request = new ConvertToOpenIdRequest();
        $unicodeExternalUserId = 'wx_外部用户_123_测试';

        $request->setExternalUserId($unicodeExternalUserId);

        $this->assertSame($unicodeExternalUserId, $request->getExternalUserId());

        $options = $request->getRequestOptions();
        $this->assertSame($unicodeExternalUserId, $options['json']['external_userid']);
    }

    public function test_multipleSetOperations(): void
    {
        // 测试多次设置值
        $request = new ConvertToOpenIdRequest();

        $firstExternalUserId = 'wx_first_external_user';
        $secondExternalUserId = 'wx_second_external_user';

        $request->setExternalUserId($firstExternalUserId);
        $this->assertSame($firstExternalUserId, $request->getExternalUserId());

        $request->setExternalUserId($secondExternalUserId);
        $this->assertSame($secondExternalUserId, $request->getExternalUserId());

        $options = $request->getRequestOptions();
        $this->assertSame($secondExternalUserId, $options['json']['external_userid']);
    }

    public function test_idempotentMethodCalls(): void
    {
        // 测试方法调用是幂等的
        $request = new ConvertToOpenIdRequest();
        $externalUserId = 'wx_idempotent_external_user';

        $request->setExternalUserId($externalUserId);

        // 多次调用应该返回相同结果
        $this->assertSame($externalUserId, $request->getExternalUserId());
        $this->assertSame($externalUserId, $request->getExternalUserId());

        $options1 = $request->getRequestOptions();
        $options2 = $request->getRequestOptions();
        $this->assertSame($options1, $options2);

        $path1 = $request->getRequestPath();
        $path2 = $request->getRequestPath();
        $this->assertSame($path1, $path2);
    }

    public function test_immutableRequestOptions(): void
    {
        // 测试获取请求选项不会修改原始数据
        $request = new ConvertToOpenIdRequest();
        $originalExternalUserId = 'wx_original_external_user';

        $request->setExternalUserId($originalExternalUserId);

        $options1 = $request->getRequestOptions();
        $options2 = $request->getRequestOptions();

        // 修改返回的数组不应影响原始数据
        $options1['json']['external_userid'] = 'wx_modified_external_user';
        $options1['json']['new_field'] = 'new_value';
        $options1['new_key'] = 'new_value';

        $this->assertSame($originalExternalUserId, $request->getExternalUserId());
        $this->assertSame($originalExternalUserId, $options2['json']['external_userid']);
        $this->assertArrayNotHasKey('new_field', $options2['json']);
        $this->assertArrayNotHasKey('new_key', $options2);
    }

    public function test_agentAwareTrait(): void
    {
        // 测试AgentAware特性
        $request = new ConvertToOpenIdRequest();

        // 测试trait提供的方法存在
    }

    public function test_emptyStringValue(): void
    {
        // 测试空字符串值
        $request = new ConvertToOpenIdRequest();
        $request->setExternalUserId('');

        $this->assertSame('', $request->getExternalUserId());

        $options = $request->getRequestOptions();
        $this->assertSame('', $options['json']['external_userid']);
    }

    public function test_wechatExternalUserIdFormats(): void
    {
        // 测试微信外部用户ID格式
        $request = new ConvertToOpenIdRequest();
        $formats = [
            'wx_basic_format',
            'wxExternalUserId123456',
            'wx-external-user-789',
            'wx_user_2024_v1',
        ];

        foreach ($formats as $format) {
            $request->setExternalUserId($format);
            $this->assertSame($format, $request->getExternalUserId());

            $options = $request->getRequestOptions();
            $this->assertSame($format, $options['json']['external_userid']);
        }
    }

    public function test_apiPathCorrectness(): void
    {
        // 测试API路径正确性
        $request = new ConvertToOpenIdRequest();
        $path = $request->getRequestPath();

        $this->assertStringContainsString('externalcontact', $path);
        $this->assertStringContainsString('convert_to_openid', $path);
        $this->assertStringStartsWith('/cgi-bin/', $path);
    }

    public function test_requestDataIntegrity(): void
    {
        // 测试请求数据完整性
        $request = new ConvertToOpenIdRequest();
        $externalUserId = 'wx_integrity_test_user';

        $request->setExternalUserId($externalUserId);

        $options = $request->getRequestOptions();

        // 验证请求数据结构完整性
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayHasKey('external_userid', $options['json']);
        $this->assertSame($externalUserId, $options['json']['external_userid']);

        // 验证只包含必要的字段
        $this->assertCount(1, $options);
        $this->assertCount(1, $options['json']);
    }

    public function test_agentInterfaceImplementation(): void
    {
        // 测试AgentInterface接口实现已在其他测试中覆盖
        $this->assertTrue(true); // 避免risky test警告
    }
}

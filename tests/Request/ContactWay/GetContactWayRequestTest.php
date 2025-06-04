<?php

namespace WechatWorkExternalContactBundle\Tests\Request\ContactWay;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkExternalContactBundle\Request\ContactWay\GetContactWayRequest;

/**
 * GetContactWayRequest 测试
 */
class GetContactWayRequestTest extends TestCase
{
    public function test_inheritance(): void
    {
        // 测试继承关系
        $request = new GetContactWayRequest();
        $this->assertInstanceOf(ApiRequest::class, $request);
        $this->assertTrue(method_exists($request, 'getAgent'));
        $this->assertTrue(method_exists($request, 'setAgent'));
    }

    public function test_requestPath(): void
    {
        // 测试请求路径
        $request = new GetContactWayRequest();
        $this->assertSame('/cgi-bin/externalcontact/get_contact_way', $request->getRequestPath());
    }

    public function test_configId_setterAndGetter(): void
    {
        // 测试配置ID设置和获取
        $request = new GetContactWayRequest();
        $configId = 'config_12345';
        
        $request->setConfigId($configId);
        $this->assertSame($configId, $request->getConfigId());
    }

    public function test_requestOptions(): void
    {
        // 测试获取请求选项
        $request = new GetContactWayRequest();
        $configId = 'test_config_001';
        
        $request->setConfigId($configId);
        
        $expected = [
            'json' => [
                'config_id' => $configId,
            ],
        ];
        
        $this->assertSame($expected, $request->getRequestOptions());
    }

    public function test_requestOptionsStructure(): void
    {
        // 测试请求选项结构
        $request = new GetContactWayRequest();
        $request->setConfigId('test_config');
        
        $options = $request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertArrayHasKey('config_id', $options['json']);
        $this->assertCount(1, $options['json']);
    }

    public function test_businessScenario_getSalesContactWay(): void
    {
        // 测试业务场景：获取销售联系方式配置
        $request = new GetContactWayRequest();
        $salesConfigId = 'sales_qr_config_001';
        
        $request->setConfigId($salesConfigId);
        
        $this->assertSame($salesConfigId, $request->getConfigId());
        
        $options = $request->getRequestOptions();
        $this->assertSame($salesConfigId, $options['json']['config_id']);
        
        // 验证API路径正确
        $this->assertSame('/cgi-bin/externalcontact/get_contact_way', $request->getRequestPath());
    }

    public function test_businessScenario_getCustomerServiceContactWay(): void
    {
        // 测试业务场景：获取客服联系方式配置
        $request = new GetContactWayRequest();
        $csConfigId = 'customer_service_config_002';
        
        $request->setConfigId($csConfigId);
        
        $options = $request->getRequestOptions();
        $this->assertSame($csConfigId, $options['json']['config_id']);
    }

    public function test_businessScenario_getTemporaryContactWay(): void
    {
        // 测试业务场景：获取临时联系方式配置
        $request = new GetContactWayRequest();
        $tempConfigId = 'temp_contact_config_003';
        
        $request->setConfigId($tempConfigId);
        
        $this->assertSame($tempConfigId, $request->getConfigId());
        
        // 验证API路径符合获取配置要求
        $this->assertStringContainsString('get_contact_way', $request->getRequestPath());
    }

    public function test_configIdSpecialCharacters(): void
    {
        // 测试配置ID特殊字符
        $request = new GetContactWayRequest();
        $specialConfigId = 'config-id_with.special@chars_123';
        
        $request->setConfigId($specialConfigId);
        
        $this->assertSame($specialConfigId, $request->getConfigId());
        
        $options = $request->getRequestOptions();
        $this->assertSame($specialConfigId, $options['json']['config_id']);
    }

    public function test_longConfigId(): void
    {
        // 测试长配置ID
        $request = new GetContactWayRequest();
        $longConfigId = str_repeat('config_part_', 10) . 'end';
        
        $request->setConfigId($longConfigId);
        
        $this->assertSame($longConfigId, $request->getConfigId());
    }

    public function test_unicodeCharacters(): void
    {
        // 测试Unicode字符
        $request = new GetContactWayRequest();
        $unicodeConfigId = '配置_ID_测试_123';
        
        $request->setConfigId($unicodeConfigId);
        
        $this->assertSame($unicodeConfigId, $request->getConfigId());
        
        $options = $request->getRequestOptions();
        $this->assertSame($unicodeConfigId, $options['json']['config_id']);
    }

    public function test_multipleSetOperations(): void
    {
        // 测试多次设置值
        $request = new GetContactWayRequest();
        
        $firstConfigId = 'first_config_id';
        $secondConfigId = 'second_config_id';
        
        $request->setConfigId($firstConfigId);
        $this->assertSame($firstConfigId, $request->getConfigId());
        
        $request->setConfigId($secondConfigId);
        $this->assertSame($secondConfigId, $request->getConfigId());
        
        $options = $request->getRequestOptions();
        $this->assertSame($secondConfigId, $options['json']['config_id']);
    }

    public function test_idempotentMethodCalls(): void
    {
        // 测试方法调用是幂等的
        $request = new GetContactWayRequest();
        $configId = 'idempotent_config_id';
        
        $request->setConfigId($configId);
        
        // 多次调用应该返回相同结果
        $this->assertSame($configId, $request->getConfigId());
        $this->assertSame($configId, $request->getConfigId());
        
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
        $request = new GetContactWayRequest();
        $originalConfigId = 'original_config_id';
        
        $request->setConfigId($originalConfigId);
        
        $options1 = $request->getRequestOptions();
        $options2 = $request->getRequestOptions();
        
        // 修改返回的数组不应影响原始数据
        $options1['json']['config_id'] = 'modified_config_id';
        $options1['json']['new_field'] = 'new_value';
        $options1['new_key'] = 'new_value';
        
        $this->assertSame($originalConfigId, $request->getConfigId());
        $this->assertSame($originalConfigId, $options2['json']['config_id']);
        $this->assertArrayNotHasKey('new_field', $options2['json']);
        $this->assertArrayNotHasKey('new_key', $options2);
    }

    public function test_agentAwareTrait(): void
    {
        // 测试AgentAware特性
        $request = new GetContactWayRequest();
        
        // 测试trait提供的方法存在
        $this->assertTrue(method_exists($request, 'getAgent'));
        $this->assertTrue(method_exists($request, 'setAgent'));
        $this->assertTrue(is_callable([$request, 'getAgent']));
        $this->assertTrue(is_callable([$request, 'setAgent']));
    }

    public function test_emptyStringValue(): void
    {
        // 测试空字符串值
        $request = new GetContactWayRequest();
        $request->setConfigId('');
        
        $this->assertSame('', $request->getConfigId());
        
        $options = $request->getRequestOptions();
        $this->assertSame('', $options['json']['config_id']);
    }

    public function test_requestParametersCorrectness(): void
    {
        // 测试请求参数正确性
        $request = new GetContactWayRequest();
        $configId = 'param_test_config_id';
        
        $request->setConfigId($configId);
        
        $options = $request->getRequestOptions();
        
        // 验证参数结构正确
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertArrayHasKey('config_id', $options['json']);
        $this->assertSame($configId, $options['json']['config_id']);
        
        // 验证只包含必要的参数
        $this->assertCount(1, $options);
        $this->assertCount(1, $options['json']);
    }

    public function test_apiEndpointCorrectness(): void
    {
        // 测试API端点正确性
        $request = new GetContactWayRequest();
        $path = $request->getRequestPath();
        
        $this->assertStringContainsString('externalcontact', $path);
        $this->assertStringContainsString('get_contact_way', $path);
        $this->assertStringStartsWith('/cgi-bin/', $path);
        $this->assertStringEndsWith('/get_contact_way', $path);
    }

    public function test_jsonRequestFormat(): void
    {
        // 测试JSON请求格式
        $request = new GetContactWayRequest();
        $configId = 'json_format_config_id';
        
        $request->setConfigId($configId);
        
        $options = $request->getRequestOptions();
        
        // 验证使用json而不是query格式
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayNotHasKey('query', $options);
        $this->assertArrayNotHasKey('body', $options);
        $this->assertArrayNotHasKey('form_params', $options);
    }

    public function test_businessScenario_configurationManagement(): void
    {
        // 测试业务场景：配置管理
        $request = new GetContactWayRequest();
        $managementConfigId = 'mgmt_config_001';
        
        $request->setConfigId($managementConfigId);
        
        $this->assertSame($managementConfigId, $request->getConfigId());
        
        $options = $request->getRequestOptions();
        $this->assertSame($managementConfigId, $options['json']['config_id']);
        
        // 验证API支持配置获取
        $this->assertStringContainsString('get_contact_way', $request->getRequestPath());
    }

    public function test_businessScenario_configurationValidation(): void
    {
        // 测试业务场景：配置验证
        $request = new GetContactWayRequest();
        $validationConfigId = 'validation_config_002';
        
        $request->setConfigId($validationConfigId);
        
        $options = $request->getRequestOptions();
        $this->assertSame($validationConfigId, $options['json']['config_id']);
    }

    public function test_requestDataIntegrity(): void
    {
        // 测试请求数据完整性
        $request = new GetContactWayRequest();
        $configId = 'integrity_test_config_id';
        
        $request->setConfigId($configId);
        
        $options = $request->getRequestOptions();
        
        // 验证请求数据结构完整性
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertSame($configId, $options['json']['config_id']);
        
        // 验证只包含必要的字段
        $this->assertCount(1, $options);
        $this->assertCount(1, $options['json']);
    }

    public function test_configIdValidation(): void
    {
        // 测试配置ID验证
        $request = new GetContactWayRequest();
        
        // 测试配置ID是必需的字符串
        $configId = 'validation_test_config_id';
        $request->setConfigId($configId);
        $this->assertIsString($request->getConfigId());
        $this->assertSame($configId, $request->getConfigId());
    }

    public function test_configIdFormats(): void
    {
        // 测试配置ID格式
        $request = new GetContactWayRequest();
        $formats = [
            'simple_config_id',
            'config-with-dashes',
            'config_with_underscores',
            'config.with.dots',
            'config123456',
            'UPPERCASE_CONFIG_ID',
        ];
        
        foreach ($formats as $format) {
            $request->setConfigId($format);
            $this->assertSame($format, $request->getConfigId());
            
            $options = $request->getRequestOptions();
            $this->assertSame($format, $options['json']['config_id']);
        }
    }

    public function test_configIdPersistence(): void
    {
        // 测试配置ID持久性
        $request = new GetContactWayRequest();
        $configId = 'persistence_test_config_id';
        
        $request->setConfigId($configId);
        
        // 多次获取应保持一致
        $this->assertSame($configId, $request->getConfigId());
        
        $options = $request->getRequestOptions();
        $this->assertSame($configId, $options['json']['config_id']);
        
        // 再次获取选项应保持一致
        $optionsAgain = $request->getRequestOptions();
        $this->assertSame($configId, $optionsAgain['json']['config_id']);
    }
} 
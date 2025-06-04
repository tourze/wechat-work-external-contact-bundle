<?php

namespace WechatWorkExternalContactBundle\Tests\Request\ContactWay;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkExternalContactBundle\Request\ContactWay\UpdateContactWayRequest;

/**
 * UpdateContactWayRequest 测试
 */
class UpdateContactWayRequestTest extends TestCase
{
    public function test_inheritance(): void
    {
        // 测试继承关系
        $request = new UpdateContactWayRequest();
        $this->assertInstanceOf(ApiRequest::class, $request);
        $this->assertTrue(method_exists($request, 'getAgent'));
        $this->assertTrue(method_exists($request, 'setAgent'));
    }

    public function test_requestPath(): void
    {
        // 测试请求路径
        $request = new UpdateContactWayRequest();
        $this->assertSame('/cgi-bin/externalcontact/update_contact_way', $request->getRequestPath());
    }

    public function test_configId_setterAndGetter(): void
    {
        // 测试配置ID设置和获取
        $request = new UpdateContactWayRequest();
        $configId = 'config_12345';
        
        $request->setConfigId($configId);
        $this->assertSame($configId, $request->getConfigId());
    }

    public function test_contactWayFields(): void
    {
        // 测试ContactWayField字段
        $request = new UpdateContactWayRequest();
        
        // 测试基本字段
        $request->setType(1);
        $request->setScene(2);
        $request->setUser(['user001']);
        $request->setSkipVerify(true);
        $request->setState('update_test');
        
        $this->assertSame(1, $request->getType());
        $this->assertSame(2, $request->getScene());
        $this->assertSame(['user001'], $request->getUser());
        $this->assertTrue($request->isSkipVerify());
        $this->assertSame('update_test', $request->getState());
    }

    public function test_requestOptions(): void
    {
        // 测试获取请求选项
        $request = new UpdateContactWayRequest();
        $configId = 'update_config_001';
        
        $request->setConfigId($configId);
        $request->setType(1);
        $request->setScene(2);
        $request->setUser(['user001']);
        
        $options = $request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayHasKey('config_id', $options['json']);
        $this->assertArrayHasKey('type', $options['json']);
        $this->assertArrayHasKey('scene', $options['json']);
        $this->assertSame($configId, $options['json']['config_id']);
    }

    public function test_businessScenario_updateSalesContactWay(): void
    {
        // 测试业务场景：更新销售联系方式
        $request = new UpdateContactWayRequest();
        $salesConfigId = 'sales_config_001';
        
        $request->setConfigId($salesConfigId);
        $request->setType(1); // 单人
        $request->setScene(2); // 二维码
        $request->setUser(['sales_manager_001']);
        $request->setSkipVerify(false); // 需要验证
        $request->setState('updated_sales_channel');
        $request->setRemark('更新后的销售渠道');
        
        $options = $request->getRequestOptions();
        
        $this->assertSame($salesConfigId, $options['json']['config_id']);
        $this->assertSame(1, $options['json']['type']);
        $this->assertSame(2, $options['json']['scene']);
        $this->assertSame(['sales_manager_001'], $options['json']['user']);
        $this->assertFalse($options['json']['skip_verify']);
        $this->assertSame('updated_sales_channel', $options['json']['state']);
        $this->assertSame('更新后的销售渠道', $options['json']['remark']);
        
        // 验证API路径正确
        $this->assertSame('/cgi-bin/externalcontact/update_contact_way', $request->getRequestPath());
    }

    public function test_businessScenario_updateMultiUserContactWay(): void
    {
        // 测试业务场景：更新多人联系方式
        $request = new UpdateContactWayRequest();
        $multiConfigId = 'multi_config_002';
        
        $request->setConfigId($multiConfigId);
        $request->setType(2); // 多人
        $request->setScene(1); // 小程序
        $request->setStyle(1);
        $request->setParty([100, 200]); // 部门
        $request->setSkipVerify(true);
        $request->setState('updated_multi_channel');
        
        $options = $request->getRequestOptions();
        
        $this->assertSame($multiConfigId, $options['json']['config_id']);
        $this->assertSame(2, $options['json']['type']);
        $this->assertSame(1, $options['json']['scene']);
        $this->assertSame(1, $options['json']['style']);
        $this->assertSame([100, 200], $options['json']['party']);
        $this->assertTrue($options['json']['skip_verify']);
    }

    public function test_businessScenario_updateTemporaryContactWay(): void
    {
        // 测试业务场景：更新临时联系方式
        $request = new UpdateContactWayRequest();
        $tempConfigId = 'temp_config_003';
        
        $request->setConfigId($tempConfigId);
        $request->setType(1);
        $request->setScene(2);
        $request->setUser(['temp_service_001']);
        $request->setTemp(true); // 临时会话
        $request->setExpiresIn(172800); // 2天
        $request->setChatExpiresIn(86400); // 1天
        $request->setUnionId('temp_union_updated');
        $request->setConclusions([
            ['text' => ['content' => '更新后的结束语']],
        ]);
        
        $options = $request->getRequestOptions();
        
        $this->assertSame($tempConfigId, $options['json']['config_id']);
        $this->assertTrue($options['json']['is_temp']);
        $this->assertSame(172800, $options['json']['expires_in']);
        $this->assertSame(86400, $options['json']['chat_expires_in']);
        $this->assertSame('temp_union_updated', $options['json']['unionid']);
        $this->assertArrayHasKey('conclusions', $options['json']);
    }

    public function test_configIdWithTraitFields(): void
    {
        // 测试配置ID与trait字段结合
        $request = new UpdateContactWayRequest();
        $configId = 'combined_config_test';
        
        $request->setConfigId($configId);
        $request->setType(1);
        $request->setScene(2);
        $request->setExclusive(true);
        $request->setRemark('组合测试');
        
        $options = $request->getRequestOptions();
        
        // 验证config_id包含在json中
        $this->assertArrayHasKey('config_id', $options['json']);
        $this->assertSame($configId, $options['json']['config_id']);
        
        // 验证trait字段也存在
        $this->assertArrayHasKey('type', $options['json']);
        $this->assertArrayHasKey('scene', $options['json']);
        $this->assertArrayHasKey('is_exclusive', $options['json']);
        $this->assertArrayHasKey('remark', $options['json']);
    }

    public function test_multipleSetOperations(): void
    {
        // 测试多次设置值
        $request = new UpdateContactWayRequest();
        
        $request->setConfigId('first_config');
        $request->setConfigId('second_config');
        $this->assertSame('second_config', $request->getConfigId());
        
        $request->setType(1);
        $request->setType(2);
        $this->assertSame(2, $request->getType());
        
        $request->setState('first_state');
        $request->setState('second_state');
        $this->assertSame('second_state', $request->getState());
    }

    public function test_idempotentMethodCalls(): void
    {
        // 测试方法调用是幂等的
        $request = new UpdateContactWayRequest();
        $configId = 'idempotent_config';
        
        $request->setConfigId($configId);
        $request->setType(1);
        $request->setScene(2);
        
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
        $request = new UpdateContactWayRequest();
        $originalConfigId = 'original_config';
        
        $request->setConfigId($originalConfigId);
        $request->setType(1);
        $request->setState('original_state');
        
        $options1 = $request->getRequestOptions();
        $options2 = $request->getRequestOptions();
        
        // 修改返回的数组不应影响原始数据
        $options1['json']['config_id'] = 'modified_config';
        $options1['json']['type'] = 2;
        $options1['json']['state'] = 'modified_state';
        $options1['json']['new_field'] = 'new_value';
        
        $this->assertSame($originalConfigId, $request->getConfigId());
        $this->assertSame(1, $request->getType());
        $this->assertSame('original_state', $request->getState());
        
        $this->assertSame($originalConfigId, $options2['json']['config_id']);
        $this->assertSame(1, $options2['json']['type']);
        $this->assertSame('original_state', $options2['json']['state']);
        $this->assertArrayNotHasKey('new_field', $options2['json']);
    }

    public function test_agentAwareTrait(): void
    {
        // 测试AgentAware特性
        $request = new UpdateContactWayRequest();
        
        // 测试trait提供的方法存在
        $this->assertTrue(method_exists($request, 'getAgent'));
        $this->assertTrue(method_exists($request, 'setAgent'));
        $this->assertTrue(is_callable([$request, 'getAgent']));
        $this->assertTrue(is_callable([$request, 'setAgent']));
    }

    public function test_contactWayFieldTrait(): void
    {
        // 测试ContactWayField特性
        $request = new UpdateContactWayRequest();
        
        // 验证trait提供的主要方法都存在
        $methods = [
            'getType', 'setType', 'getScene', 'setScene', 'getStyle', 'setStyle',
            'getUser', 'setUser', 'isSkipVerify', 'setSkipVerify', 'getState', 'setState',
            'getParty', 'setParty', 'isTemp', 'setTemp', 'getExpiresIn', 'setExpiresIn',
            'getChatExpiresIn', 'setChatExpiresIn', 'getUnionId', 'setUnionId',
            'isExclusive', 'setExclusive', 'getConclusions', 'setConclusions',
            'getRemark', 'setRemark'
        ];
        
        foreach ($methods as $method) {
            $this->assertTrue(method_exists($request, $method));
            $this->assertTrue(is_callable([$request, $method]));
        }
    }

    public function test_apiEndpointCorrectness(): void
    {
        // 测试API端点正确性
        $request = new UpdateContactWayRequest();
        $path = $request->getRequestPath();
        
        $this->assertStringContainsString('externalcontact', $path);
        $this->assertStringContainsString('update_contact_way', $path);
        $this->assertStringStartsWith('/cgi-bin/', $path);
        $this->assertStringEndsWith('/update_contact_way', $path);
    }

    public function test_jsonRequestFormat(): void
    {
        // 测试JSON请求格式
        $request = new UpdateContactWayRequest();
        $request->setConfigId('json_test_config');
        $request->setType(1);
        $request->setScene(2);
        
        $options = $request->getRequestOptions();
        
        // 验证使用json而不是query格式
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayNotHasKey('query', $options);
        $this->assertArrayNotHasKey('body', $options);
        $this->assertArrayNotHasKey('form_params', $options);
    }

    public function test_requestDataIntegrity(): void
    {
        // 测试请求数据完整性
        $request = new UpdateContactWayRequest();
        $configId = 'integrity_test_config';
        
        $request->setConfigId($configId);
        $request->setType(2);
        $request->setScene(1);
        $request->setParty([100]);
        $request->setState('integrity_test');
        
        $options = $request->getRequestOptions();
        
        // 验证请求数据结构完整性
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertSame($configId, $options['json']['config_id']);
        $this->assertSame(2, $options['json']['type']);
        $this->assertSame(1, $options['json']['scene']);
        $this->assertSame([100], $options['json']['party']);
        $this->assertSame('integrity_test', $options['json']['state']);
        
        // 验证只包含设置的字段
        $this->assertCount(1, $options);
    }

    public function test_configIdSpecialCharacters(): void
    {
        // 测试配置ID特殊字符
        $request = new UpdateContactWayRequest();
        $specialConfigId = 'update-config_with.special@chars_123';
        
        $request->setConfigId($specialConfigId);
        $request->setType(1);
        $request->setScene(2);
        
        $this->assertSame($specialConfigId, $request->getConfigId());
        
        $options = $request->getRequestOptions();
        $this->assertSame($specialConfigId, $options['json']['config_id']);
    }

    public function test_emptyStringConfigId(): void
    {
        // 测试空字符串配置ID
        $request = new UpdateContactWayRequest();
        $request->setConfigId('');
        $request->setType(1);
        $request->setScene(2);
        
        $this->assertSame('', $request->getConfigId());
        
        $options = $request->getRequestOptions();
        $this->assertSame('', $options['json']['config_id']);
    }

    public function test_configIdPersistence(): void
    {
        // 测试配置ID持久性
        $request = new UpdateContactWayRequest();
        $configId = 'persistence_test_config';
        
        $request->setConfigId($configId);
        $request->setType(1);
        $request->setScene(2);
        
        // 多次获取应保持一致
        $this->assertSame($configId, $request->getConfigId());
        
        $options = $request->getRequestOptions();
        $this->assertSame($configId, $options['json']['config_id']);
        
        // 再次获取选项应保持一致
        $optionsAgain = $request->getRequestOptions();
        $this->assertSame($configId, $optionsAgain['json']['config_id']);
    }
} 
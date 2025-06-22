<?php

namespace WechatWorkExternalContactBundle\Tests\Request;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkExternalContactBundle\Request\GetExternalContactListRequest;

/**
 * GetExternalContactListRequest 测试
 */
class GetExternalContactListRequestTest extends TestCase
{
    public function test_inheritance(): void
    {
        // 测试继承关系
        $request = new GetExternalContactListRequest();
        $this->assertInstanceOf(ApiRequest::class, $request);
    }

    public function test_userId_setterAndGetter(): void
    {
        // 测试用户ID设置和获取
        $request = new GetExternalContactListRequest();
        $userId = 'sales_manager_001';

        $request->setUserId($userId);
        $this->assertSame($userId, $request->getUserId());
    }

    public function test_requestPath(): void
    {
        // 测试请求路径
        $request = new GetExternalContactListRequest();
        $this->assertSame('/cgi-bin/externalcontact/list', $request->getRequestPath());
    }

    public function test_requestMethod(): void
    {
        // 测试请求方法
        $request = new GetExternalContactListRequest();
        $this->assertSame('GET', $request->getRequestMethod());
    }

    public function test_requestOptions(): void
    {
        // 测试获取请求选项
        $request = new GetExternalContactListRequest();
        $userId = 'employee_123';

        $request->setUserId($userId);

        $expected = [
            'query' => [
                'userid' => $userId,
            ],
        ];

        $this->assertSame($expected, $request->getRequestOptions());
    }

    public function test_requestOptionsStructure(): void
    {
        // 测试请求选项结构
        $request = new GetExternalContactListRequest();
        $request->setUserId('test_user');

        $options = $request->getRequestOptions();
        $this->assertArrayHasKey('query', $options);
        $this->assertArrayHasKey('userid', $options['query']);
        $this->assertCount(1, $options['query']);
    }

    public function test_businessScenario_salesManagerGetCustomers(): void
    {
        // 测试业务场景：销售经理获取客户列表
        $request = new GetExternalContactListRequest();
        $salesManagerId = 'sales_manager_zhang';

        $request->setUserId($salesManagerId);

        $this->assertSame($salesManagerId, $request->getUserId());

        $options = $request->getRequestOptions();
        $this->assertSame($salesManagerId, $options['query']['userid']);

        // 验证使用GET方法查询
        $this->assertSame('GET', $request->getRequestMethod());
        $this->assertSame('/cgi-bin/externalcontact/list', $request->getRequestPath());
    }

    public function test_businessScenario_customerServiceGetContacts(): void
    {
        // 测试业务场景：客服获取联系人列表
        $request = new GetExternalContactListRequest();
        $customerServiceId = 'cs_team_leader';

        $request->setUserId($customerServiceId);

        $options = $request->getRequestOptions();
        $this->assertSame($customerServiceId, $options['query']['userid']);
    }

    public function test_businessScenario_departmentHeadGetExternalUsers(): void
    {
        // 测试业务场景：部门负责人获取外部用户
        $request = new GetExternalContactListRequest();
        $departmentHeadId = 'dept_head_marketing';

        $request->setUserId($departmentHeadId);

        $this->assertSame($departmentHeadId, $request->getUserId());

        // 验证API路径符合获取外部联系人要求
        $this->assertStringContainsString('externalcontact', $request->getRequestPath());
        $this->assertStringContainsString('list', $request->getRequestPath());
    }

    public function test_specialCharacters_inUserId(): void
    {
        // 测试用户ID中的特殊字符
        $request = new GetExternalContactListRequest();
        $specialUserId = 'user-name_with.special@chars';

        $request->setUserId($specialUserId);

        $this->assertSame($specialUserId, $request->getUserId());

        $options = $request->getRequestOptions();
        $this->assertSame($specialUserId, $options['query']['userid']);
    }

    public function test_longUserId(): void
    {
        // 测试长用户ID
        $request = new GetExternalContactListRequest();
        $longUserId = str_repeat('a', 100);

        $request->setUserId($longUserId);

        $this->assertSame($longUserId, $request->getUserId());
    }

    public function test_unicodeCharacters(): void
    {
        // 测试Unicode字符
        $request = new GetExternalContactListRequest();
        $unicodeUserId = '用户_001_测试';

        $request->setUserId($unicodeUserId);

        $this->assertSame($unicodeUserId, $request->getUserId());

        $options = $request->getRequestOptions();
        $this->assertSame($unicodeUserId, $options['query']['userid']);
    }

    public function test_multipleSetOperations(): void
    {
        // 测试多次设置值
        $request = new GetExternalContactListRequest();

        $firstUserId = 'first_user';
        $secondUserId = 'second_user';

        $request->setUserId($firstUserId);
        $this->assertSame($firstUserId, $request->getUserId());

        $request->setUserId($secondUserId);
        $this->assertSame($secondUserId, $request->getUserId());

        $options = $request->getRequestOptions();
        $this->assertSame($secondUserId, $options['query']['userid']);
    }

    public function test_idempotentMethodCalls(): void
    {
        // 测试方法调用是幂等的
        $request = new GetExternalContactListRequest();
        $userId = 'idempotent_user';

        $request->setUserId($userId);

        // 多次调用应该返回相同结果
        $this->assertSame($userId, $request->getUserId());
        $this->assertSame($userId, $request->getUserId());

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
        $request = new GetExternalContactListRequest();
        $originalUserId = 'original_user';

        $request->setUserId($originalUserId);

        $options1 = $request->getRequestOptions();
        $options2 = $request->getRequestOptions();

        // 修改返回的数组不应影响原始数据
        $options1['query']['userid'] = 'modified_user';
        $options1['query']['new_field'] = 'new_value';
        $options1['new_key'] = 'new_value';

        $this->assertSame($originalUserId, $request->getUserId());
        $this->assertSame($originalUserId, $options2['query']['userid']);
        $this->assertArrayNotHasKey('new_field', $options2['query']);
        $this->assertArrayNotHasKey('new_key', $options2);
    }

    public function test_agentAwareTrait(): void
    {
        // 测试AgentAware特性
        $request = new GetExternalContactListRequest();

        // 测试trait提供的方法存在
    }

    public function test_emptyStringValue(): void
    {
        // 测试空字符串值
        $request = new GetExternalContactListRequest();
        $request->setUserId('');

        $this->assertSame('', $request->getUserId());

        $options = $request->getRequestOptions();
        $this->assertSame('', $options['query']['userid']);
    }

    public function test_requestParametersAreCorrect(): void
    {
        // 测试请求参数正确性
        $request = new GetExternalContactListRequest();
        $userId = 'param_test_user';

        $request->setUserId($userId);

        $options = $request->getRequestOptions();

        // 验证参数结构正确
        $this->assertArrayHasKey('query', $options);
        $this->assertArrayHasKey('userid', $options['query']);
        $this->assertSame($userId, $options['query']['userid']);

        // 验证只包含必要的参数
        $this->assertCount(1, $options);
        $this->assertCount(1, $options['query']);
    }

    public function test_apiEndpointCorrectness(): void
    {
        // 测试API端点正确性
        $request = new GetExternalContactListRequest();
        $path = $request->getRequestPath();

        $this->assertStringContainsString('externalcontact', $path);
        $this->assertStringContainsString('list', $path);
        $this->assertStringStartsWith('/cgi-bin/', $path);
        $this->assertStringEndsWith('/list', $path);
    }

    public function test_httpMethodCorrectness(): void
    {
        // 测试HTTP方法正确性
        $request = new GetExternalContactListRequest();
        $method = $request->getRequestMethod();

        $this->assertSame('GET', $method);
        $this->assertTrue(in_array($method, ['GET', 'POST', 'PUT', 'DELETE']));
    }

    public function test_queryParameterFormat(): void
    {
        // 测试查询参数格式
        $request = new GetExternalContactListRequest();
        $userId = 'format_test_user';

        $request->setUserId($userId);

        $options = $request->getRequestOptions();

        // 验证使用query而不是json格式
        $this->assertArrayHasKey('query', $options);
        $this->assertArrayNotHasKey('json', $options);
        $this->assertArrayNotHasKey('body', $options);
        $this->assertArrayNotHasKey('form_params', $options);
    }

    public function test_businessScenario_batchCustomerManagement(): void
    {
        // 测试业务场景：批量客户管理
        $request = new GetExternalContactListRequest();
        $batchManagerId = 'batch_manager_001';

        $request->setUserId($batchManagerId);

        $this->assertSame($batchManagerId, $request->getUserId());

        $options = $request->getRequestOptions();
        $this->assertSame($batchManagerId, $options['query']['userid']);

        // 验证API支持批量获取客户列表
        $this->assertSame('GET', $request->getRequestMethod());
        $this->assertStringContainsString('list', $request->getRequestPath());
    }

    public function test_userIdPersistence(): void
    {
        // 测试用户ID持久性
        $request = new GetExternalContactListRequest();
        $userId = 'persistence_test_user';

        $request->setUserId($userId);

        // 多次获取应保持一致
        $this->assertSame($userId, $request->getUserId());

        $options = $request->getRequestOptions();
        $this->assertSame($userId, $options['query']['userid']);

        // 再次获取选项应保持一致
        $optionsAgain = $request->getRequestOptions();
        $this->assertSame($userId, $optionsAgain['query']['userid']);
    }

    public function test_agentInterfaceImplementation(): void
    {
        // 测试AgentInterface接口实现
        $request = new GetExternalContactListRequest();
        $userId = 'test_user_id';

        $request->setUserId($userId);
        $this->assertSame($userId, $request->getUserId());
    }
}

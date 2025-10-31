<?php

namespace WechatWorkExternalContactBundle\Tests\Request;

use HttpClientBundle\Request\RequestInterface;
use HttpClientBundle\Tests\Request\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkExternalContactBundle\Request\GetExternalContactListRequest;

/**
 * GetExternalContactListRequest 测试
 *
 * @internal
 */
#[CoversClass(GetExternalContactListRequest::class)]
final class GetExternalContactListRequestTest extends RequestTestCase
{
    private GetExternalContactListRequest $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new GetExternalContactListRequest();
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

    public function testUserIdSetterAndGetter(): void
    {
        // 测试用户ID设置和获取
        $userId = 'sales_manager_001';

        $this->request->setUserId($userId);
        $this->assertSame($userId, $this->request->getUserId());
    }

    public function testRequestPath(): void
    {
        // 测试请求路径
        $this->assertSame('/cgi-bin/externalcontact/list', $this->request->getRequestPath());
    }

    public function testRequestMethod(): void
    {
        // 测试请求方法
        $this->assertSame('GET', $this->request->getRequestMethod());
    }

    public function testRequestOptions(): void
    {
        // 测试获取请求选项
        $userId = 'employee_123';

        $this->request->setUserId($userId);

        $expected = [
            'query' => [
                'userid' => $userId,
            ],
        ];

        $this->assertSame($expected, $this->request->getRequestOptions());
    }

    public function testRequestOptionsStructure(): void
    {
        // 测试请求选项结构
        $this->request->setUserId('test_user');

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('query', $options);
        $query = $options['query'];
        $this->assertIsArray($query);
        $this->assertArrayHasKey('userid', $query);
        $this->assertCount(1, $query);
    }

    public function testBusinessScenarioSalesManagerGetCustomers(): void
    {
        // 测试业务场景：销售经理获取客户列表
        $salesManagerId = 'sales_manager_zhang';

        $this->request->setUserId($salesManagerId);

        $this->assertSame($salesManagerId, $this->request->getUserId());

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('query', $options);
        $query = $options['query'];
        $this->assertIsArray($query);
        $this->assertArrayHasKey('userid', $query);
        $this->assertSame($salesManagerId, $query['userid']);

        // 验证使用GET方法查询
        $this->assertSame('GET', $this->request->getRequestMethod());
        $this->assertSame('/cgi-bin/externalcontact/list', $this->request->getRequestPath());
    }

    public function testBusinessScenarioCustomerServiceGetContacts(): void
    {
        // 测试业务场景：客服获取联系人列表
        $customerServiceId = 'cs_team_leader';

        $this->request->setUserId($customerServiceId);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('query', $options);
        $query = $options['query'];
        $this->assertIsArray($query);
        $this->assertArrayHasKey('userid', $query);
        $this->assertSame($customerServiceId, $query['userid']);
    }

    public function testBusinessScenarioDepartmentHeadGetExternalUsers(): void
    {
        // 测试业务场景：部门负责人获取外部用户
        $departmentHeadId = 'dept_head_marketing';

        $this->request->setUserId($departmentHeadId);

        $this->assertSame($departmentHeadId, $this->request->getUserId());

        // 验证API路径符合获取外部联系人要求
        $this->assertStringContainsString('externalcontact', $this->request->getRequestPath());
        $this->assertStringContainsString('list', $this->request->getRequestPath());
    }

    public function testSpecialCharactersInUserId(): void
    {
        // 测试用户ID中的特殊字符
        $specialUserId = 'user-name_with.special@chars';

        $this->request->setUserId($specialUserId);

        $this->assertSame($specialUserId, $this->request->getUserId());

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('query', $options);
        $query = $options['query'];
        $this->assertIsArray($query);
        $this->assertArrayHasKey('userid', $query);
        $this->assertSame($specialUserId, $query['userid']);
    }

    public function testLongUserId(): void
    {
        // 测试长用户ID
        $longUserId = str_repeat('a', 100);

        $this->request->setUserId($longUserId);

        $this->assertSame($longUserId, $this->request->getUserId());
    }

    public function testUnicodeCharacters(): void
    {
        // 测试Unicode字符
        $unicodeUserId = '用户_001_测试';

        $this->request->setUserId($unicodeUserId);

        $this->assertSame($unicodeUserId, $this->request->getUserId());

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('query', $options);
        $query = $options['query'];
        $this->assertIsArray($query);
        $this->assertArrayHasKey('userid', $query);
        $this->assertSame($unicodeUserId, $query['userid']);
    }

    public function testMultipleSetOperations(): void
    {
        // 测试多次设置值
        $firstUserId = 'first_user';
        $secondUserId = 'second_user';

        $this->request->setUserId($firstUserId);
        $this->assertSame($firstUserId, $this->request->getUserId());

        $this->request->setUserId($secondUserId);
        $this->assertSame($secondUserId, $this->request->getUserId());

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('query', $options);
        $query = $options['query'];
        $this->assertIsArray($query);
        $this->assertArrayHasKey('userid', $query);
        $this->assertSame($secondUserId, $query['userid']);
    }

    public function testIdempotentMethodCalls(): void
    {
        // 测试方法调用是幂等的
        $userId = 'idempotent_user';

        $this->request->setUserId($userId);

        // 多次调用应该返回相同结果
        $this->assertSame($userId, $this->request->getUserId());
        $this->assertSame($userId, $this->request->getUserId());

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
        $originalUserId = 'original_user';

        $this->request->setUserId($originalUserId);

        $options1 = $this->request->getRequestOptions();
        $options2 = $this->request->getRequestOptions();

        // 修改返回的数组不应影响原始数据
        $this->assertIsArray($options1);
        $this->assertArrayHasKey('query', $options1);
        $this->assertIsArray($options1['query']);
        $options1['query']['userid'] = 'modified_user';
        $options1['query']['new_field'] = 'new_value';
        $options1['new_key'] = 'new_value';

        $this->assertSame($originalUserId, $this->request->getUserId());
        $this->assertNotNull($options2);
        $this->assertArrayHasKey('query', $options2);
        $query2 = $options2['query'];
        $this->assertIsArray($query2);
        $this->assertArrayHasKey('userid', $query2);
        $this->assertSame($originalUserId, $query2['userid']);
        $this->assertArrayNotHasKey('new_field', $query2);
        $this->assertArrayNotHasKey('new_key', $options2);
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

    public function testEmptyStringValue(): void
    {
        // 测试空字符串值
        $this->request->setUserId('');

        $this->assertSame('', $this->request->getUserId());

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('query', $options);
        $query = $options['query'];
        $this->assertIsArray($query);
        $this->assertArrayHasKey('userid', $query);
        $this->assertSame('', $query['userid']);
    }

    public function testRequestParametersAreCorrect(): void
    {
        // 测试请求参数正确性
        $userId = 'param_test_user';

        $this->request->setUserId($userId);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);

        // 验证参数结构正确
        $this->assertArrayHasKey('query', $options);
        $query = $options['query'];
        $this->assertIsArray($query);
        $this->assertArrayHasKey('userid', $query);
        $this->assertSame($userId, $query['userid']);

        // 验证只包含必要的参数
        $this->assertCount(1, $options);
        $this->assertCount(1, $query);
    }

    public function testApiEndpointCorrectness(): void
    {
        // 测试API端点正确性
        $path = $this->request->getRequestPath();

        $this->assertStringContainsString('externalcontact', $path);
        $this->assertStringContainsString('list', $path);
        $this->assertStringStartsWith('/cgi-bin/', $path);
        $this->assertStringEndsWith('/list', $path);
    }

    public function testHttpMethodCorrectness(): void
    {
        // 测试HTTP方法正确性
        $method = $this->request->getRequestMethod();

        $this->assertSame('GET', $method);
        $this->assertContains($method, ['GET', 'POST', 'PUT', 'DELETE']);
    }

    public function testQueryParameterFormat(): void
    {
        // 测试查询参数格式
        $userId = 'format_test_user';

        $this->request->setUserId($userId);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);

        // 验证使用query而不是json格式
        $this->assertArrayHasKey('query', $options);
        $this->assertArrayNotHasKey('json', $options);
        $this->assertArrayNotHasKey('body', $options);
        $this->assertArrayNotHasKey('form_params', $options);
    }

    public function testBusinessScenarioBatchCustomerManagement(): void
    {
        // 测试业务场景：批量客户管理
        $batchManagerId = 'batch_manager_001';

        $this->request->setUserId($batchManagerId);

        $this->assertSame($batchManagerId, $this->request->getUserId());

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('query', $options);
        $query = $options['query'];
        $this->assertIsArray($query);
        $this->assertArrayHasKey('userid', $query);
        $this->assertSame($batchManagerId, $query['userid']);

        // 验证API支持批量获取客户列表
        $this->assertSame('GET', $this->request->getRequestMethod());
        $this->assertStringContainsString('list', $this->request->getRequestPath());
    }

    public function testUserIdPersistence(): void
    {
        // 测试用户ID持久性
        $userId = 'persistence_test_user';

        $this->request->setUserId($userId);

        // 多次获取应保持一致
        $this->assertSame($userId, $this->request->getUserId());

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('query', $options);
        $query = $options['query'];
        $this->assertIsArray($query);
        $this->assertArrayHasKey('userid', $query);
        $this->assertSame($userId, $query['userid']);

        // 再次获取选项应保持一致
        $optionsAgain = $this->request->getRequestOptions();
        $this->assertNotNull($optionsAgain);
        $this->assertArrayHasKey('query', $optionsAgain);
        $queryAgain = $optionsAgain['query'];
        $this->assertIsArray($queryAgain);
        $this->assertArrayHasKey('userid', $queryAgain);
        $this->assertSame($userId, $queryAgain['userid']);
    }

    public function testAgentInterfaceImplementation(): void
    {
        // 测试AgentInterface接口实现
        $userId = 'test_user_id';

        $this->request->setUserId($userId);
        $this->assertSame($userId, $this->request->getUserId());
    }
}

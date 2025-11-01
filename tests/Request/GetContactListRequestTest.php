<?php

namespace WechatWorkExternalContactBundle\Tests\Request;

use HttpClientBundle\Request\RequestInterface;
use HttpClientBundle\Test\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkExternalContactBundle\Request\GetContactListRequest;

/**
 * GetContactListRequest 测试
 *
 * @internal
 */
#[CoversClass(GetContactListRequest::class)]
final class GetContactListRequestTest extends RequestTestCase
{
    private GetContactListRequest $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new GetContactListRequest();
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

    public function testCursorSetterAndGetter(): void
    {
        // 测试游标设置和获取
        $cursor = 'cursor_page_001';

        $this->request->setCursor($cursor);
        $this->assertSame($cursor, $this->request->getCursor());
    }

    public function testCursorWithNull(): void
    {
        // 测试null游标
        $this->request->setCursor(null);

        $this->assertNull($this->request->getCursor());
    }

    public function testLimitSetterAndGetter(): void
    {
        // 测试限制数量设置和获取
        $limit = 500;

        $this->request->setLimit($limit);
        $this->assertSame($limit, $this->request->getLimit());
    }

    public function testLimitWithNull(): void
    {
        // 测试null限制数量
        $this->request->setLimit(null);

        $this->assertNull($this->request->getLimit());
    }

    public function testRequestPath(): void
    {
        // 测试请求路径
        $this->assertSame('/cgi-bin/externalcontact/contact_list', $this->request->getRequestPath());
    }

    public function testRequestOptionsWithBothParameters(): void
    {
        // 测试两个参数都有的请求选项
        $cursor = 'pagination_cursor_123';
        $limit = 100;

        $this->request->setCursor($cursor);
        $this->request->setLimit($limit);

        $expected = [
            'json' => [
                'cursor' => $cursor,
                'limit' => $limit,
            ],
        ];

        $this->assertSame($expected, $this->request->getRequestOptions());
    }

    public function testRequestOptionsWithOnlyCursor(): void
    {
        // 测试仅有游标的请求选项
        $cursor = 'only_cursor_456';

        $this->request->setCursor($cursor);

        $expected = [
            'json' => [
                'cursor' => $cursor,
            ],
        ];

        $this->assertSame($expected, $this->request->getRequestOptions());
    }

    public function testRequestOptionsWithOnlyLimit(): void
    {
        // 测试仅有限制的请求选项
        $limit = 200;

        $this->request->setLimit($limit);

        $expected = [
            'json' => [
                'limit' => $limit,
            ],
        ];

        $this->assertSame($expected, $this->request->getRequestOptions());
    }

    public function testRequestOptionsWithNullValues(): void
    {
        // 测试null值的请求选项
        $this->request->setCursor(null);
        $this->request->setLimit(null);

        $expected = [
            'json' => [],
        ];

        $this->assertSame($expected, $this->request->getRequestOptions());
    }

    public function testRequestOptionsStructure(): void
    {
        // 测试请求选项结构
        $this->request->setCursor('test_cursor');
        $this->request->setLimit(50);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $json = $options['json'];
        $this->assertIsArray($json);
        $this->assertArrayHasKey('cursor', $json);
        $this->assertArrayHasKey('limit', $json);
        $this->assertCount(2, $json);
    }

    public function testBusinessScenarioPaginatedContactRetrieval(): void
    {
        // 测试业务场景：分页获取联系人
        $firstPageCursor = null; // 首次调用不填
        $pageSize = 1000; // 默认最大值

        $this->request->setCursor($firstPageCursor);
        $this->request->setLimit($pageSize);

        $this->assertNull($this->request->getCursor());
        $this->assertSame($pageSize, $this->request->getLimit());

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertArrayNotHasKey('cursor', $options['json']);
        $this->assertSame($pageSize, $options['json']['limit']);

        // 验证API路径正确
        $this->assertSame('/cgi-bin/externalcontact/contact_list', $this->request->getRequestPath());
    }

    public function testBusinessScenarioContinuePagination(): void
    {
        // 测试业务场景：继续分页
        $nextCursor = 'next_page_cursor_from_previous_response';
        $limit = 500;

        $this->request->setCursor($nextCursor);
        $this->request->setLimit($limit);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertSame($nextCursor, $options['json']['cursor']);
        $this->assertSame($limit, $options['json']['limit']);
    }

    public function testBusinessScenarioBatchContactProcessing(): void
    {
        // 测试业务场景：批量联系人处理
        $batchSize = 200;

        $this->request->setLimit($batchSize);

        $this->assertSame($batchSize, $this->request->getLimit());

        // 验证批量处理的API路径
        $this->assertStringContainsString('contact_list', $this->request->getRequestPath());
    }

    public function testLimitBoundaryValues(): void
    {
        // 测试限制边界值

        // 测试最小值
        $this->request->setLimit(1);
        $this->assertSame(1, $this->request->getLimit());

        // 测试最大值
        $this->request->setLimit(1000);
        $this->assertSame(1000, $this->request->getLimit());

        // 测试中间值
        $this->request->setLimit(500);
        $this->assertSame(500, $this->request->getLimit());
    }

    public function testCursorSpecialCharacters(): void
    {
        // 测试游标特殊字符
        $specialCursor = 'cursor_with-special_chars@123.test';

        $this->request->setCursor($specialCursor);

        $this->assertSame($specialCursor, $this->request->getCursor());

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertSame($specialCursor, $options['json']['cursor']);
    }

    public function testLongCursor(): void
    {
        // 测试长游标
        $longCursor = str_repeat('cursor_part_', 10) . 'end';

        $this->request->setCursor($longCursor);

        $this->assertSame($longCursor, $this->request->getCursor());
    }

    public function testUnicodeCharacters(): void
    {
        // 测试Unicode字符
        $unicodeCursor = '游标_测试_123';

        $this->request->setCursor($unicodeCursor);

        $this->assertSame($unicodeCursor, $this->request->getCursor());

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertSame($unicodeCursor, $options['json']['cursor']);
    }

    public function testMultipleSetOperations(): void
    {
        // 测试多次设置值

        $firstCursor = 'first_cursor';
        $firstLimit = 100;
        $secondCursor = 'second_cursor';
        $secondLimit = 200;

        $this->request->setCursor($firstCursor);
        $this->request->setLimit($firstLimit);

        $this->assertSame($firstCursor, $this->request->getCursor());
        $this->assertSame($firstLimit, $this->request->getLimit());

        $this->request->setCursor($secondCursor);
        $this->request->setLimit($secondLimit);

        $this->assertSame($secondCursor, $this->request->getCursor());
        $this->assertSame($secondLimit, $this->request->getLimit());

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertSame($secondCursor, $options['json']['cursor']);
        $this->assertSame($secondLimit, $options['json']['limit']);
    }

    public function testResetToNull(): void
    {
        // 测试重置为null

        $this->request->setCursor('initial_cursor');
        $this->request->setLimit(100);

        // 重置为null
        $this->request->setCursor(null);
        $this->request->setLimit(null);

        $this->assertNull($this->request->getCursor());
        $this->assertNull($this->request->getLimit());

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertArrayNotHasKey('cursor', $options['json']);
        $this->assertArrayNotHasKey('limit', $options['json']);
        $this->assertSame([], $options['json']);
    }

    public function testIdempotentMethodCalls(): void
    {
        // 测试方法调用是幂等的
        $cursor = 'idempotent_cursor';
        $limit = 300;

        $this->request->setCursor($cursor);
        $this->request->setLimit($limit);

        // 多次调用应该返回相同结果
        $this->assertSame($cursor, $this->request->getCursor());
        $this->assertSame($cursor, $this->request->getCursor());

        $this->assertSame($limit, $this->request->getLimit());
        $this->assertSame($limit, $this->request->getLimit());

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
        $originalCursor = 'original_cursor';
        $originalLimit = 250;

        $this->request->setCursor($originalCursor);
        $this->request->setLimit($originalLimit);

        $options1 = $this->request->getRequestOptions();
        $options2 = $this->request->getRequestOptions();

        // 修改返回的数组不应影响原始数据
        $this->assertIsArray($options1);
        $this->assertArrayHasKey('json', $options1);
        $this->assertIsArray($options1['json']);
        $options1['json']['cursor'] = 'modified_cursor';
        $options1['json']['limit'] = 999;
        $options1['json']['new_field'] = 'new_value';
        $options1['new_key'] = 'new_value';

        $this->assertSame($originalCursor, $this->request->getCursor());
        $this->assertSame($originalLimit, $this->request->getLimit());

        $this->assertNotNull($options2);
        $this->assertIsArray($options2);
        $this->assertArrayHasKey('json', $options2);
        $this->assertIsArray($options2['json']);
        $this->assertSame($originalCursor, $options2['json']['cursor']);
        $this->assertSame($originalLimit, $options2['json']['limit']);
        $this->assertArrayNotHasKey('new_field', $options2['json']);
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

    public function testEmptyStringValues(): void
    {
        // 测试空字符串值
        $this->request->setCursor('');

        $this->assertSame('', $this->request->getCursor());

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertSame('', $options['json']['cursor']);
    }

    public function testRequestParametersCorrectness(): void
    {
        // 测试请求参数正确性
        $cursor = 'test_cursor_param';
        $limit = 150;

        $this->request->setCursor($cursor);
        $this->request->setLimit($limit);

        $options = $this->request->getRequestOptions();

        // 验证参数结构正确
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $json = $options['json'];
        $this->assertIsArray($json);
        $this->assertArrayHasKey('cursor', $json);
        $this->assertArrayHasKey('limit', $json);
        $this->assertSame($cursor, $json['cursor']);
        $this->assertSame($limit, $json['limit']);

        // 验证只包含设置的参数
        $this->assertCount(1, $options);
        $this->assertIsArray($json);
        $this->assertCount(2, $json);
    }

    public function testApiEndpointCorrectness(): void
    {
        // 测试API端点正确性
        $path = $this->request->getRequestPath();

        $this->assertStringContainsString('externalcontact', $path);
        $this->assertStringContainsString('contact_list', $path);
        $this->assertStringStartsWith('/cgi-bin/', $path);
        $this->assertStringEndsWith('/contact_list', $path);
    }

    public function testJsonRequestFormat(): void
    {
        // 测试JSON请求格式
        $cursor = 'json_format_cursor';
        $limit = 400;

        $this->request->setCursor($cursor);
        $this->request->setLimit($limit);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);

        // 验证使用json而不是query格式
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayNotHasKey('query', $options);
        $this->assertArrayNotHasKey('body', $options);
        $this->assertArrayNotHasKey('form_params', $options);
    }

    public function testBusinessScenarioDataExport(): void
    {
        // 测试业务场景：数据导出
        $exportBatchSize = 1000; // 最大批次大小

        $this->request->setLimit($exportBatchSize);

        $this->assertSame($exportBatchSize, $this->request->getLimit());

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertSame($exportBatchSize, $options['json']['limit']);

        // 验证API支持大批量数据导出
        $this->assertStringContainsString('contact_list', $this->request->getRequestPath());
    }

    public function testBusinessScenarioIncrementalSync(): void
    {
        // 测试业务场景：增量同步
        $syncCursor = 'incremental_sync_cursor_v2';
        $syncBatchSize = 100;

        $this->request->setCursor($syncCursor);
        $this->request->setLimit($syncBatchSize);

        $this->assertSame($syncCursor, $this->request->getCursor());
        $this->assertSame($syncBatchSize, $this->request->getLimit());

        // 验证支持增量同步的参数格式
        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertArrayHasKey('cursor', $options['json']);
        $this->assertArrayHasKey('limit', $options['json']);
    }

    public function testRequestDataIntegrity(): void
    {
        // 测试请求数据完整性
        $cursor = 'integrity_test_cursor';
        $limit = 75;

        $this->request->setCursor($cursor);
        $this->request->setLimit($limit);

        $options = $this->request->getRequestOptions();

        // 验证请求数据结构完整性
        $this->assertNotNull($options);
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertSame($cursor, $options['json']['cursor']);
        $this->assertSame($limit, $options['json']['limit']);

        // 验证只包含必要的字段
        $this->assertCount(1, $options);
        $this->assertIsArray($options['json']);
        $this->assertCount(2, $options['json']);
    }

    public function testAgentInterfaceImplementation(): void
    {
        // 测试AgentInterface接口实现的完整功能

        // 测试初始状态
        $this->assertNull($this->request->getAgent());

        // 测试设置和获取agent
        $agent = $this->createMock(AgentInterface::class);
        $this->request->setAgent($agent);
        $this->assertSame($agent, $this->request->getAgent());

        // 测试设置为 null
        $this->request->setAgent(null);
        $this->assertNull($this->request->getAgent());
    }
}

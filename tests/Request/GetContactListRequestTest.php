<?php

namespace WechatWorkExternalContactBundle\Tests\Request;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkExternalContactBundle\Request\GetContactListRequest;

/**
 * GetContactListRequest 测试
 */
class GetContactListRequestTest extends TestCase
{
    public function test_inheritance(): void
    {
        // 测试继承关系
        $request = new GetContactListRequest();
        $this->assertInstanceOf(ApiRequest::class, $request);
        $this->assertTrue(method_exists($request, 'getAgent'));
        $this->assertTrue(method_exists($request, 'setAgent'));
    }

    public function test_cursor_setterAndGetter(): void
    {
        // 测试游标设置和获取
        $request = new GetContactListRequest();
        $cursor = 'cursor_page_001';
        
        $request->setCursor($cursor);
        $this->assertSame($cursor, $request->getCursor());
    }

    public function test_cursor_withNull(): void
    {
        // 测试null游标
        $request = new GetContactListRequest();
        $request->setCursor(null);
        
        $this->assertNull($request->getCursor());
    }

    public function test_limit_setterAndGetter(): void
    {
        // 测试限制数量设置和获取
        $request = new GetContactListRequest();
        $limit = 500;
        
        $request->setLimit($limit);
        $this->assertSame($limit, $request->getLimit());
    }

    public function test_limit_withNull(): void
    {
        // 测试null限制数量
        $request = new GetContactListRequest();
        $request->setLimit(null);
        
        $this->assertNull($request->getLimit());
    }

    public function test_requestPath(): void
    {
        // 测试请求路径
        $request = new GetContactListRequest();
        $this->assertSame('/cgi-bin/externalcontact/contact_list', $request->getRequestPath());
    }

    public function test_requestOptions_withBothParameters(): void
    {
        // 测试两个参数都有的请求选项
        $request = new GetContactListRequest();
        $cursor = 'pagination_cursor_123';
        $limit = 100;
        
        $request->setCursor($cursor);
        $request->setLimit($limit);
        
        $expected = [
            'json' => [
                'cursor' => $cursor,
                'limit' => $limit,
            ],
        ];
        
        $this->assertSame($expected, $request->getRequestOptions());
    }

    public function test_requestOptions_withOnlyCursor(): void
    {
        // 测试仅有游标的请求选项
        $request = new GetContactListRequest();
        $cursor = 'only_cursor_456';
        
        $request->setCursor($cursor);
        
        $expected = [
            'json' => [
                'cursor' => $cursor,
            ],
        ];
        
        $this->assertSame($expected, $request->getRequestOptions());
    }

    public function test_requestOptions_withOnlyLimit(): void
    {
        // 测试仅有限制的请求选项
        $request = new GetContactListRequest();
        $limit = 200;
        
        $request->setLimit($limit);
        
        $expected = [
            'json' => [
                'limit' => $limit,
            ],
        ];
        
        $this->assertSame($expected, $request->getRequestOptions());
    }

    public function test_requestOptions_withNullValues(): void
    {
        // 测试null值的请求选项
        $request = new GetContactListRequest();
        $request->setCursor(null);
        $request->setLimit(null);
        
        $expected = [
            'json' => [],
        ];
        
        $this->assertSame($expected, $request->getRequestOptions());
    }

    public function test_requestOptionsStructure(): void
    {
        // 测试请求选项结构
        $request = new GetContactListRequest();
        $request->setCursor('test_cursor');
        $request->setLimit(50);
        
        $options = $request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertArrayHasKey('cursor', $options['json']);
        $this->assertArrayHasKey('limit', $options['json']);
        $this->assertCount(2, $options['json']);
    }

    public function test_businessScenario_paginatedContactRetrieval(): void
    {
        // 测试业务场景：分页获取联系人
        $request = new GetContactListRequest();
        $firstPageCursor = null; // 首次调用不填
        $pageSize = 1000; // 默认最大值
        
        $request->setCursor($firstPageCursor);
        $request->setLimit($pageSize);
        
        $this->assertNull($request->getCursor());
        $this->assertSame($pageSize, $request->getLimit());
        
        $options = $request->getRequestOptions();
        $this->assertArrayNotHasKey('cursor', $options['json']);
        $this->assertSame($pageSize, $options['json']['limit']);
        
        // 验证API路径正确
        $this->assertSame('/cgi-bin/externalcontact/contact_list', $request->getRequestPath());
    }

    public function test_businessScenario_continuePagination(): void
    {
        // 测试业务场景：继续分页
        $request = new GetContactListRequest();
        $nextCursor = 'next_page_cursor_from_previous_response';
        $limit = 500;
        
        $request->setCursor($nextCursor);
        $request->setLimit($limit);
        
        $options = $request->getRequestOptions();
        $this->assertSame($nextCursor, $options['json']['cursor']);
        $this->assertSame($limit, $options['json']['limit']);
    }

    public function test_businessScenario_batchContactProcessing(): void
    {
        // 测试业务场景：批量联系人处理
        $request = new GetContactListRequest();
        $batchSize = 200;
        
        $request->setLimit($batchSize);
        
        $this->assertSame($batchSize, $request->getLimit());
        
        // 验证批量处理的API路径
        $this->assertStringContainsString('contact_list', $request->getRequestPath());
    }

    public function test_limitBoundaryValues(): void
    {
        // 测试限制边界值
        $request = new GetContactListRequest();
        
        // 测试最小值
        $request->setLimit(1);
        $this->assertSame(1, $request->getLimit());
        
        // 测试最大值
        $request->setLimit(1000);
        $this->assertSame(1000, $request->getLimit());
        
        // 测试中间值
        $request->setLimit(500);
        $this->assertSame(500, $request->getLimit());
    }

    public function test_cursorSpecialCharacters(): void
    {
        // 测试游标特殊字符
        $request = new GetContactListRequest();
        $specialCursor = 'cursor_with-special_chars@123.test';
        
        $request->setCursor($specialCursor);
        
        $this->assertSame($specialCursor, $request->getCursor());
        
        $options = $request->getRequestOptions();
        $this->assertSame($specialCursor, $options['json']['cursor']);
    }

    public function test_longCursor(): void
    {
        // 测试长游标
        $request = new GetContactListRequest();
        $longCursor = str_repeat('cursor_part_', 10) . 'end';
        
        $request->setCursor($longCursor);
        
        $this->assertSame($longCursor, $request->getCursor());
    }

    public function test_unicodeCharacters(): void
    {
        // 测试Unicode字符
        $request = new GetContactListRequest();
        $unicodeCursor = '游标_测试_123';
        
        $request->setCursor($unicodeCursor);
        
        $this->assertSame($unicodeCursor, $request->getCursor());
        
        $options = $request->getRequestOptions();
        $this->assertSame($unicodeCursor, $options['json']['cursor']);
    }

    public function test_multipleSetOperations(): void
    {
        // 测试多次设置值
        $request = new GetContactListRequest();
        
        $firstCursor = 'first_cursor';
        $firstLimit = 100;
        $secondCursor = 'second_cursor';
        $secondLimit = 200;
        
        $request->setCursor($firstCursor);
        $request->setLimit($firstLimit);
        
        $this->assertSame($firstCursor, $request->getCursor());
        $this->assertSame($firstLimit, $request->getLimit());
        
        $request->setCursor($secondCursor);
        $request->setLimit($secondLimit);
        
        $this->assertSame($secondCursor, $request->getCursor());
        $this->assertSame($secondLimit, $request->getLimit());
        
        $options = $request->getRequestOptions();
        $this->assertSame($secondCursor, $options['json']['cursor']);
        $this->assertSame($secondLimit, $options['json']['limit']);
    }

    public function test_resetToNull(): void
    {
        // 测试重置为null
        $request = new GetContactListRequest();
        
        $request->setCursor('initial_cursor');
        $request->setLimit(100);
        
        // 重置为null
        $request->setCursor(null);
        $request->setLimit(null);
        
        $this->assertNull($request->getCursor());
        $this->assertNull($request->getLimit());
        
        $options = $request->getRequestOptions();
        $this->assertArrayNotHasKey('cursor', $options['json']);
        $this->assertArrayNotHasKey('limit', $options['json']);
        $this->assertSame([], $options['json']);
    }

    public function test_idempotentMethodCalls(): void
    {
        // 测试方法调用是幂等的
        $request = new GetContactListRequest();
        $cursor = 'idempotent_cursor';
        $limit = 300;
        
        $request->setCursor($cursor);
        $request->setLimit($limit);
        
        // 多次调用应该返回相同结果
        $this->assertSame($cursor, $request->getCursor());
        $this->assertSame($cursor, $request->getCursor());
        
        $this->assertSame($limit, $request->getLimit());
        $this->assertSame($limit, $request->getLimit());
        
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
        $request = new GetContactListRequest();
        $originalCursor = 'original_cursor';
        $originalLimit = 250;
        
        $request->setCursor($originalCursor);
        $request->setLimit($originalLimit);
        
        $options1 = $request->getRequestOptions();
        $options2 = $request->getRequestOptions();
        
        // 修改返回的数组不应影响原始数据
        $options1['json']['cursor'] = 'modified_cursor';
        $options1['json']['limit'] = 999;
        $options1['json']['new_field'] = 'new_value';
        $options1['new_key'] = 'new_value';
        
        $this->assertSame($originalCursor, $request->getCursor());
        $this->assertSame($originalLimit, $request->getLimit());
        
        $this->assertSame($originalCursor, $options2['json']['cursor']);
        $this->assertSame($originalLimit, $options2['json']['limit']);
        $this->assertArrayNotHasKey('new_field', $options2['json']);
        $this->assertArrayNotHasKey('new_key', $options2);
    }

    public function test_agentAwareTrait(): void
    {
        // 测试AgentAware特性
        $request = new GetContactListRequest();
        
        // 测试trait提供的方法存在
        $this->assertTrue(method_exists($request, 'getAgent'));
        $this->assertTrue(method_exists($request, 'setAgent'));
        $this->assertTrue(is_callable([$request, 'getAgent']));
        $this->assertTrue(is_callable([$request, 'setAgent']));
    }

    public function test_emptyStringValues(): void
    {
        // 测试空字符串值
        $request = new GetContactListRequest();
        $request->setCursor('');
        
        $this->assertSame('', $request->getCursor());
        
        $options = $request->getRequestOptions();
        $this->assertSame('', $options['json']['cursor']);
    }

    public function test_requestParametersCorrectness(): void
    {
        // 测试请求参数正确性
        $request = new GetContactListRequest();
        $cursor = 'test_cursor_param';
        $limit = 150;
        
        $request->setCursor($cursor);
        $request->setLimit($limit);
        
        $options = $request->getRequestOptions();
        
        // 验证参数结构正确
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertArrayHasKey('cursor', $options['json']);
        $this->assertArrayHasKey('limit', $options['json']);
        $this->assertSame($cursor, $options['json']['cursor']);
        $this->assertSame($limit, $options['json']['limit']);
        
        // 验证只包含设置的参数
        $this->assertCount(1, $options);
        $this->assertCount(2, $options['json']);
    }

    public function test_apiEndpointCorrectness(): void
    {
        // 测试API端点正确性
        $request = new GetContactListRequest();
        $path = $request->getRequestPath();
        
        $this->assertStringContainsString('externalcontact', $path);
        $this->assertStringContainsString('contact_list', $path);
        $this->assertStringStartsWith('/cgi-bin/', $path);
        $this->assertStringEndsWith('/contact_list', $path);
    }

    public function test_jsonRequestFormat(): void
    {
        // 测试JSON请求格式
        $request = new GetContactListRequest();
        $cursor = 'json_format_cursor';
        $limit = 400;
        
        $request->setCursor($cursor);
        $request->setLimit($limit);
        
        $options = $request->getRequestOptions();
        
        // 验证使用json而不是query格式
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayNotHasKey('query', $options);
        $this->assertArrayNotHasKey('body', $options);
        $this->assertArrayNotHasKey('form_params', $options);
    }

    public function test_businessScenario_dataExport(): void
    {
        // 测试业务场景：数据导出
        $request = new GetContactListRequest();
        $exportBatchSize = 1000; // 最大批次大小
        
        $request->setLimit($exportBatchSize);
        
        $this->assertSame($exportBatchSize, $request->getLimit());
        
        $options = $request->getRequestOptions();
        $this->assertSame($exportBatchSize, $options['json']['limit']);
        
        // 验证API支持大批量数据导出
        $this->assertStringContainsString('contact_list', $request->getRequestPath());
    }

    public function test_businessScenario_incrementalSync(): void
    {
        // 测试业务场景：增量同步
        $request = new GetContactListRequest();
        $syncCursor = 'incremental_sync_cursor_v2';
        $syncBatchSize = 100;
        
        $request->setCursor($syncCursor);
        $request->setLimit($syncBatchSize);
        
        $this->assertSame($syncCursor, $request->getCursor());
        $this->assertSame($syncBatchSize, $request->getLimit());
        
        // 验证支持增量同步的参数格式
        $options = $request->getRequestOptions();
        $this->assertArrayHasKey('cursor', $options['json']);
        $this->assertArrayHasKey('limit', $options['json']);
    }

    public function test_requestDataIntegrity(): void
    {
        // 测试请求数据完整性
        $request = new GetContactListRequest();
        $cursor = 'integrity_test_cursor';
        $limit = 75;
        
        $request->setCursor($cursor);
        $request->setLimit($limit);
        
        $options = $request->getRequestOptions();
        
        // 验证请求数据结构完整性
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertSame($cursor, $options['json']['cursor']);
        $this->assertSame($limit, $options['json']['limit']);
        
        // 验证只包含必要的字段
        $this->assertCount(1, $options);
        $this->assertCount(2, $options['json']);
    }
} 
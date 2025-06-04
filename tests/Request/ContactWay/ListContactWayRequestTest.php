<?php

namespace WechatWorkExternalContactBundle\Tests\Request\ContactWay;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkExternalContactBundle\Request\ContactWay\ListContactWayRequest;

/**
 * ListContactWayRequest 测试
 */
class ListContactWayRequestTest extends TestCase
{
    public function test_inheritance(): void
    {
        // 测试继承关系
        $request = new ListContactWayRequest();
        $this->assertInstanceOf(ApiRequest::class, $request);
        $this->assertTrue(method_exists($request, 'getAgent'));
        $this->assertTrue(method_exists($request, 'setAgent'));
    }

    public function test_requestPath(): void
    {
        // 测试请求路径
        $request = new ListContactWayRequest();
        $this->assertSame('/cgi-bin/externalcontact/list_contact_way', $request->getRequestPath());
    }

    public function test_defaultValues(): void
    {
        // 测试默认值
        $request = new ListContactWayRequest();
        
        $this->assertSame(100, $request->getLimit()); // 默认值100
        $this->assertNull($request->getStartTime());
        $this->assertNull($request->getEndTime());
        $this->assertNull($request->getCursor());
    }

    public function test_startTime_setterAndGetter(): void
    {
        // 测试开始时间设置和获取
        $request = new ListContactWayRequest();
        $startTime = time() - 7 * 24 * 3600; // 7天前
        
        $request->setStartTime($startTime);
        $this->assertSame($startTime, $request->getStartTime());
        
        $request->setStartTime(null);
        $this->assertNull($request->getStartTime());
    }

    public function test_endTime_setterAndGetter(): void
    {
        // 测试结束时间设置和获取
        $request = new ListContactWayRequest();
        $endTime = time(); // 当前时间
        
        $request->setEndTime($endTime);
        $this->assertSame($endTime, $request->getEndTime());
        
        $request->setEndTime(null);
        $this->assertNull($request->getEndTime());
    }

    public function test_cursor_setterAndGetter(): void
    {
        // 测试游标设置和获取
        $request = new ListContactWayRequest();
        $cursor = 'next_cursor_12345';
        
        $request->setCursor($cursor);
        $this->assertSame($cursor, $request->getCursor());
        
        $request->setCursor(null);
        $this->assertNull($request->getCursor());
    }

    public function test_limit_setterAndGetter(): void
    {
        // 测试限制数量设置和获取
        $request = new ListContactWayRequest();
        
        $request->setLimit(50);
        $this->assertSame(50, $request->getLimit());
        
        $request->setLimit(1000); // 最大值
        $this->assertSame(1000, $request->getLimit());
        
        $request->setLimit(1);
        $this->assertSame(1, $request->getLimit());
    }

    public function test_requestOptions_defaultOnly(): void
    {
        // 测试只有默认值的请求选项
        $request = new ListContactWayRequest();
        
        $options = $request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayHasKey('limit', $options['json']);
        $this->assertSame(100, $options['json']['limit']);
        
        // 应该只包含limit字段
        $this->assertCount(1, $options['json']);
        $this->assertArrayNotHasKey('start_time', $options['json']);
        $this->assertArrayNotHasKey('end_time', $options['json']);
        $this->assertArrayNotHasKey('cursor', $options['json']);
    }

    public function test_requestOptions_withAllFields(): void
    {
        // 测试包含所有字段的请求选项
        $request = new ListContactWayRequest();
        $startTime = time() - 7 * 24 * 3600;
        $endTime = time();
        $cursor = 'cursor_12345';
        $limit = 50;
        
        $request->setStartTime($startTime);
        $request->setEndTime($endTime);
        $request->setCursor($cursor);
        $request->setLimit($limit);
        
        $options = $request->getRequestOptions();
        
        $this->assertSame($startTime, $options['json']['start_time']);
        $this->assertSame($endTime, $options['json']['end_time']);
        $this->assertSame($cursor, $options['json']['cursor']);
        $this->assertSame($limit, $options['json']['limit']);
        $this->assertCount(4, $options['json']);
    }

    public function test_requestOptions_partialFields(): void
    {
        // 测试部分字段的请求选项
        $request = new ListContactWayRequest();
        $startTime = time() - 24 * 3600; // 1天前
        $limit = 200;
        
        $request->setStartTime($startTime);
        $request->setLimit($limit);
        // endTime和cursor保持null
        
        $options = $request->getRequestOptions();
        
        $this->assertArrayHasKey('start_time', $options['json']);
        $this->assertArrayHasKey('limit', $options['json']);
        $this->assertArrayNotHasKey('end_time', $options['json']);
        $this->assertArrayNotHasKey('cursor', $options['json']);
        $this->assertSame($startTime, $options['json']['start_time']);
        $this->assertSame($limit, $options['json']['limit']);
        $this->assertCount(2, $options['json']);
    }

    public function test_businessScenario_firstPageList(): void
    {
        // 测试业务场景：获取第一页列表
        $request = new ListContactWayRequest();
        $request->setLimit(20); // 每页20条
        
        $options = $request->getRequestOptions();
        
        $this->assertSame(20, $options['json']['limit']);
        $this->assertArrayNotHasKey('cursor', $options['json']);
        
        // 验证API路径正确
        $this->assertSame('/cgi-bin/externalcontact/list_contact_way', $request->getRequestPath());
    }

    public function test_businessScenario_paginatedList(): void
    {
        // 测试业务场景：分页获取列表
        $request = new ListContactWayRequest();
        $cursor = 'page_2_cursor_abc123';
        $limit = 100;
        
        $request->setCursor($cursor);
        $request->setLimit($limit);
        
        $options = $request->getRequestOptions();
        
        $this->assertSame($cursor, $options['json']['cursor']);
        $this->assertSame($limit, $options['json']['limit']);
        
        // 验证分页参数正确
        $this->assertArrayHasKey('cursor', $options['json']);
        $this->assertArrayHasKey('limit', $options['json']);
    }

    public function test_businessScenario_timeRangeList(): void
    {
        // 测试业务场景：按时间范围获取列表
        $request = new ListContactWayRequest();
        $startTime = strtotime('2024-01-01 00:00:00');
        $endTime = strtotime('2024-01-31 23:59:59');
        
        $request->setStartTime($startTime);
        $request->setEndTime($endTime);
        $request->setLimit(500);
        
        $options = $request->getRequestOptions();
        
        $this->assertSame($startTime, $options['json']['start_time']);
        $this->assertSame($endTime, $options['json']['end_time']);
        $this->assertSame(500, $options['json']['limit']);
        
        // 验证时间范围查询
        $this->assertArrayHasKey('start_time', $options['json']);
        $this->assertArrayHasKey('end_time', $options['json']);
    }

    public function test_businessScenario_recentContactWays(): void
    {
        // 测试业务场景：获取最近的联系方式
        $request = new ListContactWayRequest();
        $recentTime = time() - 3 * 24 * 3600; // 最近3天
        
        $request->setStartTime($recentTime);
        $request->setLimit(10); // 只要最近10个
        
        $options = $request->getRequestOptions();
        
        $this->assertSame($recentTime, $options['json']['start_time']);
        $this->assertSame(10, $options['json']['limit']);
        $this->assertArrayNotHasKey('end_time', $options['json']); // 不设置结束时间
    }

    public function test_limitBoundaryValues(): void
    {
        // 测试限制数量边界值
        $request = new ListContactWayRequest();
        
        // 最小值：1
        $request->setLimit(1);
        $this->assertSame(1, $request->getLimit());
        
        // 默认值：100
        $request->setLimit(100);
        $this->assertSame(100, $request->getLimit());
        
        // 最大值：1000
        $request->setLimit(1000);
        $this->assertSame(1000, $request->getLimit());
    }

    public function test_timestampValues(): void
    {
        // 测试时间戳值
        $request = new ListContactWayRequest();
        
        // 测试各种时间戳
        $timestamps = [
            1640995200, // 2022-01-01 00:00:00
            time(), // 当前时间
            time() - 86400, // 1天前
            time() + 86400, // 1天后
        ];
        
        foreach ($timestamps as $timestamp) {
            $request->setStartTime($timestamp);
            $this->assertSame($timestamp, $request->getStartTime());
            
            $request->setEndTime($timestamp);
            $this->assertSame($timestamp, $request->getEndTime());
        }
    }

    public function test_cursorFormats(): void
    {
        // 测试游标格式
        $request = new ListContactWayRequest();
        $cursors = [
            'simple_cursor',
            'cursor_with_numbers_123',
            'cursor-with-dashes',
            'cursor_with_underscores',
            'UPPERCASE_CURSOR',
            'cursor.with.dots',
            base64_encode('encoded_cursor'),
        ];
        
        foreach ($cursors as $cursor) {
            $request->setCursor($cursor);
            $this->assertSame($cursor, $request->getCursor());
            
            $options = $request->getRequestOptions();
            $this->assertSame($cursor, $options['json']['cursor']);
        }
    }

    public function test_multipleSetOperations(): void
    {
        // 测试多次设置值
        $request = new ListContactWayRequest();
        
        $request->setLimit(50);
        $request->setLimit(200);
        $this->assertSame(200, $request->getLimit());
        
        $request->setCursor('first_cursor');
        $request->setCursor('second_cursor');
        $this->assertSame('second_cursor', $request->getCursor());
        
        $firstTime = time() - 86400;
        $secondTime = time() - 3600;
        $request->setStartTime($firstTime);
        $request->setStartTime($secondTime);
        $this->assertSame($secondTime, $request->getStartTime());
    }

    public function test_idempotentMethodCalls(): void
    {
        // 测试方法调用是幂等的
        $request = new ListContactWayRequest();
        $startTime = time() - 86400;
        $cursor = 'test_cursor';
        
        $request->setStartTime($startTime);
        $request->setCursor($cursor);
        $request->setLimit(50);
        
        // 多次调用应该返回相同结果
        $this->assertSame($startTime, $request->getStartTime());
        $this->assertSame($startTime, $request->getStartTime());
        
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
        $request = new ListContactWayRequest();
        $originalStartTime = time() - 86400;
        $originalCursor = 'original_cursor';
        
        $request->setStartTime($originalStartTime);
        $request->setCursor($originalCursor);
        $request->setLimit(100);
        
        $options1 = $request->getRequestOptions();
        $options2 = $request->getRequestOptions();
        
        // 修改返回的数组不应影响原始数据
        $options1['json']['start_time'] = time();
        $options1['json']['cursor'] = 'modified_cursor';
        $options1['json']['limit'] = 500;
        $options1['json']['new_field'] = 'new_value';
        
        $this->assertSame($originalStartTime, $request->getStartTime());
        $this->assertSame($originalCursor, $request->getCursor());
        $this->assertSame(100, $request->getLimit());
        
        $this->assertSame($originalStartTime, $options2['json']['start_time']);
        $this->assertSame($originalCursor, $options2['json']['cursor']);
        $this->assertSame(100, $options2['json']['limit']);
        $this->assertArrayNotHasKey('new_field', $options2['json']);
    }

    public function test_agentAwareTrait(): void
    {
        // 测试AgentAware特性
        $request = new ListContactWayRequest();
        
        // 测试trait提供的方法存在
        $this->assertTrue(method_exists($request, 'getAgent'));
        $this->assertTrue(method_exists($request, 'setAgent'));
        $this->assertTrue(is_callable([$request, 'getAgent']));
        $this->assertTrue(is_callable([$request, 'setAgent']));
    }

    public function test_emptyCursor(): void
    {
        // 测试空游标
        $request = new ListContactWayRequest();
        $request->setCursor('');
        
        $this->assertSame('', $request->getCursor());
        
        $options = $request->getRequestOptions();
        $this->assertSame('', $options['json']['cursor']);
    }

    public function test_apiEndpointCorrectness(): void
    {
        // 测试API端点正确性
        $request = new ListContactWayRequest();
        $path = $request->getRequestPath();
        
        $this->assertStringContainsString('externalcontact', $path);
        $this->assertStringContainsString('list_contact_way', $path);
        $this->assertStringStartsWith('/cgi-bin/', $path);
        $this->assertStringEndsWith('/list_contact_way', $path);
    }

    public function test_jsonRequestFormat(): void
    {
        // 测试JSON请求格式
        $request = new ListContactWayRequest();
        $request->setLimit(50);
        $request->setCursor('test_cursor');
        
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
        $request = new ListContactWayRequest();
        $startTime = time() - 86400;
        $endTime = time();
        $cursor = 'integrity_cursor';
        $limit = 200;
        
        $request->setStartTime($startTime);
        $request->setEndTime($endTime);
        $request->setCursor($cursor);
        $request->setLimit($limit);
        
        $options = $request->getRequestOptions();
        
        // 验证请求数据结构完整性
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertSame($startTime, $options['json']['start_time']);
        $this->assertSame($endTime, $options['json']['end_time']);
        $this->assertSame($cursor, $options['json']['cursor']);
        $this->assertSame($limit, $options['json']['limit']);
        
        // 验证只包含设置的字段
        $this->assertCount(1, $options);
        $this->assertCount(4, $options['json']);
    }

    public function test_nullFieldsNotIncluded(): void
    {
        // 测试null字段不包含在请求中
        $request = new ListContactWayRequest();
        $request->setLimit(100); // 只设置limit
        // 其他字段保持null
        
        $options = $request->getRequestOptions();
        
        $this->assertArrayHasKey('limit', $options['json']);
        $this->assertArrayNotHasKey('start_time', $options['json']);
        $this->assertArrayNotHasKey('end_time', $options['json']);
        $this->assertArrayNotHasKey('cursor', $options['json']);
        $this->assertCount(1, $options['json']);
    }

    public function test_timeRangeLogic(): void
    {
        // 测试时间范围逻辑
        $request = new ListContactWayRequest();
        $startTime = time() - 7 * 24 * 3600; // 7天前
        $endTime = time(); // 现在
        
        $request->setStartTime($startTime);
        $request->setEndTime($endTime);
        
        // 验证开始时间小于结束时间
        $this->assertLessThan($request->getEndTime(), $request->getStartTime());
        
        $options = $request->getRequestOptions();
        $this->assertLessThan($options['json']['end_time'], $options['json']['start_time']);
    }
} 
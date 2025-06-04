<?php

namespace WechatWorkExternalContactBundle\Tests\Request;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkExternalContactBundle\Request\GetUserBehaviorDataRequest;

/**
 * GetUserBehaviorDataRequest 测试
 */
class GetUserBehaviorDataRequestTest extends TestCase
{
    public function test_inheritance(): void
    {
        // 测试继承关系
        $request = new GetUserBehaviorDataRequest();
        $this->assertInstanceOf(ApiRequest::class, $request);
    }

    public function test_usesAgentAwareTrait(): void
    {
        // 测试使用AgentAware trait
        $request = new GetUserBehaviorDataRequest();
        $this->assertTrue(method_exists($request, 'getAgent'));
        $this->assertTrue(method_exists($request, 'setAgent'));
    }

    public function test_getRequestPath(): void
    {
        // 测试请求路径
        $request = new GetUserBehaviorDataRequest();
        $this->assertSame('/cgi-bin/externalcontact/get_user_behavior_data', $request->getRequestPath());
    }

    public function test_getRequestMethod(): void
    {
        // 测试请求方法
        $request = new GetUserBehaviorDataRequest();
        $this->assertSame('POST', $request->getRequestMethod());
    }

    public function test_startTime_setterAndGetter(): void
    {
        // 测试开始时间设置和获取
        $request = new GetUserBehaviorDataRequest();
        $startTime = Carbon::now()->subDays(7);
        
        $request->setStartTime($startTime);
        $this->assertSame($startTime, $request->getStartTime());
    }

    public function test_endTime_setterAndGetter(): void
    {
        // 测试结束时间设置和获取
        $request = new GetUserBehaviorDataRequest();
        $endTime = Carbon::now();
        
        $request->setEndTime($endTime);
        $this->assertSame($endTime, $request->getEndTime());
    }

    public function test_userIds_setterAndGetter(): void
    {
        // 测试用户ID列表设置和获取
        $request = new GetUserBehaviorDataRequest();
        $userIds = ['user1', 'user2', 'user3'];
        
        $request->setUserIds($userIds);
        $this->assertSame($userIds, $request->getUserIds());
    }

    public function test_partyIds_setterAndGetter(): void
    {
        // 测试部门ID列表设置和获取
        $request = new GetUserBehaviorDataRequest();
        $partyIds = [1, 2, 3];
        
        $request->setPartyIds($partyIds);
        $this->assertSame($partyIds, $request->getPartyIds());
    }

    public function test_defaultValues(): void
    {
        // 测试默认值
        $request = new GetUserBehaviorDataRequest();
        
        $this->assertSame([], $request->getUserIds());
        $this->assertSame([], $request->getPartyIds());
    }

    public function test_emptyArraysInitialization(): void
    {
        // 测试空数组初始化
        $request = new GetUserBehaviorDataRequest();
        
        $this->assertEmpty($request->getUserIds());
        $this->assertEmpty($request->getPartyIds());
        $this->assertIsArray($request->getUserIds());
        $this->assertIsArray($request->getPartyIds());
    }

    public function test_getRequestOptions_withUserIds(): void
    {
        // 测试带用户ID的请求选项
        $request = new GetUserBehaviorDataRequest();
        $startTime = Carbon::create(2023, 1, 1, 0, 0, 0);
        $endTime = Carbon::create(2023, 1, 7, 23, 59, 59);
        $userIds = ['user1', 'user2'];
        
        $request->setStartTime($startTime);
        $request->setEndTime($endTime);
        $request->setUserIds($userIds);
        
        $options = $request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayHasKey('start_time', $options['json']);
        $this->assertArrayHasKey('end_time', $options['json']);
        $this->assertArrayHasKey('userid', $options['json']);
        $this->assertArrayNotHasKey('partyid', $options['json']);
        
        $this->assertSame($startTime->getTimestamp(), $options['json']['start_time']);
        $this->assertSame($endTime->getTimestamp(), $options['json']['end_time']);
        $this->assertSame($userIds, $options['json']['userid']);
    }

    public function test_getRequestOptions_withPartyIds(): void
    {
        // 测试带部门ID的请求选项
        $request = new GetUserBehaviorDataRequest();
        $startTime = Carbon::create(2023, 1, 1);
        $endTime = Carbon::create(2023, 1, 7);
        $partyIds = [1, 2, 3];
        
        $request->setStartTime($startTime);
        $request->setEndTime($endTime);
        $request->setPartyIds($partyIds);
        
        $options = $request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayHasKey('start_time', $options['json']);
        $this->assertArrayHasKey('end_time', $options['json']);
        $this->assertArrayHasKey('partyid', $options['json']);
        $this->assertArrayNotHasKey('userid', $options['json']);
        
        $this->assertSame($partyIds, $options['json']['partyid']);
    }

    public function test_getRequestOptions_withBothUserIdsAndPartyIds(): void
    {
        // 测试同时包含用户ID和部门ID
        $request = new GetUserBehaviorDataRequest();
        $startTime = Carbon::create(2023, 1, 1);
        $endTime = Carbon::create(2023, 1, 7);
        $userIds = ['user1'];
        $partyIds = [1];
        
        $request->setStartTime($startTime);
        $request->setEndTime($endTime);
        $request->setUserIds($userIds);
        $request->setPartyIds($partyIds);
        
        $options = $request->getRequestOptions();
        
        $this->assertArrayHasKey('userid', $options['json']);
        $this->assertArrayHasKey('partyid', $options['json']);
        $this->assertSame($userIds, $options['json']['userid']);
        $this->assertSame($partyIds, $options['json']['partyid']);
    }

    public function test_getRequestOptions_throwsExceptionWhenBothEmpty(): void
    {
        // 测试当用户ID和部门ID都为空时抛出异常
        $request = new GetUserBehaviorDataRequest();
        $startTime = Carbon::create(2023, 1, 1);
        $endTime = Carbon::create(2023, 1, 7);
        
        $request->setStartTime($startTime);
        $request->setEndTime($endTime);
        // 不设置userIds和partyIds，使用默认空数组
        
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('userid和partyid不可同时为空');
        
        $request->getRequestOptions();
    }

    public function test_getRequestOptions_emptyUserIdsButHasPartyIds(): void
    {
        // 测试用户ID为空但部门ID不为空
        $request = new GetUserBehaviorDataRequest();
        $startTime = Carbon::create(2023, 1, 1);
        $endTime = Carbon::create(2023, 1, 7);
        $partyIds = [1];
        
        $request->setStartTime($startTime);
        $request->setEndTime($endTime);
        $request->setUserIds([]); // 显式设置为空数组
        $request->setPartyIds($partyIds);
        
        $options = $request->getRequestOptions();
        
        $this->assertArrayNotHasKey('userid', $options['json']);
        $this->assertArrayHasKey('partyid', $options['json']);
    }

    public function test_getRequestOptions_emptyPartyIdsButHasUserIds(): void
    {
        // 测试部门ID为空但用户ID不为空
        $request = new GetUserBehaviorDataRequest();
        $startTime = Carbon::create(2023, 1, 1);
        $endTime = Carbon::create(2023, 1, 7);
        $userIds = ['user1'];
        
        $request->setStartTime($startTime);
        $request->setEndTime($endTime);
        $request->setUserIds($userIds);
        $request->setPartyIds([]); // 显式设置为空数组
        
        $options = $request->getRequestOptions();
        
        $this->assertArrayHasKey('userid', $options['json']);
        $this->assertArrayNotHasKey('partyid', $options['json']);
    }

    public function test_timestampConversion(): void
    {
        // 测试时间戳转换
        $request = new GetUserBehaviorDataRequest();
        $startTime = Carbon::create(2023, 6, 15, 10, 30, 45);
        $endTime = Carbon::create(2023, 6, 16, 15, 45, 30);
        
        $request->setStartTime($startTime);
        $request->setEndTime($endTime);
        $request->setUserIds(['user1']);
        
        $options = $request->getRequestOptions();
        
        $this->assertSame(1686825045, $options['json']['start_time']); // 2023-06-15 10:30:45的时间戳
        $this->assertSame(1686930330, $options['json']['end_time']); // 2023-06-16 15:45:30的时间戳
    }

    public function test_carbonInterfaceCompatibility(): void
    {
        // 测试CarbonInterface兼容性
        $request = new GetUserBehaviorDataRequest();
        
        $startTime = Carbon::now()->subWeek();
        $endTime = Carbon::now();
        
        $request->setStartTime($startTime);
        $request->setEndTime($endTime);
        
        $this->assertInstanceOf(CarbonInterface::class, $request->getStartTime());
        $this->assertInstanceOf(CarbonInterface::class, $request->getEndTime());
    }

    public function test_largeUserIdsList(): void
    {
        // 测试大用户ID列表（文档限制100个）
        $request = new GetUserBehaviorDataRequest();
        $userIds = [];
        for ($i = 1; $i <= 100; $i++) {
            $userIds[] = "user_{$i}";
        }
        
        $request->setUserIds($userIds);
        $this->assertCount(100, $request->getUserIds());
        $this->assertSame($userIds, $request->getUserIds());
    }

    public function test_largePartyIdsList(): void
    {
        // 测试大部门ID列表（文档限制100个）
        $request = new GetUserBehaviorDataRequest();
        $partyIds = range(1, 100);
        
        $request->setPartyIds($partyIds);
        $this->assertCount(100, $request->getPartyIds());
        $this->assertSame($partyIds, $request->getPartyIds());
    }

    public function test_businessScenario_weeklyReport(): void
    {
        // 测试业务场景：周报数据
        $request = new GetUserBehaviorDataRequest();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        $salesTeam = ['sales1', 'sales2', 'sales3'];
        
        $request->setStartTime($startOfWeek);
        $request->setEndTime($endOfWeek);
        $request->setUserIds($salesTeam);
        
        $this->assertSame('/cgi-bin/externalcontact/get_user_behavior_data', $request->getRequestPath());
        $this->assertSame('POST', $request->getRequestMethod());
        
        $options = $request->getRequestOptions();
        $this->assertArrayHasKey('userid', $options['json']);
        $this->assertSame($salesTeam, $options['json']['userid']);
    }

    public function test_businessScenario_departmentMonthlyReport(): void
    {
        // 测试业务场景：部门月报
        $request = new GetUserBehaviorDataRequest();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $departments = [1, 2, 5, 10]; // 销售部、市场部、客服部、产品部
        
        $request->setStartTime($startOfMonth);
        $request->setEndTime($endOfMonth);
        $request->setPartyIds($departments);
        
        $options = $request->getRequestOptions();
        $this->assertArrayHasKey('partyid', $options['json']);
        $this->assertArrayNotHasKey('userid', $options['json']);
        $this->assertSame($departments, $options['json']['partyid']);
    }

    public function test_businessScenario_crossDepartmentAnalysis(): void
    {
        // 测试业务场景：跨部门分析（同时包含特定用户和部门）
        $request = new GetUserBehaviorDataRequest();
        $startTime = Carbon::now()->subMonth();
        $endTime = Carbon::now();
        $keyUsers = ['manager1', 'director1'];
        $targetDepts = [1, 3];
        
        $request->setStartTime($startTime);
        $request->setEndTime($endTime);
        $request->setUserIds($keyUsers);
        $request->setPartyIds($targetDepts);
        
        $options = $request->getRequestOptions();
        $this->assertArrayHasKey('userid', $options['json']);
        $this->assertArrayHasKey('partyid', $options['json']);
        $this->assertSame($keyUsers, $options['json']['userid']);
        $this->assertSame($targetDepts, $options['json']['partyid']);
    }

    public function test_multipleSetCalls(): void
    {
        // 测试多次设置值
        $request = new GetUserBehaviorDataRequest();
        
        $firstUsers = ['user1', 'user2'];
        $secondUsers = ['user3', 'user4'];
        
        $request->setUserIds($firstUsers);
        $this->assertSame($firstUsers, $request->getUserIds());
        
        $request->setUserIds($secondUsers);
        $this->assertSame($secondUsers, $request->getUserIds());
    }

    public function test_immutableBehavior(): void
    {
        // 测试不可变行为
        $request = new GetUserBehaviorDataRequest();
        $originalUsers = ['user1', 'user2'];
        $originalParties = [1, 2];
        
        $request->setUserIds($originalUsers);
        $request->setPartyIds($originalParties);
        
        $retrievedUsers = $request->getUserIds();
        $retrievedParties = $request->getPartyIds();
        
        // 修改返回的数组不应影响原始数据
        $retrievedUsers[] = 'user3';
        $retrievedParties[] = 3;
        
        $this->assertSame($originalUsers, $request->getUserIds());
        $this->assertSame($originalParties, $request->getPartyIds());
        $this->assertCount(2, $request->getUserIds());
        $this->assertCount(2, $request->getPartyIds());
    }
} 
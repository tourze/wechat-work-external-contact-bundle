<?php

namespace WechatWorkExternalContactBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use WechatWorkExternalContactBundle\Entity\UserBehaviorDataByParty;
use Tourze\WechatWorkContracts\DepartmentInterface;

/**
 * UserBehaviorDataByParty 实体测试用例
 * 
 * 测试按部门统计的联系客户行为数据实体的所有功能
 */
class UserBehaviorDataByPartyTest extends TestCase
{
    private UserBehaviorDataByParty $behaviorData;

    protected function setUp(): void
    {
        $this->behaviorData = new UserBehaviorDataByParty();
    }

    public function test_constructor_setsDefaultValues(): void
    {
        $data = new UserBehaviorDataByParty();
        
        $this->assertSame(0, $data->getId());
        $this->assertNull($data->getParty());
        $this->assertNull($data->getCreateTime());
        $this->assertNull($data->getUpdateTime());
        
        // 测试 BehaviorDataTrait 的默认值
        $this->assertNull($data->getDate());
        $this->assertNull($data->getNewApplyCount());
        $this->assertNull($data->getNewContactCount());
        $this->assertNull($data->getChatCount());
        $this->assertNull($data->getMessageCount());
        $this->assertNull($data->getAvgReplyTime());
        $this->assertNull($data->getNegativeFeedbackCount());
        $this->assertNull($data->getReplyPercentage());
    }

    public function test_setParty_withValidDepartment_setsDepartmentCorrectly(): void
    {
        /** @var DepartmentInterface&MockObject $department */
        $department = $this->createMock(DepartmentInterface::class);
        
        $result = $this->behaviorData->setParty($department);
        
        $this->assertSame($this->behaviorData, $result);
        $this->assertSame($department, $this->behaviorData->getParty());
    }

    public function test_setParty_withNull_setsNull(): void
    {
        /** @var DepartmentInterface&MockObject $department */
        $department = $this->createMock(DepartmentInterface::class);
        $this->behaviorData->setParty($department);
        
        $result = $this->behaviorData->setParty(null);
        
        $this->assertSame($this->behaviorData, $result);
        $this->assertNull($this->behaviorData->getParty());
    }

    public function test_setCreateTime_withValidDateTime_setsTimeCorrectly(): void
    {
        $createTime = new \DateTime('2024-01-01 08:00:00');
        
        $this->behaviorData->setCreateTime($createTime);
        
        $this->assertSame($createTime, $this->behaviorData->getCreateTime());
    }

    public function test_setCreateTime_withNull_setsNull(): void
    {
        $this->behaviorData->setCreateTime(new \DateTime());
        
        $this->behaviorData->setCreateTime(null);
        
        $this->assertNull($this->behaviorData->getCreateTime());
    }

    public function test_setUpdateTime_withValidDateTime_setsTimeCorrectly(): void
    {
        $updateTime = new \DateTime('2024-01-30 18:30:00');
        
        $this->behaviorData->setUpdateTime($updateTime);
        
        $this->assertSame($updateTime, $this->behaviorData->getUpdateTime());
    }

    public function test_setUpdateTime_withNull_setsNull(): void
    {
        $this->behaviorData->setUpdateTime(new \DateTime());
        
        $this->behaviorData->setUpdateTime(null);
        
        $this->assertNull($this->behaviorData->getUpdateTime());
    }

    /**
     * 测试 BehaviorDataTrait 的功能
     */
    public function test_setDate_withValidDateTime_setsTimeCorrectly(): void
    {
        $date = new \DateTime('2024-01-15 00:00:00');
        
        $result = $this->behaviorData->setDate($date);
        
        $this->assertSame($this->behaviorData, $result);
        $this->assertSame($date, $this->behaviorData->getDate());
    }

    public function test_setNewApplyCount_withValidCount_setsCountCorrectly(): void
    {
        $newApplyCount = 15;
        
        $result = $this->behaviorData->setNewApplyCount($newApplyCount);
        
        $this->assertSame($this->behaviorData, $result);
        $this->assertSame($newApplyCount, $this->behaviorData->getNewApplyCount());
    }

    public function test_setNewContactCount_withValidCount_setsCountCorrectly(): void
    {
        $newContactCount = 50;
        
        $result = $this->behaviorData->setNewContactCount($newContactCount);
        
        $this->assertSame($this->behaviorData, $result);
        $this->assertSame($newContactCount, $this->behaviorData->getNewContactCount());
    }

    public function test_setChatCount_withValidCount_setsCountCorrectly(): void
    {
        $chatCount = 120;
        
        $result = $this->behaviorData->setChatCount($chatCount);
        
        $this->assertSame($this->behaviorData, $result);
        $this->assertSame($chatCount, $this->behaviorData->getChatCount());
    }

    public function test_setMessageCount_withValidCount_setsCountCorrectly(): void
    {
        $messageCount = 500;
        
        $result = $this->behaviorData->setMessageCount($messageCount);
        
        $this->assertSame($this->behaviorData, $result);
        $this->assertSame($messageCount, $this->behaviorData->getMessageCount());
    }

    public function test_setAvgReplyTime_withValidTime_setsTimeCorrectly(): void
    {
        $avgReplyTime = 30; // 30分钟
        
        $result = $this->behaviorData->setAvgReplyTime($avgReplyTime);
        
        $this->assertSame($this->behaviorData, $result);
        $this->assertSame($avgReplyTime, $this->behaviorData->getAvgReplyTime());
    }

    public function test_setNegativeFeedbackCount_withValidCount_setsCountCorrectly(): void
    {
        $negativeFeedbackCount = 5;
        
        $result = $this->behaviorData->setNegativeFeedbackCount($negativeFeedbackCount);
        
        $this->assertSame($this->behaviorData, $result);
        $this->assertSame($negativeFeedbackCount, $this->behaviorData->getNegativeFeedbackCount());
    }

    public function test_setReplyPercentage_withValidPercentage_setsPercentageCorrectly(): void
    {
        $replyPercentage = 85.5;
        
        $result = $this->behaviorData->setReplyPercentage($replyPercentage);
        
        $this->assertSame($this->behaviorData, $result);
        $this->assertSame($replyPercentage, $this->behaviorData->getReplyPercentage());
    }

    /**
     * 测试链式调用
     */
    public function test_chainedSetters_returnSameInstance(): void
    {
        /** @var DepartmentInterface&MockObject $department */
        $department = $this->createMock(DepartmentInterface::class);
        
        $date = new \DateTime('2024-01-15 00:00:00');
        $createTime = new \DateTime('2024-01-01 08:00:00');
        $updateTime = new \DateTime('2024-01-30 18:00:00');
        
        $result = $this->behaviorData
            ->setParty($department)
            ->setDate($date)
            ->setNewApplyCount(15)
            ->setNewContactCount(50)
            ->setChatCount(120)
            ->setMessageCount(500)
            ->setAvgReplyTime(30)
            ->setNegativeFeedbackCount(5)
            ->setReplyPercentage(85.5);
        
        $this->behaviorData->setCreateTime($createTime);
        $this->behaviorData->setUpdateTime($updateTime);
        
        $this->assertSame($this->behaviorData, $result);
        $this->assertSame($department, $this->behaviorData->getParty());
        $this->assertSame($date, $this->behaviorData->getDate());
        $this->assertSame(15, $this->behaviorData->getNewApplyCount());
        $this->assertSame(50, $this->behaviorData->getNewContactCount());
        $this->assertSame(120, $this->behaviorData->getChatCount());
        $this->assertSame(500, $this->behaviorData->getMessageCount());
        $this->assertSame(30, $this->behaviorData->getAvgReplyTime());
        $this->assertSame(5, $this->behaviorData->getNegativeFeedbackCount());
        $this->assertSame(85.5, $this->behaviorData->getReplyPercentage());
        $this->assertSame($createTime, $this->behaviorData->getCreateTime());
        $this->assertSame($updateTime, $this->behaviorData->getUpdateTime());
    }

    /**
     * 测试边界场景
     */
    public function test_edgeCases_extremeValues(): void
    {
        // 测试极端整数值
        $this->behaviorData->setNewContactCount(PHP_INT_MAX);
        $this->assertSame(PHP_INT_MAX, $this->behaviorData->getNewContactCount());
        
        $this->behaviorData->setMessageCount(0);
        $this->assertSame(0, $this->behaviorData->getMessageCount());
        
        $this->behaviorData->setAvgReplyTime(PHP_INT_MIN);
        $this->assertSame(PHP_INT_MIN, $this->behaviorData->getAvgReplyTime());
    }

    public function test_edgeCases_floatValues(): void
    {
        // 测试极端浮点值
        $this->behaviorData->setReplyPercentage(0.0);
        $this->assertSame(0.0, $this->behaviorData->getReplyPercentage());
        
        $this->behaviorData->setReplyPercentage(100.0);
        $this->assertSame(100.0, $this->behaviorData->getReplyPercentage());
        
        $this->behaviorData->setReplyPercentage(99.999);
        $this->assertSame(99.999, $this->behaviorData->getReplyPercentage());
    }

    public function test_edgeCases_dateTimeTypes(): void
    {
        // 测试DateTime
        $dateTime = new \DateTime('2024-01-15 12:30:45');
        $this->behaviorData->setDate($dateTime);
        $this->assertSame($dateTime, $this->behaviorData->getDate());
        
        // 测试DateTimeImmutable
        $dateTimeImmutable = new \DateTimeImmutable('2024-02-20 09:15:30');
        $this->behaviorData->setCreateTime($dateTimeImmutable);
        $this->assertSame($dateTimeImmutable, $this->behaviorData->getCreateTime());
    }

    /**
     * 测试业务逻辑场景
     */
    public function test_businessScenario_departmentDailyStats(): void
    {
        /** @var DepartmentInterface&MockObject $salesDept */
        $salesDept = $this->createMock(DepartmentInterface::class);
        
        $date = new \DateTime('2024-01-15 00:00:00');
        $createTime = new \DateTime('2024-01-16 08:00:00');
        
        // 模拟销售部门的日统计数据
        $this->behaviorData
            ->setParty($salesDept)
            ->setDate($date)
            ->setNewApplyCount(25)      // 新增申请人数
            ->setNewContactCount(1200)  // 总联系人数
            ->setChatCount(3)           // 新增聊天次数
            ->setMessageCount(5000)     // 总消息数
            ->setAvgReplyTime(1800)     // 平均回复时间30分钟
            ->setNegativeFeedbackCount(2) // 负面反馈次数
            ->setReplyPercentage(92.5); // 回复率92.5%
        
        $this->behaviorData->setCreateTime($createTime);
        
        // 验证业务状态
        $this->assertNotNull($this->behaviorData->getParty());
        $this->assertSame($date, $this->behaviorData->getDate());
        $this->assertSame(25, $this->behaviorData->getNewApplyCount());
        $this->assertSame(1200, $this->behaviorData->getNewContactCount());
        $this->assertSame(3, $this->behaviorData->getChatCount());
        $this->assertSame(5000, $this->behaviorData->getMessageCount());
        $this->assertSame(1800, $this->behaviorData->getAvgReplyTime());
        $this->assertSame(2, $this->behaviorData->getNegativeFeedbackCount());
        $this->assertSame(92.5, $this->behaviorData->getReplyPercentage());
        
        // 验证时间逻辑
        $this->assertTrue($date < $createTime);
    }

    public function test_businessScenario_departmentWeeklyStats(): void
    {
        /** @var DepartmentInterface&MockObject $marketingDept */
        $marketingDept = $this->createMock(DepartmentInterface::class);
        
        $date = new \DateTime('2024-01-08 00:00:00'); // 周一
        
        // 模拟市场部门的周统计数据
        $this->behaviorData
            ->setParty($marketingDept)
            ->setDate($date)
            ->setNewApplyCount(150)     // 一周新增150个申请
            ->setNewContactCount(3500)  // 总联系人数
            ->setChatCount(45)          // 一周新增聊天次数
            ->setMessageCount(10000)    // 一周总消息数
            ->setAvgReplyTime(2400)     // 平均回复时间40分钟
            ->setNegativeFeedbackCount(8) // 负面反馈次数
            ->setReplyPercentage(88.2); // 回复率88.2%
        
        // 验证周统计数据的合理性
        $this->assertTrue($this->behaviorData->getNewApplyCount() > 0);
        $this->assertTrue($this->behaviorData->getNewContactCount() > $this->behaviorData->getNewApplyCount());
        $this->assertTrue($this->behaviorData->getMessageCount() >= $this->behaviorData->getNewContactCount());
        $this->assertTrue($this->behaviorData->getReplyPercentage() >= 0 && $this->behaviorData->getReplyPercentage() <= 100);
    }

    public function test_businessScenario_perfectPerformanceDepartment(): void
    {
        /** @var DepartmentInterface&MockObject $techDept */
        $techDept = $this->createMock(DepartmentInterface::class);
        
        // 模拟表现完美的技术部门
        $this->behaviorData
            ->setParty($techDept)
            ->setDate(new \DateTime('2024-01-15'))
            ->setNewApplyCount(20)
            ->setNewContactCount(800)
            ->setChatCount(100)
            ->setMessageCount(2000)
            ->setAvgReplyTime(600)        // 10分钟快速回复
            ->setNegativeFeedbackCount(0)   // 无负面反馈
            ->setReplyPercentage(100.0);  // 100%回复率
        
        // 验证完美表现
        $this->assertSame(20, $this->behaviorData->getNewApplyCount());
        $this->assertSame(800, $this->behaviorData->getNewContactCount());
        $this->assertSame(0, $this->behaviorData->getNegativeFeedbackCount());
        $this->assertSame(100.0, $this->behaviorData->getReplyPercentage());
        $this->assertTrue($this->behaviorData->getAvgReplyTime() <= 600); // 快速回复
    }

    public function test_businessScenario_problematicDepartment(): void
    {
        /** @var DepartmentInterface&MockObject $dept */
        $dept = $this->createMock(DepartmentInterface::class);
        
        // 模拟有问题的部门
        $this->behaviorData
            ->setParty($dept)
            ->setDate(new \DateTime('2024-01-15'))
            ->setNewApplyCount(5)         // 新增申请人数很少
            ->setNewContactCount(200)     // 总量下降
            ->setChatCount(10)            // 新增聊天次数较少
            ->setMessageCount(500)         // 总量下降
            ->setAvgReplyTime(7200)        // 2小时回复时间过长
            ->setNegativeFeedbackCount(20)  // 负面反馈次数较多
            ->setReplyPercentage(45.5);    // 回复率较低
        
        // 验证问题指标
        $this->assertTrue($this->behaviorData->getNewApplyCount() < 10);
        $this->assertTrue($this->behaviorData->getNewContactCount() > $this->behaviorData->getNewApplyCount());
        $this->assertTrue($this->behaviorData->getMessageCount() > $this->behaviorData->getNewContactCount());
        $this->assertTrue($this->behaviorData->getAvgReplyTime() > 3600); // 超过1小时
        $this->assertTrue($this->behaviorData->getNegativeFeedbackCount() > 10);
        $this->assertTrue($this->behaviorData->getReplyPercentage() < 50);
    }

    public function test_businessScenario_monthlyTrendAnalysis(): void
    {
        /** @var DepartmentInterface&MockObject $dept */
        $dept = $this->createMock(DepartmentInterface::class);
        
        $createTime = new \DateTime('2024-02-01 08:00:00');
        $updateTime = new \DateTime('2024-02-01 18:00:00');
        
        // 模拟月度趋势分析数据
        $this->behaviorData
            ->setParty($dept)
            ->setDate(new \DateTime('2024-01-01')) // 1月份数据
            ->setNewApplyCount(300)
            ->setNewContactCount(5000)
            ->setChatCount(100)
            ->setMessageCount(2000)
            ->setAvgReplyTime(1500)  // 25分钟
            ->setNegativeFeedbackCount(10)
            ->setReplyPercentage(90.8);
        
        $this->behaviorData->setCreateTime($createTime);
        $this->behaviorData->setUpdateTime($updateTime);
        
        // 验证月度数据的合理性
        $this->assertTrue($this->behaviorData->getNewApplyCount() >= 0);
        $this->assertTrue($this->behaviorData->getNewContactCount() > 0);
        $this->assertTrue($this->behaviorData->getMessageCount() >= 0); // messageCount可能小于newContactCount
        $this->assertTrue($this->behaviorData->getReplyPercentage() >= 0);
        $this->assertTrue($this->behaviorData->getReplyPercentage() <= 100);
        $this->assertTrue($updateTime > $createTime);
    }

    /**
     * 测试null值处理
     */
    public function test_nullValueHandling_allNullValues(): void
    {
        $this->behaviorData
            ->setParty(null)
            ->setNewApplyCount(null)
            ->setNewContactCount(null)
            ->setChatCount(null)
            ->setMessageCount(null)
            ->setAvgReplyTime(null)
            ->setNegativeFeedbackCount(null)
            ->setReplyPercentage(null);
        
        $this->behaviorData->setCreateTime(null);
        $this->behaviorData->setUpdateTime(null);
        
        $this->assertNull($this->behaviorData->getParty());
        $this->assertNull($this->behaviorData->getNewApplyCount());
        $this->assertNull($this->behaviorData->getNewContactCount());
        $this->assertNull($this->behaviorData->getChatCount());
        $this->assertNull($this->behaviorData->getMessageCount());
        $this->assertNull($this->behaviorData->getAvgReplyTime());
        $this->assertNull($this->behaviorData->getNegativeFeedbackCount());
        $this->assertNull($this->behaviorData->getReplyPercentage());
        $this->assertNull($this->behaviorData->getCreateTime());
        $this->assertNull($this->behaviorData->getUpdateTime());
    }

    public function test_setDate_withNull_expectsException(): void
    {
        // setDate 不接受 null，会抛出类型错误
        $this->expectException(\TypeError::class);
        $this->behaviorData->setDate(/** @phpstan-ignore-next-line */ null);
    }
} 
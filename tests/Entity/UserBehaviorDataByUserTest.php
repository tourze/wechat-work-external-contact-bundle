<?php

namespace WechatWorkExternalContactBundle\Tests\Entity;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tourze\WechatWorkContracts\UserInterface;
use WechatWorkExternalContactBundle\Entity\UserBehaviorDataByUser;

/**
 * UserBehaviorDataByUser 实体测试用例
 * 
 * 测试按用户统计的联系客户行为数据实体的所有功能
 */
class UserBehaviorDataByUserTest extends TestCase
{
    private UserBehaviorDataByUser $behaviorData;

    protected function setUp(): void
    {
        $this->behaviorData = new UserBehaviorDataByUser();
    }

    public function test_constructor_setsDefaultValues(): void
    {
        $data = new UserBehaviorDataByUser();
        
        $this->assertSame(0, $data->getId());
        $this->assertNull($data->getUser());
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

    public function test_setUser_withValidUser_setsUserCorrectly(): void
    {
        /** @var UserInterface&MockObject $user */
        $user = $this->createMock(UserInterface::class);
        
        $result = $this->behaviorData->setUser($user);
        
        $this->assertSame($this->behaviorData, $result);
        $this->assertSame($user, $this->behaviorData->getUser());
    }

    public function test_setUser_withNull_setsNull(): void
    {
        /** @var UserInterface&MockObject $user */
        $user = $this->createMock(UserInterface::class);
        $this->behaviorData->setUser($user);
        
        $result = $this->behaviorData->setUser(null);
        
        $this->assertSame($this->behaviorData, $result);
        $this->assertNull($this->behaviorData->getUser());
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
        /** @var UserInterface&MockObject $user */
        $user = $this->createMock(UserInterface::class);
        
        $date = new \DateTime('2024-01-15 00:00:00');
        $createTime = new \DateTime('2024-01-01 08:00:00');
        $updateTime = new \DateTime('2024-01-30 18:00:00');
        
        $result = $this->behaviorData
            ->setUser($user)
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
        $this->assertSame($user, $this->behaviorData->getUser());
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
    public function test_businessScenario_salesPersonDailyStats(): void
    {
        /** @var UserInterface&MockObject $salesPerson */
        $salesPerson = $this->createMock(UserInterface::class);
        
        $date = new \DateTime('2024-01-15 00:00:00');
        $createTime = new \DateTime('2024-01-16 08:00:00');
        
        // 模拟销售人员的日统计数据
        $this->behaviorData
            ->setUser($salesPerson)
            ->setDate($date)
            ->setNewApplyCount(8)       // 发起8个好友申请
            ->setNewContactCount(6)     // 新增6个联系人
            ->setChatCount(25)          // 进行25次聊天
            ->setMessageCount(120)      // 发送120条消息
            ->setAvgReplyTime(15)       // 平均回复时间15分钟
            ->setNegativeFeedbackCount(0) // 无负面反馈
            ->setReplyPercentage(95.0); // 回复率95%
        
        $this->behaviorData->setCreateTime($createTime);
        
        // 验证业务状态
        $this->assertNotNull($this->behaviorData->getUser());
        $this->assertSame($date, $this->behaviorData->getDate());
        $this->assertSame(8, $this->behaviorData->getNewApplyCount());
        $this->assertSame(6, $this->behaviorData->getNewContactCount());
        $this->assertSame(25, $this->behaviorData->getChatCount());
        $this->assertSame(120, $this->behaviorData->getMessageCount());
        $this->assertSame(15, $this->behaviorData->getAvgReplyTime());
        $this->assertSame(0, $this->behaviorData->getNegativeFeedbackCount());
        $this->assertSame(95.0, $this->behaviorData->getReplyPercentage());
        
        // 验证时间逻辑
        $this->assertTrue($date < $createTime);
    }

    public function test_businessScenario_customerServiceWeeklyStats(): void
    {
        /** @var UserInterface&MockObject $serviceUser */
        $serviceUser = $this->createMock(UserInterface::class);
        
        $date = new \DateTime('2024-01-08 00:00:00'); // 周一
        
        // 模拟客服人员的周统计数据
        $this->behaviorData
            ->setUser($serviceUser)
            ->setDate($date)
            ->setNewApplyCount(50)      // 一周发起50个申请
            ->setNewContactCount(45)    // 一周新增45个联系人
            ->setChatCount(200)         // 一周进行200次聊天
            ->setMessageCount(1500)     // 一周发送1500条消息
            ->setAvgReplyTime(5)        // 平均回复时间5分钟
            ->setNegativeFeedbackCount(2) // 2次负面反馈
            ->setReplyPercentage(98.5); // 回复率98.5%
        
        // 验证周统计数据的合理性
        $this->assertTrue($this->behaviorData->getNewApplyCount() > 0);
        $this->assertTrue($this->behaviorData->getNewContactCount() <= $this->behaviorData->getNewApplyCount());
        $this->assertTrue($this->behaviorData->getMessageCount() > $this->behaviorData->getChatCount());
        $this->assertTrue($this->behaviorData->getReplyPercentage() >= 0 && $this->behaviorData->getReplyPercentage() <= 100);
        $this->assertTrue($this->behaviorData->getAvgReplyTime() <= 10); // 快速响应
    }

    public function test_businessScenario_topPerformerUser(): void
    {
        /** @var UserInterface&MockObject $topUser */
        $topUser = $this->createMock(UserInterface::class);
        
        // 模拟顶级表现用户
        $this->behaviorData
            ->setUser($topUser)
            ->setDate(new \DateTime('2024-01-15'))
            ->setNewApplyCount(30)
            ->setNewContactCount(28)    // 高成功率
            ->setChatCount(150)
            ->setMessageCount(800)
            ->setAvgReplyTime(3)        // 3分钟极快回复
            ->setNegativeFeedbackCount(0)   // 无负面反馈
            ->setReplyPercentage(100.0);  // 100%回复率
        
        // 验证顶级表现
        $this->assertTrue($this->behaviorData->getNewContactCount() / $this->behaviorData->getNewApplyCount() > 0.9); // 90%以上申请成功率
        $this->assertSame(0, $this->behaviorData->getNegativeFeedbackCount());
        $this->assertSame(100.0, $this->behaviorData->getReplyPercentage());
        $this->assertTrue($this->behaviorData->getAvgReplyTime() <= 5); // 超快回复
    }

    public function test_businessScenario_strugglingUser(): void
    {
        /** @var UserInterface&MockObject $strugglingUser */
        $strugglingUser = $this->createMock(UserInterface::class);
        
        // 模拟表现不佳的用户
        $this->behaviorData
            ->setUser($strugglingUser)
            ->setDate(new \DateTime('2024-01-15'))
            ->setNewApplyCount(20)      // 发起申请较多
            ->setNewContactCount(5)     // 但成功率很低
            ->setChatCount(10)          // 聊天很少
            ->setMessageCount(30)       // 消息很少
            ->setAvgReplyTime(180)      // 3小时回复时间过长
            ->setNegativeFeedbackCount(8)  // 负面反馈较多
            ->setReplyPercentage(35.0); // 回复率很低
        
        // 验证问题指标
        $this->assertTrue($this->behaviorData->getNewContactCount() / $this->behaviorData->getNewApplyCount() < 0.3); // 申请成功率低于30%
        $this->assertTrue($this->behaviorData->getAvgReplyTime() > 60); // 回复时间超过1小时
        $this->assertTrue($this->behaviorData->getNegativeFeedbackCount() > 5);
        $this->assertTrue($this->behaviorData->getReplyPercentage() < 50);
    }

    public function test_businessScenario_monthlyUserAnalysis(): void
    {
        /** @var UserInterface&MockObject $user */
        $user = $this->createMock(UserInterface::class);
        
        $createTime = new \DateTime('2024-02-01 08:00:00');
        $updateTime = new \DateTime('2024-02-01 18:00:00');
        
        // 模拟月度用户分析数据
        $this->behaviorData
            ->setUser($user)
            ->setDate(new \DateTime('2024-01-01')) // 1月份数据
            ->setNewApplyCount(120)
            ->setNewContactCount(100)
            ->setChatCount(500)
            ->setMessageCount(2500)
            ->setAvgReplyTime(20)   // 20分钟
            ->setNegativeFeedbackCount(5)
            ->setReplyPercentage(88.5);
        
        $this->behaviorData->setCreateTime($createTime);
        $this->behaviorData->setUpdateTime($updateTime);
        
        // 验证月度数据的合理性
        $this->assertTrue($this->behaviorData->getNewApplyCount() >= 0);
        $this->assertTrue($this->behaviorData->getNewContactCount() <= $this->behaviorData->getNewApplyCount());
        $this->assertTrue($this->behaviorData->getMessageCount() >= 0);
        $this->assertTrue($this->behaviorData->getReplyPercentage() >= 0);
        $this->assertTrue($this->behaviorData->getReplyPercentage() <= 100);
        $this->assertTrue($updateTime > $createTime);
    }

    /**
     * 测试用户类型业务场景
     */
    public function test_businessScenario_newEmployeeUser(): void
    {
        /** @var UserInterface&MockObject $newEmployee */
        $newEmployee = $this->createMock(UserInterface::class);
        
        // 模拟新员工的数据
        $this->behaviorData
            ->setUser($newEmployee)
            ->setDate(new \DateTime('2024-01-15'))
            ->setNewApplyCount(5)       // 谨慎发起申请
            ->setNewContactCount(4)     // 成功率较高
            ->setChatCount(15)          // 聊天次数适中
            ->setMessageCount(60)       // 消息数量适中
            ->setAvgReplyTime(45)       // 45分钟，有点慢但可接受
            ->setNegativeFeedbackCount(0) // 无负面反馈
            ->setReplyPercentage(80.0); // 80%回复率，还在学习中
        
        // 验证新员工特征
        $this->assertTrue($this->behaviorData->getNewApplyCount() <= 10); // 比较保守
        $this->assertTrue($this->behaviorData->getNewContactCount() / $this->behaviorData->getNewApplyCount() >= 0.7); // 成功率较高
        $this->assertSame(0, $this->behaviorData->getNegativeFeedbackCount()); // 无负面反馈
        $this->assertTrue($this->behaviorData->getReplyPercentage() >= 70); // 较好的回复率
    }

    public function test_businessScenario_veteranEmployeeUser(): void
    {
        /** @var UserInterface&MockObject $veteran */
        $veteran = $this->createMock(UserInterface::class);
        
        // 模拟资深员工的数据
        $this->behaviorData
            ->setUser($veteran)
            ->setDate(new \DateTime('2024-01-15'))
            ->setNewApplyCount(40)      // 积极发起申请
            ->setNewContactCount(35)    // 高成功率
            ->setChatCount(200)         // 大量聊天
            ->setMessageCount(1000)     // 大量消息
            ->setAvgReplyTime(8)        // 8分钟快速回复
            ->setNegativeFeedbackCount(1) // 极少负面反馈
            ->setReplyPercentage(95.0); // 95%高回复率
        
        // 验证资深员工特征
        $this->assertTrue($this->behaviorData->getNewApplyCount() >= 30); // 积极主动
        $this->assertTrue($this->behaviorData->getNewContactCount() / $this->behaviorData->getNewApplyCount() >= 0.8); // 高成功率
        $this->assertTrue($this->behaviorData->getAvgReplyTime() <= 10); // 快速回复
        $this->assertTrue($this->behaviorData->getReplyPercentage() >= 90); // 高回复率
        $this->assertTrue($this->behaviorData->getNegativeFeedbackCount() <= 2); // 很少负面反馈
    }

    /**
     * 测试null值处理
     */
    public function test_nullValueHandling_allNullValues(): void
    {
        $this->behaviorData
            ->setUser(null)
            ->setNewApplyCount(null)
            ->setNewContactCount(null)
            ->setChatCount(null)
            ->setMessageCount(null)
            ->setAvgReplyTime(null)
            ->setNegativeFeedbackCount(null)
            ->setReplyPercentage(null);
        
        $this->behaviorData->setCreateTime(null);
        $this->behaviorData->setUpdateTime(null);
        
        $this->assertNull($this->behaviorData->getUser());
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
        /** @var \DateTimeInterface $nullValue */
        $nullValue = null;
        $this->behaviorData->setDate($nullValue);
    }
} 
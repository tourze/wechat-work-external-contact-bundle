<?php

namespace WechatWorkExternalContactBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatWorkExternalContactBundle\Entity\BehaviorDataTrait;

/**
 * BehaviorDataTrait 测试用例
 *
 * 测试行为数据特性的所有数据访问器方法
 */
class BehaviorDataTraitTest extends TestCase
{
    private object $traitObject;

    protected function setUp(): void
    {
        // 创建使用trait的匿名类实例
        $this->traitObject = new class {
            use BehaviorDataTrait;
        };
    }

    public function test_setDate_withValidDate_setsDateCorrectly(): void
    {
        $date = new \DateTime('2024-01-15');

        $result = $this->traitObject->setDate($date);

        $this->assertSame($this->traitObject, $result);
        $this->assertSame($date, $this->traitObject->getDate());
    }

    public function test_getDate_initiallyReturnsNull(): void
    {
        $this->assertNull($this->traitObject->getDate());
    }

    public function test_setNewApplyCount_withValidInteger_setsCountCorrectly(): void
    {
        $count = 42;
        
        $result = $this->traitObject->setNewApplyCount($count);
        
        $this->assertSame($this->traitObject, $result);
        $this->assertSame($count, $this->traitObject->getNewApplyCount());
    }

    public function test_setNewApplyCount_withNull_setsNull(): void
    {
        $this->traitObject->setNewApplyCount(100);
        
        $result = $this->traitObject->setNewApplyCount(null);
        
        $this->assertSame($this->traitObject, $result);
        $this->assertNull($this->traitObject->getNewApplyCount());
    }

    public function test_setNewApplyCount_withZero_setsZero(): void
    {
        $result = $this->traitObject->setNewApplyCount(0);
        
        $this->assertSame($this->traitObject, $result);
        $this->assertSame(0, $this->traitObject->getNewApplyCount());
    }

    public function test_setNewApplyCount_withNegativeValue_setsNegativeValue(): void
    {
        $result = $this->traitObject->setNewApplyCount(-5);
        
        $this->assertSame($this->traitObject, $result);
        $this->assertSame(-5, $this->traitObject->getNewApplyCount());
    }

    public function test_getNewApplyCount_initiallyReturnsNull(): void
    {
        $this->assertNull($this->traitObject->getNewApplyCount());
    }

    public function test_setNewContactCount_withValidInteger_setsCountCorrectly(): void
    {
        $count = 25;
        
        $result = $this->traitObject->setNewContactCount($count);
        
        $this->assertSame($this->traitObject, $result);
        $this->assertSame($count, $this->traitObject->getNewContactCount());
    }

    public function test_setNewContactCount_withNull_setsNull(): void
    {
        $this->traitObject->setNewContactCount(50);
        
        $result = $this->traitObject->setNewContactCount(null);
        
        $this->assertSame($this->traitObject, $result);
        $this->assertNull($this->traitObject->getNewContactCount());
    }

    public function test_getNewContactCount_initiallyReturnsNull(): void
    {
        $this->assertNull($this->traitObject->getNewContactCount());
    }

    public function test_setChatCount_withValidInteger_setsCountCorrectly(): void
    {
        $count = 150;
        
        $result = $this->traitObject->setChatCount($count);
        
        $this->assertSame($this->traitObject, $result);
        $this->assertSame($count, $this->traitObject->getChatCount());
    }

    public function test_setChatCount_withNull_setsNull(): void
    {
        $this->traitObject->setChatCount(75);
        
        $result = $this->traitObject->setChatCount(null);
        
        $this->assertSame($this->traitObject, $result);
        $this->assertNull($this->traitObject->getChatCount());
    }

    public function test_getChatCount_initiallyReturnsNull(): void
    {
        $this->assertNull($this->traitObject->getChatCount());
    }

    public function test_setMessageCount_withValidInteger_setsCountCorrectly(): void
    {
        $count = 500;
        
        $result = $this->traitObject->setMessageCount($count);
        
        $this->assertSame($this->traitObject, $result);
        $this->assertSame($count, $this->traitObject->getMessageCount());
    }

    public function test_setMessageCount_withNull_setsNull(): void
    {
        $this->traitObject->setMessageCount(300);
        
        $result = $this->traitObject->setMessageCount(null);
        
        $this->assertSame($this->traitObject, $result);
        $this->assertNull($this->traitObject->getMessageCount());
    }

    public function test_getMessageCount_initiallyReturnsNull(): void
    {
        $this->assertNull($this->traitObject->getMessageCount());
    }

    public function test_setReplyPercentage_withValidFloat_setsPercentageCorrectly(): void
    {
        $percentage = 85.5;
        
        $result = $this->traitObject->setReplyPercentage($percentage);
        
        $this->assertSame($this->traitObject, $result);
        $this->assertSame($percentage, $this->traitObject->getReplyPercentage());
    }

    public function test_setReplyPercentage_withNull_setsNull(): void
    {
        $this->traitObject->setReplyPercentage(90.0);
        
        $result = $this->traitObject->setReplyPercentage(null);
        
        $this->assertSame($this->traitObject, $result);
        $this->assertNull($this->traitObject->getReplyPercentage());
    }

    public function test_setReplyPercentage_withZero_setsZero(): void
    {
        $result = $this->traitObject->setReplyPercentage(0.0);
        
        $this->assertSame($this->traitObject, $result);
        $this->assertSame(0.0, $this->traitObject->getReplyPercentage());
    }

    public function test_setReplyPercentage_withMaxPercentage_setsMaxPercentage(): void
    {
        $result = $this->traitObject->setReplyPercentage(100.0);
        
        $this->assertSame($this->traitObject, $result);
        $this->assertSame(100.0, $this->traitObject->getReplyPercentage());
    }

    public function test_getReplyPercentage_initiallyReturnsNull(): void
    {
        $this->assertNull($this->traitObject->getReplyPercentage());
    }

    public function test_setAvgReplyTime_withValidInteger_setsTimeCorrectly(): void
    {
        $time = 45; // 45分钟
        
        $result = $this->traitObject->setAvgReplyTime($time);
        
        $this->assertSame($this->traitObject, $result);
        $this->assertSame($time, $this->traitObject->getAvgReplyTime());
    }

    public function test_setAvgReplyTime_withNull_setsNull(): void
    {
        $this->traitObject->setAvgReplyTime(30);
        
        $result = $this->traitObject->setAvgReplyTime(null);
        
        $this->assertSame($this->traitObject, $result);
        $this->assertNull($this->traitObject->getAvgReplyTime());
    }

    public function test_setAvgReplyTime_withZero_setsZero(): void
    {
        $result = $this->traitObject->setAvgReplyTime(0);
        
        $this->assertSame($this->traitObject, $result);
        $this->assertSame(0, $this->traitObject->getAvgReplyTime());
    }

    public function test_getAvgReplyTime_initiallyReturnsNull(): void
    {
        $this->assertNull($this->traitObject->getAvgReplyTime());
    }

    public function test_setNegativeFeedbackCount_withValidInteger_setsCountCorrectly(): void
    {
        $count = 3;
        
        $result = $this->traitObject->setNegativeFeedbackCount($count);
        
        $this->assertSame($this->traitObject, $result);
        $this->assertSame($count, $this->traitObject->getNegativeFeedbackCount());
    }

    public function test_setNegativeFeedbackCount_withNull_setsNull(): void
    {
        $this->traitObject->setNegativeFeedbackCount(5);
        
        $result = $this->traitObject->setNegativeFeedbackCount(null);
        
        $this->assertSame($this->traitObject, $result);
        $this->assertNull($this->traitObject->getNegativeFeedbackCount());
    }

    public function test_setNegativeFeedbackCount_withZero_setsZero(): void
    {
        $result = $this->traitObject->setNegativeFeedbackCount(0);
        
        $this->assertSame($this->traitObject, $result);
        $this->assertSame(0, $this->traitObject->getNegativeFeedbackCount());
    }

    public function test_getNegativeFeedbackCount_initiallyReturnsNull(): void
    {
        $this->assertNull($this->traitObject->getNegativeFeedbackCount());
    }

    /**
     * 测试所有字段的链式调用功能
     */
    public function test_chainedSetters_returnSameInstance(): void
    {
        $date = new \DateTime('2024-01-01');
        
        $result = $this->traitObject
            ->setDate($date)
            ->setNewApplyCount(10)
            ->setNewContactCount(5)
            ->setChatCount(20)
            ->setMessageCount(100)
            ->setReplyPercentage(80.5)
            ->setAvgReplyTime(30)
            ->setNegativeFeedbackCount(2);
        
        $this->assertSame($this->traitObject, $result);
        $this->assertSame($date, $this->traitObject->getDate());
        $this->assertSame(10, $this->traitObject->getNewApplyCount());
        $this->assertSame(5, $this->traitObject->getNewContactCount());
        $this->assertSame(20, $this->traitObject->getChatCount());
        $this->assertSame(100, $this->traitObject->getMessageCount());
        $this->assertSame(80.5, $this->traitObject->getReplyPercentage());
        $this->assertSame(30, $this->traitObject->getAvgReplyTime());
        $this->assertSame(2, $this->traitObject->getNegativeFeedbackCount());
    }

    /**
     * 测试边界值情况
     */
    public function test_boundaryValues_areHandledCorrectly(): void
    {
        // 测试最大整数值
        $maxInt = PHP_INT_MAX;
        $this->traitObject->setNewApplyCount($maxInt);
        $this->assertSame($maxInt, $this->traitObject->getNewApplyCount());
        
        // 测试最小整数值
        $minInt = PHP_INT_MIN;
        $this->traitObject->setNewContactCount($minInt);
        $this->assertSame($minInt, $this->traitObject->getNewContactCount());
        
        // 测试极小浮点数
        $smallFloat = 0.0001;
        $this->traitObject->setReplyPercentage($smallFloat);
        $this->assertSame($smallFloat, $this->traitObject->getReplyPercentage());
        
        // 测试极大浮点数
        $largeFloat = 999999.9999;
        $this->traitObject->setReplyPercentage($largeFloat);
        $this->assertSame($largeFloat, $this->traitObject->getReplyPercentage());
    }

    /**
     * 测试DateTimeInterface的各种实现
     */
    public function test_setDate_withDifferentDateTimeImplementations(): void
    {
        // 测试DateTime
        $dateTime = new \DateTime('2024-01-15 12:30:45');
        $this->traitObject->setDate($dateTime);
        $this->assertSame($dateTime, $this->traitObject->getDate());
        
        // 测试DateTimeImmutable
        $dateTimeImmutable = new \DateTimeImmutable('2024-02-20 09:15:30');
        $this->traitObject->setDate($dateTimeImmutable);
        $this->assertSame($dateTimeImmutable, $this->traitObject->getDate());
    }
}

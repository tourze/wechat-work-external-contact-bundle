<?php

namespace WechatWorkExternalContactBundle\Tests\Entity;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tourze\WechatWorkContracts\CorpInterface;
use Tourze\WechatWorkContracts\UserInterface;
use WechatWorkExternalContactBundle\Entity\ExternalServiceRelation;
use WechatWorkExternalContactBundle\Entity\ExternalUser;

/**
 * ExternalServiceRelation 实体测试用例
 *
 * 测试外部联系人服务关系实体的所有功能
 */
class ExternalServiceRelationTest extends TestCase
{
    private ExternalServiceRelation $relation;

    protected function setUp(): void
    {
        $this->relation = new ExternalServiceRelation();
    }

    public function test_constructor_setsDefaultValues(): void
    {
        $relation = new ExternalServiceRelation();
        
        $this->assertSame(0, $relation->getId());
        $this->assertNull($relation->getCorp());
        $this->assertNull($relation->getUser());
        $this->assertNull($relation->getExternalUser());
        $this->assertNull($relation->getAddExternalContactTime());
        $this->assertNull($relation->getAddHalfExternalContactTime());
        $this->assertNull($relation->getDelExternalContactTime());
        $this->assertNull($relation->getDelFollowUserTime());
        $this->assertNull($relation->getCreateTime());
        $this->assertNull($relation->getUpdateTime());
    }

    public function test_setCorp_withValidCorp_setsCorpCorrectly(): void
    {
        /** @var CorpInterface&MockObject $corp */
        $corp = $this->createMock(CorpInterface::class);
        
        $result = $this->relation->setCorp($corp);
        
        $this->assertSame($this->relation, $result);
        $this->assertSame($corp, $this->relation->getCorp());
    }

    public function test_setCorp_withNull_setsNull(): void
    {
        /** @var CorpInterface&MockObject $corp */
        $corp = $this->createMock(CorpInterface::class);
        $this->relation->setCorp($corp);
        
        $result = $this->relation->setCorp(null);
        
        $this->assertSame($this->relation, $result);
        $this->assertNull($this->relation->getCorp());
    }

    public function test_setUser_withValidUser_setsUserCorrectly(): void
    {
        /** @var UserInterface&MockObject $user */
        $user = $this->createMock(UserInterface::class);
        
        $result = $this->relation->setUser($user);
        
        $this->assertSame($this->relation, $result);
        $this->assertSame($user, $this->relation->getUser());
    }

    public function test_setUser_withNull_setsNull(): void
    {
        /** @var UserInterface&MockObject $user */
        $user = $this->createMock(UserInterface::class);
        $this->relation->setUser($user);
        
        $result = $this->relation->setUser(null);
        
        $this->assertSame($this->relation, $result);
        $this->assertNull($this->relation->getUser());
    }

    public function test_setExternalUser_withValidExternalUser_setsExternalUserCorrectly(): void
    {
        /** @var ExternalUser&MockObject $externalUser */
        $externalUser = $this->createMock(ExternalUser::class);
        
        $result = $this->relation->setExternalUser($externalUser);
        
        $this->assertSame($this->relation, $result);
        $this->assertSame($externalUser, $this->relation->getExternalUser());
    }

    public function test_setExternalUser_withNull_setsNull(): void
    {
        /** @var ExternalUser&MockObject $externalUser */
        $externalUser = $this->createMock(ExternalUser::class);
        $this->relation->setExternalUser($externalUser);
        
        $result = $this->relation->setExternalUser(null);
        
        $this->assertSame($this->relation, $result);
        $this->assertNull($this->relation->getExternalUser());
    }

    public function test_setAddExternalContactTime_withValidDateTime_setsTimeCorrectly(): void
    {
        $addTime = new \DateTimeImmutable('2024-01-15 10:30:00');
        
        $result = $this->relation->setAddExternalContactTime($addTime);
        
        $this->assertSame($this->relation, $result);
        $this->assertSame($addTime, $this->relation->getAddExternalContactTime());
    }

    public function test_setAddExternalContactTime_withNull_setsNull(): void
    {
        $this->relation->setAddExternalContactTime(new \DateTimeImmutable());
        
        $result = $this->relation->setAddExternalContactTime(null);
        
        $this->assertSame($this->relation, $result);
        $this->assertNull($this->relation->getAddExternalContactTime());
    }

    public function test_setAddHalfExternalContactTime_withValidDateTime_setsTimeCorrectly(): void
    {
        $addHalfTime = new \DateTimeImmutable('2024-01-16 14:20:00');
        
        $result = $this->relation->setAddHalfExternalContactTime($addHalfTime);
        
        $this->assertSame($this->relation, $result);
        $this->assertSame($addHalfTime, $this->relation->getAddHalfExternalContactTime());
    }

    public function test_setAddHalfExternalContactTime_withNull_setsNull(): void
    {
        $this->relation->setAddHalfExternalContactTime(new \DateTimeImmutable());
        
        $result = $this->relation->setAddHalfExternalContactTime(null);
        
        $this->assertSame($this->relation, $result);
        $this->assertNull($this->relation->getAddHalfExternalContactTime());
    }

    public function test_setDelExternalContactTime_withValidDateTime_setsTimeCorrectly(): void
    {
        $delTime = new \DateTimeImmutable('2024-01-20 09:15:00');
        
        $result = $this->relation->setDelExternalContactTime($delTime);
        
        $this->assertSame($this->relation, $result);
        $this->assertSame($delTime, $this->relation->getDelExternalContactTime());
    }

    public function test_setDelExternalContactTime_withNull_setsNull(): void
    {
        $this->relation->setDelExternalContactTime(new \DateTimeImmutable());
        
        $result = $this->relation->setDelExternalContactTime(null);
        
        $this->assertSame($this->relation, $result);
        $this->assertNull($this->relation->getDelExternalContactTime());
    }

    public function test_setDelFollowUserTime_withValidDateTime_setsTimeCorrectly(): void
    {
        $delFollowTime = new \DateTimeImmutable('2024-01-25 16:45:00');
        
        $result = $this->relation->setDelFollowUserTime($delFollowTime);
        
        $this->assertSame($this->relation, $result);
        $this->assertSame($delFollowTime, $this->relation->getDelFollowUserTime());
    }

    public function test_setDelFollowUserTime_withNull_setsNull(): void
    {
        $this->relation->setDelFollowUserTime(new \DateTimeImmutable());
        
        $result = $this->relation->setDelFollowUserTime(null);
        
        $this->assertSame($this->relation, $result);
        $this->assertNull($this->relation->getDelFollowUserTime());
    }

    public function test_setCreateTime_withValidDateTime_setsTimeCorrectly(): void
    {
        $createTime = new \DateTimeImmutable('2024-01-01 08:00:00');
        
        $this->relation->setCreateTime($createTime);
        
        $this->assertSame($createTime, $this->relation->getCreateTime());
    }

    public function test_setCreateTime_withNull_setsNull(): void
    {
        $this->relation->setCreateTime(new \DateTimeImmutable());
        
        $this->relation->setCreateTime(null);
        
        $this->assertNull($this->relation->getCreateTime());
    }

    public function test_setUpdateTime_withValidDateTime_setsTimeCorrectly(): void
    {
        $updateTime = new \DateTimeImmutable('2024-01-30 18:30:00');
        
        $this->relation->setUpdateTime($updateTime);
        
        $this->assertSame($updateTime, $this->relation->getUpdateTime());
    }

    public function test_setUpdateTime_withNull_setsNull(): void
    {
        $this->relation->setUpdateTime(new \DateTimeImmutable());
        
        $this->relation->setUpdateTime(null);
        
        $this->assertNull($this->relation->getUpdateTime());
    }

    /**
     * 测试链式调用
     */
    public function test_chainedSetters_returnSameInstance(): void
    {
        /** @var CorpInterface&MockObject $corp */
        $corp = $this->createMock(CorpInterface::class);
        /** @var UserInterface&MockObject $user */
        $user = $this->createMock(UserInterface::class);
        /** @var ExternalUser&MockObject $externalUser */
        $externalUser = $this->createMock(ExternalUser::class);
        
        $addTime = new \DateTimeImmutable('2024-01-15 10:00:00');
        $addHalfTime = new \DateTimeImmutable('2024-01-16 11:00:00');
        $delTime = new \DateTimeImmutable('2024-01-20 12:00:00');
        $delFollowTime = new \DateTimeImmutable('2024-01-25 13:00:00');
        $createTime = new \DateTimeImmutable('2024-01-01 08:00:00');
        $updateTime = new \DateTimeImmutable('2024-01-30 18:00:00');
        
        $result = $this->relation
            ->setCorp($corp)
            ->setUser($user)
            ->setExternalUser($externalUser)
            ->setAddExternalContactTime($addTime)
            ->setAddHalfExternalContactTime($addHalfTime)
            ->setDelExternalContactTime($delTime)
            ->setDelFollowUserTime($delFollowTime);
        
        $this->relation->setCreateTime($createTime);
        $this->relation->setUpdateTime($updateTime);
        
        $this->assertSame($this->relation, $result);
        $this->assertSame($corp, $this->relation->getCorp());
        $this->assertSame($user, $this->relation->getUser());
        $this->assertSame($externalUser, $this->relation->getExternalUser());
        $this->assertSame($addTime, $this->relation->getAddExternalContactTime());
        $this->assertSame($addHalfTime, $this->relation->getAddHalfExternalContactTime());
        $this->assertSame($delTime, $this->relation->getDelExternalContactTime());
        $this->assertSame($delFollowTime, $this->relation->getDelFollowUserTime());
        $this->assertSame($createTime, $this->relation->getCreateTime());
        $this->assertSame($updateTime, $this->relation->getUpdateTime());
    }

    /**
     * 测试时间序列场景
     */
    public function test_timeSequence_addFollowDelete(): void
    {
        $addTime = new \DateTimeImmutable('2024-01-15 10:00:00');
        $addHalfTime = new \DateTimeImmutable('2024-01-15 10:05:00'); // 5分钟后对方主动添加
        $delTime = new \DateTimeImmutable('2024-01-20 15:30:00'); // 5天后删除
        
        $this->relation
            ->setAddExternalContactTime($addTime)
            ->setAddHalfExternalContactTime($addHalfTime)
            ->setDelExternalContactTime($delTime);
        
        // 验证时间序列
        $this->assertTrue($addTime < $addHalfTime);
        $this->assertTrue($addHalfTime < $delTime);
        
        $this->assertSame($addTime, $this->relation->getAddExternalContactTime());
        $this->assertSame($addHalfTime, $this->relation->getAddHalfExternalContactTime());
        $this->assertSame($delTime, $this->relation->getDelExternalContactTime());
        $this->assertNull($this->relation->getDelFollowUserTime());
    }

    public function test_timeSequence_mutualDeletion(): void
    {
        $addTime = new \DateTimeImmutable('2024-01-15 10:00:00');
        $delTime = new \DateTimeImmutable('2024-01-20 15:30:00'); // 成员删除外部联系人
        $delFollowTime = new \DateTimeImmutable('2024-01-25 14:20:00'); // 外部联系人删除成员
        
        $this->relation
            ->setAddExternalContactTime($addTime)
            ->setDelExternalContactTime($delTime)
            ->setDelFollowUserTime($delFollowTime);
        
        // 验证删除时间序列
        $this->assertTrue($addTime < $delTime);
        $this->assertTrue($delTime < $delFollowTime);
        
        $this->assertSame($addTime, $this->relation->getAddExternalContactTime());
        $this->assertSame($delTime, $this->relation->getDelExternalContactTime());
        $this->assertSame($delFollowTime, $this->relation->getDelFollowUserTime());
        $this->assertNull($this->relation->getAddHalfExternalContactTime());
    }

    /**
     * 测试边界场景
     */
    public function test_edgeCases_dateTimeTypes(): void
    {
        // 测试DateTime
        $dateTime = new \DateTimeImmutable('2024-01-15 12:30:45');
        $this->relation->setAddExternalContactTime($dateTime);
        $this->assertSame($dateTime, $this->relation->getAddExternalContactTime());
        
        // 测试DateTimeImmutable
        $dateTimeImmutable = new \DateTimeImmutable('2024-02-20 09:15:30');
        $this->relation->setAddHalfExternalContactTime($dateTimeImmutable);
        $this->assertSame($dateTimeImmutable, $this->relation->getAddHalfExternalContactTime());
        
        // 测试不同时区的DateTime
        $dateTimeUtc = new \DateTimeImmutable('2024-03-15 14:30:00', new \DateTimeZone('UTC'));
        $this->relation->setDelExternalContactTime($dateTimeUtc);
        $this->assertSame($dateTimeUtc, $this->relation->getDelExternalContactTime());
        $this->assertEquals('UTC', $dateTimeUtc->getTimezone()->getName());
    }

    public function test_edgeCases_extremeDateTimes(): void
    {
        // 测试极端早期时间
        $earlyDate = new \DateTimeImmutable('1970-01-01 00:00:01');
        $this->relation->setAddExternalContactTime($earlyDate);
        $this->assertSame($earlyDate, $this->relation->getAddExternalContactTime());
        
        // 测试未来时间
        $futureDate = new \DateTimeImmutable('2099-12-31 23:59:59');
        $this->relation->setDelFollowUserTime($futureDate);
        $this->assertSame($futureDate, $this->relation->getDelFollowUserTime());
    }

    /**
     * 测试关联关系的null处理
     */
    public function test_relationshipNullHandling_allNullValues(): void
    {
        $this->relation
            ->setCorp(null)
            ->setUser(null)
            ->setExternalUser(null)
            ->setAddExternalContactTime(null)
            ->setAddHalfExternalContactTime(null)
            ->setDelExternalContactTime(null)
            ->setDelFollowUserTime(null);
        
        $this->relation->setCreateTime(null);
        $this->relation->setUpdateTime(null);
        
        $this->assertNull($this->relation->getCorp());
        $this->assertNull($this->relation->getUser());
        $this->assertNull($this->relation->getExternalUser());
        $this->assertNull($this->relation->getAddExternalContactTime());
        $this->assertNull($this->relation->getAddHalfExternalContactTime());
        $this->assertNull($this->relation->getDelExternalContactTime());
        $this->assertNull($this->relation->getDelFollowUserTime());
        $this->assertNull($this->relation->getCreateTime());
        $this->assertNull($this->relation->getUpdateTime());
    }

    /**
     * 测试业务逻辑场景
     */
    public function test_businessScenario_normalContactFlow(): void
    {
        /** @var CorpInterface&MockObject $corp */
        $corp = $this->createMock(CorpInterface::class);
        /** @var UserInterface&MockObject $user */
        $user = $this->createMock(UserInterface::class);
        /** @var ExternalUser&MockObject $externalUser */
        $externalUser = $this->createMock(ExternalUser::class);
        
        $createTime = new \DateTimeImmutable('2024-01-01 00:00:00');
        $addTime = new \DateTimeImmutable('2024-01-15 10:30:00');
        $updateTime = new \DateTimeImmutable('2024-01-15 10:30:01');
        
        // 模拟正常的联系流程：创建关系 -> 添加联系人 -> 更新记录
        $this->relation->setCreateTime($createTime);
        
        $this->relation
            ->setCorp($corp)
            ->setUser($user)
            ->setExternalUser($externalUser)
            ->setAddExternalContactTime($addTime);
        
        $this->relation->setUpdateTime($updateTime);
        
        // 验证业务状态
        $this->assertNotNull($this->relation->getCorp());
        $this->assertNotNull($this->relation->getUser());
        $this->assertNotNull($this->relation->getExternalUser());
        $this->assertNotNull($this->relation->getAddExternalContactTime());
        $this->assertNull($this->relation->getDelExternalContactTime()); // 未删除
        $this->assertNull($this->relation->getDelFollowUserTime()); // 未被删除
        
        // 验证时间逻辑
        $this->assertTrue($createTime <= $addTime);
        $this->assertTrue($addTime <= $updateTime);
    }

    public function test_businessScenario_bidirectionalConnection(): void
    {
        $addTime = new \DateTimeImmutable('2024-01-15 10:00:00');
        $addHalfTime = new \DateTimeImmutable('2024-01-15 10:05:00');
        
        // 模拟双向添加：成员添加外部联系人 -> 外部联系人确认添加
        $this->relation
            ->setAddExternalContactTime($addTime)
            ->setAddHalfExternalContactTime($addHalfTime);
        
        // 验证双向添加完成
        $this->assertNotNull($this->relation->getAddExternalContactTime());
        $this->assertNotNull($this->relation->getAddHalfExternalContactTime());
        
        // 验证添加顺序合理（成员先添加，外部联系人后确认）
        $this->assertTrue($this->relation->getAddExternalContactTime() <= $this->relation->getAddHalfExternalContactTime());
    }

    public function test_businessScenario_oneSidedDeletion(): void
    {
        $addTime = new \DateTimeImmutable('2024-01-15 10:00:00');
        $delTime = new \DateTimeImmutable('2024-01-20 15:30:00');
        
        // 模拟单方面删除：只有成员删除外部联系人
        $this->relation
            ->setAddExternalContactTime($addTime)
            ->setDelExternalContactTime($delTime);
        
        // 验证单方面删除状态
        $this->assertNotNull($this->relation->getAddExternalContactTime());
        $this->assertNotNull($this->relation->getDelExternalContactTime());
        $this->assertNull($this->relation->getDelFollowUserTime()); // 外部联系人未删除成员
        
        // 验证时间逻辑
        $this->assertTrue($this->relation->getAddExternalContactTime() < $this->relation->getDelExternalContactTime());
    }
} 
<?php

namespace WechatWorkExternalContactBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\WechatWorkContracts\CorpInterface;
use Tourze\WechatWorkContracts\UserInterface;
use WechatWorkExternalContactBundle\Entity\ExternalServiceRelation;
use WechatWorkExternalContactBundle\Entity\ExternalUser;

/**
 * ExternalServiceRelation 实体测试用例
 * 测试外部联系人服务关系实体的所有功能
 *
 * @internal
 */
#[CoversClass(ExternalServiceRelation::class)]
final class ExternalServiceRelationTest extends AbstractEntityTestCase
{
    protected function createEntity(): ExternalServiceRelation
    {
        return new ExternalServiceRelation();
    }

    public function testConstructorSetsDefaultValues(): void
    {
        $relation = $this->createEntity();

        $this->assertSame(0, $relation->getId());
        $this->assertNull($relation->getCorp());
        $this->assertNull($relation->getUser());
        $this->assertNull($relation->getExternalUser());
        $this->assertNull($relation->getAddExternalContactTime());
        $this->assertNull($relation->getAddHalfExternalContactTime());
        $this->assertNull($relation->getDelExternalContactTime());
        $this->assertNull($relation->getDelFollowUserTime());
    }

    public function testSetCorpWithValidCorpSetsCorpCorrectly(): void
    {
        $relation = $this->createEntity();
        $corp = $this->createMock(CorpInterface::class);

        $relation->setCorp($corp);

        $this->assertSame($corp, $relation->getCorp());
    }

    public function testSetUserWithValidUserSetsUserCorrectly(): void
    {
        $relation = $this->createEntity();
        $user = $this->createMock(UserInterface::class);

        $relation->setUser($user);

        $this->assertSame($user, $relation->getUser());
    }

    public function testSetExternalUserWithValidUserSetsUserCorrectly(): void
    {
        $relation = $this->createEntity();
        $externalUser = $this->createMock(ExternalUser::class);

        $relation->setExternalUser($externalUser);

        $this->assertSame($externalUser, $relation->getExternalUser());
    }

    public function testSetAddExternalContactTimeWithValidTimeSetsTimeCorrectly(): void
    {
        $relation = $this->createEntity();
        $time = new \DateTimeImmutable('2024-01-01 10:00:00');

        $relation->setAddExternalContactTime($time);

        $this->assertSame($time, $relation->getAddExternalContactTime());
    }

    public function testSetAddHalfExternalContactTimeWithValidTimeSetsTimeCorrectly(): void
    {
        $relation = $this->createEntity();
        $time = new \DateTimeImmutable('2024-01-01 10:00:00');

        $relation->setAddHalfExternalContactTime($time);

        $this->assertSame($time, $relation->getAddHalfExternalContactTime());
    }

    public function testSetDelExternalContactTimeWithValidTimeSetsTimeCorrectly(): void
    {
        $relation = $this->createEntity();
        $time = new \DateTimeImmutable('2024-01-01 10:00:00');

        $relation->setDelExternalContactTime($time);

        $this->assertSame($time, $relation->getDelExternalContactTime());
    }

    public function testSetDelFollowUserTimeWithValidTimeSetsTimeCorrectly(): void
    {
        $relation = $this->createEntity();
        $time = new \DateTimeImmutable('2024-01-01 10:00:00');

        $relation->setDelFollowUserTime($time);

        $this->assertSame($time, $relation->getDelFollowUserTime());
    }

    public function testChainedSettersReturnSameInstance(): void
    {
        $relation = $this->createEntity();
        $corp = $this->createMock(CorpInterface::class);
        $user = $this->createMock(UserInterface::class);
        $externalUser = $this->createMock(ExternalUser::class);
        $time = new \DateTimeImmutable('2024-01-01 10:00:00');

        $relation->setCorp($corp);
        $relation->setUser($user);
        $relation->setExternalUser($externalUser);
        $relation->setAddExternalContactTime($time);
        $relation->setAddHalfExternalContactTime($time);
        $relation->setDelExternalContactTime($time);
        $relation->setDelFollowUserTime($time);
        $this->assertSame($corp, $relation->getCorp());
        $this->assertSame($user, $relation->getUser());
        $this->assertSame($externalUser, $relation->getExternalUser());
        $this->assertSame($time, $relation->getAddExternalContactTime());
        $this->assertSame($time, $relation->getAddHalfExternalContactTime());
        $this->assertSame($time, $relation->getDelExternalContactTime());
        $this->assertSame($time, $relation->getDelFollowUserTime());
    }

    public function testSetNullValues(): void
    {
        $relation = $this->createEntity();

        // 设置一些值
        $corp = $this->createMock(CorpInterface::class);
        $time = new \DateTimeImmutable('2024-01-01 10:00:00');
        $relation->setCorp($corp);
        $relation->setAddExternalContactTime($time);

        // 设置为null
        $relation->setCorp(null);
        $relation->setAddExternalContactTime(null);
        $this->assertNull($relation->getCorp());
        $this->assertNull($relation->getAddExternalContactTime());
    }

    /**
     * 提供实体属性用于测试 getter 和 setter 方法.
     *
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'addExternalContactTime' => ['addExternalContactTime', new \DateTimeImmutable()];
        yield 'addHalfExternalContactTime' => ['addHalfExternalContactTime', new \DateTimeImmutable()];
        yield 'delExternalContactTime' => ['delExternalContactTime', new \DateTimeImmutable()];
        yield 'delFollowUserTime' => ['delFollowUserTime', new \DateTimeImmutable()];
    }
}

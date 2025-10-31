<?php

namespace WechatWorkExternalContactBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatWorkExternalContactBundle\Entity\ExternalUser;

/**
 * ExternalUser 实体测试用例
 * 测试外部联系人实体的所有功能，包括接口实现
 *
 * @internal
 */
#[CoversClass(ExternalUser::class)]
final class ExternalUserTest extends AbstractEntityTestCase
{
    protected function createEntity(): ExternalUser
    {
        return new ExternalUser();
    }

    public function testConstructorSetsDefaultValues(): void
    {
        $user = $this->createEntity();

        $this->assertSame(0, $user->getId());
        $this->assertNull($user->getCorp());
        $this->assertNull($user->getNickname());
        $this->assertNull($user->getExternalUserId());
        $this->assertNull($user->getUnionId());
        $this->assertNull($user->getAvatar());
        $this->assertNull($user->getGender());
        $this->assertSame([], $user->getEnterSessionContext());
        $this->assertNull($user->getRemark());
        $this->assertSame([], $user->getTags());
        $this->assertNull($user->isCustomer());
        $this->assertNull($user->getTmpOpenId());
        $this->assertNull($user->getAddTime());
        $this->assertNull($user->getRawData());
        $this->assertNull($user->getCreateTime());
        $this->assertNull($user->getUpdateTime());
    }

    public function testSetNicknameWithValidStringSetsNicknameCorrectly(): void
    {
        $user = $this->createEntity();
        $nickname = '张三';

        $user->setNickname($nickname);
        $this->assertSame($nickname, $user->getNickname());
    }

    public function testSetNicknameWithEmptyStringSetsEmptyString(): void
    {
        $user = $this->createEntity();
        $user->setNickname('');
        $this->assertSame('', $user->getNickname());
    }

    public function testSetExternalUserIdWithValidIdSetsIdCorrectly(): void
    {
        $user = $this->createEntity();
        $externalUserId = 'ext_user_123456';

        $user->setExternalUserId($externalUserId);
        $this->assertSame($externalUserId, $user->getExternalUserId());
    }

    public function testSetUnionIdWithValidIdSetsIdCorrectly(): void
    {
        $user = $this->createEntity();
        $unionId = 'union_123456789';

        $user->setUnionId($unionId);
        $this->assertSame($unionId, $user->getUnionId());
    }

    public function testSetAvatarWithValidUrlSetsAvatarCorrectly(): void
    {
        $user = $this->createEntity();
        $avatarUrl = 'https://example.com/avatar.jpg';

        $user->setAvatar($avatarUrl);
        $this->assertSame($avatarUrl, $user->getAvatar());
    }

    public function testSetGenderWithValidGenderSetsGenderCorrectly(): void
    {
        $user = $this->createEntity();
        $gender = 1; // 男性

        $user->setGender($gender);
        $this->assertSame($gender, $user->getGender());
    }

    public function testSetCustomerWithTrueSetsTrue(): void
    {
        $user = $this->createEntity();
        $user->setCustomer(true);
        $this->assertTrue($user->isCustomer());
    }

    public function testToStringWithoutIdReturnsEmptyString(): void
    {
        $user = $this->createEntity();
        $result = (string) $user;

        $this->assertSame('', $result);
    }

    public function testRetrievePlainArrayReturnsCorrectStructure(): void
    {
        $user = $this->createEntity();
        $user->setExternalUserId('ext_789');

        $result = $user->retrievePlainArray();

        $expected = [
            'id' => 0,
            'externalUserId' => 'ext_789',
        ];

        $this->assertSame($expected, $result);
    }

    public function testRetrieveApiArrayReturnsCorrectStructure(): void
    {
        $user = $this->createEntity();
        $user->setNickname('李四');
        $user->setExternalUserId('ext_456');
        $user->setUnionId('union_789');
        $user->setAvatar('https://example.com/avatar2.jpg');
        $user->setGender(2);

        $result = $user->retrieveApiArray();

        $expected = [
            'id' => 0,
            'createTime' => null,
            'updateTime' => null,
            'nickname' => '李四',
            'externalUserId' => 'ext_456',
            'unionId' => 'union_789',
            'avatar' => 'https://example.com/avatar2.jpg',
            'gender' => 2,
        ];

        $this->assertSame($expected, $result);
    }

    public function testChainedSettersReturnSameInstance(): void
    {
        $user = $this->createEntity();
        $user->setNickname('测试用户');
        $user->setExternalUserId('ext_test');
        $user->setUnionId('union_test');
        $user->setAvatar('https://test.com/avatar.jpg');
        $user->setGender(1);
        $this->assertSame('测试用户', $user->getNickname());
        $this->assertSame('ext_test', $user->getExternalUserId());
        $this->assertSame('union_test', $user->getUnionId());
        $this->assertSame('https://test.com/avatar.jpg', $user->getAvatar());
        $this->assertSame(1, $user->getGender());
    }

    /**
     * 提供实体属性用于测试 getter 和 setter 方法.
     *
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'nickname' => ['nickname', '测试昵称'];
        yield 'externalUserId' => ['externalUserId', 'ext_user_123'];
        yield 'unionId' => ['unionId', 'union_123'];
        yield 'avatar' => ['avatar', 'https://example.com/avatar.jpg'];
        yield 'gender' => ['gender', 1];
        yield 'remark' => ['remark', '测试备注'];
        yield 'tmpOpenId' => ['tmpOpenId', 'tmp_open_123'];
        yield 'customer' => ['customer', true];
        yield 'addTime' => ['addTime', new \DateTimeImmutable()];
        yield 'rawData' => ['rawData', ['key' => 'value']];
    }
}

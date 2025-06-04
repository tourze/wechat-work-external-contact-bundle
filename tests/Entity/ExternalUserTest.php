<?php

namespace WechatWorkExternalContactBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\WechatWorkExternalContactModel\ExternalContactInterface;
use WechatWorkExternalContactBundle\Entity\ExternalUser;

/**
 * ExternalUser 实体测试用例
 * 
 * 测试外部联系人实体的所有功能，包括接口实现
 */
class ExternalUserTest extends TestCase
{
    private ExternalUser $externalUser;

    protected function setUp(): void
    {
        $this->externalUser = new ExternalUser();
    }

    public function test_constructor_setsDefaultValues(): void
    {
        $user = new ExternalUser();
        
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

    public function test_implementsCorrectInterfaces(): void
    {
        $this->assertInstanceOf(\Stringable::class, $this->externalUser);
        $this->assertInstanceOf(PlainArrayInterface::class, $this->externalUser);
        $this->assertInstanceOf(ApiArrayInterface::class, $this->externalUser);
        $this->assertInstanceOf(ExternalContactInterface::class, $this->externalUser);
    }

    public function test_setNickname_withValidString_setsNicknameCorrectly(): void
    {
        $nickname = '张三';
        
        $result = $this->externalUser->setNickname($nickname);
        
        $this->assertSame($this->externalUser, $result);
        $this->assertSame($nickname, $this->externalUser->getNickname());
    }

    public function test_setNickname_withEmptyString_setsEmptyString(): void
    {
        $result = $this->externalUser->setNickname('');
        
        $this->assertSame($this->externalUser, $result);
        $this->assertSame('', $this->externalUser->getNickname());
    }

    public function test_setNickname_withLongString_setsLongString(): void
    {
        $longNickname = str_repeat('测试用户名', 20); // 120个字符
        
        $result = $this->externalUser->setNickname($longNickname);
        
        $this->assertSame($this->externalUser, $result);
        $this->assertSame($longNickname, $this->externalUser->getNickname());
    }

    public function test_setExternalUserId_withValidId_setsIdCorrectly(): void
    {
        $externalUserId = 'ext_user_123456';
        
        $result = $this->externalUser->setExternalUserId($externalUserId);
        
        $this->assertSame($this->externalUser, $result);
        $this->assertSame($externalUserId, $this->externalUser->getExternalUserId());
    }

    public function test_setUnionId_withValidId_setsIdCorrectly(): void
    {
        $unionId = 'union_123456789';
        
        $result = $this->externalUser->setUnionId($unionId);
        
        $this->assertSame($this->externalUser, $result);
        $this->assertSame($unionId, $this->externalUser->getUnionId());
    }

    public function test_setUnionId_withNull_setsNull(): void
    {
        $this->externalUser->setUnionId('some_id');
        
        $result = $this->externalUser->setUnionId(null);
        
        $this->assertSame($this->externalUser, $result);
        $this->assertNull($this->externalUser->getUnionId());
    }

    public function test_setAvatar_withValidUrl_setsAvatarCorrectly(): void
    {
        $avatarUrl = 'https://example.com/avatar.jpg';
        
        $result = $this->externalUser->setAvatar($avatarUrl);
        
        $this->assertSame($this->externalUser, $result);
        $this->assertSame($avatarUrl, $this->externalUser->getAvatar());
    }

    public function test_setAvatar_withNull_setsNull(): void
    {
        $this->externalUser->setAvatar('https://example.com/old.jpg');
        
        $result = $this->externalUser->setAvatar(null);
        
        $this->assertSame($this->externalUser, $result);
        $this->assertNull($this->externalUser->getAvatar());
    }

    public function test_setGender_withValidGender_setsGenderCorrectly(): void
    {
        $gender = 1; // 男性
        
        $result = $this->externalUser->setGender($gender);
        
        $this->assertSame($this->externalUser, $result);
        $this->assertSame($gender, $this->externalUser->getGender());
    }

    public function test_setGender_withFemaleGender_setsGenderCorrectly(): void
    {
        $gender = 2; // 女性
        
        $result = $this->externalUser->setGender($gender);
        
        $this->assertSame($this->externalUser, $result);
        $this->assertSame($gender, $this->externalUser->getGender());
    }

    public function test_setGender_withUnknownGender_setsGenderCorrectly(): void
    {
        $gender = 0; // 未知
        
        $result = $this->externalUser->setGender($gender);
        
        $this->assertSame($this->externalUser, $result);
        $this->assertSame($gender, $this->externalUser->getGender());
    }

    public function test_setGender_withNull_setsNull(): void
    {
        $this->externalUser->setGender(1);
        
        $result = $this->externalUser->setGender(null);
        
        $this->assertSame($this->externalUser, $result);
        $this->assertNull($this->externalUser->getGender());
    }

    public function test_setEnterSessionContext_withValidArray_setsContextCorrectly(): void
    {
        $context = [
            'scene' => 'qr_code',
            'scene_param' => 'scene_value'
        ];
        
        $result = $this->externalUser->setEnterSessionContext($context);
        
        $this->assertSame($this->externalUser, $result);
        $this->assertSame($context, $this->externalUser->getEnterSessionContext());
    }

    public function test_setEnterSessionContext_withEmptyArray_setsEmptyArray(): void
    {
        $result = $this->externalUser->setEnterSessionContext([]);
        
        $this->assertSame($this->externalUser, $result);
        $this->assertSame([], $this->externalUser->getEnterSessionContext());
    }

    public function test_setEnterSessionContext_withNull_setsNull(): void
    {
        $result = $this->externalUser->setEnterSessionContext(null);
        
        $this->assertSame($this->externalUser, $result);
        $this->assertNull($this->externalUser->getEnterSessionContext());
    }

    public function test_setRemark_withValidRemark_setsRemarkCorrectly(): void
    {
        $remark = '这是一个重要的客户';
        
        $result = $this->externalUser->setRemark($remark);
        
        $this->assertSame($this->externalUser, $result);
        $this->assertSame($remark, $this->externalUser->getRemark());
    }

    public function test_setRemark_withNull_setsNull(): void
    {
        $this->externalUser->setRemark('old remark');
        
        $result = $this->externalUser->setRemark(null);
        
        $this->assertSame($this->externalUser, $result);
        $this->assertNull($this->externalUser->getRemark());
    }

    public function test_setTags_withValidArray_setsTagsCorrectly(): void
    {
        $tags = [
            ['tag_id' => 'tag1', 'tag_name' => '重要客户'],
            ['tag_id' => 'tag2', 'tag_name' => 'VIP']
        ];
        
        $result = $this->externalUser->setTags($tags);
        
        $this->assertSame($this->externalUser, $result);
        $this->assertSame($tags, $this->externalUser->getTags());
    }

    public function test_setTags_withEmptyArray_setsEmptyArray(): void
    {
        $result = $this->externalUser->setTags([]);
        
        $this->assertSame($this->externalUser, $result);
        $this->assertSame([], $this->externalUser->getTags());
    }

    public function test_setTags_defaultBehavior_returnsEmptyArray(): void
    {
        // 测试未调用setTags时的默认行为
        $this->assertSame([], $this->externalUser->getTags());
    }

    public function test_setCustomer_withTrue_setsTrue(): void
    {
        $result = $this->externalUser->setCustomer(true);
        
        $this->assertSame($this->externalUser, $result);
        $this->assertTrue($this->externalUser->isCustomer());
    }

    public function test_setCustomer_withFalse_setsFalse(): void
    {
        $result = $this->externalUser->setCustomer(false);
        
        $this->assertSame($this->externalUser, $result);
        $this->assertFalse($this->externalUser->isCustomer());
    }

    public function test_setCustomer_withNull_setsNull(): void
    {
        $this->externalUser->setCustomer(true);
        
        $result = $this->externalUser->setCustomer(null);
        
        $this->assertSame($this->externalUser, $result);
        $this->assertNull($this->externalUser->isCustomer());
    }

    public function test_setTmpOpenId_withValidId_setsIdCorrectly(): void
    {
        $tmpOpenId = 'tmp_open_id_123456';
        
        $result = $this->externalUser->setTmpOpenId($tmpOpenId);
        
        $this->assertSame($this->externalUser, $result);
        $this->assertSame($tmpOpenId, $this->externalUser->getTmpOpenId());
    }

    public function test_setTmpOpenId_withNull_setsNull(): void
    {
        $this->externalUser->setTmpOpenId('some_id');
        
        $result = $this->externalUser->setTmpOpenId(null);
        
        $this->assertSame($this->externalUser, $result);
        $this->assertNull($this->externalUser->getTmpOpenId());
    }

    public function test_setAddTime_withValidDateTime_setsTimeCorrectly(): void
    {
        $addTime = new \DateTime('2024-01-15 10:30:00');
        
        $result = $this->externalUser->setAddTime($addTime);
        
        $this->assertSame($this->externalUser, $result);
        $this->assertSame($addTime, $this->externalUser->getAddTime());
    }

    public function test_setAddTime_withNull_setsNull(): void
    {
        $this->externalUser->setAddTime(new \DateTime());
        
        $result = $this->externalUser->setAddTime(null);
        
        $this->assertSame($this->externalUser, $result);
        $this->assertNull($this->externalUser->getAddTime());
    }

    public function test_setRawData_withValidArray_setsDataCorrectly(): void
    {
        $rawData = [
            'external_userid' => 'ext_123',
            'name' => '张三',
            'avatar' => 'https://example.com/avatar.jpg'
        ];
        
        $result = $this->externalUser->setRawData($rawData);
        
        $this->assertSame($this->externalUser, $result);
        $this->assertSame($rawData, $this->externalUser->getRawData());
    }

    public function test_setRawData_withNull_setsNull(): void
    {
        $this->externalUser->setRawData(['key' => 'value']);
        
        $result = $this->externalUser->setRawData(null);
        
        $this->assertSame($this->externalUser, $result);
        $this->assertNull($this->externalUser->getRawData());
    }

    public function test_setCreateTime_withValidDateTime_setsTimeCorrectly(): void
    {
        $createTime = new \DateTime('2024-01-01 00:00:00');
        
        $this->externalUser->setCreateTime($createTime);
        
        $this->assertSame($createTime, $this->externalUser->getCreateTime());
    }

    public function test_setCreateTime_withNull_setsNull(): void
    {
        $this->externalUser->setCreateTime(new \DateTime());
        
        $this->externalUser->setCreateTime(null);
        
        $this->assertNull($this->externalUser->getCreateTime());
    }

    public function test_setUpdateTime_withValidDateTime_setsTimeCorrectly(): void
    {
        $updateTime = new \DateTime('2024-01-15 12:00:00');
        
        $this->externalUser->setUpdateTime($updateTime);
        
        $this->assertSame($updateTime, $this->externalUser->getUpdateTime());
    }

    public function test_setUpdateTime_withNull_setsNull(): void
    {
        $this->externalUser->setUpdateTime(new \DateTime());
        
        $this->externalUser->setUpdateTime(null);
        
        $this->assertNull($this->externalUser->getUpdateTime());
    }

    /**
     * 测试 __toString 方法
     */
    public function test_toString_withoutId_returnsEmptyString(): void
    {
        $result = (string) $this->externalUser;
        
        $this->assertSame('', $result);
    }

    public function test_toString_withIdAndData_returnsCorrectFormat(): void
    {
        // 使用反射设置ID (因为ID是自动生成的)
        $reflection = new \ReflectionClass($this->externalUser);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($this->externalUser, 123);
        
        $this->externalUser->setNickname('张三');
        $this->externalUser->setExternalUserId('ext_123456');
        
        $result = (string) $this->externalUser;
        
        $this->assertSame('张三[ext_123456]', $result);
    }

    public function test_toString_withIdButNoNickname_returnsCorrectFormat(): void
    {
        $reflection = new \ReflectionClass($this->externalUser);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($this->externalUser, 123);
        
        $this->externalUser->setExternalUserId('ext_123456');
        
        $result = (string) $this->externalUser;
        
        $this->assertSame('[ext_123456]', $result);
    }

    /**
     * 测试 PlainArray 接口实现
     */
    public function test_retrievePlainArray_returnsCorrectStructure(): void
    {
        $reflection = new \ReflectionClass($this->externalUser);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($this->externalUser, 456);
        
        $this->externalUser->setExternalUserId('ext_789');
        
        $result = $this->externalUser->retrievePlainArray();
        
        $expected = [
            'id' => 456,
            'externalUserId' => 'ext_789'
        ];
        
        $this->assertSame($expected, $result);
    }

    /**
     * 测试 ApiArray 接口实现
     */
    public function test_retrieveApiArray_returnsCorrectStructure(): void
    {
        $reflection = new \ReflectionClass($this->externalUser);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($this->externalUser, 789);
        
        $createTime = new \DateTime('2024-01-01 10:00:00');
        $updateTime = new \DateTime('2024-01-15 15:30:00');
        
        $this->externalUser->setCreateTime($createTime);
        $this->externalUser->setUpdateTime($updateTime);
        $this->externalUser->setNickname('李四');
        $this->externalUser->setExternalUserId('ext_456');
        $this->externalUser->setUnionId('union_789');
        $this->externalUser->setAvatar('https://example.com/avatar2.jpg');
        $this->externalUser->setGender(2);
        
        $result = $this->externalUser->retrieveApiArray();
        
        $expected = [
            'id' => 789,
            'createTime' => '2024-01-01 10:00:00',
            'updateTime' => '2024-01-15 15:30:00',
            'nickname' => '李四',
            'externalUserId' => 'ext_456',
            'unionId' => 'union_789',
            'avatar' => 'https://example.com/avatar2.jpg',
            'gender' => 2
        ];
        
        $this->assertSame($expected, $result);
    }

    public function test_retrieveApiArray_withNullTimes_returnsNullForTimes(): void
    {
        $reflection = new \ReflectionClass($this->externalUser);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($this->externalUser, 123);
        
        $result = $this->externalUser->retrieveApiArray();
        
        $this->assertNull($result['createTime']);
        $this->assertNull($result['updateTime']);
    }

    /**
     * 测试链式调用
     */
    public function test_chainedSetters_returnSameInstance(): void
    {
        $createTime = new \DateTime('2024-01-01');
        $addTime = new \DateTime('2024-01-15');
        
        $result = $this->externalUser
            ->setNickname('测试用户')
            ->setExternalUserId('ext_test')
            ->setUnionId('union_test')
            ->setAvatar('https://test.com/avatar.jpg')
            ->setGender(1)
            ->setEnterSessionContext(['scene' => 'test'])
            ->setRemark('测试备注')
            ->setTags([['tag_id' => 'test']])
            ->setCustomer(true)
            ->setTmpOpenId('tmp_test')
            ->setAddTime($addTime)
            ->setRawData(['test' => 'data']);
        
        $this->externalUser->setCreateTime($createTime);
        
        $this->assertSame($this->externalUser, $result);
        $this->assertSame('测试用户', $this->externalUser->getNickname());
        $this->assertSame('ext_test', $this->externalUser->getExternalUserId());
        $this->assertSame('union_test', $this->externalUser->getUnionId());
        $this->assertSame('https://test.com/avatar.jpg', $this->externalUser->getAvatar());
        $this->assertSame(1, $this->externalUser->getGender());
        $this->assertSame(['scene' => 'test'], $this->externalUser->getEnterSessionContext());
        $this->assertSame('测试备注', $this->externalUser->getRemark());
        $this->assertSame([['tag_id' => 'test']], $this->externalUser->getTags());
        $this->assertTrue($this->externalUser->isCustomer());
        $this->assertSame('tmp_test', $this->externalUser->getTmpOpenId());
        $this->assertSame($addTime, $this->externalUser->getAddTime());
        $this->assertSame(['test' => 'data'], $this->externalUser->getRawData());
        $this->assertSame($createTime, $this->externalUser->getCreateTime());
    }

    /**
     * 测试边界场景
     */
    public function test_edgeCases_longStrings(): void
    {
        $longString = str_repeat('x', 1000);
        
        $this->externalUser->setNickname($longString);
        $this->externalUser->setExternalUserId($longString);
        $this->externalUser->setUnionId($longString);
        $this->externalUser->setAvatar($longString);
        $this->externalUser->setRemark($longString);
        $this->externalUser->setTmpOpenId($longString);
        
        $this->assertSame($longString, $this->externalUser->getNickname());
        $this->assertSame($longString, $this->externalUser->getExternalUserId());
        $this->assertSame($longString, $this->externalUser->getUnionId());
        $this->assertSame($longString, $this->externalUser->getAvatar());
        $this->assertSame($longString, $this->externalUser->getRemark());
        $this->assertSame($longString, $this->externalUser->getTmpOpenId());
    }

    public function test_edgeCases_extremeGenderValues(): void
    {
        // 测试极端整数值
        $this->externalUser->setGender(PHP_INT_MAX);
        $this->assertSame(PHP_INT_MAX, $this->externalUser->getGender());
        
        $this->externalUser->setGender(PHP_INT_MIN);
        $this->assertSame(PHP_INT_MIN, $this->externalUser->getGender());
        
        $this->externalUser->setGender(-999);
        $this->assertSame(-999, $this->externalUser->getGender());
    }

    public function test_edgeCases_complexArrayData(): void
    {
        $complexContext = [
            'scene' => 'complex',
            'nested' => [
                'deep' => [
                    'data' => 'value'
                ]
            ],
            'numbers' => [1, 2, 3, 4, 5],
            'mixed' => ['string', 123, true, null]
        ];
        
        $this->externalUser->setEnterSessionContext($complexContext);
        $this->externalUser->setTags($complexContext);
        $this->externalUser->setRawData($complexContext);
        
        $this->assertSame($complexContext, $this->externalUser->getEnterSessionContext());
        $this->assertSame($complexContext, $this->externalUser->getTags());
        $this->assertSame($complexContext, $this->externalUser->getRawData());
    }
} 
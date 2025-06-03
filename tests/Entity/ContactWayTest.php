<?php

namespace WechatWorkExternalContactBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatWorkExternalContactBundle\Entity\ContactWay;
use Tourze\Arrayable\PlainArrayInterface;

/**
 * ContactWay 实体测试用例
 * 
 * 测试客户联系「联系我」实体的所有功能
 */
class ContactWayTest extends TestCase
{
    private ContactWay $contactWay;

    protected function setUp(): void
    {
        $this->contactWay = new ContactWay();
    }

    public function test_constructor_setsDefaultValues(): void
    {
        $contactWay = new ContactWay();
        
        $this->assertNull($contactWay->getId());
        $this->assertNull($contactWay->getCorp());
        $this->assertNull($contactWay->getAgent());
        $this->assertNull($contactWay->getConfigId());
        $this->assertNull($contactWay->getType());
        $this->assertNull($contactWay->getScene());
        $this->assertNull($contactWay->getStyle());
        $this->assertNull($contactWay->getRemark());
        $this->assertTrue($contactWay->isSkipVerify());
        $this->assertNull($contactWay->getState());
        $this->assertNull($contactWay->getUser());
        $this->assertNull($contactWay->getParty());
        $this->assertFalse($contactWay->isTemp());
        $this->assertNull($contactWay->getExpiresIn());
        $this->assertNull($contactWay->getChatExpiresIn());
        $this->assertNull($contactWay->getUnionId());
        $this->assertFalse($contactWay->isExclusive());
        $this->assertNull($contactWay->getConclusions());
        $this->assertNull($contactWay->getQrCode());
        $this->assertNull($contactWay->getCreatedFromIp());
        $this->assertNull($contactWay->getUpdatedFromIp());
        $this->assertNull($contactWay->getCreatedBy());
        $this->assertNull($contactWay->getUpdatedBy());
        $this->assertNull($contactWay->getCreateTime());
        $this->assertNull($contactWay->getUpdateTime());
    }

    public function test_implementsCorrectInterfaces(): void
    {
        $this->assertInstanceOf(PlainArrayInterface::class, $this->contactWay);
    }

    public function test_setConfigId_withValidId_setsIdCorrectly(): void
    {
        $configId = 'config_123456';
        
        $result = $this->contactWay->setConfigId($configId);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertSame($configId, $this->contactWay->getConfigId());
    }

    public function test_setConfigId_withNull_setsNull(): void
    {
        $this->contactWay->setConfigId('old_config');
        
        $result = $this->contactWay->setConfigId(null);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertNull($this->contactWay->getConfigId());
    }

    public function test_setType_withValidType_setsTypeCorrectly(): void
    {
        $type = 1; // 单人联系方式
        
        $result = $this->contactWay->setType($type);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertSame($type, $this->contactWay->getType());
    }

    public function test_setType_withMultiplePersonType_setsTypeCorrectly(): void
    {
        $type = 2; // 多人联系方式
        
        $result = $this->contactWay->setType($type);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertSame($type, $this->contactWay->getType());
    }

    public function test_setScene_withValidScene_setsSceneCorrectly(): void
    {
        $scene = 1; // 在小程序中联系
        
        $result = $this->contactWay->setScene($scene);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertSame($scene, $this->contactWay->getScene());
    }

    public function test_setScene_withQrcodeScene_setsSceneCorrectly(): void
    {
        $scene = 2; // 通过二维码联系
        
        $result = $this->contactWay->setScene($scene);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertSame($scene, $this->contactWay->getScene());
    }

    public function test_setStyle_withValidStyle_setsStyleCorrectly(): void
    {
        $style = 1; // 小程序控件样式
        
        $result = $this->contactWay->setStyle($style);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertSame($style, $this->contactWay->getStyle());
    }

    public function test_setStyle_withNull_setsNull(): void
    {
        $this->contactWay->setStyle(1);
        
        $result = $this->contactWay->setStyle(null);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertNull($this->contactWay->getStyle());
    }

    public function test_setRemark_withValidRemark_setsRemarkCorrectly(): void
    {
        $remark = '这是测试备注';
        
        $result = $this->contactWay->setRemark($remark);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertSame($remark, $this->contactWay->getRemark());
    }

    public function test_setRemark_withNull_setsNull(): void
    {
        $this->contactWay->setRemark('old remark');
        
        $result = $this->contactWay->setRemark(null);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertNull($this->contactWay->getRemark());
    }

    public function test_setSkipVerify_withTrue_setsTrue(): void
    {
        $result = $this->contactWay->setSkipVerify(true);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertTrue($this->contactWay->isSkipVerify());
    }

    public function test_setSkipVerify_withFalse_setsFalse(): void
    {
        $result = $this->contactWay->setSkipVerify(false);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertFalse($this->contactWay->isSkipVerify());
    }

    public function test_setSkipVerify_withNull_setsNull(): void
    {
        $this->contactWay->setSkipVerify(true);
        
        $result = $this->contactWay->setSkipVerify(null);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertNull($this->contactWay->isSkipVerify());
    }

    public function test_setState_withValidState_setsStateCorrectly(): void
    {
        $state = 'channel_123';
        
        $result = $this->contactWay->setState($state);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertSame($state, $this->contactWay->getState());
    }

    public function test_setState_withNull_setsNull(): void
    {
        $this->contactWay->setState('old_state');
        
        $result = $this->contactWay->setState(null);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertNull($this->contactWay->getState());
    }

    public function test_setUser_withValidArray_setsUserCorrectly(): void
    {
        $user = ['user1', 'user2', 'user3'];
        
        $result = $this->contactWay->setUser($user);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertSame($user, $this->contactWay->getUser());
    }

    public function test_setUser_withEmptyArray_setsEmptyArray(): void
    {
        $result = $this->contactWay->setUser([]);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertSame([], $this->contactWay->getUser());
    }

    public function test_setUser_withNull_setsNull(): void
    {
        $this->contactWay->setUser(['user1']);
        
        $result = $this->contactWay->setUser(null);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertNull($this->contactWay->getUser());
    }

    public function test_setParty_withValidArray_setsPartyCorrectly(): void
    {
        $party = [1, 2, 3]; // 部门ID数组
        
        $result = $this->contactWay->setParty($party);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertSame($party, $this->contactWay->getParty());
    }

    public function test_setParty_withNull_setsNull(): void
    {
        $this->contactWay->setParty([1, 2]);
        
        $result = $this->contactWay->setParty(null);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertNull($this->contactWay->getParty());
    }

    public function test_setTemp_withTrue_setsTrue(): void
    {
        $result = $this->contactWay->setTemp(true);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertTrue($this->contactWay->isTemp());
    }

    public function test_setTemp_withFalse_setsFalse(): void
    {
        $result = $this->contactWay->setTemp(false);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertFalse($this->contactWay->isTemp());
    }

    public function test_setTemp_withNull_setsNull(): void
    {
        $this->contactWay->setTemp(true);
        
        $result = $this->contactWay->setTemp(null);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertNull($this->contactWay->isTemp());
    }

    public function test_setExpiresIn_withValidSeconds_setsSecondsCorrectly(): void
    {
        $expiresIn = 86400; // 24小时
        
        $result = $this->contactWay->setExpiresIn($expiresIn);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertSame($expiresIn, $this->contactWay->getExpiresIn());
    }

    public function test_setExpiresIn_withNull_setsNull(): void
    {
        $this->contactWay->setExpiresIn(3600);
        
        $result = $this->contactWay->setExpiresIn(null);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertNull($this->contactWay->getExpiresIn());
    }

    public function test_setChatExpiresIn_withValidSeconds_setsSecondsCorrectly(): void
    {
        $chatExpiresIn = 3600; // 1小时
        
        $result = $this->contactWay->setChatExpiresIn($chatExpiresIn);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertSame($chatExpiresIn, $this->contactWay->getChatExpiresIn());
    }

    public function test_setChatExpiresIn_withNull_setsNull(): void
    {
        $this->contactWay->setChatExpiresIn(1800);
        
        $result = $this->contactWay->setChatExpiresIn(null);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertNull($this->contactWay->getChatExpiresIn());
    }

    public function test_setUnionId_withValidId_setsIdCorrectly(): void
    {
        $unionId = 'union_123456789';
        
        $result = $this->contactWay->setUnionId($unionId);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertSame($unionId, $this->contactWay->getUnionId());
    }

    public function test_setUnionId_withNull_setsNull(): void
    {
        $this->contactWay->setUnionId('old_union');
        
        $result = $this->contactWay->setUnionId(null);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertNull($this->contactWay->getUnionId());
    }

    public function test_setExclusive_withTrue_setsTrue(): void
    {
        $result = $this->contactWay->setExclusive(true);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertTrue($this->contactWay->isExclusive());
    }

    public function test_setExclusive_withFalse_setsFalse(): void
    {
        $result = $this->contactWay->setExclusive(false);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertFalse($this->contactWay->isExclusive());
    }

    public function test_setExclusive_withNull_setsNull(): void
    {
        $this->contactWay->setExclusive(true);
        
        $result = $this->contactWay->setExclusive(null);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertNull($this->contactWay->isExclusive());
    }

    public function test_setConclusions_withValidArray_setConclusionsCorrectly(): void
    {
        $conclusions = [
            'text' => ['content' => '感谢您的咨询'],
            'image' => ['media_id' => 'media_123']
        ];
        
        $result = $this->contactWay->setConclusions($conclusions);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertSame($conclusions, $this->contactWay->getConclusions());
    }

    public function test_setConclusions_withNull_setsNull(): void
    {
        $this->contactWay->setConclusions(['text' => 'old']);
        
        $result = $this->contactWay->setConclusions(null);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertNull($this->contactWay->getConclusions());
    }

    public function test_setQrCode_withValidUrl_setsUrlCorrectly(): void
    {
        $qrCode = 'https://example.com/qrcode.jpg';
        
        $result = $this->contactWay->setQrCode($qrCode);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertSame($qrCode, $this->contactWay->getQrCode());
    }

    public function test_setQrCode_withNull_setsNull(): void
    {
        $this->contactWay->setQrCode('old_qrcode');
        
        $result = $this->contactWay->setQrCode(null);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertNull($this->contactWay->getQrCode());
    }

    public function test_setCreatedFromIp_withValidIp_setsIpCorrectly(): void
    {
        $ip = '192.168.1.1';
        
        $result = $this->contactWay->setCreatedFromIp($ip);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertSame($ip, $this->contactWay->getCreatedFromIp());
    }

    public function test_setCreatedFromIp_withNull_setsNull(): void
    {
        $this->contactWay->setCreatedFromIp('127.0.0.1');
        
        $result = $this->contactWay->setCreatedFromIp(null);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertNull($this->contactWay->getCreatedFromIp());
    }

    public function test_setUpdatedFromIp_withValidIp_setsIpCorrectly(): void
    {
        $ip = '10.0.0.1';
        
        $result = $this->contactWay->setUpdatedFromIp($ip);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertSame($ip, $this->contactWay->getUpdatedFromIp());
    }

    public function test_setUpdatedFromIp_withNull_setsNull(): void
    {
        $this->contactWay->setUpdatedFromIp('172.16.0.1');
        
        $result = $this->contactWay->setUpdatedFromIp(null);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertNull($this->contactWay->getUpdatedFromIp());
    }

    public function test_setCreatedBy_withValidUser_setsUserCorrectly(): void
    {
        $createdBy = 'admin_user';
        
        $result = $this->contactWay->setCreatedBy($createdBy);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertSame($createdBy, $this->contactWay->getCreatedBy());
    }

    public function test_setCreatedBy_withNull_setsNull(): void
    {
        $this->contactWay->setCreatedBy('old_user');
        
        $result = $this->contactWay->setCreatedBy(null);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertNull($this->contactWay->getCreatedBy());
    }

    public function test_setUpdatedBy_withValidUser_setsUserCorrectly(): void
    {
        $updatedBy = 'updated_user';
        
        $result = $this->contactWay->setUpdatedBy($updatedBy);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertSame($updatedBy, $this->contactWay->getUpdatedBy());
    }

    public function test_setUpdatedBy_withNull_setsNull(): void
    {
        $this->contactWay->setUpdatedBy('old_user');
        
        $result = $this->contactWay->setUpdatedBy(null);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertNull($this->contactWay->getUpdatedBy());
    }

    public function test_setCreateTime_withValidDateTime_setsTimeCorrectly(): void
    {
        $createTime = new \DateTime('2024-01-01 10:00:00');
        
        $this->contactWay->setCreateTime($createTime);
        
        $this->assertSame($createTime, $this->contactWay->getCreateTime());
    }

    public function test_setCreateTime_withNull_setsNull(): void
    {
        $this->contactWay->setCreateTime(new \DateTime());
        
        $this->contactWay->setCreateTime(null);
        
        $this->assertNull($this->contactWay->getCreateTime());
    }

    public function test_setUpdateTime_withValidDateTime_setsTimeCorrectly(): void
    {
        $updateTime = new \DateTime('2024-01-15 12:00:00');
        
        $this->contactWay->setUpdateTime($updateTime);
        
        $this->assertSame($updateTime, $this->contactWay->getUpdateTime());
    }

    public function test_setUpdateTime_withNull_setsNull(): void
    {
        $this->contactWay->setUpdateTime(new \DateTime());
        
        $this->contactWay->setUpdateTime(null);
        
        $this->assertNull($this->contactWay->getUpdateTime());
    }

    /**
     * 测试 PlainArray 接口实现
     */
    public function test_retrievePlainArray_returnsCorrectStructure(): void
    {
        // 使用反射设置ID (因为ID是自动生成的)
        $reflection = new \ReflectionClass($this->contactWay);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($this->contactWay, '123456789');
        
        $this->contactWay->setConfigId('config_test');
        
        $result = $this->contactWay->retrievePlainArray();
        
        $expected = [
            'id' => '123456789',
            'configId' => 'config_test'
        ];
        
        $this->assertSame($expected, $result);
    }

    /**
     * 测试链式调用
     */
    public function test_chainedSetters_returnSameInstance(): void
    {
        $createTime = new \DateTime('2024-01-01');
        $updateTime = new \DateTime('2024-01-15');
        
        $result = $this->contactWay
            ->setConfigId('config_chain')
            ->setType(1)
            ->setScene(2)
            ->setStyle(1)
            ->setRemark('链式调用测试')
            ->setSkipVerify(false)
            ->setState('chain_state')
            ->setUser(['user1', 'user2'])
            ->setParty([1, 2])
            ->setTemp(true)
            ->setExpiresIn(3600)
            ->setChatExpiresIn(1800)
            ->setUnionId('union_chain')
            ->setExclusive(true)
            ->setConclusions(['text' => 'conclusion'])
            ->setQrCode('https://qr.example.com')
            ->setCreatedFromIp('192.168.1.1')
            ->setUpdatedFromIp('192.168.1.2')
            ->setCreatedBy('admin')
            ->setUpdatedBy('editor');
        
        $this->contactWay->setCreateTime($createTime);
        $this->contactWay->setUpdateTime($updateTime);
        
        $this->assertSame($this->contactWay, $result);
        $this->assertSame('config_chain', $this->contactWay->getConfigId());
        $this->assertSame(1, $this->contactWay->getType());
        $this->assertSame(2, $this->contactWay->getScene());
        $this->assertSame(1, $this->contactWay->getStyle());
        $this->assertSame('链式调用测试', $this->contactWay->getRemark());
        $this->assertFalse($this->contactWay->isSkipVerify());
        $this->assertSame('chain_state', $this->contactWay->getState());
        $this->assertSame(['user1', 'user2'], $this->contactWay->getUser());
        $this->assertSame([1, 2], $this->contactWay->getParty());
        $this->assertTrue($this->contactWay->isTemp());
        $this->assertSame(3600, $this->contactWay->getExpiresIn());
        $this->assertSame(1800, $this->contactWay->getChatExpiresIn());
        $this->assertSame('union_chain', $this->contactWay->getUnionId());
        $this->assertTrue($this->contactWay->isExclusive());
        $this->assertSame(['text' => 'conclusion'], $this->contactWay->getConclusions());
        $this->assertSame('https://qr.example.com', $this->contactWay->getQrCode());
        $this->assertSame('192.168.1.1', $this->contactWay->getCreatedFromIp());
        $this->assertSame('192.168.1.2', $this->contactWay->getUpdatedFromIp());
        $this->assertSame('admin', $this->contactWay->getCreatedBy());
        $this->assertSame('editor', $this->contactWay->getUpdatedBy());
        $this->assertSame($createTime, $this->contactWay->getCreateTime());
        $this->assertSame($updateTime, $this->contactWay->getUpdateTime());
    }

    /**
     * 测试边界场景
     */
    public function test_edgeCases_extremeValues(): void
    {
        // 测试极端整数值
        $this->contactWay->setType(PHP_INT_MAX);
        $this->assertSame(PHP_INT_MAX, $this->contactWay->getType());
        
        $this->contactWay->setScene(PHP_INT_MIN);
        $this->assertSame(PHP_INT_MIN, $this->contactWay->getScene());
        
        $this->contactWay->setExpiresIn(0);
        $this->assertSame(0, $this->contactWay->getExpiresIn());
        
        $this->contactWay->setChatExpiresIn(-1);
        $this->assertSame(-1, $this->contactWay->getChatExpiresIn());
    }

    public function test_edgeCases_longStrings(): void
    {
        $longString = str_repeat('x', 1000);
        
        $this->contactWay->setConfigId($longString);
        $this->contactWay->setRemark($longString);
        $this->contactWay->setState($longString);
        $this->contactWay->setUnionId($longString);
        $this->contactWay->setQrCode($longString);
        $this->contactWay->setCreatedFromIp($longString);
        $this->contactWay->setUpdatedFromIp($longString);
        $this->contactWay->setCreatedBy($longString);
        $this->contactWay->setUpdatedBy($longString);
        
        $this->assertSame($longString, $this->contactWay->getConfigId());
        $this->assertSame($longString, $this->contactWay->getRemark());
        $this->assertSame($longString, $this->contactWay->getState());
        $this->assertSame($longString, $this->contactWay->getUnionId());
        $this->assertSame($longString, $this->contactWay->getQrCode());
        $this->assertSame($longString, $this->contactWay->getCreatedFromIp());
        $this->assertSame($longString, $this->contactWay->getUpdatedFromIp());
        $this->assertSame($longString, $this->contactWay->getCreatedBy());
        $this->assertSame($longString, $this->contactWay->getUpdatedBy());
    }

    public function test_edgeCases_complexArrayData(): void
    {
        $complexUser = [
            'user1', 'user2', 'user3', 
            'very_long_user_id_' . str_repeat('x', 100)
        ];
        
        $complexParty = [1, 2, 3, 999999999, -1, 0];
        
        $complexConclusions = [
            'text' => [
                'content' => '这是一个非常长的结束语内容' . str_repeat('测试', 50)
            ],
            'image' => [
                'media_id' => 'media_' . str_repeat('a', 100)
            ],
            'link' => [
                'title' => '链接标题',
                'picurl' => 'https://example.com/very/long/path/to/image.jpg',
                'desc' => '链接描述',
                'url' => 'https://example.com/very/long/path/to/target/page.html'
            ]
        ];
        
        $this->contactWay->setUser($complexUser);
        $this->contactWay->setParty($complexParty);
        $this->contactWay->setConclusions($complexConclusions);
        
        $this->assertSame($complexUser, $this->contactWay->getUser());
        $this->assertSame($complexParty, $this->contactWay->getParty());
        $this->assertSame($complexConclusions, $this->contactWay->getConclusions());
    }

    public function test_edgeCases_dateTimeTypes(): void
    {
        // 测试DateTime
        $dateTime = new \DateTime('2024-01-15 12:30:45');
        $this->contactWay->setCreateTime($dateTime);
        $this->assertSame($dateTime, $this->contactWay->getCreateTime());
        
        // 测试DateTimeImmutable
        $dateTimeImmutable = new \DateTimeImmutable('2024-02-20 09:15:30');
        $this->contactWay->setUpdateTime($dateTimeImmutable);
        $this->assertSame($dateTimeImmutable, $this->contactWay->getUpdateTime());
    }
}

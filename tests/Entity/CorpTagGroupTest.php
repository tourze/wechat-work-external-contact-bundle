<?php

namespace WechatWorkExternalContactBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatWorkExternalContactBundle\Entity\CorpTagGroup;
use WechatWorkExternalContactBundle\Entity\CorpTagItem;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * CorpTagGroup 实体测试用例
 * 
 * 测试企业标签分组实体的所有功能
 */
class CorpTagGroupTest extends TestCase
{
    private CorpTagGroup $corpTagGroup;

    protected function setUp(): void
    {
        $this->corpTagGroup = new CorpTagGroup();
    }

    public function test_constructor_setsDefaultValues(): void
    {
        $tagGroup = new CorpTagGroup();
        
        $this->assertSame(0, $tagGroup->getId());
        $this->assertNull($tagGroup->getCorp());
        $this->assertNull($tagGroup->getAgent());
        $this->assertNull($tagGroup->getRemoteId());
        $this->assertInstanceOf(Collection::class, $tagGroup->getItems());
        $this->assertInstanceOf(ArrayCollection::class, $tagGroup->getItems());
        $this->assertTrue($tagGroup->getItems()->isEmpty());
        $this->assertNull($tagGroup->getSortNumber());
        $this->assertNull($tagGroup->getCreatedFromIp());
        $this->assertNull($tagGroup->getUpdatedFromIp());
        $this->assertNull($tagGroup->getCreatedBy());
        $this->assertNull($tagGroup->getUpdatedBy());
        $this->assertNull($tagGroup->getCreateTime());
        $this->assertNull($tagGroup->getUpdateTime());
    }

    public function test_setName_withValidName_setsNameCorrectly(): void
    {
        $name = '重要客户';
        
        $result = $this->corpTagGroup->setName($name);
        
        $this->assertSame($this->corpTagGroup, $result);
        $this->assertSame($name, $this->corpTagGroup->getName());
    }

    public function test_setName_withEmptyString_setsEmptyString(): void
    {
        $result = $this->corpTagGroup->setName('');
        
        $this->assertSame($this->corpTagGroup, $result);
        $this->assertSame('', $this->corpTagGroup->getName());
    }

    public function test_setName_withLongString_setsLongString(): void
    {
        $longName = str_repeat('标签分组', 20); // 60个字符
        
        $result = $this->corpTagGroup->setName($longName);
        
        $this->assertSame($this->corpTagGroup, $result);
        $this->assertSame($longName, $this->corpTagGroup->getName());
    }

    public function test_setRemoteId_withValidId_setsIdCorrectly(): void
    {
        $remoteId = 'remote_group_123456';
        
        $result = $this->corpTagGroup->setRemoteId($remoteId);
        
        $this->assertSame($this->corpTagGroup, $result);
        $this->assertSame($remoteId, $this->corpTagGroup->getRemoteId());
    }

    public function test_setRemoteId_withNull_setsNull(): void
    {
        $this->corpTagGroup->setRemoteId('old_remote_id');
        
        $result = $this->corpTagGroup->setRemoteId(null);
        
        $this->assertSame($this->corpTagGroup, $result);
        $this->assertNull($this->corpTagGroup->getRemoteId());
    }

    public function test_setSortNumber_withValidNumber_setsNumberCorrectly(): void
    {
        $sortNumber = 100;
        
        $result = $this->corpTagGroup->setSortNumber($sortNumber);
        
        $this->assertSame($this->corpTagGroup, $result);
        $this->assertSame($sortNumber, $this->corpTagGroup->getSortNumber());
    }

    public function test_setSortNumber_withZero_setsZero(): void
    {
        $result = $this->corpTagGroup->setSortNumber(0);
        
        $this->assertSame($this->corpTagGroup, $result);
        $this->assertSame(0, $this->corpTagGroup->getSortNumber());
    }

    public function test_setSortNumber_withNegativeNumber_setsNegativeNumber(): void
    {
        $result = $this->corpTagGroup->setSortNumber(-10);
        
        $this->assertSame($this->corpTagGroup, $result);
        $this->assertSame(-10, $this->corpTagGroup->getSortNumber());
    }

    public function test_addItem_withNewItem_addsItemToCollection(): void
    {
        $item = $this->createMock(CorpTagItem::class);
        $item->expects($this->once())
             ->method('setTagGroup')
             ->with($this->corpTagGroup);
        
        $result = $this->corpTagGroup->addItem($item);
        
        $this->assertSame($this->corpTagGroup, $result);
        $this->assertTrue($this->corpTagGroup->getItems()->contains($item));
        $this->assertCount(1, $this->corpTagGroup->getItems());
    }

    public function test_addItem_withExistingItem_doesNotAddDuplicate(): void
    {
        $item = $this->createMock(CorpTagItem::class);
        $item->expects($this->once())
             ->method('setTagGroup');
        
        // 添加第一次
        $this->corpTagGroup->addItem($item);
        $firstCount = $this->corpTagGroup->getItems()->count();
        
        // 尝试再次添加相同项
        $result = $this->corpTagGroup->addItem($item);
        
        $this->assertSame($this->corpTagGroup, $result);
        $this->assertCount($firstCount, $this->corpTagGroup->getItems());
    }

    public function test_addItem_withMultipleItems_addsAllItems(): void
    {
        $item1 = $this->createMock(CorpTagItem::class);
        $item2 = $this->createMock(CorpTagItem::class);
        
        $item1->expects($this->once())->method('setTagGroup')->with($this->corpTagGroup);
        $item2->expects($this->once())->method('setTagGroup')->with($this->corpTagGroup);
        
        $this->corpTagGroup->addItem($item1);
        $this->corpTagGroup->addItem($item2);
        
        $this->assertCount(2, $this->corpTagGroup->getItems());
        $this->assertTrue($this->corpTagGroup->getItems()->contains($item1));
        $this->assertTrue($this->corpTagGroup->getItems()->contains($item2));
    }

    public function test_removeItem_withExistingItem_removesItemFromCollection(): void
    {
        $item = $this->createMock(CorpTagItem::class);
        
        // 设置期望：setTagGroup 被调用两次，第一次传入group对象，第二次传入null
        $item->expects($this->exactly(2))
             ->method('setTagGroup')
             ->with($this->callback(function ($arg) {
                 static $callCount = 0;
                 $callCount++;
                 if ($callCount === 1) {
                     return $arg === $this->corpTagGroup;
                 } else {
                     return $arg === null;
                 }
             }));
        $item->expects($this->once())->method('getTagGroup')->willReturn($this->corpTagGroup);
        
        // 先添加项
        $this->corpTagGroup->addItem($item);
        $this->assertCount(1, $this->corpTagGroup->getItems());
        
        // 移除项
        $result = $this->corpTagGroup->removeItem($item);
        
        $this->assertSame($this->corpTagGroup, $result);
        $this->assertFalse($this->corpTagGroup->getItems()->contains($item));
        $this->assertCount(0, $this->corpTagGroup->getItems());
    }

    public function test_removeItem_withNonExistingItem_doesNothing(): void
    {
        $item = $this->createMock(CorpTagItem::class);
        $item->expects($this->never())->method('setTagGroup');
        
        $result = $this->corpTagGroup->removeItem($item);
        
        $this->assertSame($this->corpTagGroup, $result);
        $this->assertCount(0, $this->corpTagGroup->getItems());
    }

    public function test_removeItem_whenItemTagGroupDiffers_removesButDoesNotSetNull(): void
    {
        $item = $this->createMock(CorpTagItem::class);
        $otherGroup = $this->createMock(CorpTagGroup::class);
        
        $item->expects($this->once())->method('setTagGroup')->with($this->corpTagGroup);
        
        // 添加项
        $this->corpTagGroup->addItem($item);
        
        // 模拟项的标签组已经被改变
        $item->expects($this->once())->method('getTagGroup')->willReturn($otherGroup);
        // 不应该再次调用setTagGroup(null)
        
        $result = $this->corpTagGroup->removeItem($item);
        
        $this->assertSame($this->corpTagGroup, $result);
        $this->assertFalse($this->corpTagGroup->getItems()->contains($item));
    }

    public function test_setCreatedFromIp_withValidIp_setsIpCorrectly(): void
    {
        $ip = '192.168.1.1';
        
        $result = $this->corpTagGroup->setCreatedFromIp($ip);
        
        $this->assertSame($this->corpTagGroup, $result);
        $this->assertSame($ip, $this->corpTagGroup->getCreatedFromIp());
    }

    public function test_setCreatedFromIp_withNull_setsNull(): void
    {
        $this->corpTagGroup->setCreatedFromIp('127.0.0.1');
        
        $result = $this->corpTagGroup->setCreatedFromIp(null);
        
        $this->assertSame($this->corpTagGroup, $result);
        $this->assertNull($this->corpTagGroup->getCreatedFromIp());
    }

    public function test_setUpdatedFromIp_withValidIp_setsIpCorrectly(): void
    {
        $ip = '10.0.0.1';
        
        $result = $this->corpTagGroup->setUpdatedFromIp($ip);
        
        $this->assertSame($this->corpTagGroup, $result);
        $this->assertSame($ip, $this->corpTagGroup->getUpdatedFromIp());
    }

    public function test_setUpdatedFromIp_withNull_setsNull(): void
    {
        $this->corpTagGroup->setUpdatedFromIp('172.16.0.1');
        
        $result = $this->corpTagGroup->setUpdatedFromIp(null);
        
        $this->assertSame($this->corpTagGroup, $result);
        $this->assertNull($this->corpTagGroup->getUpdatedFromIp());
    }

    public function test_setCreatedBy_withValidUser_setsUserCorrectly(): void
    {
        $createdBy = 'admin_user';
        
        $result = $this->corpTagGroup->setCreatedBy($createdBy);
        
        $this->assertSame($this->corpTagGroup, $result);
        $this->assertSame($createdBy, $this->corpTagGroup->getCreatedBy());
    }

    public function test_setCreatedBy_withNull_setsNull(): void
    {
        $this->corpTagGroup->setCreatedBy('old_user');
        
        $result = $this->corpTagGroup->setCreatedBy(null);
        
        $this->assertSame($this->corpTagGroup, $result);
        $this->assertNull($this->corpTagGroup->getCreatedBy());
    }

    public function test_setUpdatedBy_withValidUser_setsUserCorrectly(): void
    {
        $updatedBy = 'updated_user';
        
        $result = $this->corpTagGroup->setUpdatedBy($updatedBy);
        
        $this->assertSame($this->corpTagGroup, $result);
        $this->assertSame($updatedBy, $this->corpTagGroup->getUpdatedBy());
    }

    public function test_setUpdatedBy_withNull_setsNull(): void
    {
        $this->corpTagGroup->setUpdatedBy('old_user');
        
        $result = $this->corpTagGroup->setUpdatedBy(null);
        
        $this->assertSame($this->corpTagGroup, $result);
        $this->assertNull($this->corpTagGroup->getUpdatedBy());
    }

    public function test_setCreateTime_withValidDateTime_setsTimeCorrectly(): void
    {
        $createTime = new \DateTime('2024-01-01 10:00:00');
        
        $this->corpTagGroup->setCreateTime($createTime);
        
        $this->assertSame($createTime, $this->corpTagGroup->getCreateTime());
    }

    public function test_setCreateTime_withNull_setsNull(): void
    {
        $this->corpTagGroup->setCreateTime(new \DateTime());
        
        $this->corpTagGroup->setCreateTime(null);
        
        $this->assertNull($this->corpTagGroup->getCreateTime());
    }

    public function test_setUpdateTime_withValidDateTime_setsTimeCorrectly(): void
    {
        $updateTime = new \DateTime('2024-01-15 12:00:00');
        
        $this->corpTagGroup->setUpdateTime($updateTime);
        
        $this->assertSame($updateTime, $this->corpTagGroup->getUpdateTime());
    }

    public function test_setUpdateTime_withNull_setsNull(): void
    {
        $this->corpTagGroup->setUpdateTime(new \DateTime());
        
        $this->corpTagGroup->setUpdateTime(null);
        
        $this->assertNull($this->corpTagGroup->getUpdateTime());
    }

    /**
     * 测试链式调用
     */
    public function test_chainedSetters_returnSameInstance(): void
    {
        $createTime = new \DateTime('2024-01-01');
        $updateTime = new \DateTime('2024-01-15');
        
        $result = $this->corpTagGroup
            ->setName('链式调用测试分组')
            ->setRemoteId('remote_chain')
            ->setSortNumber(50)
            ->setCreatedFromIp('192.168.1.1')
            ->setUpdatedFromIp('192.168.1.2')
            ->setCreatedBy('admin')
            ->setUpdatedBy('editor');
        
        $this->corpTagGroup->setCreateTime($createTime);
        $this->corpTagGroup->setUpdateTime($updateTime);
        
        $this->assertSame($this->corpTagGroup, $result);
        $this->assertSame('链式调用测试分组', $this->corpTagGroup->getName());
        $this->assertSame('remote_chain', $this->corpTagGroup->getRemoteId());
        $this->assertSame(50, $this->corpTagGroup->getSortNumber());
        $this->assertSame('192.168.1.1', $this->corpTagGroup->getCreatedFromIp());
        $this->assertSame('192.168.1.2', $this->corpTagGroup->getUpdatedFromIp());
        $this->assertSame('admin', $this->corpTagGroup->getCreatedBy());
        $this->assertSame('editor', $this->corpTagGroup->getUpdatedBy());
        $this->assertSame($createTime, $this->corpTagGroup->getCreateTime());
        $this->assertSame($updateTime, $this->corpTagGroup->getUpdateTime());
    }

    /**
     * 测试边界场景
     */
    public function test_edgeCases_extremeValues(): void
    {
        // 测试极端整数值
        $this->corpTagGroup->setSortNumber(PHP_INT_MAX);
        $this->assertSame(PHP_INT_MAX, $this->corpTagGroup->getSortNumber());
        
        $this->corpTagGroup->setSortNumber(PHP_INT_MIN);
        $this->assertSame(PHP_INT_MIN, $this->corpTagGroup->getSortNumber());
    }

    public function test_edgeCases_longStrings(): void
    {
        $longString = str_repeat('x', 1000);
        
        $this->corpTagGroup->setName($longString);
        $this->corpTagGroup->setRemoteId($longString);
        $this->corpTagGroup->setCreatedFromIp($longString);
        $this->corpTagGroup->setUpdatedFromIp($longString);
        $this->corpTagGroup->setCreatedBy($longString);
        $this->corpTagGroup->setUpdatedBy($longString);
        
        $this->assertSame($longString, $this->corpTagGroup->getName());
        $this->assertSame($longString, $this->corpTagGroup->getRemoteId());
        $this->assertSame($longString, $this->corpTagGroup->getCreatedFromIp());
        $this->assertSame($longString, $this->corpTagGroup->getUpdatedFromIp());
        $this->assertSame($longString, $this->corpTagGroup->getCreatedBy());
        $this->assertSame($longString, $this->corpTagGroup->getUpdatedBy());
    }

    public function test_edgeCases_dateTimeTypes(): void
    {
        // 测试DateTime
        $dateTime = new \DateTime('2024-01-15 12:30:45');
        $this->corpTagGroup->setCreateTime($dateTime);
        $this->assertSame($dateTime, $this->corpTagGroup->getCreateTime());
        
        // 测试DateTimeImmutable
        $dateTimeImmutable = new \DateTimeImmutable('2024-02-20 09:15:30');
        $this->corpTagGroup->setUpdateTime($dateTimeImmutable);
        $this->assertSame($dateTimeImmutable, $this->corpTagGroup->getUpdateTime());
    }

    /**
     * 测试Collection操作的复杂场景
     */
    public function test_itemCollection_simpleOperations(): void
    {
        $item1 = $this->createMock(CorpTagItem::class);
        $item2 = $this->createMock(CorpTagItem::class);
        $item3 = $this->createMock(CorpTagItem::class);
        
        $item1->expects($this->once())->method('setTagGroup')->with($this->corpTagGroup);
        $item2->expects($this->once())->method('setTagGroup')->with($this->corpTagGroup);
        $item3->expects($this->once())->method('setTagGroup')->with($this->corpTagGroup);
        
        // 添加多个项
        $this->corpTagGroup->addItem($item1);
        $this->corpTagGroup->addItem($item2);
        $this->corpTagGroup->addItem($item3);
        
        $this->assertCount(3, $this->corpTagGroup->getItems());
        $this->assertTrue($this->corpTagGroup->getItems()->contains($item1));
        $this->assertTrue($this->corpTagGroup->getItems()->contains($item2));
        $this->assertTrue($this->corpTagGroup->getItems()->contains($item3));
    }

    public function test_itemCollection_isIterable(): void
    {
        $item1 = $this->createMock(CorpTagItem::class);
        $item2 = $this->createMock(CorpTagItem::class);
        
        $item1->expects($this->once())->method('setTagGroup');
        $item2->expects($this->once())->method('setTagGroup');
        
        $this->corpTagGroup->addItem($item1);
        $this->corpTagGroup->addItem($item2);
        
        $items = [];
        foreach ($this->corpTagGroup->getItems() as $item) {
            $items[] = $item;
        }
        
        $this->assertCount(2, $items);
        $this->assertContains($item1, $items);
        $this->assertContains($item2, $items);
    }
} 
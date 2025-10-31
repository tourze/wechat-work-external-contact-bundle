<?php

namespace WechatWorkExternalContactBundle\Tests\Request\Attachment;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatWorkExternalContactBundle\Request\Attachment\File;

/**
 * File 附件测试
 *
 * @internal
 */
#[CoversClass(File::class)]
#[RunTestsInSeparateProcesses] final class FileTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void        // 此测试不需要特殊的设置
    {
    }

    public function testInheritance(): void
    {
        // 测试继承关系
        $file = self::getService(File::class);
        $this->assertInstanceOf(PlainArrayInterface::class, $file);

        // 测试PlainArrayInterface接口的实际功能
        $file->setMediaId('test_media_id');
        $array = $file->retrievePlainArray();
        $this->assertIsArray($array);
        $this->assertArrayHasKey('msgtype', $array);
        $this->assertArrayHasKey('file', $array);
    }

    public function testMediaIdSetterAndGetter(): void
    {
        // 测试媒体ID设置和获取
        $file = self::getService(File::class);
        $mediaId = 'file_media_id_123';

        $file->setMediaId($mediaId);
        $this->assertSame($mediaId, $file->getMediaId());
    }

    public function testMediaIdWithSpecialCharacters(): void
    {
        // 测试特殊字符媒体ID
        $file = self::getService(File::class);
        $specialMediaId = 'file_abc-123_test@domain.com';
        $file->setMediaId($specialMediaId);

        $this->assertSame($specialMediaId, $file->getMediaId());
    }

    public function testMediaIdWithLongString(): void
    {
        // 测试长字符串媒体ID
        $file = self::getService(File::class);
        $longMediaId = str_repeat('a', 255);
        $file->setMediaId($longMediaId);

        $this->assertSame($longMediaId, $file->getMediaId());
    }

    public function testRetrievePlainArray(): void
    {
        // 测试获取普通数组
        $file = self::getService(File::class);
        $mediaId = 'test_file_media_id';
        $file->setMediaId($mediaId);

        $expected = [
            'msgtype' => 'file',
            'file' => [
                'media_id' => $mediaId,
            ],
        ];

        $this->assertSame($expected, $file->retrievePlainArray());
    }

    public function testRetrievePlainArrayStructure(): void
    {
        // 测试数组结构
        $file = self::getService(File::class);
        $file->setMediaId('structure_test_media');

        $array = $file->retrievePlainArray();
        $this->assertCount(2, $array);
        $this->assertArrayHasKey('msgtype', $array);
        $this->assertArrayHasKey('file', $array);
        $this->assertSame('file', $array['msgtype']);
        $this->assertIsArray($array['file']);
        $this->assertCount(1, $array['file']);
        $this->assertArrayHasKey('media_id', $array['file']);
    }

    public function testBusinessScenarioContractFile(): void
    {
        // 测试业务场景：合同文件
        $file = self::getService(File::class);
        $contractFileMediaId = 'contract_2024_media_id';
        $file->setMediaId($contractFileMediaId);

        $array = $file->retrievePlainArray();
        $this->assertIsArray($array);
        $this->assertArrayHasKey('msgtype', $array);
        $this->assertArrayHasKey('file', $array);
        $fileArray = $array['file'];
        $this->assertIsArray($fileArray);
        $this->assertArrayHasKey('media_id', $fileArray);

        $this->assertSame('file', $array['msgtype']);
        $this->assertSame($contractFileMediaId, $fileArray['media_id']);

        // 验证符合企业微信API要求
        $this->assertArrayHasKey('msgtype', $array);
        $this->assertArrayHasKey('file', $array);
    }

    public function testBusinessScenarioProductCatalog(): void
    {
        // 测试业务场景：产品目录文件
        $file = self::getService(File::class);
        $catalogMediaId = 'product_catalog_pdf_media_456';
        $file->setMediaId($catalogMediaId);

        $array = $file->retrievePlainArray();
        $this->assertIsArray($array);
        $this->assertArrayHasKey('file', $array);
        $fileArray = $array['file'];
        $this->assertIsArray($fileArray);
        $this->assertArrayHasKey('media_id', $fileArray);
        $this->assertSame($catalogMediaId, $fileArray['media_id']);
    }

    public function testBusinessScenarioUserManual(): void
    {
        // 测试业务场景：用户手册
        $file = self::getService(File::class);
        $manualMediaId = 'user_manual_doc_media_789';
        $file->setMediaId($manualMediaId);

        $array = $file->retrievePlainArray();
        $this->assertIsArray($array);
        $this->assertArrayHasKey('msgtype', $array);
        $this->assertArrayHasKey('file', $array);
        $fileArray = $array['file'];
        $this->assertIsArray($fileArray);
        $this->assertArrayHasKey('media_id', $fileArray);

        $this->assertSame('file', $array['msgtype']);
        $this->assertSame($manualMediaId, $fileArray['media_id']);
    }

    public function testMultipleSetCalls(): void
    {
        // 测试多次设置值
        $file = self::getService(File::class);

        $firstMediaId = 'first_file_media_id';
        $secondMediaId = 'second_file_media_id';

        $file->setMediaId($firstMediaId);
        $this->assertSame($firstMediaId, $file->getMediaId());

        $file->setMediaId($secondMediaId);
        $this->assertSame($secondMediaId, $file->getMediaId());

        $array = $file->retrievePlainArray();
        $this->assertIsArray($array);
        $this->assertArrayHasKey('file', $array);
        $fileArray = $array['file'];
        $this->assertIsArray($fileArray);
        $this->assertArrayHasKey('media_id', $fileArray);
        $this->assertSame($secondMediaId, $fileArray['media_id']);
    }

    public function testRetrievePlainArrayDoesNotModifyOriginalData(): void
    {
        // 测试获取数组不会修改原始数据
        $file = self::getService(File::class);
        $originalMediaId = 'original_file_media_id';
        $file->setMediaId($originalMediaId);

        $array1 = $file->retrievePlainArray();
        $array2 = $file->retrievePlainArray();

        // 修改返回的数组不应影响原始数据
        $this->assertIsArray($array1);
        $this->assertArrayHasKey('file', $array1);
        $this->assertIsArray($array1['file']);
        $array1['file']['media_id'] = 'modified_media_id';
        $array1['msgtype'] = 'modified_type';

        $this->assertSame($originalMediaId, $file->getMediaId());
        $this->assertIsArray($array2);
        $this->assertArrayHasKey('file', $array2);
        $this->assertArrayHasKey('msgtype', $array2);
        $fileArray2 = $array2['file'];
        $this->assertIsArray($fileArray2);
        $this->assertArrayHasKey('media_id', $fileArray2);
        $this->assertSame($originalMediaId, $fileArray2['media_id']);
        $this->assertSame('file', $array2['msgtype']);
    }

    public function testImmutableBehavior(): void
    {
        // 测试不可变行为
        $file = self::getService(File::class);
        $mediaId = 'immutable_test_file_media';
        $file->setMediaId($mediaId);

        $array = $file->retrievePlainArray();

        // 修改数组不应影响file对象
        $this->assertIsArray($array);
        $this->assertArrayHasKey('file', $array);
        $this->assertIsArray($array['file']);
        $array['file']['media_id'] = 'changed_media_id';
        $array['msgtype'] = 'changed_type';
        $array['new_key'] = 'new_value';

        $this->assertSame($mediaId, $file->getMediaId());

        $newArray = $file->retrievePlainArray();
        $this->assertIsArray($newArray);
        $this->assertArrayHasKey('file', $newArray);
        $this->assertArrayHasKey('msgtype', $newArray);
        $fileArrayNew = $newArray['file'];
        $this->assertIsArray($fileArrayNew);
        $this->assertArrayHasKey('media_id', $fileArrayNew);
        $this->assertSame($mediaId, $fileArrayNew['media_id']);
        $this->assertSame('file', $newArray['msgtype']);
        $this->assertArrayNotHasKey('new_key', $newArray);
    }

    public function testMethodCallsAreIdempotent(): void
    {
        // 测试方法调用是幂等的
        $file = self::getService(File::class);
        $mediaId = 'idempotent_test_file_media';
        $file->setMediaId($mediaId);

        // 多次调用应该返回相同结果
        $mediaId1 = $file->getMediaId();
        $mediaId2 = $file->getMediaId();
        $this->assertSame($mediaId1, $mediaId2);

        $array1 = $file->retrievePlainArray();
        $array2 = $file->retrievePlainArray();
        $this->assertSame($array1, $array2);
    }

    public function testPlainArrayInterfaceImplementation(): void
    {
        // 测试PlainArrayInterface接口实现
        $file = self::getService(File::class);
        $file->setMediaId('interface_test_file_media');

        $array = $file->retrievePlainArray();
        // 移除冗余检查，直接验证返回的数组
        $this->assertCount(2, $array);
        $this->assertArrayHasKey('msgtype', $array);
        $this->assertArrayHasKey('file', $array);
    }
}

<?php

namespace WechatWorkExternalContactBundle\Tests\Request\Attachment;

use PHPUnit\Framework\TestCase;
use Tourze\Arrayable\PlainArrayInterface;
use WechatWorkExternalContactBundle\Request\Attachment\BaseAttachment;
use WechatWorkExternalContactBundle\Request\Attachment\File;

/**
 * File 附件测试
 */
class FileTest extends TestCase
{
    public function test_inheritance(): void
    {
        // 测试继承关系
        $file = new File();
        $this->assertInstanceOf(BaseAttachment::class, $file);
        $this->assertInstanceOf(PlainArrayInterface::class, $file);
    }

    public function test_mediaId_setterAndGetter(): void
    {
        // 测试媒体ID设置和获取
        $file = new File();
        $mediaId = 'file_media_id_123';

        $file->setMediaId($mediaId);
        $this->assertSame($mediaId, $file->getMediaId());
    }

    public function test_mediaId_withSpecialCharacters(): void
    {
        // 测试特殊字符媒体ID
        $file = new File();
        $specialMediaId = 'file_abc-123_test@domain.com';
        $file->setMediaId($specialMediaId);

        $this->assertSame($specialMediaId, $file->getMediaId());
    }

    public function test_mediaId_withLongString(): void
    {
        // 测试长字符串媒体ID
        $file = new File();
        $longMediaId = str_repeat('a', 255);
        $file->setMediaId($longMediaId);

        $this->assertSame($longMediaId, $file->getMediaId());
    }

    public function test_retrievePlainArray(): void
    {
        // 测试获取普通数组
        $file = new File();
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

    public function test_retrievePlainArray_structure(): void
    {
        // 测试数组结构
        $file = new File();
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

    public function test_businessScenario_contractFile(): void
    {
        // 测试业务场景：合同文件
        $file = new File();
        $contractFileMediaId = 'contract_2024_media_id';
        $file->setMediaId($contractFileMediaId);

        $array = $file->retrievePlainArray();

        $this->assertSame('file', $array['msgtype']);
        $this->assertSame($contractFileMediaId, $array['file']['media_id']);

        // 验证符合企业微信API要求
        $this->assertArrayHasKey('msgtype', $array);
        $this->assertArrayHasKey('file', $array);
    }

    public function test_businessScenario_productCatalog(): void
    {
        // 测试业务场景：产品目录文件
        $file = new File();
        $catalogMediaId = 'product_catalog_pdf_media_456';
        $file->setMediaId($catalogMediaId);

        $array = $file->retrievePlainArray();
        $this->assertSame($catalogMediaId, $array['file']['media_id']);
    }

    public function test_businessScenario_userManual(): void
    {
        // 测试业务场景：用户手册
        $file = new File();
        $manualMediaId = 'user_manual_doc_media_789';
        $file->setMediaId($manualMediaId);

        $array = $file->retrievePlainArray();

        $this->assertSame('file', $array['msgtype']);
        $this->assertSame($manualMediaId, $array['file']['media_id']);
    }

    public function test_multipleSetCalls(): void
    {
        // 测试多次设置值
        $file = new File();

        $firstMediaId = 'first_file_media_id';
        $secondMediaId = 'second_file_media_id';

        $file->setMediaId($firstMediaId);
        $this->assertSame($firstMediaId, $file->getMediaId());

        $file->setMediaId($secondMediaId);
        $this->assertSame($secondMediaId, $file->getMediaId());

        $array = $file->retrievePlainArray();
        $this->assertSame($secondMediaId, $array['file']['media_id']);
    }

    public function test_retrievePlainArrayDoesNotModifyOriginalData(): void
    {
        // 测试获取数组不会修改原始数据
        $file = new File();
        $originalMediaId = 'original_file_media_id';
        $file->setMediaId($originalMediaId);

        $array1 = $file->retrievePlainArray();
        $array2 = $file->retrievePlainArray();

        // 修改返回的数组不应影响原始数据
        $array1['file']['media_id'] = 'modified_media_id';
        $array1['msgtype'] = 'modified_type';

        $this->assertSame($originalMediaId, $file->getMediaId());
        $this->assertSame($originalMediaId, $array2['file']['media_id']);
        $this->assertSame('file', $array2['msgtype']);
    }

    public function test_immutableBehavior(): void
    {
        // 测试不可变行为
        $file = new File();
        $mediaId = 'immutable_test_file_media';
        $file->setMediaId($mediaId);

        $array = $file->retrievePlainArray();

        // 修改数组不应影响file对象
        $array['file']['media_id'] = 'changed_media_id';
        $array['msgtype'] = 'changed_type';
        $array['new_key'] = 'new_value';

        $this->assertSame($mediaId, $file->getMediaId());

        $newArray = $file->retrievePlainArray();
        $this->assertSame($mediaId, $newArray['file']['media_id']);
        $this->assertSame('file', $newArray['msgtype']);
        $this->assertArrayNotHasKey('new_key', $newArray);
    }

    public function test_methodCallsAreIdempotent(): void
    {
        // 测试方法调用是幂等的
        $file = new File();
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

    public function test_plainArrayInterfaceImplementation(): void
    {
        // 测试PlainArrayInterface接口实现
        $file = new File();
        $file->setMediaId('interface_test_file_media');

        $array = $file->retrievePlainArray();
        // 移除冗余检查，直接验证返回的数组
        $this->assertCount(2, $array);
        $this->assertArrayHasKey('msgtype', $array);
        $this->assertArrayHasKey('file', $array);
    }
}

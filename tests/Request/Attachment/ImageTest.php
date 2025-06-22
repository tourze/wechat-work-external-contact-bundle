<?php

namespace WechatWorkExternalContactBundle\Tests\Request\Attachment;

use PHPUnit\Framework\TestCase;
use Tourze\Arrayable\PlainArrayInterface;
use WechatWorkExternalContactBundle\Request\Attachment\BaseAttachment;
use WechatWorkExternalContactBundle\Request\Attachment\Image;

/**
 * Image 附件测试
 */
class ImageTest extends TestCase
{
    public function test_inheritance(): void
    {
        // 测试继承关系
        $image = new Image();
        $this->assertInstanceOf(BaseAttachment::class, $image);
        $this->assertInstanceOf(PlainArrayInterface::class, $image);
    }

    public function test_mediaId_setterAndGetter(): void
    {
        // 测试媒体ID设置和获取
        $image = new Image();
        $mediaId = 'image_media_id_123';

        $image->setMediaId($mediaId);
        $this->assertSame($mediaId, $image->getMediaId());
    }

    public function test_mediaId_withSpecialCharacters(): void
    {
        // 测试特殊字符媒体ID
        $image = new Image();
        $specialMediaId = 'image_abc-123_test@domain.com';
        $image->setMediaId($specialMediaId);

        $this->assertSame($specialMediaId, $image->getMediaId());
    }

    public function test_mediaId_withLongString(): void
    {
        // 测试长字符串媒体ID
        $image = new Image();
        $longMediaId = str_repeat('a', 255);
        $image->setMediaId($longMediaId);

        $this->assertSame($longMediaId, $image->getMediaId());
    }

    public function test_retrievePlainArray(): void
    {
        // 测试获取普通数组
        $image = new Image();
        $mediaId = 'test_image_media_id';
        $image->setMediaId($mediaId);

        $expected = [
            'msgtype' => 'image',
            'image' => [
                'media_id' => $mediaId,
            ],
        ];

        $this->assertSame($expected, $image->retrievePlainArray());
    }

    public function test_retrievePlainArray_structure(): void
    {
        // 测试数组结构
        $image = new Image();
        $image->setMediaId('structure_test_media');

        $array = $image->retrievePlainArray();

        $this->assertCount(2, $array);
        $this->assertArrayHasKey('msgtype', $array);
        $this->assertArrayHasKey('image', $array);
        $this->assertSame('image', $array['msgtype']);
        $this->assertIsArray($array['image']);
        $this->assertCount(1, $array['image']);
        $this->assertArrayHasKey('media_id', $array['image']);
    }

    public function test_createFromMediaId(): void
    {
        // 测试静态工厂方法
        $mediaId = 'factory_test_media_id';
        $image = Image::createFromMediaId($mediaId);

        $this->assertInstanceOf(Image::class, $image);
        $this->assertSame($mediaId, $image->getMediaId());
    }

    public function test_createFromMediaId_withDifferentValues(): void
    {
        // 测试不同值的静态工厂方法
        $mediaIds = [
            'simple_media',
            'media_with_numbers_123',
            'media-with-dashes',
            'media_with_underscores',
            str_repeat('long_media_', 10),
        ];

        foreach ($mediaIds as $mediaId) {
            $image = Image::createFromMediaId($mediaId);
            $this->assertSame($mediaId, $image->getMediaId());

            $array = $image->retrievePlainArray();
            $this->assertSame($mediaId, $array['image']['media_id']);
        }
    }

    public function test_businessScenario_welcomeImage(): void
    {
        // 测试业务场景：欢迎图片
        $image = new Image();
        $welcomeImageMediaId = 'welcome_image_2024_media_id';
        $image->setMediaId($welcomeImageMediaId);

        $array = $image->retrievePlainArray();

        $this->assertSame('image', $array['msgtype']);
        $this->assertSame($welcomeImageMediaId, $array['image']['media_id']);

        // 验证符合企业微信API要求
        $this->assertArrayHasKey('msgtype', $array);
        $this->assertArrayHasKey('image', $array);
    }

    public function test_businessScenario_productImage(): void
    {
        // 测试业务场景：产品图片
        $productImageMediaId = 'product_showcase_media_123';
        $image = Image::createFromMediaId($productImageMediaId);

        $this->assertSame($productImageMediaId, $image->getMediaId());

        $array = $image->retrievePlainArray();
        $this->assertSame($productImageMediaId, $array['image']['media_id']);
    }

    public function test_businessScenario_qrcodeImage(): void
    {
        // 测试业务场景：二维码图片
        $qrcodeMediaId = 'qrcode_contact_media_456';
        $image = Image::createFromMediaId($qrcodeMediaId);

        $array = $image->retrievePlainArray();

        $this->assertSame('image', $array['msgtype']);
        $this->assertSame($qrcodeMediaId, $array['image']['media_id']);
    }

    public function test_multipleSetCalls(): void
    {
        // 测试多次设置值
        $image = new Image();

        $firstMediaId = 'first_media_id';
        $secondMediaId = 'second_media_id';

        $image->setMediaId($firstMediaId);
        $this->assertSame($firstMediaId, $image->getMediaId());

        $image->setMediaId($secondMediaId);
        $this->assertSame($secondMediaId, $image->getMediaId());

        $array = $image->retrievePlainArray();
        $this->assertSame($secondMediaId, $array['image']['media_id']);
    }

    public function test_retrievePlainArrayDoesNotModifyOriginalData(): void
    {
        // 测试获取数组不会修改原始数据
        $image = new Image();
        $originalMediaId = 'original_media_id';
        $image->setMediaId($originalMediaId);

        $array1 = $image->retrievePlainArray();
        $array2 = $image->retrievePlainArray();

        // 修改返回的数组不应影响原始数据
        $array1['image']['media_id'] = 'modified_media_id';
        $array1['msgtype'] = 'modified_type';

        $this->assertSame($originalMediaId, $image->getMediaId());
        $this->assertSame($originalMediaId, $array2['image']['media_id']);
        $this->assertSame('image', $array2['msgtype']);
    }

    public function test_immutableBehavior(): void
    {
        // 测试不可变行为
        $image = new Image();
        $mediaId = 'immutable_test_media';
        $image->setMediaId($mediaId);

        $array = $image->retrievePlainArray();

        // 修改数组不应影响image对象
        $array['image']['media_id'] = 'changed_media_id';
        $array['msgtype'] = 'changed_type';
        $array['new_key'] = 'new_value';

        $this->assertSame($mediaId, $image->getMediaId());

        $newArray = $image->retrievePlainArray();
        $this->assertSame($mediaId, $newArray['image']['media_id']);
        $this->assertSame('image', $newArray['msgtype']);
        $this->assertArrayNotHasKey('new_key', $newArray);
    }

    public function test_methodCallsAreIdempotent(): void
    {
        // 测试方法调用是幂等的
        $image = new Image();
        $mediaId = 'idempotent_test_media';
        $image->setMediaId($mediaId);

        // 多次调用应该返回相同结果
        $mediaId1 = $image->getMediaId();
        $mediaId2 = $image->getMediaId();
        $this->assertSame($mediaId1, $mediaId2);

        $array1 = $image->retrievePlainArray();
        $array2 = $image->retrievePlainArray();
        $this->assertSame($array1, $array2);
    }

    public function test_factoryMethodIndependence(): void
    {
        // 测试工厂方法创建的对象独立性
        $mediaId1 = 'factory_media_1';
        $mediaId2 = 'factory_media_2';

        $image1 = Image::createFromMediaId($mediaId1);
        $image2 = Image::createFromMediaId($mediaId2);

        $this->assertNotSame($image1, $image2);
        $this->assertSame($mediaId1, $image1->getMediaId());
        $this->assertSame($mediaId2, $image2->getMediaId());

        // 修改一个不应影响另一个
        $image1->setMediaId('changed_media_1');
        $this->assertSame('changed_media_1', $image1->getMediaId());
        $this->assertSame($mediaId2, $image2->getMediaId());
    }

    public function test_plainArrayInterfaceImplementation(): void
    {
        // 测试PlainArrayInterface接口实现
        $image = new Image();
        $image->setMediaId('interface_test_media');

        $array = $image->retrievePlainArray();
        // 移除冗余检查，直接验证返回的数组
        $this->assertCount(2, $array);
        $this->assertArrayHasKey('msgtype', $array);
        $this->assertArrayHasKey('image', $array);
    }
}

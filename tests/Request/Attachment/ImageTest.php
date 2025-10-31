<?php

namespace WechatWorkExternalContactBundle\Tests\Request\Attachment;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatWorkExternalContactBundle\Request\Attachment\Image;

/**
 * Image 附件测试
 *
 * @internal
 */
#[CoversClass(Image::class)]
#[RunTestsInSeparateProcesses] final class ImageTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void        // 此测试不需要特殊的设置
    {
    }

    public function testInheritance(): void
    {
        // 测试基本功能
        $image = self::getService(Image::class);
        $this->assertNotNull($image);

        // 设置必需的属性后测试
        $image->setMediaId('test_media_id');
        $this->assertIsArray($image->retrievePlainArray());
    }

    public function testMediaIdSetterAndGetter(): void
    {
        // 测试媒体ID设置和获取
        $image = self::getService(Image::class);
        $mediaId = 'image_media_id_123';

        $image->setMediaId($mediaId);
        $this->assertSame($mediaId, $image->getMediaId());
    }

    public function testMediaIdWithSpecialCharacters(): void
    {
        // 测试特殊字符媒体ID
        $image = self::getService(Image::class);
        $specialMediaId = 'image_abc-123_test@domain.com';
        $image->setMediaId($specialMediaId);

        $this->assertSame($specialMediaId, $image->getMediaId());
    }

    public function testMediaIdWithLongString(): void
    {
        // 测试长字符串媒体ID
        $image = self::getService(Image::class);
        $longMediaId = str_repeat('a', 255);
        $image->setMediaId($longMediaId);

        $this->assertSame($longMediaId, $image->getMediaId());
    }

    public function testRetrievePlainArray(): void
    {
        // 测试获取普通数组
        $image = self::getService(Image::class);
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

    public function testRetrievePlainArrayStructure(): void
    {
        // 测试数组结构
        $image = self::getService(Image::class);
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

    public function testCreateFromMediaId(): void
    {
        // 测试通过依赖注入创建对象并设置媒体ID
        $mediaId = 'factory_test_media_id';
        $image = self::getService(Image::class);
        $image->setMediaId($mediaId);

        $this->assertInstanceOf(Image::class, $image);
        $this->assertSame($mediaId, $image->getMediaId());
    }

    public function testCreateFromMediaIdWithDifferentValues(): void
    {
        // 测试不同值的媒体ID设置
        $mediaIds = [
            'simple_media',
            'media_with_numbers_123',
            'media-with-dashes',
            'media_with_underscores',
            str_repeat('long_media_', 10),
        ];

        foreach ($mediaIds as $mediaId) {
            $image = self::getService(Image::class);
            $image->setMediaId($mediaId);
            $this->assertSame($mediaId, $image->getMediaId());

            $array = $image->retrievePlainArray();
            $this->assertIsArray($array);
            $this->assertArrayHasKey('image', $array);
            $imageArray = $array['image'];
            $this->assertIsArray($imageArray);
            $this->assertArrayHasKey('media_id', $imageArray);
            $this->assertSame($mediaId, $imageArray['media_id']);
        }
    }

    public function testBusinessScenarioWelcomeImage(): void
    {
        // 测试业务场景：欢迎图片
        $image = self::getService(Image::class);
        $welcomeImageMediaId = 'welcome_image_2024_media_id';
        $image->setMediaId($welcomeImageMediaId);

        $array = $image->retrievePlainArray();
        $this->assertIsArray($array);
        $this->assertArrayHasKey('msgtype', $array);
        $this->assertArrayHasKey('image', $array);
        $imageArray = $array['image'];
        $this->assertIsArray($imageArray);
        $this->assertArrayHasKey('media_id', $imageArray);

        $this->assertSame('image', $array['msgtype']);
        $this->assertSame($welcomeImageMediaId, $imageArray['media_id']);

        // 验证符合企业微信API要求
        $this->assertArrayHasKey('msgtype', $array);
        $this->assertArrayHasKey('image', $array);
    }

    public function testBusinessScenarioProductImage(): void
    {
        // 测试业务场景：产品图片
        $productImageMediaId = 'product_showcase_media_123';
        $image = self::getService(Image::class);
        $image->setMediaId($productImageMediaId);

        $this->assertSame($productImageMediaId, $image->getMediaId());

        $array = $image->retrievePlainArray();
        $this->assertIsArray($array);
        $this->assertArrayHasKey('image', $array);
        $imageArray = $array['image'];
        $this->assertIsArray($imageArray);
        $this->assertArrayHasKey('media_id', $imageArray);
        $this->assertSame($productImageMediaId, $imageArray['media_id']);
    }

    public function testBusinessScenarioQrcodeImage(): void
    {
        // 测试业务场景：二维码图片
        $qrcodeMediaId = 'qrcode_contact_media_456';
        $image = self::getService(Image::class);
        $image->setMediaId($qrcodeMediaId);

        $array = $image->retrievePlainArray();
        $this->assertIsArray($array);
        $this->assertArrayHasKey('msgtype', $array);
        $this->assertArrayHasKey('image', $array);
        $imageArray = $array['image'];
        $this->assertIsArray($imageArray);
        $this->assertArrayHasKey('media_id', $imageArray);

        $this->assertSame('image', $array['msgtype']);
        $this->assertSame($qrcodeMediaId, $imageArray['media_id']);
    }

    public function testMultipleSetCalls(): void
    {
        // 测试多次设置值
        $image = self::getService(Image::class);

        $firstMediaId = 'first_media_id';
        $secondMediaId = 'second_media_id';

        $image->setMediaId($firstMediaId);
        $this->assertSame($firstMediaId, $image->getMediaId());

        $image->setMediaId($secondMediaId);
        $this->assertSame($secondMediaId, $image->getMediaId());

        $array = $image->retrievePlainArray();
        $this->assertIsArray($array);
        $this->assertArrayHasKey('image', $array);
        $imageArray = $array['image'];
        $this->assertIsArray($imageArray);
        $this->assertArrayHasKey('media_id', $imageArray);
        $this->assertSame($secondMediaId, $imageArray['media_id']);
    }

    public function testRetrievePlainArrayDoesNotModifyOriginalData(): void
    {
        // 测试获取数组不会修改原始数据
        $image = self::getService(Image::class);
        $originalMediaId = 'original_media_id';
        $image->setMediaId($originalMediaId);

        $array1 = $image->retrievePlainArray();
        $array2 = $image->retrievePlainArray();

        // 修改返回的数组不应影响原始数据
        $this->assertIsArray($array1);
        $this->assertArrayHasKey('image', $array1);
        $this->assertIsArray($array1['image']);
        $array1['image']['media_id'] = 'modified_media_id';
        $array1['msgtype'] = 'modified_type';

        $this->assertSame($originalMediaId, $image->getMediaId());
        $this->assertIsArray($array2);
        $this->assertArrayHasKey('image', $array2);
        $this->assertArrayHasKey('msgtype', $array2);
        $imageArray2 = $array2['image'];
        $this->assertIsArray($imageArray2);
        $this->assertArrayHasKey('media_id', $imageArray2);
        $this->assertSame($originalMediaId, $imageArray2['media_id']);
        $this->assertSame('image', $array2['msgtype']);
    }

    public function testImmutableBehavior(): void
    {
        // 测试不可变行为
        $image = self::getService(Image::class);
        $mediaId = 'immutable_test_media';
        $image->setMediaId($mediaId);

        $array = $image->retrievePlainArray();

        // 修改数组不应影响image对象
        $this->assertIsArray($array);
        $this->assertArrayHasKey('image', $array);
        $this->assertIsArray($array['image']);
        $array['image']['media_id'] = 'changed_media_id';
        $array['msgtype'] = 'changed_type';
        $array['new_key'] = 'new_value';

        $this->assertSame($mediaId, $image->getMediaId());

        $newArray = $image->retrievePlainArray();
        $this->assertIsArray($newArray);
        $this->assertArrayHasKey('image', $newArray);
        $this->assertArrayHasKey('msgtype', $newArray);
        $imageArrayNew = $newArray['image'];
        $this->assertIsArray($imageArrayNew);
        $this->assertArrayHasKey('media_id', $imageArrayNew);
        $this->assertSame($mediaId, $imageArrayNew['media_id']);
        $this->assertSame('image', $newArray['msgtype']);
        $this->assertArrayNotHasKey('new_key', $newArray);
    }

    public function testMethodCallsAreIdempotent(): void
    {
        // 测试方法调用是幂等的
        $image = self::getService(Image::class);
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

    public function testServiceContainerBehavior(): void
    {
        // 测试通过服务容器获取的对象行为
        $mediaId1 = 'service_media_1';
        $mediaId2 = 'service_media_2';

        $image1 = self::getService(Image::class);
        $image2 = self::getService(Image::class);

        // 通过服务容器获取的是同一个实例（单例模式）
        $this->assertSame($image1, $image2);

        // 设置和获取媒体ID应该正常工作
        $image1->setMediaId($mediaId1);
        $this->assertSame($mediaId1, $image1->getMediaId());
        $this->assertSame($mediaId1, $image2->getMediaId()); // 因为是同一个实例

        // 修改会影响所有引用
        $image1->setMediaId($mediaId2);
        $this->assertSame($mediaId2, $image1->getMediaId());
        $this->assertSame($mediaId2, $image2->getMediaId());
    }

    public function testPlainArrayInterfaceImplementation(): void
    {
        // 测试PlainArrayInterface接口实现
        $image = self::getService(Image::class);
        $image->setMediaId('interface_test_media');

        $array = $image->retrievePlainArray();
        // 移除冗余检查，直接验证返回的数组
        $this->assertCount(2, $array);
        $this->assertArrayHasKey('msgtype', $array);
        $this->assertArrayHasKey('image', $array);
    }
}

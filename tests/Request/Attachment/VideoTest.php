<?php

namespace WechatWorkExternalContactBundle\Tests\Request\Attachment;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatWorkExternalContactBundle\Request\Attachment\Video;

/**
 * Video 附件测试
 *
 * @internal
 */
#[CoversClass(Video::class)]
#[RunTestsInSeparateProcesses] final class VideoTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void        // 此测试不需要特殊的设置
    {
    }

    public function testInheritance(): void
    {
        // 测试继承关系
        $video = self::getService(Video::class);
        $this->assertInstanceOf(PlainArrayInterface::class, $video);

        // 测试PlainArrayInterface接口的实际功能
        $video->setMediaId('test_video_media_id');
        $array = $video->retrievePlainArray();
        $this->assertIsArray($array);
        $this->assertArrayHasKey('msgtype', $array);
        $this->assertArrayHasKey('video', $array);
    }

    public function testMediaIdSetterAndGetter(): void
    {
        // 测试媒体ID设置和获取
        $video = self::getService(Video::class);
        $mediaId = 'video_media_id_123';

        $video->setMediaId($mediaId);
        $this->assertSame($mediaId, $video->getMediaId());
    }

    public function testMediaIdWithSpecialCharacters(): void
    {
        // 测试特殊字符媒体ID
        $video = self::getService(Video::class);
        $specialMediaId = 'video_abc-123_test@domain.com';
        $video->setMediaId($specialMediaId);

        $this->assertSame($specialMediaId, $video->getMediaId());
    }

    public function testMediaIdWithLongString(): void
    {
        // 测试长字符串媒体ID
        $video = self::getService(Video::class);
        $longMediaId = str_repeat('v', 255);
        $video->setMediaId($longMediaId);

        $this->assertSame($longMediaId, $video->getMediaId());
    }

    public function testRetrievePlainArray(): void
    {
        // 测试获取普通数组
        $video = self::getService(Video::class);
        $mediaId = 'test_video_media_id';
        $video->setMediaId($mediaId);

        $expected = [
            'msgtype' => 'video',
            'video' => [
                'media_id' => $mediaId,
            ],
        ];

        $this->assertSame($expected, $video->retrievePlainArray());
    }

    public function testRetrievePlainArrayStructure(): void
    {
        // 测试数组结构
        $video = self::getService(Video::class);
        $video->setMediaId('structure_test_media');

        $array = $video->retrievePlainArray();

        $this->assertCount(2, $array);
        $this->assertArrayHasKey('msgtype', $array);
        $this->assertArrayHasKey('video', $array);
        $this->assertSame('video', $array['msgtype']);
        $this->assertIsArray($array['video']);
        $this->assertCount(1, $array['video']);
        $this->assertArrayHasKey('media_id', $array['video']);
    }

    public function testBusinessScenarioProductDemoVideo(): void
    {
        // 测试业务场景：产品演示视频
        $video = self::getService(Video::class);
        $demoVideoMediaId = 'product_demo_2024_media_id';
        $video->setMediaId($demoVideoMediaId);

        $array = $video->retrievePlainArray();
        $this->assertIsArray($array);
        $this->assertArrayHasKey('msgtype', $array);
        $this->assertArrayHasKey('video', $array);
        $videoArray = $array['video'];
        $this->assertIsArray($videoArray);
        $this->assertArrayHasKey('media_id', $videoArray);

        $this->assertSame('video', $array['msgtype']);
        $this->assertSame($demoVideoMediaId, $videoArray['media_id']);

        // 验证符合企业微信API要求
        $this->assertArrayHasKey('msgtype', $array);
        $this->assertArrayHasKey('video', $array);
    }

    public function testBusinessScenarioTrainingVideo(): void
    {
        // 测试业务场景：培训视频
        $video = self::getService(Video::class);
        $trainingVideoMediaId = 'training_course_media_456';
        $video->setMediaId($trainingVideoMediaId);

        $array = $video->retrievePlainArray();
        $this->assertIsArray($array);
        $this->assertArrayHasKey('video', $array);
        $videoArray = $array['video'];
        $this->assertIsArray($videoArray);
        $this->assertArrayHasKey('media_id', $videoArray);
        $this->assertSame($trainingVideoMediaId, $videoArray['media_id']);
    }

    public function testBusinessScenarioWelcomeVideo(): void
    {
        // 测试业务场景：欢迎视频
        $video = self::getService(Video::class);
        $welcomeVideoMediaId = 'welcome_intro_media_789';
        $video->setMediaId($welcomeVideoMediaId);

        $array = $video->retrievePlainArray();
        $this->assertIsArray($array);
        $this->assertArrayHasKey('msgtype', $array);
        $this->assertArrayHasKey('video', $array);
        $videoArray = $array['video'];
        $this->assertIsArray($videoArray);
        $this->assertArrayHasKey('media_id', $videoArray);

        $this->assertSame('video', $array['msgtype']);
        $this->assertSame($welcomeVideoMediaId, $videoArray['media_id']);
    }

    public function testMultipleSetCalls(): void
    {
        // 测试多次设置值
        $video = self::getService(Video::class);

        $firstMediaId = 'first_video_media_id';
        $secondMediaId = 'second_video_media_id';

        $video->setMediaId($firstMediaId);
        $this->assertSame($firstMediaId, $video->getMediaId());

        $video->setMediaId($secondMediaId);
        $this->assertSame($secondMediaId, $video->getMediaId());

        $array = $video->retrievePlainArray();
        $this->assertIsArray($array);
        $this->assertArrayHasKey('video', $array);
        $videoArray = $array['video'];
        $this->assertIsArray($videoArray);
        $this->assertArrayHasKey('media_id', $videoArray);
        $this->assertSame($secondMediaId, $videoArray['media_id']);
    }

    public function testRetrievePlainArrayDoesNotModifyOriginalData(): void
    {
        // 测试获取数组不会修改原始数据
        $video = self::getService(Video::class);
        $originalMediaId = 'original_video_media_id';
        $video->setMediaId($originalMediaId);

        $array1 = $video->retrievePlainArray();
        $array2 = $video->retrievePlainArray();

        // 修改返回的数组不应影响原始数据
        $this->assertIsArray($array1);
        $this->assertArrayHasKey('video', $array1);
        $this->assertIsArray($array1['video']);
        $array1['video']['media_id'] = 'modified_media_id';
        $array1['msgtype'] = 'modified_type';

        $this->assertSame($originalMediaId, $video->getMediaId());
        $this->assertIsArray($array2);
        $this->assertArrayHasKey('video', $array2);
        $this->assertArrayHasKey('msgtype', $array2);
        $videoArray2 = $array2['video'];
        $this->assertIsArray($videoArray2);
        $this->assertArrayHasKey('media_id', $videoArray2);
        $this->assertSame($originalMediaId, $videoArray2['media_id']);
        $this->assertSame('video', $array2['msgtype']);
    }

    public function testImmutableBehavior(): void
    {
        // 测试不可变行为
        $video = self::getService(Video::class);
        $mediaId = 'immutable_test_video_media';
        $video->setMediaId($mediaId);

        $array = $video->retrievePlainArray();

        // 修改数组不应影响video对象
        $this->assertIsArray($array);
        $this->assertArrayHasKey('video', $array);
        $this->assertIsArray($array['video']);
        $array['video']['media_id'] = 'changed_media_id';
        $array['msgtype'] = 'changed_type';
        $array['new_key'] = 'new_value';

        $this->assertSame($mediaId, $video->getMediaId());

        $newArray = $video->retrievePlainArray();
        $this->assertIsArray($newArray);
        $this->assertArrayHasKey('video', $newArray);
        $this->assertArrayHasKey('msgtype', $newArray);
        $videoArrayNew = $newArray['video'];
        $this->assertIsArray($videoArrayNew);
        $this->assertArrayHasKey('media_id', $videoArrayNew);
        $this->assertSame($mediaId, $videoArrayNew['media_id']);
        $this->assertSame('video', $newArray['msgtype']);
        $this->assertArrayNotHasKey('new_key', $newArray);
    }

    public function testMethodCallsAreIdempotent(): void
    {
        // 测试方法调用是幂等的
        $video = self::getService(Video::class);
        $mediaId = 'idempotent_test_video_media';
        $video->setMediaId($mediaId);

        // 多次调用应该返回相同结果
        $mediaId1 = $video->getMediaId();
        $mediaId2 = $video->getMediaId();
        $this->assertSame($mediaId1, $mediaId2);

        $array1 = $video->retrievePlainArray();
        $array2 = $video->retrievePlainArray();
        $this->assertSame($array1, $array2);
    }

    public function testPlainArrayInterfaceImplementation(): void
    {
        // 测试PlainArrayInterface接口实现
        $video = self::getService(Video::class);
        $video->setMediaId('interface_test_media');

        $array = $video->retrievePlainArray();
        // 移除冗余检查，直接验证返回的数组
        $this->assertCount(2, $array);
        $this->assertArrayHasKey('msgtype', $array);
        $this->assertArrayHasKey('video', $array);
    }
}

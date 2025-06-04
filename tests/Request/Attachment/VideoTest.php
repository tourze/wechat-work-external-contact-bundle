<?php

namespace WechatWorkExternalContactBundle\Tests\Request\Attachment;

use PHPUnit\Framework\TestCase;
use Tourze\Arrayable\PlainArrayInterface;
use WechatWorkExternalContactBundle\Request\Attachment\BaseAttachment;
use WechatWorkExternalContactBundle\Request\Attachment\Video;

/**
 * Video 附件测试
 */
class VideoTest extends TestCase
{
    public function test_inheritance(): void
    {
        // 测试继承关系
        $video = new Video();
        $this->assertInstanceOf(BaseAttachment::class, $video);
        $this->assertInstanceOf(PlainArrayInterface::class, $video);
    }

    public function test_mediaId_setterAndGetter(): void
    {
        // 测试媒体ID设置和获取
        $video = new Video();
        $mediaId = 'video_media_id_123';
        
        $video->setMediaId($mediaId);
        $this->assertSame($mediaId, $video->getMediaId());
    }

    public function test_mediaId_withSpecialCharacters(): void
    {
        // 测试特殊字符媒体ID
        $video = new Video();
        $specialMediaId = 'video_abc-123_test@domain.com';
        $video->setMediaId($specialMediaId);
        
        $this->assertSame($specialMediaId, $video->getMediaId());
    }

    public function test_mediaId_withLongString(): void
    {
        // 测试长字符串媒体ID
        $video = new Video();
        $longMediaId = str_repeat('v', 255);
        $video->setMediaId($longMediaId);
        
        $this->assertSame($longMediaId, $video->getMediaId());
    }

    public function test_retrievePlainArray(): void
    {
        // 测试获取普通数组
        $video = new Video();
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

    public function test_retrievePlainArray_structure(): void
    {
        // 测试数组结构
        $video = new Video();
        $video->setMediaId('structure_test_video_media');
        
        $array = $video->retrievePlainArray();
        
        $this->assertIsArray($array);
        $this->assertCount(2, $array);
        $this->assertArrayHasKey('msgtype', $array);
        $this->assertArrayHasKey('video', $array);
        $this->assertSame('video', $array['msgtype']);
        $this->assertIsArray($array['video']);
        $this->assertCount(1, $array['video']);
        $this->assertArrayHasKey('media_id', $array['video']);
    }

    public function test_businessScenario_productDemoVideo(): void
    {
        // 测试业务场景：产品演示视频
        $video = new Video();
        $demoVideoMediaId = 'product_demo_2024_media_id';
        $video->setMediaId($demoVideoMediaId);
        
        $array = $video->retrievePlainArray();
        
        $this->assertSame('video', $array['msgtype']);
        $this->assertSame($demoVideoMediaId, $array['video']['media_id']);
        
        // 验证符合企业微信API要求
        $this->assertArrayHasKey('msgtype', $array);
        $this->assertArrayHasKey('video', $array);
    }

    public function test_businessScenario_trainingVideo(): void
    {
        // 测试业务场景：培训视频
        $video = new Video();
        $trainingVideoMediaId = 'training_course_media_456';
        $video->setMediaId($trainingVideoMediaId);
        
        $array = $video->retrievePlainArray();
        $this->assertSame($trainingVideoMediaId, $array['video']['media_id']);
    }

    public function test_businessScenario_welcomeVideo(): void
    {
        // 测试业务场景：欢迎视频
        $video = new Video();
        $welcomeVideoMediaId = 'welcome_intro_media_789';
        $video->setMediaId($welcomeVideoMediaId);
        
        $array = $video->retrievePlainArray();
        
        $this->assertSame('video', $array['msgtype']);
        $this->assertSame($welcomeVideoMediaId, $array['video']['media_id']);
    }

    public function test_multipleSetCalls(): void
    {
        // 测试多次设置值
        $video = new Video();
        
        $firstMediaId = 'first_video_media_id';
        $secondMediaId = 'second_video_media_id';
        
        $video->setMediaId($firstMediaId);
        $this->assertSame($firstMediaId, $video->getMediaId());
        
        $video->setMediaId($secondMediaId);
        $this->assertSame($secondMediaId, $video->getMediaId());
        
        $array = $video->retrievePlainArray();
        $this->assertSame($secondMediaId, $array['video']['media_id']);
    }

    public function test_retrievePlainArrayDoesNotModifyOriginalData(): void
    {
        // 测试获取数组不会修改原始数据
        $video = new Video();
        $originalMediaId = 'original_video_media_id';
        $video->setMediaId($originalMediaId);
        
        $array1 = $video->retrievePlainArray();
        $array2 = $video->retrievePlainArray();
        
        // 修改返回的数组不应影响原始数据
        $array1['video']['media_id'] = 'modified_media_id';
        $array1['msgtype'] = 'modified_type';
        
        $this->assertSame($originalMediaId, $video->getMediaId());
        $this->assertSame($originalMediaId, $array2['video']['media_id']);
        $this->assertSame('video', $array2['msgtype']);
    }

    public function test_immutableBehavior(): void
    {
        // 测试不可变行为
        $video = new Video();
        $mediaId = 'immutable_test_video_media';
        $video->setMediaId($mediaId);
        
        $array = $video->retrievePlainArray();
        
        // 修改数组不应影响video对象
        $array['video']['media_id'] = 'changed_media_id';
        $array['msgtype'] = 'changed_type';
        $array['new_key'] = 'new_value';
        
        $this->assertSame($mediaId, $video->getMediaId());
        
        $newArray = $video->retrievePlainArray();
        $this->assertSame($mediaId, $newArray['video']['media_id']);
        $this->assertSame('video', $newArray['msgtype']);
        $this->assertArrayNotHasKey('new_key', $newArray);
    }

    public function test_methodCallsAreIdempotent(): void
    {
        // 测试方法调用是幂等的
        $video = new Video();
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

    public function test_plainArrayInterfaceImplementation(): void
    {
        // 测试PlainArrayInterface接口实现
        $video = new Video();
        $video->setMediaId('interface_test_video_media');
        
        $this->assertTrue(method_exists($video, 'retrievePlainArray'));
        $this->assertTrue(is_callable([$video, 'retrievePlainArray']));
        
        $array = $video->retrievePlainArray();
        $this->assertIsArray($array);
    }
} 
<?php

namespace WechatWorkExternalContactBundle\Tests\Request\GroupWelcomeTemplate;

use PHPUnit\Framework\TestCase;
use WechatWorkExternalContactBundle\Request\GroupWelcomeTemplate\FieldTrait;

/**
 * GroupWelcomeTemplate FieldTrait 测试
 * 创建一个测试用的具体类来测试trait功能
 */
class FieldTraitTest extends TestCase
{
    private FieldTraitTestClass $instance;

    protected function setUp(): void
    {
        $this->instance = new FieldTraitTestClass();
    }

    public function test_notify_setterAndGetter(): void
    {
        // 测试通知设置和获取
        $this->instance->setNotify(true);
        $this->assertTrue($this->instance->isNotify());

        $this->instance->setNotify(false);
        $this->assertFalse($this->instance->isNotify());
    }

    public function test_textContent_setterAndGetter(): void
    {
        // 测试文本内容设置和获取
        $content = '欢迎加入我们的群聊！';
        $this->instance->setTextContent($content);
        $this->assertSame($content, $this->instance->getTextContent());

        $this->instance->setTextContent(null);
        $this->assertNull($this->instance->getTextContent());
    }

    public function test_imageMediaId_setterAndGetter(): void
    {
        // 测试图片媒体ID设置和获取
        $mediaId = 'media_image_123';
        $this->instance->setImageMediaId($mediaId);
        $this->assertSame($mediaId, $this->instance->getImageMediaId());

        $this->instance->setImageMediaId(null);
        $this->assertNull($this->instance->getImageMediaId());
    }

    public function test_imagePicUrl_setterAndGetter(): void
    {
        // 测试图片URL设置和获取
        $picUrl = 'https://example.com/image.jpg';
        $this->instance->setImagePicUrl($picUrl);
        $this->assertSame($picUrl, $this->instance->getImagePicUrl());

        $this->instance->setImagePicUrl(null);
        $this->assertNull($this->instance->getImagePicUrl());
    }

    public function test_linkTitle_setterAndGetter(): void
    {
        // 测试链接标题设置和获取
        $title = '了解更多信息';
        $this->instance->setLinkTitle($title);
        $this->assertSame($title, $this->instance->getLinkTitle());

        $this->instance->setLinkTitle(null);
        $this->assertNull($this->instance->getLinkTitle());
    }

    public function test_linkPicUrl_setterAndGetter(): void
    {
        // 测试链接图片URL设置和获取
        $picUrl = 'https://example.com/link-pic.jpg';
        $this->instance->setLinkPicUrl($picUrl);
        $this->assertSame($picUrl, $this->instance->getLinkPicUrl());

        $this->instance->setLinkPicUrl(null);
        $this->assertNull($this->instance->getLinkPicUrl());
    }

    public function test_linkDesc_setterAndGetter(): void
    {
        // 测试链接描述设置和获取
        $desc = '点击查看详细内容';
        $this->instance->setLinkDesc($desc);
        $this->assertSame($desc, $this->instance->getLinkDesc());

        $this->instance->setLinkDesc(null);
        $this->assertNull($this->instance->getLinkDesc());
    }

    public function test_linkUrl_setterAndGetter(): void
    {
        // 测试链接URL设置和获取
        $url = 'https://example.com/detail';
        $this->instance->setLinkUrl($url);
        $this->assertSame($url, $this->instance->getLinkUrl());

        $this->instance->setLinkUrl(null);
        $this->assertNull($this->instance->getLinkUrl());
    }

    public function test_miniprogramTitle_setterAndGetter(): void
    {
        // 测试小程序标题设置和获取
        $title = '小程序标题';
        $this->instance->setMiniprogramTitle($title);
        $this->assertSame($title, $this->instance->getMiniprogramTitle());

        $this->instance->setMiniprogramTitle(null);
        $this->assertNull($this->instance->getMiniprogramTitle());
    }

    public function test_miniprogramPicMediaId_setterAndGetter(): void
    {
        // 测试小程序图片媒体ID设置和获取
        $mediaId = 'miniprogram_media_123';
        $this->instance->setMiniprogramPicMediaId($mediaId);
        $this->assertSame($mediaId, $this->instance->getMiniprogramPicMediaId());

        $this->instance->setMiniprogramPicMediaId(null);
        $this->assertNull($this->instance->getMiniprogramPicMediaId());
    }

    public function test_miniprogramAppId_setterAndGetter(): void
    {
        // 测试小程序AppID设置和获取
        $appId = 'wx1234567890abcdef';
        $this->instance->setMiniprogramAppId($appId);
        $this->assertSame($appId, $this->instance->getMiniprogramAppId());

        $this->instance->setMiniprogramAppId(null);
        $this->assertNull($this->instance->getMiniprogramAppId());
    }

    public function test_miniprogramPage_setterAndGetter(): void
    {
        // 测试小程序页面路径设置和获取
        $page = 'pages/index/index';
        $this->instance->setMiniprogramPage($page);
        $this->assertSame($page, $this->instance->getMiniprogramPage());

        $this->instance->setMiniprogramPage(null);
        $this->assertNull($this->instance->getMiniprogramPage());
    }

    public function test_fileMediaId_setterAndGetter(): void
    {
        // 测试文件媒体ID设置和获取
        $mediaId = 'file_media_123';
        $this->instance->setFileMediaId($mediaId);
        $this->assertSame($mediaId, $this->instance->getFileMediaId());

        $this->instance->setFileMediaId(null);
        $this->assertNull($this->instance->getFileMediaId());
    }

    public function test_videoMediaId_setterAndGetter(): void
    {
        // 测试视频媒体ID设置和获取
        $mediaId = 'video_media_123';
        $this->instance->setVideoMediaId($mediaId);
        $this->assertSame($mediaId, $this->instance->getVideoMediaId());

        $this->instance->setVideoMediaId(null);
        $this->assertNull($this->instance->getVideoMediaId());
    }

    public function test_getFieldJson_basicNotifyOnly(): void
    {
        // 测试只有notify的基本配置
        $this->instance->setNotify(true);

        $json = $this->instance->getFieldJson();

        $this->assertIsArray($json);
        $this->assertArrayHasKey('notify', $json);
        $this->assertSame(1, $json['notify']);
        $this->assertCount(1, $json);
    }

    public function test_getFieldJson_notifyFalse(): void
    {
        // 测试notify为false的情况
        $this->instance->setNotify(false);

        $json = $this->instance->getFieldJson();

        $this->assertArrayHasKey('notify', $json);
        $this->assertSame(0, $json['notify']);
    }

    public function test_getFieldJson_withTextContent(): void
    {
        // 测试包含文本内容的JSON输出
        $this->instance->setNotify(true);
        $this->instance->setTextContent('欢迎加入群聊！');

        $json = $this->instance->getFieldJson();

        $this->assertArrayHasKey('notify', $json);
        $this->assertArrayHasKey('text', $json);
        $this->assertSame(1, $json['notify']);
        $this->assertIsArray($json['text']);
        $this->assertArrayHasKey('content', $json['text']);
        $this->assertSame('欢迎加入群聊！', $json['text']['content']);
    }

    public function test_getFieldJson_withImageMediaId(): void
    {
        // 测试包含图片媒体ID的JSON输出
        $this->instance->setNotify(true);
        $this->instance->setImageMediaId('image_media_123');

        $json = $this->instance->getFieldJson();

        $this->assertArrayHasKey('image', $json);
        $this->assertIsArray($json['image']);
        $this->assertArrayHasKey('media_id', $json['image']);
        $this->assertSame('image_media_123', $json['image']['media_id']);
    }

    public function test_getFieldJson_withImagePicUrl(): void
    {
        // 测试包含图片URL的JSON输出
        $this->instance->setNotify(true);
        $this->instance->setImagePicUrl('https://example.com/image.jpg');

        $json = $this->instance->getFieldJson();

        $this->assertArrayHasKey('image', $json);
        $this->assertIsArray($json['image']);
        $this->assertArrayHasKey('pic_url', $json['image']);
        $this->assertSame('https://example.com/image.jpg', $json['image']['pic_url']);
    }

    public function test_getFieldJson_withLink(): void
    {
        // 测试包含链接的JSON输出
        $this->instance->setNotify(true);
        $this->instance->setLinkTitle('了解更多');
        $this->instance->setLinkPicUrl('https://example.com/pic.jpg');
        $this->instance->setLinkDesc('点击了解详情');
        $this->instance->setLinkUrl('https://example.com/detail');

        $json = $this->instance->getFieldJson();

        $this->assertArrayHasKey('link', $json);
        $this->assertIsArray($json['link']);
        $this->assertArrayHasKey('title', $json['link']);
        $this->assertArrayHasKey('picurl', $json['link']);
        $this->assertArrayHasKey('desc', $json['link']);
        $this->assertArrayHasKey('url', $json['link']);
        $this->assertSame('了解更多', $json['link']['title']);
        $this->assertSame('https://example.com/pic.jpg', $json['link']['picurl']);
        $this->assertSame('点击了解详情', $json['link']['desc']);
        $this->assertSame('https://example.com/detail', $json['link']['url']);
    }

    public function test_getFieldJson_withMiniprogram(): void
    {
        // 测试包含小程序的JSON输出
        $this->instance->setNotify(true);
        $this->instance->setMiniprogramTitle('小程序标题');
        $this->instance->setMiniprogramPicMediaId('miniprogram_pic_123');
        $this->instance->setMiniprogramAppId('wx1234567890abcdef');
        $this->instance->setMiniprogramPage('pages/index/index');

        $json = $this->instance->getFieldJson();

        $this->assertArrayHasKey('miniprogram', $json);
        $this->assertIsArray($json['miniprogram']);
        $this->assertArrayHasKey('title', $json['miniprogram']);
        $this->assertArrayHasKey('pic_media_id', $json['miniprogram']);
        $this->assertArrayHasKey('appid', $json['miniprogram']);
        $this->assertArrayHasKey('page', $json['miniprogram']);
        $this->assertSame('小程序标题', $json['miniprogram']['title']);
        $this->assertSame('miniprogram_pic_123', $json['miniprogram']['pic_media_id']);
        $this->assertSame('wx1234567890abcdef', $json['miniprogram']['appid']);
        $this->assertSame('pages/index/index', $json['miniprogram']['page']);
    }

    public function test_getFieldJson_withFile(): void
    {
        // 测试包含文件的JSON输出
        $this->instance->setNotify(true);
        $this->instance->setFileMediaId('file_media_123');

        $json = $this->instance->getFieldJson();

        $this->assertArrayHasKey('file', $json);
        $this->assertIsArray($json['file']);
        $this->assertArrayHasKey('media_id', $json['file']);
        $this->assertSame('file_media_123', $json['file']['media_id']);
    }

    public function test_getFieldJson_withVideo(): void
    {
        // 测试包含视频的JSON输出
        $this->instance->setNotify(true);
        $this->instance->setVideoMediaId('video_media_123');

        $json = $this->instance->getFieldJson();

        $this->assertArrayHasKey('video', $json);
        $this->assertIsArray($json['video']);
        $this->assertArrayHasKey('media_id', $json['video']);
        $this->assertSame('video_media_123', $json['video']['media_id']);
    }

    public function test_getFieldJson_linkWithoutUrl(): void
    {
        // 测试没有URL的链接不会被包含
        $this->instance->setNotify(true);
        $this->instance->setLinkTitle('标题');
        $this->instance->setLinkDesc('描述');
        // 没有设置linkUrl

        $json = $this->instance->getFieldJson();

        $this->assertArrayNotHasKey('link', $json);
    }

    public function test_getFieldJson_miniprogramWithoutPage(): void
    {
        // 测试没有页面的小程序不会被包含
        $this->instance->setNotify(true);
        $this->instance->setMiniprogramTitle('标题');
        $this->instance->setMiniprogramAppId('wx123');
        // 没有设置miniprogramPage

        $json = $this->instance->getFieldJson();

        $this->assertArrayNotHasKey('miniprogram', $json);
    }

    public function test_getFieldJson_multipleContentTypes(): void
    {
        // 测试多种内容类型同时存在
        $this->instance->setNotify(false);
        $this->instance->setTextContent('欢迎消息');
        $this->instance->setImageMediaId('img_123');
        $this->instance->setLinkTitle('链接标题');
        $this->instance->setLinkUrl('https://example.com');
        $this->instance->setFileMediaId('file_123');

        $json = $this->instance->getFieldJson();

        $this->assertArrayHasKey('notify', $json);
        $this->assertArrayHasKey('text', $json);
        $this->assertArrayHasKey('image', $json);
        $this->assertArrayHasKey('link', $json);
        $this->assertArrayHasKey('file', $json);
        $this->assertSame(0, $json['notify']);
    }

    public function test_createFromEntity(): void
    {
        // 测试从实体创建的静态方法存在
        $this->assertTrue(method_exists(FieldTraitTestClass::class, 'createFromEntity'));
        $this->assertTrue(is_callable([FieldTraitTestClass::class, 'createFromEntity']));
        
        // 验证方法是静态的
        $reflection = new \ReflectionMethod(FieldTraitTestClass::class, 'createFromEntity');
        $this->assertTrue($reflection->isStatic());
    }

    public function test_businessScenario_textOnlyWelcome(): void
    {
        // 测试业务场景：纯文本欢迎语
        $this->instance->setNotify(true);
        $this->instance->setTextContent('欢迎加入我们的团队群聊！期待与大家一起合作。');

        $json = $this->instance->getFieldJson();

        $this->assertSame(1, $json['notify']);
        $this->assertArrayHasKey('text', $json);
        $this->assertSame('欢迎加入我们的团队群聊！期待与大家一起合作。', $json['text']['content']);
        $this->assertCount(2, $json); // 只有notify和text
    }

    public function test_businessScenario_richMediaWelcome(): void
    {
        // 测试业务场景：富媒体欢迎语
        $this->instance->setNotify(true);
        $this->instance->setTextContent('欢迎加入！');
        $this->instance->setImageMediaId('welcome_image_123');
        $this->instance->setLinkTitle('了解公司');
        $this->instance->setLinkPicUrl('https://company.com/logo.jpg');
        $this->instance->setLinkDesc('点击了解我们公司');
        $this->instance->setLinkUrl('https://company.com/about');

        $json = $this->instance->getFieldJson();

        $this->assertArrayHasKey('text', $json);
        $this->assertArrayHasKey('image', $json);
        $this->assertArrayHasKey('link', $json);
        $this->assertSame('welcome_image_123', $json['image']['media_id']);
        $this->assertSame('了解公司', $json['link']['title']);
    }

    public function test_businessScenario_miniprogramWelcome(): void
    {
        // 测试业务场景：小程序欢迎语
        $this->instance->setNotify(false);
        $this->instance->setTextContent('欢迎使用我们的小程序！');
        $this->instance->setMiniprogramTitle('团队协作工具');
        $this->instance->setMiniprogramPicMediaId('miniprogram_cover_123');
        $this->instance->setMiniprogramAppId('wxabcd1234efgh5678');
        $this->instance->setMiniprogramPage('pages/welcome/welcome');

        $json = $this->instance->getFieldJson();

        $this->assertSame(0, $json['notify']);
        $this->assertArrayHasKey('miniprogram', $json);
        $this->assertSame('团队协作工具', $json['miniprogram']['title']);
        $this->assertSame('wxabcd1234efgh5678', $json['miniprogram']['appid']);
        $this->assertSame('pages/welcome/welcome', $json['miniprogram']['page']);
    }

    public function test_textContentMaxLength(): void
    {
        // 测试文本内容最大长度（3000字节）
        $this->instance->setNotify(true);
        $longText = str_repeat('欢迎', 1000); // 3000字节
        $this->instance->setTextContent($longText);

        $this->assertSame($longText, $this->instance->getTextContent());
        $this->assertSame(6000, strlen($this->instance->getTextContent())); // UTF-8中文每个字符3字节
        
        $json = $this->instance->getFieldJson();
        $this->assertSame($longText, $json['text']['content']);
    }

    public function test_linkTitleMaxLength(): void
    {
        // 测试链接标题最大长度（128字节）
        $this->instance->setNotify(true);
        $longTitle = str_repeat('标', 42) . '题'; // 约128字节
        $this->instance->setLinkTitle($longTitle);
        $this->instance->setLinkUrl('https://example.com');

        $this->assertSame($longTitle, $this->instance->getLinkTitle());
        
        $json = $this->instance->getFieldJson();
        $this->assertSame($longTitle, $json['link']['title']);
    }

    public function test_linkDescMaxLength(): void
    {
        // 测试链接描述最大长度（512字节）
        $this->instance->setNotify(true);
        $longDesc = str_repeat('描述', 85) . '述'; // 约512字节
        $this->instance->setLinkDesc($longDesc);
        $this->instance->setLinkUrl('https://example.com');

        $this->assertSame($longDesc, $this->instance->getLinkDesc());
        
        $json = $this->instance->getFieldJson();
        $this->assertSame($longDesc, $json['link']['desc']);
    }

    public function test_miniprogramTitleMaxLength(): void
    {
        // 测试小程序标题最大长度（64字节）
        $this->instance->setNotify(true);
        $longTitle = str_repeat('程', 21) . '序'; // 约64字节
        $this->instance->setMiniprogramTitle($longTitle);
        $this->instance->setMiniprogramPage('pages/index');

        $this->assertSame($longTitle, $this->instance->getMiniprogramTitle());
        
        $json = $this->instance->getFieldJson();
        $this->assertSame($longTitle, $json['miniprogram']['title']);
    }

    public function test_specialCharactersInContent(): void
    {
        // 测试内容中的特殊字符
        $this->instance->setNotify(true);
        $specialContent = '欢迎！包含特殊字符："\'&<>{}[]()@#$%^*+=|\\';
        $this->instance->setTextContent($specialContent);

        $this->assertSame($specialContent, $this->instance->getTextContent());
        
        $json = $this->instance->getFieldJson();
        $this->assertSame($specialContent, $json['text']['content']);
    }

    public function test_urlValidation(): void
    {
        // 测试URL格式
        $this->instance->setNotify(true);
        $validUrls = [
            'https://example.com',
            'http://test.com/path?param=value',
            'https://subdomain.example.com/path/to/resource#anchor'
        ];

        foreach ($validUrls as $url) {
            $this->instance->setLinkUrl($url);
            $this->assertSame($url, $this->instance->getLinkUrl());
            
            $json = $this->instance->getFieldJson();
            $this->assertSame($url, $json['link']['url']);
        }
    }
}

/**
 * 测试用的具体类，使用FieldTrait trait
 */
class FieldTraitTestClass
{
    use FieldTrait;
    
    private ?string $agent = null;
    
    public function getAgent(): ?string
    {
        return $this->agent;
    }
    
    public function setAgent(?string $agent): void
    {
        $this->agent = $agent;
    }
} 
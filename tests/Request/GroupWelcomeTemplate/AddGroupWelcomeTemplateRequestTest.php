<?php

namespace WechatWorkExternalContactBundle\Tests\Request\GroupWelcomeTemplate;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkBundle\Request\AgentAware;
use WechatWorkExternalContactBundle\Request\GroupWelcomeTemplate\AddGroupWelcomeTemplateRequest;

/**
 * AddGroupWelcomeTemplateRequest 测试
 */
class AddGroupWelcomeTemplateRequestTest extends TestCase
{
    private AddGroupWelcomeTemplateRequest $request;

    protected function setUp(): void
    {
        $this->request = new AddGroupWelcomeTemplateRequest();
    }

    public function test_inheritance(): void
    {
        // 测试继承关系
        $this->assertInstanceOf(ApiRequest::class, $this->request);
    }

    public function test_traits(): void
    {
        // 测试使用的trait
        $this->assertContains(AgentAware::class, class_uses($this->request));
    }

    public function test_getRequestPath(): void
    {
        // 测试请求路径
        $expectedPath = '/cgi-bin/externalcontact/group_welcome_template/add';
        $this->assertSame($expectedPath, $this->request->getRequestPath());
    }

    public function test_agent_setterAndGetter(): void
    {
        // 测试AgentAware trait的功能存在性
        $this->assertTrue(method_exists($this->request, 'setAgent'));
        $this->assertTrue(method_exists($this->request, 'getAgent'));
        $this->assertTrue(is_callable([$this->request, 'setAgent']));
        $this->assertTrue(is_callable([$this->request, 'getAgent']));
    }

    public function test_notify_functionality(): void
    {
        // 测试notify字段功能
        $this->request->setNotify(true);
        $this->assertTrue($this->request->isNotify());

        $this->request->setNotify(false);
        $this->assertFalse($this->request->isNotify());
    }

    public function test_textContent_functionality(): void
    {
        // 测试文本内容功能
        $content = '欢迎加入我们的群聊！';
        $this->request->setTextContent($content);
        $this->assertSame($content, $this->request->getTextContent());
    }

    public function test_getRequestOptions_basicConfiguration(): void
    {
        // 测试基本请求选项
        $this->request->setNotify(true);
        $this->request->setTextContent('测试欢迎语');

        $options = $this->request->getRequestOptions();

        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertArrayHasKey('notify', $options['json']);
        $this->assertArrayHasKey('text', $options['json']);
        $this->assertSame(1, $options['json']['notify']);
        $this->assertSame('测试欢迎语', $options['json']['text']['content']);
    }

    public function test_getRequestOptions_withAllFields(): void
    {
        // 测试包含所有字段的请求选项
        $this->request->setNotify(false);
        $this->request->setTextContent('完整欢迎语');
        $this->request->setImageMediaId('img_123');
        $this->request->setLinkTitle('链接标题');
        $this->request->setLinkUrl('https://example.com');
        $this->request->setMiniprogramTitle('小程序标题');
        $this->request->setMiniprogramPage('pages/index');
        $this->request->setFileMediaId('file_123');
        $this->request->setVideoMediaId('video_123');

        $options = $this->request->getRequestOptions();
        $json = $options['json'];

        $this->assertSame(0, $json['notify']);
        $this->assertArrayHasKey('text', $json);
        $this->assertArrayHasKey('image', $json);
        $this->assertArrayHasKey('link', $json);
        $this->assertArrayHasKey('miniprogram', $json);
        $this->assertArrayHasKey('file', $json);
        $this->assertArrayHasKey('video', $json);
    }

    public function test_businessScenario_textOnlyWelcome(): void
    {
        // 测试业务场景：纯文本欢迎语添加
        $this->request->setNotify(true);
        $this->request->setTextContent('欢迎新成员加入我们的工作群！期待大家一起努力。');

        $options = $this->request->getRequestOptions();
        $json = $options['json'];

        $this->assertSame(1, $json['notify']);
        $this->assertSame('欢迎新成员加入我们的工作群！期待大家一起努力。', $json['text']['content']);
        $this->assertCount(2, $json); // 只有notify和text
    }

    public function test_businessScenario_richMediaWelcome(): void
    {
        // 测试业务场景：富媒体欢迎语添加
        $this->request->setNotify(true);
        $this->request->setTextContent('欢迎加入公司大家庭！');
        $this->request->setImageMediaId('company_welcome_image');
        $this->request->setLinkTitle('员工手册');
        $this->request->setLinkPicUrl('https://company.com/handbook-cover.jpg');
        $this->request->setLinkDesc('查看完整的员工入职指南');
        $this->request->setLinkUrl('https://company.com/employee-handbook');

        $options = $this->request->getRequestOptions();
        $json = $options['json'];

        $this->assertArrayHasKey('text', $json);
        $this->assertArrayHasKey('image', $json);
        $this->assertArrayHasKey('link', $json);
        $this->assertSame('company_welcome_image', $json['image']['media_id']);
        $this->assertSame('员工手册', $json['link']['title']);
    }

    public function test_businessScenario_miniprogramWelcome(): void
    {
        // 测试业务场景：小程序欢迎语添加
        $this->request->setNotify(false);
        $this->request->setTextContent('欢迎使用我们的企业应用！');
        $this->request->setMiniprogramTitle('企业工作台');
        $this->request->setMiniprogramPicMediaId('app_cover_media');
        $this->request->setMiniprogramAppId('wxabcd1234567890ef');
        $this->request->setMiniprogramPage('pages/welcome/welcome');

        $options = $this->request->getRequestOptions();
        $json = $options['json'];

        $this->assertSame(0, $json['notify']);
        $this->assertArrayHasKey('miniprogram', $json);
        $this->assertSame('企业工作台', $json['miniprogram']['title']);
        $this->assertSame('wxabcd1234567890ef', $json['miniprogram']['appid']);
    }

    public function test_businessScenario_fileAndVideoWelcome(): void
    {
        // 测试业务场景：文件和视频欢迎语添加
        $this->request->setNotify(true);
        $this->request->setTextContent('新员工培训资料');
        $this->request->setFileMediaId('training_doc_123');
        $this->request->setVideoMediaId('training_video_456');

        $options = $this->request->getRequestOptions();
        $json = $options['json'];

        $this->assertArrayHasKey('file', $json);
        $this->assertArrayHasKey('video', $json);
        $this->assertSame('training_doc_123', $json['file']['media_id']);
        $this->assertSame('training_video_456', $json['video']['media_id']);
    }

    public function test_emptyRequestOptions(): void
    {
        // 测试空请求选项（只有默认notify）
        $options = $this->request->getRequestOptions();
        $json = $options['json'];

        $this->assertIsArray($json);
        $this->assertArrayHasKey('notify', $json);
        $this->assertSame(1, $json['notify']); // 默认为true
        $this->assertCount(1, $json);
    }

    public function test_requestOptionsReturnFormat(): void
    {
        // 测试请求选项返回格式
        $this->request->setTextContent('测试内容');
        $options = $this->request->getRequestOptions();

        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
    }

    public function test_imageMediaIdVsPicUrl(): void
    {
        // 测试图片媒体ID和图片URL的处理
        $this->request->setNotify(true);
        
        // 设置媒体ID
        $this->request->setImageMediaId('media_123');
        $options1 = $this->request->getRequestOptions();
        $this->assertArrayHasKey('media_id', $options1['json']['image']);
        
        // 设置图片URL
        $this->request->setImageMediaId(null);
        $this->request->setImagePicUrl('https://example.com/pic.jpg');
        $options2 = $this->request->getRequestOptions();
        $this->assertArrayHasKey('pic_url', $options2['json']['image']);
    }

    public function test_linkRequiresUrl(): void
    {
        // 测试链接需要URL才能生效
        $this->request->setNotify(true);
        $this->request->setLinkTitle('标题');
        $this->request->setLinkDesc('描述');
        // 不设置URL

        $options = $this->request->getRequestOptions();
        $this->assertArrayNotHasKey('link', $options['json']);

        // 设置URL后链接生效
        $this->request->setLinkUrl('https://example.com');
        $options = $this->request->getRequestOptions();
        $this->assertArrayHasKey('link', $options['json']);
    }

    public function test_miniprogramRequiresPage(): void
    {
        // 测试小程序需要页面路径才能生效
        $this->request->setNotify(true);
        $this->request->setMiniprogramTitle('标题');
        $this->request->setMiniprogramAppId('wx123');
        // 不设置页面

        $options = $this->request->getRequestOptions();
        $this->assertArrayNotHasKey('miniprogram', $options['json']);

        // 设置页面后小程序生效
        $this->request->setMiniprogramPage('pages/index');
        $options = $this->request->getRequestOptions();
        $this->assertArrayHasKey('miniprogram', $options['json']);
    }

    public function test_agentFieldHandling(): void
    {
        // 测试代理字段处理 - 只检查方法存在性
        $this->assertTrue(method_exists($this->request, 'setAgent'));
        $this->assertTrue(method_exists($this->request, 'getAgent'));
        
        // 代理字段不应出现在JSON中
        $options = $this->request->getRequestOptions();
        $this->assertArrayNotHasKey('agent', $options['json']);
    }

    public function test_requestPath_immutable(): void
    {
        // 测试请求路径不可变
        $path1 = $this->request->getRequestPath();
        $this->request->setTextContent('测试');
        $path2 = $this->request->getRequestPath();
        
        $this->assertSame($path1, $path2);
        $this->assertSame('/cgi-bin/externalcontact/group_welcome_template/add', $path1);
    }

    public function test_specialCharactersInContent(): void
    {
        // 测试内容中的特殊字符
        $specialContent = '欢迎！包含特殊字符："\'&<>{}[]()@#$%^*+=|\\';
        $this->request->setTextContent($specialContent);

        $options = $this->request->getRequestOptions();
        $this->assertSame($specialContent, $options['json']['text']['content']);
    }

    public function test_longContentHandling(): void
    {
        // 测试长内容处理
        $longContent = str_repeat('欢迎新成员！', 200); // 很长的欢迎语
        $this->request->setTextContent($longContent);

        $options = $this->request->getRequestOptions();
        $this->assertSame($longContent, $options['json']['text']['content']);
    }

    public function test_multipleSetOperations(): void
    {
        // 测试多次设置操作
        $this->request->setNotify(true);
        $this->request->setNotify(false);
        $this->assertFalse($this->request->isNotify());

        $this->request->setTextContent('第一条消息');
        $this->request->setTextContent('第二条消息');
        $this->assertSame('第二条消息', $this->request->getTextContent());
    }
} 
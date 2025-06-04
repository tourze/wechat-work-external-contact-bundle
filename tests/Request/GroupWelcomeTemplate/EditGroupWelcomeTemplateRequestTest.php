<?php

namespace WechatWorkExternalContactBundle\Tests\Request\GroupWelcomeTemplate;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkBundle\Request\AgentAware;
use WechatWorkExternalContactBundle\Request\GroupWelcomeTemplate\EditGroupWelcomeTemplateRequest;

/**
 * EditGroupWelcomeTemplateRequest 测试
 */
class EditGroupWelcomeTemplateRequestTest extends TestCase
{
    private EditGroupWelcomeTemplateRequest $request;

    protected function setUp(): void
    {
        $this->request = new EditGroupWelcomeTemplateRequest();
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
        $expectedPath = '/cgi-bin/externalcontact/group_welcome_template/edit';
        $this->assertSame($expectedPath, $this->request->getRequestPath());
    }

    public function test_templateId_setterAndGetter(): void
    {
        // 测试模板ID设置和获取
        $templateId = 'template_123456';
        $this->request->setTemplateId($templateId);
        $this->assertSame($templateId, $this->request->getTemplateId());
    }

    public function test_agent_methods(): void
    {
        // 测试AgentAware trait的方法存在性
        $this->assertTrue(method_exists($this->request, 'setAgent'));
        $this->assertTrue(method_exists($this->request, 'getAgent'));
        $this->assertTrue(is_callable([$this->request, 'setAgent']));
        $this->assertTrue(is_callable([$this->request, 'getAgent']));
    }

    public function test_fieldTrait_functionality(): void
    {
        // 测试FieldTrait功能
        $this->request->setNotify(true);
        $this->assertTrue($this->request->isNotify());

        $this->request->setTextContent('修改后的欢迎语');
        $this->assertSame('修改后的欢迎语', $this->request->getTextContent());
    }

    public function test_getRequestOptions_withTemplateId(): void
    {
        // 测试包含模板ID的请求选项（需要先设置templateId）
        $this->request->setTemplateId('template_edit_123');
        $this->request->setNotify(false);
        $this->request->setTextContent('更新的欢迎语内容');

        $options = $this->request->getRequestOptions();

        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertArrayHasKey('notify', $options['json']);
        $this->assertArrayHasKey('text', $options['json']);
        $this->assertSame(0, $options['json']['notify']);
        $this->assertSame('更新的欢迎语内容', $options['json']['text']['content']);
    }

    public function test_getRequestOptions_withAllFields(): void
    {
        // 测试包含所有字段的请求选项
        $this->request->setTemplateId('template_full_123');
        $this->request->setNotify(true);
        $this->request->setTextContent('完整的修改内容');
        $this->request->setImageMediaId('new_img_123');
        $this->request->setLinkTitle('新链接标题');
        $this->request->setLinkUrl('https://newexample.com');
        $this->request->setMiniprogramTitle('新小程序标题');
        $this->request->setMiniprogramPage('pages/new');
        $this->request->setFileMediaId('new_file_123');
        $this->request->setVideoMediaId('new_video_123');

        $options = $this->request->getRequestOptions();
        $json = $options['json'];

        $this->assertSame(1, $json['notify']);
        $this->assertArrayHasKey('text', $json);
        $this->assertArrayHasKey('image', $json);
        $this->assertArrayHasKey('link', $json);
        $this->assertArrayHasKey('miniprogram', $json);
        $this->assertArrayHasKey('file', $json);
        $this->assertArrayHasKey('video', $json);
    }

    public function test_businessScenario_updateTextContent(): void
    {
        // 测试业务场景：更新文本内容
        $templateId = 'welcome_template_001';
        $this->request->setTemplateId($templateId);
        $this->request->setNotify(true);
        $this->request->setTextContent('欢迎语内容已更新，请查看最新的入群指南。');

        $options = $this->request->getRequestOptions();
        $json = $options['json'];

        $this->assertSame(1, $json['notify']);
        $this->assertSame('欢迎语内容已更新，请查看最新的入群指南。', $json['text']['content']);
        $this->assertCount(2, $json); // notify和text
    }

    public function test_businessScenario_updateRichMedia(): void
    {
        // 测试业务场景：更新富媒体内容
        $templateId = 'rich_template_002';
        $this->request->setTemplateId($templateId);
        $this->request->setNotify(false);
        $this->request->setTextContent('更新的富媒体欢迎语');
        $this->request->setImageMediaId('updated_image_456');
        $this->request->setLinkTitle('更新的链接');
        $this->request->setLinkPicUrl('https://updated.com/pic.jpg');
        $this->request->setLinkDesc('查看更新的内容');
        $this->request->setLinkUrl('https://updated.com/content');

        $options = $this->request->getRequestOptions();
        $json = $options['json'];

        $this->assertArrayHasKey('text', $json);
        $this->assertArrayHasKey('image', $json);
        $this->assertArrayHasKey('link', $json);
        $this->assertSame('updated_image_456', $json['image']['media_id']);
        $this->assertSame('更新的链接', $json['link']['title']);
    }

    public function test_businessScenario_updateMiniprogram(): void
    {
        // 测试业务场景：更新小程序内容
        $templateId = 'miniprogram_template_003';
        $this->request->setTemplateId($templateId);
        $this->request->setNotify(true);
        $this->request->setTextContent('小程序内容已更新');
        $this->request->setMiniprogramTitle('新版企业工具');
        $this->request->setMiniprogramPicMediaId('new_miniprogram_pic');
        $this->request->setMiniprogramAppId('wxnew123456789abc');
        $this->request->setMiniprogramPage('pages/updated/index');

        $options = $this->request->getRequestOptions();
        $json = $options['json'];

        $this->assertArrayHasKey('miniprogram', $json);
        $this->assertSame('新版企业工具', $json['miniprogram']['title']);
        $this->assertSame('wxnew123456789abc', $json['miniprogram']['appid']);
        $this->assertSame('pages/updated/index', $json['miniprogram']['page']);
    }

    public function test_businessScenario_removeContent(): void
    {
        // 测试业务场景：移除某些内容（设置为null）
        $templateId = 'remove_template_004';
        $this->request->setTemplateId($templateId);
        $this->request->setNotify(false);
        $this->request->setTextContent('简化的欢迎语');
        // 不设置图片、链接等，相当于移除这些内容

        $options = $this->request->getRequestOptions();
        $json = $options['json'];

        $this->assertArrayHasKey('text', $json);
        $this->assertArrayNotHasKey('image', $json);
        $this->assertArrayNotHasKey('link', $json);
        $this->assertArrayNotHasKey('miniprogram', $json);
        $this->assertArrayNotHasKey('file', $json);
        $this->assertArrayNotHasKey('video', $json);
    }

    public function test_templateId_requiredForEdit(): void
    {
        // 测试编辑操作需要模板ID
        $this->expectException(\Error::class); // 访问未初始化的属性会抛出Error
        
        $this->request->getTemplateId();
    }

    public function test_templateId_differentFormats(): void
    {
        // 测试不同格式的模板ID
        $templateIds = [
            'template_123',
            'tpl_456789',
            'welcome_msg_001',
            'group-welcome-2023-001'
        ];

        foreach ($templateIds as $templateId) {
            $this->request->setTemplateId($templateId);
            $this->assertSame($templateId, $this->request->getTemplateId());
        }
    }

    public function test_requestPath_immutable(): void
    {
        // 测试请求路径不可变
        $path1 = $this->request->getRequestPath();
        $this->request->setTemplateId('test_123');
        $this->request->setTextContent('测试');
        $path2 = $this->request->getRequestPath();
        
        $this->assertSame($path1, $path2);
        $this->assertSame('/cgi-bin/externalcontact/group_welcome_template/edit', $path1);
    }

    public function test_requestOptionsFormat(): void
    {
        // 测试请求选项格式
        $this->request->setTemplateId('format_test_123');
        $this->request->setTextContent('格式测试');
        
        $options = $this->request->getRequestOptions();

        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
    }

    public function test_contentUpdate_preservesTemplateId(): void
    {
        // 测试内容更新保持模板ID
        $templateId = 'preserve_test_123';
        $this->request->setTemplateId($templateId);
        
        $this->request->setTextContent('第一次更新');
        $this->assertSame($templateId, $this->request->getTemplateId());
        
        $this->request->setTextContent('第二次更新');
        $this->assertSame($templateId, $this->request->getTemplateId());
        
        $this->request->setNotify(false);
        $this->assertSame($templateId, $this->request->getTemplateId());
    }

    public function test_emptyContentUpdate(): void
    {
        // 测试空内容更新
        $this->request->setTemplateId('empty_test_123');
        // 只设置notify，不设置其他内容

        $options = $this->request->getRequestOptions();
        $json = $options['json'];

        $this->assertArrayHasKey('notify', $json);
        $this->assertSame(1, $json['notify']); // 默认为true
        $this->assertCount(1, $json);
    }

    public function test_multipleUpdates(): void
    {
        // 测试多次更新操作
        $templateId = 'multiple_test_123';
        $this->request->setTemplateId($templateId);
        
        // 第一次更新
        $this->request->setTextContent('第一次更新');
        $this->request->setNotify(true);
        
        // 第二次更新
        $this->request->setTextContent('第二次更新');
        $this->request->setNotify(false);
        
        $options = $this->request->getRequestOptions();
        $json = $options['json'];
        
        $this->assertSame('第二次更新', $json['text']['content']);
        $this->assertSame(0, $json['notify']);
    }
} 
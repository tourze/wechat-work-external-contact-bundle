<?php

namespace WechatWorkExternalContactBundle\Tests\Request\GroupWelcomeTemplate;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkBundle\Request\AgentAware;
use WechatWorkExternalContactBundle\Request\GroupWelcomeTemplate\GetGroupWelcomeTemplateRequest;

/**
 * GetGroupWelcomeTemplateRequest 测试
 */
class GetGroupWelcomeTemplateRequestTest extends TestCase
{
    private GetGroupWelcomeTemplateRequest $request;

    protected function setUp(): void
    {
        $this->request = new GetGroupWelcomeTemplateRequest();
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
        $expectedPath = '/cgi-bin/externalcontact/group_welcome_template/get';
        $this->assertSame($expectedPath, $this->request->getRequestPath());
    }

    public function test_templateId_setterAndGetter(): void
    {
        // 测试模板ID设置和获取
        $templateId = 'template_get_123456';
        $this->request->setTemplateId($templateId);
        $this->assertSame($templateId, $this->request->getTemplateId());
    }

    public function test_templateId_differentFormats(): void
    {
        // 测试不同格式的模板ID
        $formats = [
            'simple_123',
            'template_with_underscores_456',
            'templateWithCamelCase789',
            'template-with-dashes-012',
            'TEMPLATE_UPPERCASE_345',
            'template.with.dots.678',
            'template123456789',
            'a',
            'very_long_template_id_with_many_characters_and_numbers_123456789'
        ];

        foreach ($formats as $templateId) {
            $this->request->setTemplateId($templateId);
            $this->assertSame($templateId, $this->request->getTemplateId());
        }
    }

    public function test_templateId_specialCharacters(): void
    {
        // 测试包含特殊字符的模板ID
        $specialIds = [
            'template_中文_123',
            'template_emoji_😀_456',
            'template@symbol#789',
            'template%encode&012',
            'template$price*345'
        ];

        foreach ($specialIds as $templateId) {
            $this->request->setTemplateId($templateId);
            $this->assertSame($templateId, $this->request->getTemplateId());
        }
    }

    public function test_agent_methods(): void
    {
        // 测试AgentAware trait的方法存在性
        $this->assertTrue(method_exists($this->request, 'setAgent'));
        $this->assertTrue(method_exists($this->request, 'getAgent'));
        $this->assertTrue(is_callable([$this->request, 'setAgent']));
        $this->assertTrue(is_callable([$this->request, 'getAgent']));
    }

    public function test_getRequestOptions_withTemplateId(): void
    {
        // 测试包含模板ID的请求选项
        $templateId = 'welcome_template_get_001';
        $this->request->setTemplateId($templateId);

        $options = $this->request->getRequestOptions();

        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertArrayHasKey('template_id', $options['json']);
        $this->assertSame($templateId, $options['json']['template_id']);
    }

    public function test_getRequestOptions_jsonStructure(): void
    {
        // 测试JSON结构的正确性
        $templateId = 'structure_test_template';
        $this->request->setTemplateId($templateId);

        $options = $this->request->getRequestOptions();

        $this->assertIsArray($options);
        $this->assertCount(1, $options); // 只有json键
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertCount(1, $options['json']); // 只有template_id键
        $this->assertArrayHasKey('template_id', $options['json']);
    }

    public function test_businessScenario_getTextTemplate(): void
    {
        // 测试业务场景：获取文本欢迎语模板
        $templateId = 'text_welcome_template_001';
        $this->request->setTemplateId($templateId);

        $options = $this->request->getRequestOptions();

        $this->assertSame($templateId, $options['json']['template_id']);
        $this->assertSame('/cgi-bin/externalcontact/group_welcome_template/get', $this->request->getRequestPath());
    }

    public function test_businessScenario_getRichMediaTemplate(): void
    {
        // 测试业务场景：获取富媒体欢迎语模板
        $templateId = 'rich_media_template_002';
        $this->request->setTemplateId($templateId);

        $options = $this->request->getRequestOptions();

        $this->assertSame($templateId, $options['json']['template_id']);
        $this->assertArrayHasKey('template_id', $options['json']);
    }

    public function test_businessScenario_getMiniprogramTemplate(): void
    {
        // 测试业务场景：获取小程序欢迎语模板
        $templateId = 'miniprogram_welcome_template_003';
        $this->request->setTemplateId($templateId);

        $options = $this->request->getRequestOptions();

        $this->assertSame($templateId, $options['json']['template_id']);
        $this->assertIsArray($options['json']);
    }

    public function test_businessScenario_getArchivedTemplate(): void
    {
        // 测试业务场景：获取归档的欢迎语模板
        $templateId = 'archived_template_004';
        $this->request->setTemplateId($templateId);

        $options = $this->request->getRequestOptions();

        $this->assertSame($templateId, $options['json']['template_id']);
    }

    public function test_businessScenario_getDepartmentTemplate(): void
    {
        // 测试业务场景：获取部门专用欢迎语模板
        $templateId = 'dept_sales_template_005';
        $this->request->setTemplateId($templateId);

        $options = $this->request->getRequestOptions();

        $this->assertSame($templateId, $options['json']['template_id']);
    }

    public function test_templateId_requiredForGet(): void
    {
        // 测试获取操作需要模板ID
        $this->expectException(\Error::class); // 访问未初始化的属性会抛出Error
        
        $this->request->getTemplateId();
    }

    public function test_templateId_immutable(): void
    {
        // 测试模板ID的不可变性（每次设置都会覆盖）
        $firstId = 'first_template_id';
        $secondId = 'second_template_id';

        $this->request->setTemplateId($firstId);
        $this->assertSame($firstId, $this->request->getTemplateId());

        $this->request->setTemplateId($secondId);
        $this->assertSame($secondId, $this->request->getTemplateId());
        $this->assertNotSame($firstId, $this->request->getTemplateId());
    }

    public function test_requestPath_immutable(): void
    {
        // 测试请求路径的不可变性
        $path1 = $this->request->getRequestPath();
        $this->request->setTemplateId('some_template');
        $path2 = $this->request->getRequestPath();

        $this->assertSame($path1, $path2);
        $this->assertSame('/cgi-bin/externalcontact/group_welcome_template/get', $path1);
    }

    public function test_requestOptions_idempotent(): void
    {
        // 测试请求选项的幂等性
        $templateId = 'idempotent_test_template';
        $this->request->setTemplateId($templateId);

        $options1 = $this->request->getRequestOptions();
        $options2 = $this->request->getRequestOptions();

        $this->assertEquals($options1, $options2);
        $this->assertSame($options1['json']['template_id'], $options2['json']['template_id']);
    }

    public function test_templateId_boundaryCases(): void
    {
        // 测试边界情况：极短和极长的模板ID
        $shortId = 'a';
        $longId = str_repeat('template_id_', 100) . 'end';

        $this->request->setTemplateId($shortId);
        $this->assertSame($shortId, $this->request->getTemplateId());

        $this->request->setTemplateId($longId);
        $this->assertSame($longId, $this->request->getTemplateId());
    }

    public function test_multipleTemplateIdChanges(): void
    {
        // 测试多次更改模板ID
        $ids = ['id1', 'id2', 'id3', 'id4', 'id5'];

        foreach ($ids as $id) {
            $this->request->setTemplateId($id);
            $this->assertSame($id, $this->request->getTemplateId());
            
            $options = $this->request->getRequestOptions();
            $this->assertSame($id, $options['json']['template_id']);
        }
    }

    public function test_requestOptionsFormat(): void
    {
        // 测试请求选项格式的一致性
        $templateId = 'format_test_template';
        $this->request->setTemplateId($templateId);

        $options = $this->request->getRequestOptions();

        // 验证格式符合企业微信API要求
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertArrayHasKey('template_id', $options['json']);
        $this->assertIsString($options['json']['template_id']);
    }
} 
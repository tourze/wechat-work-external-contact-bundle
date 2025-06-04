<?php

namespace WechatWorkExternalContactBundle\Tests\Request\GroupWelcomeTemplate;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkBundle\Request\AgentAware;
use WechatWorkExternalContactBundle\Request\GroupWelcomeTemplate\DeleteGroupWelcomeTemplateRequest;

/**
 * DeleteGroupWelcomeTemplateRequest 测试
 */
class DeleteGroupWelcomeTemplateRequestTest extends TestCase
{
    private DeleteGroupWelcomeTemplateRequest $request;

    protected function setUp(): void
    {
        $this->request = new DeleteGroupWelcomeTemplateRequest();
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
        $expectedPath = '/cgi-bin/externalcontact/group_welcome_template/del';
        $this->assertSame($expectedPath, $this->request->getRequestPath());
    }

    public function test_templateId_setterAndGetter(): void
    {
        // 测试模板ID设置和获取
        $templateId = 'template_delete_123456';
        $this->request->setTemplateId($templateId);
        $this->assertSame($templateId, $this->request->getTemplateId());
    }

    public function test_templateId_differentFormats(): void
    {
        // 测试不同格式的模板ID
        $formats = [
            'simple_del_123',
            'template_with_underscores_456',
            'templateWithCamelCase789',
            'template-with-dashes-012',
            'TEMPLATE_DELETE_345',
            'template.delete.dots.678',
            'delete123456789',
            'x',
            'very_long_template_id_for_deletion_with_many_characters_123456789'
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
            'template_删除_123',
            'template_emoji_🗑️_456',
            'template@delete#789',
            'template%remove&012',
            'template$del*345'
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
        $templateId = 'welcome_template_del_001';
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
        $templateId = 'delete_structure_test_template';
        $this->request->setTemplateId($templateId);

        $options = $this->request->getRequestOptions();

        $this->assertIsArray($options);
        $this->assertCount(1, $options); // 只有json键
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertCount(1, $options['json']); // 只有template_id键
        $this->assertArrayHasKey('template_id', $options['json']);
    }

    public function test_businessScenario_deleteObsoleteTemplate(): void
    {
        // 测试业务场景：删除过时的欢迎语模板
        $templateId = 'obsolete_welcome_template_001';
        $this->request->setTemplateId($templateId);

        $options = $this->request->getRequestOptions();

        $this->assertSame($templateId, $options['json']['template_id']);
        $this->assertSame('/cgi-bin/externalcontact/group_welcome_template/del', $this->request->getRequestPath());
    }

    public function test_businessScenario_deleteInactiveTemplate(): void
    {
        // 测试业务场景：删除不活跃的欢迎语模板
        $templateId = 'inactive_template_002';
        $this->request->setTemplateId($templateId);

        $options = $this->request->getRequestOptions();

        $this->assertSame($templateId, $options['json']['template_id']);
        $this->assertArrayHasKey('template_id', $options['json']);
    }

    public function test_businessScenario_deleteTestTemplate(): void
    {
        // 测试业务场景：删除测试用的欢迎语模板
        $templateId = 'test_template_for_deletion_003';
        $this->request->setTemplateId($templateId);

        $options = $this->request->getRequestOptions();

        $this->assertSame($templateId, $options['json']['template_id']);
        $this->assertIsArray($options['json']);
    }

    public function test_businessScenario_deleteDuplicateTemplate(): void
    {
        // 测试业务场景：删除重复的欢迎语模板
        $templateId = 'duplicate_template_004';
        $this->request->setTemplateId($templateId);

        $options = $this->request->getRequestOptions();

        $this->assertSame($templateId, $options['json']['template_id']);
    }

    public function test_businessScenario_deleteExpiredTemplate(): void
    {
        // 测试业务场景：删除过期的欢迎语模板
        $templateId = 'expired_welcome_template_005';
        $this->request->setTemplateId($templateId);

        $options = $this->request->getRequestOptions();

        $this->assertSame($templateId, $options['json']['template_id']);
    }

    public function test_businessScenario_bulkDeletePreparation(): void
    {
        // 测试业务场景：批量删除准备（单个模板删除）
        $templateIds = [
            'bulk_delete_template_001',
            'bulk_delete_template_002', 
            'bulk_delete_template_003'
        ];

        foreach ($templateIds as $templateId) {
            $this->request->setTemplateId($templateId);
            $options = $this->request->getRequestOptions();
            $this->assertSame($templateId, $options['json']['template_id']);
        }
    }

    public function test_businessScenario_deleteAfterDepartmentChange(): void
    {
        // 测试业务场景：部门调整后删除旧欢迎语模板
        $templateId = 'old_dept_template_006';
        $this->request->setTemplateId($templateId);

        $options = $this->request->getRequestOptions();

        $this->assertSame($templateId, $options['json']['template_id']);
        $this->assertSame('/cgi-bin/externalcontact/group_welcome_template/del', $this->request->getRequestPath());
    }

    public function test_templateId_requiredForDelete(): void
    {
        // 测试删除操作需要模板ID
        $this->expectException(\Error::class); // 访问未初始化的属性会抛出Error
        
        $this->request->getTemplateId();
    }

    public function test_templateId_immutable(): void
    {
        // 测试模板ID的不可变性（每次设置都会覆盖）
        $firstId = 'first_delete_template_id';
        $secondId = 'second_delete_template_id';

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
        $this->request->setTemplateId('some_delete_template');
        $path2 = $this->request->getRequestPath();

        $this->assertSame($path1, $path2);
        $this->assertSame('/cgi-bin/externalcontact/group_welcome_template/del', $path1);
    }

    public function test_requestOptions_idempotent(): void
    {
        // 测试请求选项的幂等性
        $templateId = 'idempotent_delete_test_template';
        $this->request->setTemplateId($templateId);

        $options1 = $this->request->getRequestOptions();
        $options2 = $this->request->getRequestOptions();

        $this->assertEquals($options1, $options2);
        $this->assertSame($options1['json']['template_id'], $options2['json']['template_id']);
    }

    public function test_templateId_boundaryCases(): void
    {
        // 测试边界情况：极短和极长的模板ID
        $shortId = 'z';
        $longId = str_repeat('delete_template_id_', 100) . 'end';

        $this->request->setTemplateId($shortId);
        $this->assertSame($shortId, $this->request->getTemplateId());

        $this->request->setTemplateId($longId);
        $this->assertSame($longId, $this->request->getTemplateId());
    }

    public function test_multipleTemplateIdChanges(): void
    {
        // 测试多次更改模板ID（模拟连续删除操作）
        $ids = ['del_id1', 'del_id2', 'del_id3', 'del_id4', 'del_id5'];

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
        $templateId = 'delete_format_test_template';
        $this->request->setTemplateId($templateId);

        $options = $this->request->getRequestOptions();

        // 验证格式符合企业微信API要求
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertArrayHasKey('template_id', $options['json']);
        $this->assertIsString($options['json']['template_id']);
    }

    public function test_destructiveOperation_confirmation(): void
    {
        // 测试破坏性操作确认（验证删除操作的严格性）
        $criticalTemplateId = 'critical_production_template';
        $this->request->setTemplateId($criticalTemplateId);

        $options = $this->request->getRequestOptions();

        // 验证关键模板ID准确传递
        $this->assertSame($criticalTemplateId, $options['json']['template_id']);
        $this->assertArrayHasKey('template_id', $options['json']);
        
        // 验证删除操作路径正确
        $this->assertSame('/cgi-bin/externalcontact/group_welcome_template/del', $this->request->getRequestPath());
    }

    public function test_errorHandling_prevention(): void
    {
        // 测试错误处理预防（确保删除操作安全性）
        $templateId = 'safe_delete_template';
        $this->request->setTemplateId($templateId);

        // 验证请求结构完整性
        $options = $this->request->getRequestOptions();
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayHasKey('template_id', $options['json']);
        $this->assertNotEmpty($options['json']['template_id']);
        
        // 验证模板ID一致性
        $this->assertSame($templateId, $this->request->getTemplateId());
        $this->assertSame($templateId, $options['json']['template_id']);
    }
} 
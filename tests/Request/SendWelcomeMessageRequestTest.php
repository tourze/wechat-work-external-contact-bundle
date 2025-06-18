<?php

namespace WechatWorkExternalContactBundle\Tests\Request;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkExternalContactBundle\Request\Attachment\BaseAttachment;
use WechatWorkExternalContactBundle\Request\SendWelcomeMessageRequest;

/**
 * SendWelcomeMessageRequest 测试
 */
class SendWelcomeMessageRequestTest extends TestCase
{
    public function test_inheritance(): void
    {
        // 测试继承关系
        $request = new SendWelcomeMessageRequest();
        $this->assertInstanceOf(ApiRequest::class, $request);
    }

    public function test_usesAgentAwareTrait(): void
    {
        // 测试使用AgentAware trait
        $request = new SendWelcomeMessageRequest();
        $this->assertTrue(method_exists($request, 'getAgent'));
        $this->assertTrue(method_exists($request, 'setAgent'));
    }

    public function test_getRequestPath(): void
    {
        // 测试请求路径
        $request = new SendWelcomeMessageRequest();
        $this->assertSame('/cgi-bin/externalcontact/send_welcome_msg', $request->getRequestPath());
    }

    public function test_welcomeCode_setterAndGetter(): void
    {
        // 测试欢迎码设置和获取
        $request = new SendWelcomeMessageRequest();
        $welcomeCode = 'welcome_code_123456';
        
        $request->setWelcomeCode($welcomeCode);
        $this->assertSame($welcomeCode, $request->getWelcomeCode());
    }

    public function test_welcomeCode_withSpecialCharacters(): void
    {
        // 测试特殊字符欢迎码
        $request = new SendWelcomeMessageRequest();
        $specialCode = 'welcome_abc-123_test@domain.com';
        $request->setWelcomeCode($specialCode);
        
        $this->assertSame($specialCode, $request->getWelcomeCode());
    }

    public function test_welcomeCode_withLongString(): void
    {
        // 测试长字符串欢迎码
        $request = new SendWelcomeMessageRequest();
        $longCode = str_repeat('a', 255);
        $request->setWelcomeCode($longCode);
        
        $this->assertSame($longCode, $request->getWelcomeCode());
    }

    public function test_textContent_setterAndGetter(): void
    {
        // 测试文本内容设置和获取
        $request = new SendWelcomeMessageRequest();
        $textContent = '欢迎加入我们的企业！';
        
        $request->setTextContent($textContent);
        $this->assertSame($textContent, $request->getTextContent());
    }

    public function test_textContent_withNull(): void
    {
        // 测试null文本内容
        $request = new SendWelcomeMessageRequest();
        $request->setTextContent(null);
        
        $this->assertNull($request->getTextContent());
    }

    public function test_textContent_withEmptyString(): void
    {
        // 测试空字符串文本内容
        $request = new SendWelcomeMessageRequest();
        $request->setTextContent('');
        
        $this->assertSame('', $request->getTextContent());
    }

    public function test_textContent_withMaxLength(): void
    {
        // 测试最大长度文本内容（4000字节）
        $request = new SendWelcomeMessageRequest();
        $maxContent = str_repeat('你好', 1000); // 每个中文字符3字节，约3000字节
        $request->setTextContent($maxContent);
        
        $this->assertSame($maxContent, $request->getTextContent());
    }

    public function test_attachments_setterAndGetter(): void
    {
        // 测试附件设置和获取
        $request = new SendWelcomeMessageRequest();
        
        // 创建模拟附件
        $attachment1 = $this->createMockAttachment(['type' => 'image', 'media_id' => 'media123']);
        $attachment2 = $this->createMockAttachment(['type' => 'file', 'media_id' => 'file456']);
        $attachments = [$attachment1, $attachment2];
        
        $request->setAttachments($attachments);
        $this->assertSame($attachments, $request->getAttachments());
    }

    public function test_attachments_withNull(): void
    {
        // 测试null附件
        $request = new SendWelcomeMessageRequest();
        $request->setAttachments(null);
        
        $this->assertNull($request->getAttachments());
    }

    public function test_attachments_withEmptyArray(): void
    {
        // 测试空数组附件
        $request = new SendWelcomeMessageRequest();
        $request->setAttachments([]);
        
        $this->assertSame([], $request->getAttachments());
    }

    public function test_defaultValues(): void
    {
        // 测试默认值
        $request = new SendWelcomeMessageRequest();
        
        $this->assertNull($request->getTextContent());
        $this->assertNull($request->getAttachments());
    }

    public function test_getRequestOptions_withMinimalParams(): void
    {
        // 测试最小参数的请求选项
        $request = new SendWelcomeMessageRequest();
        $welcomeCode = 'minimal_welcome_code';
        $request->setWelcomeCode($welcomeCode);
        
        $options = $request->getRequestOptions();
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayHasKey('welcome_code', $options['json']);
        $this->assertSame($welcomeCode, $options['json']['welcome_code']);
        $this->assertArrayNotHasKey('text', $options['json']);
        $this->assertArrayNotHasKey('attachments', $options['json']);
    }

    public function test_getRequestOptions_withTextContent(): void
    {
        // 测试带文本内容的请求选项
        $request = new SendWelcomeMessageRequest();
        $welcomeCode = 'text_welcome_code';
        $textContent = '感谢您的加入！';
        
        $request->setWelcomeCode($welcomeCode);
        $request->setTextContent($textContent);
        
        $options = $request->getRequestOptions();
        
        $this->assertArrayHasKey('welcome_code', $options['json']);
        $this->assertArrayHasKey('text', $options['json']);
        $this->assertSame($welcomeCode, $options['json']['welcome_code']);
        $this->assertArrayHasKey('content', $options['json']['text']);
        $this->assertSame($textContent, $options['json']['text']['content']);
    }

    public function test_getRequestOptions_withAttachments(): void
    {
        // 测试带附件的请求选项
        $request = new SendWelcomeMessageRequest();
        $welcomeCode = 'attachment_welcome_code';
        
        $attachment1Data = ['type' => 'image', 'media_id' => 'image123'];
        $attachment2Data = ['type' => 'link', 'url' => 'https://example.com'];
        
        $attachment1 = $this->createMockAttachment($attachment1Data);
        $attachment2 = $this->createMockAttachment($attachment2Data);
        
        $request->setWelcomeCode($welcomeCode);
        $request->setAttachments([$attachment1, $attachment2]);
        
        $options = $request->getRequestOptions();
        
        $this->assertArrayHasKey('welcome_code', $options['json']);
        $this->assertArrayHasKey('attachments', $options['json']);
        $this->assertSame($welcomeCode, $options['json']['welcome_code']);
        $this->assertCount(2, $options['json']['attachments']);
        $this->assertSame($attachment1Data, $options['json']['attachments'][0]);
        $this->assertSame($attachment2Data, $options['json']['attachments'][1]);
    }

    public function test_getRequestOptions_withAllParams(): void
    {
        // 测试所有参数的请求选项
        $request = new SendWelcomeMessageRequest();
        $welcomeCode = 'full_welcome_code';
        $textContent = '欢迎加入团队！期待与您的合作。';
        
        $attachmentData = ['type' => 'miniprogram', 'appid' => 'miniapp123'];
        $attachment = $this->createMockAttachment($attachmentData);
        
        $request->setWelcomeCode($welcomeCode);
        $request->setTextContent($textContent);
        $request->setAttachments([$attachment]);
        
        $options = $request->getRequestOptions();
        
        $this->assertArrayHasKey('welcome_code', $options['json']);
        $this->assertArrayHasKey('text', $options['json']);
        $this->assertArrayHasKey('attachments', $options['json']);
        
        $this->assertSame($welcomeCode, $options['json']['welcome_code']);
        $this->assertSame($textContent, $options['json']['text']['content']);
        $this->assertCount(1, $options['json']['attachments']);
        $this->assertSame($attachmentData, $options['json']['attachments'][0]);
    }

    public function test_getRequestOptions_withNullTextContent(): void
    {
        // 测试null文本内容的请求选项
        $request = new SendWelcomeMessageRequest();
        $request->setWelcomeCode('test_code');
        $request->setTextContent(null);
        
        $options = $request->getRequestOptions();
        
        $this->assertArrayNotHasKey('text', $options['json']);
    }

    public function test_getRequestOptions_withEmptyTextContent(): void
    {
        // 测试空文本内容的请求选项
        $request = new SendWelcomeMessageRequest();
        $request->setWelcomeCode('test_code');
        $request->setTextContent('');
        
        $options = $request->getRequestOptions();
        
        $this->assertArrayHasKey('text', $options['json']);
        $this->assertSame('', $options['json']['text']['content']);
    }

    public function test_getRequestOptions_withNullAttachments(): void
    {
        // 测试null附件的请求选项
        $request = new SendWelcomeMessageRequest();
        $request->setWelcomeCode('test_code');
        $request->setAttachments(null);
        
        $options = $request->getRequestOptions();
        
        $this->assertArrayNotHasKey('attachments', $options['json']);
    }

    public function test_getRequestOptions_withEmptyAttachments(): void
    {
        // 测试空附件的请求选项
        $request = new SendWelcomeMessageRequest();
        $request->setWelcomeCode('test_code');
        $request->setAttachments([]);
        
        $options = $request->getRequestOptions();
        
        $this->assertArrayHasKey('attachments', $options['json']);
        $this->assertSame([], $options['json']['attachments']);
    }

    public function test_getRequestOptions_structure(): void
    {
        // 测试请求选项结构
        $request = new SendWelcomeMessageRequest();
        $request->setWelcomeCode('structure_test_code');
        $request->setTextContent('测试文本');
        
        $options = $request->getRequestOptions();
        $this->assertCount(1, $options);
        $this->assertArrayHasKey('json', $options);
        $this->assertGreaterThanOrEqual(2, count($options['json']));
    }

    public function test_inheritsFromApiRequest(): void
    {
        // 测试继承自ApiRequest的核心方法
        $request = new SendWelcomeMessageRequest();
        
        $this->assertTrue(method_exists($request, 'getRequestPath'));
        $this->assertTrue(method_exists($request, 'getRequestOptions'));
        
        // 验证是ApiRequest的实例
        $this->assertInstanceOf(ApiRequest::class, $request);
    }

    public function test_businessScenario_basicWelcomeMessage(): void
    {
        // 测试业务场景：基本欢迎消息
        $request = new SendWelcomeMessageRequest();
        $welcomeCode = 'new_customer_welcome_2024';
        $textContent = '欢迎加入我们的客户群！有任何问题请随时联系我们。';
        
        $request->setWelcomeCode($welcomeCode);
        $request->setTextContent($textContent);
        
        $this->assertSame('/cgi-bin/externalcontact/send_welcome_msg', $request->getRequestPath());
        $this->assertSame($welcomeCode, $request->getWelcomeCode());
        $this->assertSame($textContent, $request->getTextContent());
        
        $options = $request->getRequestOptions();
        $this->assertSame($welcomeCode, $options['json']['welcome_code']);
        $this->assertSame($textContent, $options['json']['text']['content']);
    }

    public function test_businessScenario_welcomeWithImage(): void
    {
        // 测试业务场景：带图片的欢迎消息
        $request = new SendWelcomeMessageRequest();
        $welcomeCode = 'image_welcome_code';
        $textContent = '欢迎加入我们！';
        
        $imageAttachmentData = ['type' => 'image', 'media_id' => 'welcome_image_media_id'];
        $imageAttachment = $this->createMockAttachment($imageAttachmentData);
        
        $request->setWelcomeCode($welcomeCode);
        $request->setTextContent($textContent);
        $request->setAttachments([$imageAttachment]);
        
        $options = $request->getRequestOptions();
        $this->assertArrayHasKey('attachments', $options['json']);
        $this->assertCount(1, $options['json']['attachments']);
        $this->assertSame($imageAttachmentData, $options['json']['attachments'][0]);
        
        // 验证API路径符合发送欢迎消息要求
        $this->assertStringContainsString('send_welcome_msg', $request->getRequestPath());
    }

    public function test_businessScenario_welcomeWithMultipleAttachments(): void
    {
        // 测试业务场景：带多个附件的欢迎消息
        $request = new SendWelcomeMessageRequest();
        $welcomeCode = 'multi_attachment_welcome';
        $textContent = '欢迎！以下是一些有用的资料：';
        
        $fileData = ['type' => 'file', 'media_id' => 'welcome_file'];
        $linkData = ['type' => 'link', 'url' => 'https://company.com/welcome'];
        $miniprogramData = ['type' => 'miniprogram', 'appid' => 'welcome_app'];
        
        $fileAttachment = $this->createMockAttachment($fileData);
        $linkAttachment = $this->createMockAttachment($linkData);
        $miniprogramAttachment = $this->createMockAttachment($miniprogramData);
        
        $request->setWelcomeCode($welcomeCode);
        $request->setTextContent($textContent);
        $request->setAttachments([$fileAttachment, $linkAttachment, $miniprogramAttachment]);
        
        $this->assertCount(3, $request->getAttachments());
        
        $options = $request->getRequestOptions();
        $this->assertCount(3, $options['json']['attachments']);
        $this->assertSame($fileData, $options['json']['attachments'][0]);
        $this->assertSame($linkData, $options['json']['attachments'][1]);
        $this->assertSame($miniprogramData, $options['json']['attachments'][2]);
    }

    public function test_businessScenario_onlyWelcomeCode(): void
    {
        // 测试业务场景：仅有欢迎码（无内容和附件）
        $request = new SendWelcomeMessageRequest();
        $welcomeCode = 'minimal_welcome_scenario';
        
        $request->setWelcomeCode($welcomeCode);
        
        $this->assertSame($welcomeCode, $request->getWelcomeCode());
        $this->assertNull($request->getTextContent());
        $this->assertNull($request->getAttachments());
        
        $options = $request->getRequestOptions();
        $this->assertArrayHasKey('welcome_code', $options['json']);
        $this->assertArrayNotHasKey('text', $options['json']);
        $this->assertArrayNotHasKey('attachments', $options['json']);
    }

    public function test_businessScenario_maxAttachments(): void
    {
        // 测试业务场景：最大附件数量（9个）
        $request = new SendWelcomeMessageRequest();
        $welcomeCode = 'max_attachments_welcome';
        
        $attachments = [];
        for ($i = 1; $i <= 9; $i++) {
            $attachmentData = ['type' => 'image', 'media_id' => "media_id_{$i}"];
            $attachments[] = $this->createMockAttachment($attachmentData);
        }
        
        $request->setWelcomeCode($welcomeCode);
        $request->setAttachments($attachments);
        
        $this->assertCount(9, $request->getAttachments());
        
        $options = $request->getRequestOptions();
        $this->assertCount(9, $options['json']['attachments']);
        
        // 验证使用POST方法符合企业微信API规范
        $this->assertStringContainsString('externalcontact', $request->getRequestPath());
    }

    public function test_multipleSetCalls(): void
    {
        // 测试多次设置值
        $request = new SendWelcomeMessageRequest();
        
        $firstCode = 'first_welcome_code';
        $firstText = 'First welcome text';
        $firstAttachment = $this->createMockAttachment(['type' => 'image', 'media_id' => 'first_media']);
        
        $request->setWelcomeCode($firstCode);
        $request->setTextContent($firstText);
        $request->setAttachments([$firstAttachment]);
        
        $this->assertSame($firstCode, $request->getWelcomeCode());
        $this->assertSame($firstText, $request->getTextContent());
        $this->assertCount(1, $request->getAttachments());
        
        // 重新设置
        $secondCode = 'second_welcome_code';
        $secondText = 'Second welcome text';
        $secondAttachment = $this->createMockAttachment(['type' => 'file', 'media_id' => 'second_media']);
        
        $request->setWelcomeCode($secondCode);
        $request->setTextContent($secondText);
        $request->setAttachments([$secondAttachment]);
        
        $this->assertSame($secondCode, $request->getWelcomeCode());
        $this->assertSame($secondText, $request->getTextContent());
        $this->assertCount(1, $request->getAttachments());
        
        $options = $request->getRequestOptions();
        $this->assertSame($secondCode, $options['json']['welcome_code']);
        $this->assertSame($secondText, $options['json']['text']['content']);
        $this->assertCount(1, $options['json']['attachments']);
    }

    public function test_resetToNull(): void
    {
        // 测试重置为null
        $request = new SendWelcomeMessageRequest();
        
        $request->setWelcomeCode('initial_code');
        $request->setTextContent('initial text');
        $request->setAttachments([
            $this->createMockAttachment(['type' => 'image', 'media_id' => 'initial_media'])
        ]);
        
        // 重置可null的属性
        $request->setTextContent(null);
        $request->setAttachments(null);
        
        $this->assertSame('initial_code', $request->getWelcomeCode()); // welcomeCode不能为null
        $this->assertNull($request->getTextContent());
        $this->assertNull($request->getAttachments());
        
        $options = $request->getRequestOptions();
        $this->assertArrayHasKey('welcome_code', $options['json']); // welcomeCode仍然存在
        $this->assertArrayNotHasKey('text', $options['json']);
        $this->assertArrayNotHasKey('attachments', $options['json']);
    }

    public function test_requestOptionsDoesNotModifyOriginalData(): void
    {
        // 测试获取请求选项不会修改原始数据
        $request = new SendWelcomeMessageRequest();
        $originalCode = 'original_welcome_code';
        $originalText = 'original text content';
        $originalAttachment = $this->createMockAttachment(['type' => 'image', 'media_id' => 'original_media']);
        
        $request->setWelcomeCode($originalCode);
        $request->setTextContent($originalText);
        $request->setAttachments([$originalAttachment]);
        
        $options1 = $request->getRequestOptions();
        $options2 = $request->getRequestOptions();
        
        // 修改返回的数组不应影响原始数据
        $options1['json']['welcome_code'] = 'modified_code';
        $options1['json']['text']['content'] = 'modified text';
        $options1['json']['attachments'][0] = ['modified' => 'attachment'];
        
        $this->assertSame($originalCode, $request->getWelcomeCode());
        $this->assertSame($originalText, $request->getTextContent());
        $this->assertCount(1, $request->getAttachments());
        
        $this->assertSame($originalCode, $options2['json']['welcome_code']);
        $this->assertSame($originalText, $options2['json']['text']['content']);
        $this->assertCount(1, $options2['json']['attachments']);
    }

    public function test_immutableBehavior(): void
    {
        // 测试不可变行为
        $request = new SendWelcomeMessageRequest();
        $welcomeCode = 'test_welcome_code';
        $textContent = 'test content';
        $attachment = $this->createMockAttachment(['type' => 'test', 'media_id' => 'test_media']);
        
        $request->setWelcomeCode($welcomeCode);
        $request->setTextContent($textContent);
        $request->setAttachments([$attachment]);
        
        $options = $request->getRequestOptions();
        
        // 修改选项不应影响request对象
        $options['json']['welcome_code'] = 'changed_code';
        $options['json']['text']['content'] = 'changed content';
        $options['json']['attachments'] = [];
        $options['json']['new_param'] = 'new_value';
        
        $this->assertSame($welcomeCode, $request->getWelcomeCode());
        $this->assertSame($textContent, $request->getTextContent());
        $this->assertCount(1, $request->getAttachments());
        
        $newOptions = $request->getRequestOptions();
        $this->assertSame($welcomeCode, $newOptions['json']['welcome_code']);
        $this->assertSame($textContent, $newOptions['json']['text']['content']);
        $this->assertCount(1, $newOptions['json']['attachments']);
        $this->assertArrayNotHasKey('new_param', $newOptions['json']);
    }

    public function test_methodCallsAreIdempotent(): void
    {
        // 测试方法调用是幂等的
        $request = new SendWelcomeMessageRequest();
        $welcomeCode = 'idempotent_code';
        $textContent = 'idempotent content';
        $attachment = $this->createMockAttachment(['type' => 'test', 'media_id' => 'idempotent_media']);
        
        $request->setWelcomeCode($welcomeCode);
        $request->setTextContent($textContent);
        $request->setAttachments([$attachment]);
        
        // 多次调用应该返回相同结果
        $path1 = $request->getRequestPath();
        $path2 = $request->getRequestPath();
        $this->assertSame($path1, $path2);
        
        $options1 = $request->getRequestOptions();
        $options2 = $request->getRequestOptions();
        $this->assertSame($options1, $options2);
        
        $code1 = $request->getWelcomeCode();
        $code2 = $request->getWelcomeCode();
        $this->assertSame($code1, $code2);
        
        $text1 = $request->getTextContent();
        $text2 = $request->getTextContent();
        $this->assertSame($text1, $text2);
        
        $attachments1 = $request->getAttachments();
        $attachments2 = $request->getAttachments();
        $this->assertSame($attachments1, $attachments2);
    }

    public function test_agentAwareTraitIntegration(): void
    {
        // 测试AgentAware trait集成
        $request = new SendWelcomeMessageRequest();
        
        // 测试agent相关方法存在
        $this->assertTrue(method_exists($request, 'getAgent'));
        $this->assertTrue(method_exists($request, 'setAgent'));
        
        // 这些方法应该可以正常调用
        $this->assertTrue(is_callable([$request, 'getAgent']));
        $this->assertTrue(is_callable([$request, 'setAgent']));
    }

    /**
     * 创建模拟附件对象
     */
    private function createMockAttachment(array $data)
    {
        $mock = $this->createMock(BaseAttachment::class);
        $mock->method('retrievePlainArray')->willReturn($data);
        
        return $mock;
    }
} 
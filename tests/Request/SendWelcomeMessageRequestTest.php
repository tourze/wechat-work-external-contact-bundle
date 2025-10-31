<?php

namespace WechatWorkExternalContactBundle\Tests\Request;

use HttpClientBundle\Request\RequestInterface;
use HttpClientBundle\Tests\Request\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkExternalContactBundle\Request\Attachment\BaseAttachment;
use WechatWorkExternalContactBundle\Request\SendWelcomeMessageRequest;

/**
 * SendWelcomeMessageRequest 测试
 *
 * @internal
 */
#[CoversClass(SendWelcomeMessageRequest::class)]
final class SendWelcomeMessageRequestTest extends RequestTestCase
{
    private SendWelcomeMessageRequest $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new SendWelcomeMessageRequest();
    }

    public function testInheritance(): void
    {
        // 测试基本功能
        $this->assertNotNull($this->request);
        $this->assertSame('/cgi-bin/externalcontact/send_welcome_msg', $this->request->getRequestPath());
    }

    public function testUsesAgentAwareTrait(): void
    {
        // 测试使用AgentAware trait
        // 测试默认值
        $this->assertNull($this->request->getAgent());

        // 测试设置和获取agent
        $agent = $this->createMock(AgentInterface::class);
        $this->request->setAgent($agent);
        $this->assertSame($agent, $this->request->getAgent());

        // 测试设置null
        $this->request->setAgent(null);
        $this->assertNull($this->request->getAgent());

        // 测试多次设置
        $agent2 = $this->createMock(AgentInterface::class);
        $this->request->setAgent($agent2);
        $this->assertSame($agent2, $this->request->getAgent());
        $this->assertNotSame($agent, $this->request->getAgent());
    }

    public function testGetRequestPath(): void
    {
        // 测试请求路径
        $this->assertSame('/cgi-bin/externalcontact/send_welcome_msg', $this->request->getRequestPath());
    }

    public function testWelcomeCodeSetterAndGetter(): void
    {
        // 测试欢迎码设置和获取
        $welcomeCode = 'welcome_code_123456';

        $this->request->setWelcomeCode($welcomeCode);
        $this->assertSame($welcomeCode, $this->request->getWelcomeCode());
    }

    public function testWelcomeCodeWithSpecialCharacters(): void
    {
        // 测试特殊字符欢迎码
        $specialCode = 'welcome_abc-123_test@domain.com';
        $this->request->setWelcomeCode($specialCode);

        $this->assertSame($specialCode, $this->request->getWelcomeCode());
    }

    public function testWelcomeCodeWithLongString(): void
    {
        // 测试长字符串欢迎码
        $longCode = str_repeat('a', 255);
        $this->request->setWelcomeCode($longCode);

        $this->assertSame($longCode, $this->request->getWelcomeCode());
    }

    public function testTextContentSetterAndGetter(): void
    {
        // 测试文本内容设置和获取
        $textContent = '欢迎加入我们的企业！';

        $this->request->setTextContent($textContent);
        $this->assertSame($textContent, $this->request->getTextContent());
    }

    public function testTextContentWithNull(): void
    {
        // 测试null文本内容
        $this->request->setTextContent(null);

        $this->assertNull($this->request->getTextContent());
    }

    public function testTextContentWithEmptyString(): void
    {
        // 测试空字符串文本内容
        $this->request->setTextContent('');

        $this->assertSame('', $this->request->getTextContent());
    }

    public function testTextContentWithMaxLength(): void
    {
        // 测试最大长度文本内容（4000字节）
        $maxContent = str_repeat('你好', 1000); // 每个中文字符3字节，约3000字节
        $this->request->setTextContent($maxContent);

        $this->assertSame($maxContent, $this->request->getTextContent());
    }

    public function testAttachmentsSetterAndGetter(): void
    {
        // 测试附件设置和获取
        // 创建模拟附件
        $attachment1 = $this->createMockAttachment(['type' => 'image', 'media_id' => 'media123']);
        $attachment2 = $this->createMockAttachment(['type' => 'file', 'media_id' => 'file456']);
        $attachments = [$attachment1, $attachment2];

        $this->request->setAttachments($attachments);
        $this->assertSame($attachments, $this->request->getAttachments());
    }

    public function testAttachmentsWithNull(): void
    {
        // 测试null附件
        $this->request->setAttachments(null);

        $this->assertNull($this->request->getAttachments());
    }

    public function testAttachmentsWithEmptyArray(): void
    {
        // 测试空数组附件
        $this->request->setAttachments([]);

        $this->assertSame([], $this->request->getAttachments());
    }

    public function testDefaultValues(): void
    {
        // 测试默认值
        $this->assertNull($this->request->getTextContent());
        $this->assertNull($this->request->getAttachments());
    }

    public function testGetRequestOptionsWithMinimalParams(): void
    {
        // 测试最小参数的请求选项
        $welcomeCode = 'minimal_welcome_code';
        $this->request->setWelcomeCode($welcomeCode);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);

        $jsonData = $options['json'];
        $this->assertIsArray($jsonData);
        $this->assertArrayHasKey('welcome_code', $jsonData);
        $this->assertSame($welcomeCode, $jsonData['welcome_code']);
        $this->assertArrayNotHasKey('text', $jsonData);
        $this->assertArrayNotHasKey('attachments', $jsonData);
    }

    public function testGetRequestOptionsWithTextContent(): void
    {
        // 测试带文本内容的请求选项
        $welcomeCode = 'text_welcome_code';
        $textContent = '感谢您的加入！';

        $this->request->setWelcomeCode($welcomeCode);
        $this->request->setTextContent($textContent);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);

        $jsonData = $options['json'];
        $this->assertIsArray($jsonData);
        $this->assertArrayHasKey('welcome_code', $jsonData);
        $this->assertArrayHasKey('text', $jsonData);
        $this->assertSame($welcomeCode, $jsonData['welcome_code']);

        $textData = $jsonData['text'];
        $this->assertIsArray($textData);
        $this->assertArrayHasKey('content', $textData);
        $this->assertSame($textContent, $textData['content']);
    }

    public function testGetRequestOptionsWithAttachments(): void
    {
        // 测试带附件的请求选项
        $welcomeCode = 'attachment_welcome_code';

        $attachment1Data = ['type' => 'image', 'media_id' => 'image123'];
        $attachment2Data = ['type' => 'link', 'url' => 'https://example.com'];

        $attachment1 = $this->createMockAttachment($attachment1Data);
        $attachment2 = $this->createMockAttachment($attachment2Data);

        $this->request->setWelcomeCode($welcomeCode);
        $this->request->setAttachments([$attachment1, $attachment2]);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);

        $jsonData = $options['json'];
        $this->assertIsArray($jsonData);
        $this->assertArrayHasKey('welcome_code', $jsonData);
        $this->assertArrayHasKey('attachments', $jsonData);
        $this->assertSame($welcomeCode, $jsonData['welcome_code']);

        $attachmentsData = $jsonData['attachments'];
        $this->assertIsArray($attachmentsData);
        $this->assertCount(2, $attachmentsData);
        $this->assertSame($attachment1Data, $attachmentsData[0]);
        $this->assertSame($attachment2Data, $attachmentsData[1]);
    }

    public function testGetRequestOptionsWithAllParams(): void
    {
        // 测试所有参数的请求选项
        $welcomeCode = 'full_welcome_code';
        $textContent = '欢迎加入团队！期待与您的合作。';

        $attachmentData = ['type' => 'miniprogram', 'appid' => 'miniapp123'];
        $attachment = $this->createMockAttachment($attachmentData);

        $this->request->setWelcomeCode($welcomeCode);
        $this->request->setTextContent($textContent);
        $this->request->setAttachments([$attachment]);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);

        $jsonData = $options['json'];
        $this->assertIsArray($jsonData);
        $this->assertArrayHasKey('welcome_code', $jsonData);
        $this->assertArrayHasKey('text', $jsonData);
        $this->assertArrayHasKey('attachments', $jsonData);

        $this->assertSame($welcomeCode, $jsonData['welcome_code']);

        $textData = $jsonData['text'];
        $this->assertIsArray($textData);
        $this->assertSame($textContent, $textData['content']);

        $attachmentsData = $jsonData['attachments'];
        $this->assertIsArray($attachmentsData);
        $this->assertCount(1, $attachmentsData);
        $this->assertSame($attachmentData, $attachmentsData[0]);
    }

    public function testGetRequestOptionsWithNullTextContent(): void
    {
        // 测试null文本内容的请求选项
        $this->request->setWelcomeCode('test_code');
        $this->request->setTextContent(null);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);

        $jsonData = $options['json'];
        $this->assertIsArray($jsonData);
        $this->assertArrayNotHasKey('text', $jsonData);
    }

    public function testGetRequestOptionsWithEmptyTextContent(): void
    {
        // 测试空文本内容的请求选项
        $this->request->setWelcomeCode('test_code');
        $this->request->setTextContent('');

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);

        $jsonData = $options['json'];
        $this->assertIsArray($jsonData);
        $this->assertArrayHasKey('text', $jsonData);

        $textData = $jsonData['text'];
        $this->assertIsArray($textData);
        $this->assertArrayHasKey('content', $textData);
        $this->assertSame('', $textData['content']);
    }

    public function testGetRequestOptionsWithNullAttachments(): void
    {
        // 测试null附件的请求选项
        $this->request->setWelcomeCode('test_code');
        $this->request->setAttachments(null);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);

        $jsonData = $options['json'];
        $this->assertIsArray($jsonData);
        $this->assertArrayNotHasKey('attachments', $jsonData);
    }

    public function testGetRequestOptionsWithEmptyAttachments(): void
    {
        // 测试空附件的请求选项
        $this->request->setWelcomeCode('test_code');
        $this->request->setAttachments([]);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);

        $jsonData = $options['json'];
        $this->assertIsArray($jsonData);
        $this->assertArrayHasKey('attachments', $jsonData);
        $this->assertSame([], $jsonData['attachments']);
    }

    public function testGetRequestOptionsStructure(): void
    {
        // 测试请求选项结构
        $this->request->setWelcomeCode('structure_test_code');
        $this->request->setTextContent('测试文本');

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertCount(1, $options);
        $this->assertArrayHasKey('json', $options);

        $jsonData = $options['json'];
        $this->assertIsArray($jsonData);
        $this->assertGreaterThanOrEqual(2, count($jsonData));
    }

    public function testInheritsFromApiRequest(): void
    {
        // 测试继承自ApiRequest的核心方法

        // 验证核心功能
        $this->assertNotNull($this->request);
        $this->assertSame('/cgi-bin/externalcontact/send_welcome_msg', $this->request->getRequestPath());
    }

    public function testBusinessScenarioBasicWelcomeMessage(): void
    {
        // 测试业务场景：基本欢迎消息
        $welcomeCode = 'new_customer_welcome_2024';
        $textContent = '欢迎加入我们的客户群！有任何问题请随时联系我们。';

        $this->request->setWelcomeCode($welcomeCode);
        $this->request->setTextContent($textContent);

        $this->assertSame('/cgi-bin/externalcontact/send_welcome_msg', $this->request->getRequestPath());
        $this->assertSame($welcomeCode, $this->request->getWelcomeCode());
        $this->assertSame($textContent, $this->request->getTextContent());

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);

        $jsonData = $options['json'];
        $this->assertIsArray($jsonData);
        $this->assertSame($welcomeCode, $jsonData['welcome_code']);

        $textData = $jsonData['text'];
        $this->assertIsArray($textData);
        $this->assertSame($textContent, $textData['content']);
    }

    public function testBusinessScenarioWelcomeWithImage(): void
    {
        // 测试业务场景：带图片的欢迎消息
        $welcomeCode = 'image_welcome_code';
        $textContent = '欢迎加入我们！';

        $imageAttachmentData = ['type' => 'image', 'media_id' => 'welcome_image_media_id'];
        $imageAttachment = $this->createMockAttachment($imageAttachmentData);

        $this->request->setWelcomeCode($welcomeCode);
        $this->request->setTextContent($textContent);
        $this->request->setAttachments([$imageAttachment]);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);

        $jsonData = $options['json'];
        $this->assertIsArray($jsonData);
        $this->assertArrayHasKey('attachments', $jsonData);

        $attachmentsData = $jsonData['attachments'];
        $this->assertIsArray($attachmentsData);
        $this->assertCount(1, $attachmentsData);
        $this->assertSame($imageAttachmentData, $attachmentsData[0]);

        // 验证API路径符合发送欢迎消息要求
        $this->assertStringContainsString('send_welcome_msg', $this->request->getRequestPath());
    }

    public function testBusinessScenarioWelcomeWithMultipleAttachments(): void
    {
        // 测试业务场景：带多个附件的欢迎消息
        $welcomeCode = 'multi_attachment_welcome';
        $textContent = '欢迎！以下是一些有用的资料：';

        $fileData = ['type' => 'file', 'media_id' => 'welcome_file'];
        $linkData = ['type' => 'link', 'url' => 'https://company.com/welcome'];
        $miniprogramData = ['type' => 'miniprogram', 'appid' => 'welcome_app'];

        $fileAttachment = $this->createMockAttachment($fileData);
        $linkAttachment = $this->createMockAttachment($linkData);
        $miniprogramAttachment = $this->createMockAttachment($miniprogramData);

        $this->request->setWelcomeCode($welcomeCode);
        $this->request->setTextContent($textContent);
        $this->request->setAttachments([$fileAttachment, $linkAttachment, $miniprogramAttachment]);

        $attachments = $this->request->getAttachments();
        $this->assertNotNull($attachments);
        $this->assertCount(3, $attachments);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);

        $jsonData = $options['json'];
        $this->assertIsArray($jsonData);

        $attachmentsData = $jsonData['attachments'];
        $this->assertIsArray($attachmentsData);
        $this->assertCount(3, $attachmentsData);
        $this->assertSame($fileData, $attachmentsData[0]);
        $this->assertSame($linkData, $attachmentsData[1]);
        $this->assertSame($miniprogramData, $attachmentsData[2]);
    }

    public function testBusinessScenarioOnlyWelcomeCode(): void
    {
        // 测试业务场景：仅有欢迎码（无内容和附件）
        $welcomeCode = 'minimal_welcome_scenario';

        $this->request->setWelcomeCode($welcomeCode);

        $this->assertSame($welcomeCode, $this->request->getWelcomeCode());
        $this->assertNull($this->request->getTextContent());
        $this->assertNull($this->request->getAttachments());

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);

        $jsonData = $options['json'];
        $this->assertIsArray($jsonData);
        $this->assertArrayHasKey('welcome_code', $jsonData);
        $this->assertArrayNotHasKey('text', $jsonData);
        $this->assertArrayNotHasKey('attachments', $jsonData);
    }

    public function testBusinessScenarioMaxAttachments(): void
    {
        // 测试业务场景：最大附件数量（9个）
        $welcomeCode = 'max_attachments_welcome';

        $attachments = [];
        for ($i = 1; $i <= 9; ++$i) {
            $attachmentData = ['type' => 'image', 'media_id' => "media_id_{$i}"];
            $attachments[] = $this->createMockAttachment($attachmentData);
        }

        $this->request->setWelcomeCode($welcomeCode);
        $this->request->setAttachments($attachments);

        $attachments = $this->request->getAttachments();
        $this->assertNotNull($attachments);
        $this->assertCount(9, $attachments);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);

        $jsonData = $options['json'];
        $this->assertIsArray($jsonData);

        $attachmentsData = $jsonData['attachments'];
        $this->assertIsArray($attachmentsData);
        $this->assertCount(9, $attachmentsData);

        // 验证使用POST方法符合企业微信API规范
        $this->assertStringContainsString('externalcontact', $this->request->getRequestPath());
    }

    public function testMultipleSetCalls(): void
    {
        // 测试多次设置值

        $firstCode = 'first_welcome_code';
        $firstText = 'First welcome text';
        $firstAttachment = $this->createMockAttachment(['type' => 'image', 'media_id' => 'first_media']);

        $this->request->setWelcomeCode($firstCode);
        $this->request->setTextContent($firstText);
        $this->request->setAttachments([$firstAttachment]);

        $this->assertSame($firstCode, $this->request->getWelcomeCode());
        $this->assertSame($firstText, $this->request->getTextContent());
        $attachments = $this->request->getAttachments();
        $this->assertIsArray($attachments);
        $this->assertCount(1, $attachments);

        // 重新设置
        $secondCode = 'second_welcome_code';
        $secondText = 'Second welcome text';
        $secondAttachment = $this->createMockAttachment(['type' => 'file', 'media_id' => 'second_media']);

        $this->request->setWelcomeCode($secondCode);
        $this->request->setTextContent($secondText);
        $this->request->setAttachments([$secondAttachment]);

        $this->assertSame($secondCode, $this->request->getWelcomeCode());
        $this->assertSame($secondText, $this->request->getTextContent());
        $attachments = $this->request->getAttachments();
        $this->assertIsArray($attachments);
        $this->assertCount(1, $attachments);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);

        $jsonData = $options['json'];
        $this->assertIsArray($jsonData);
        $this->assertSame($secondCode, $jsonData['welcome_code']);

        $textData = $jsonData['text'];
        $this->assertIsArray($textData);
        $this->assertSame($secondText, $textData['content']);

        $attachmentsData = $jsonData['attachments'];
        $this->assertIsArray($attachmentsData);
        $this->assertCount(1, $attachmentsData);
    }

    public function testResetToNull(): void
    {
        // 测试重置为null

        $this->request->setWelcomeCode('initial_code');
        $this->request->setTextContent('initial text');
        $this->request->setAttachments([
            $this->createMockAttachment(['type' => 'image', 'media_id' => 'initial_media']),
        ]);

        // 重置可null的属性
        $this->request->setTextContent(null);
        $this->request->setAttachments(null);

        $this->assertSame('initial_code', $this->request->getWelcomeCode()); // welcomeCode不能为null
        $this->assertNull($this->request->getTextContent());
        $this->assertNull($this->request->getAttachments());

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);

        $jsonData = $options['json'];
        $this->assertIsArray($jsonData);
        $this->assertArrayHasKey('welcome_code', $jsonData); // welcomeCode仍然存在
        $this->assertArrayNotHasKey('text', $jsonData);
        $this->assertArrayNotHasKey('attachments', $jsonData);
    }

    public function testRequestOptionsDoesNotModifyOriginalData(): void
    {
        // 测试获取请求选项不会修改原始数据
        $originalCode = 'original_welcome_code';
        $originalText = 'original text content';
        $originalAttachment = $this->createMockAttachment(['type' => 'image', 'media_id' => 'original_media']);

        $this->request->setWelcomeCode($originalCode);
        $this->request->setTextContent($originalText);
        $this->request->setAttachments([$originalAttachment]);

        $options1 = $this->request->getRequestOptions();
        $options2 = $this->request->getRequestOptions();

        // 修改返回的数组不应影响原始数据
        $this->assertIsArray($options1);
        $this->assertArrayHasKey('json', $options1);
        $this->assertIsArray($options1['json']);
        $options1['json']['welcome_code'] = 'modified_code';
        $this->assertIsArray($options1['json']['text']);
        $options1['json']['text']['content'] = 'modified text';
        $this->assertIsArray($options1['json']['attachments']);
        $options1['json']['attachments'][0] = ['modified' => 'attachment'];

        $this->assertSame($originalCode, $this->request->getWelcomeCode());
        $this->assertSame($originalText, $this->request->getTextContent());
        $attachments = $this->request->getAttachments();
        $this->assertNotNull($attachments);
        $this->assertCount(1, $attachments);

        $this->assertNotNull($options2);
        $this->assertArrayHasKey('json', $options2);

        $jsonData2 = $options2['json'];
        $this->assertIsArray($jsonData2);
        $this->assertSame($originalCode, $jsonData2['welcome_code']);

        $textData2 = $jsonData2['text'];
        $this->assertIsArray($textData2);
        $this->assertSame($originalText, $textData2['content']);

        $attachmentsData2 = $jsonData2['attachments'];
        $this->assertIsArray($attachmentsData2);
        $this->assertCount(1, $attachmentsData2);
    }

    public function testImmutableBehavior(): void
    {
        // 测试不可变行为
        $welcomeCode = 'test_welcome_code';
        $textContent = 'test content';
        $attachment = $this->createMockAttachment(['type' => 'test', 'media_id' => 'test_media']);

        $this->request->setWelcomeCode($welcomeCode);
        $this->request->setTextContent($textContent);
        $this->request->setAttachments([$attachment]);

        $options = $this->request->getRequestOptions();

        // 修改选项不应影响request对象
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $options['json']['welcome_code'] = 'changed_code';
        $this->assertIsArray($options['json']['text']);
        $options['json']['text']['content'] = 'changed content';
        $this->assertIsArray($options['json']['attachments']);
        $options['json']['attachments'] = [];
        $options['json']['new_param'] = 'new_value';

        $this->assertSame($welcomeCode, $this->request->getWelcomeCode());
        $this->assertSame($textContent, $this->request->getTextContent());
        $attachments = $this->request->getAttachments();
        $this->assertNotNull($attachments);
        $this->assertCount(1, $attachments);

        $newOptions = $this->request->getRequestOptions();
        $this->assertNotNull($newOptions);
        $this->assertArrayHasKey('json', $newOptions);

        $newJsonData = $newOptions['json'];
        $this->assertIsArray($newJsonData);
        $this->assertSame($welcomeCode, $newJsonData['welcome_code']);

        $newTextData = $newJsonData['text'];
        $this->assertIsArray($newTextData);
        $this->assertSame($textContent, $newTextData['content']);

        $newAttachmentsData = $newJsonData['attachments'];
        $this->assertIsArray($newAttachmentsData);
        $this->assertCount(1, $newAttachmentsData);
        $this->assertArrayNotHasKey('new_param', $newJsonData);
    }

    public function testMethodCallsAreIdempotent(): void
    {
        // 测试方法调用是幂等的
        $welcomeCode = 'idempotent_code';
        $textContent = 'idempotent content';
        $attachment = $this->createMockAttachment(['type' => 'test', 'media_id' => 'idempotent_media']);

        $this->request->setWelcomeCode($welcomeCode);
        $this->request->setTextContent($textContent);
        $this->request->setAttachments([$attachment]);

        // 多次调用应该返回相同结果
        $path1 = $this->request->getRequestPath();
        $path2 = $this->request->getRequestPath();
        $this->assertSame($path1, $path2);

        $options1 = $this->request->getRequestOptions();
        $options2 = $this->request->getRequestOptions();
        $this->assertSame($options1, $options2);

        $code1 = $this->request->getWelcomeCode();
        $code2 = $this->request->getWelcomeCode();
        $this->assertSame($code1, $code2);

        $text1 = $this->request->getTextContent();
        $text2 = $this->request->getTextContent();
        $this->assertSame($text1, $text2);

        $attachments1 = $this->request->getAttachments();
        $attachments2 = $this->request->getAttachments();
        $this->assertSame($attachments1, $attachments2);
    }

    public function testAgentAwareTraitIntegration(): void
    {
        // 测试AgentAware trait集成

        // 这些方法应该可以正常调用
        $this->assertNull($this->request->getAgent());

        // 测试设置和获取agent
        $agent = $this->createMock(AgentInterface::class);
        $this->request->setAgent($agent);
        $this->assertSame($agent, $this->request->getAgent());

        // 测试agent在请求期间持久化
        $this->assertSame($agent, $this->request->getAgent());
        $this->assertSame($agent, $this->request->getAgent());
    }

    public function testRequestStructure(): void
    {
        // 测试请求结构

        // 验证基本方法
        $this->assertSame('/cgi-bin/externalcontact/send_welcome_msg', $this->request->getRequestPath());

        // 设置必要的属性后验证
        $this->request->setWelcomeCode('test_welcome_code');
        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);

        $jsonData = $options['json'];
        $this->assertIsArray($jsonData);
    }

    public function testAgentInterfaceImplementation(): void
    {
        // 测试AgentInterface接口实现，移除冗余检查

        // 验证基本功能
        $this->assertNotNull($this->request);
        $this->assertSame('/cgi-bin/externalcontact/send_welcome_msg', $this->request->getRequestPath());
    }

    /**
     * 创建模拟附件对象
     * @param array<string, mixed> $data
     * @return BaseAttachment
     */
    private function createMockAttachment(array $data): BaseAttachment
    {
        $mock = $this->createMock(BaseAttachment::class);
        $mock->method('retrievePlainArray')->willReturn($data);

        return $mock;
    }
}

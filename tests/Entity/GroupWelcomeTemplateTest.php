<?php

namespace WechatWorkExternalContactBundle\Tests\Entity;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkExternalContactBundle\Entity\GroupWelcomeTemplate;
use WechatWorkMediaBundle\Entity\TempMedia;

/**
 * GroupWelcomeTemplate 实体测试用例
 * 
 * 测试群欢迎模板实体的所有功能
 */
class GroupWelcomeTemplateTest extends TestCase
{
    private GroupWelcomeTemplate $template;

    protected function setUp(): void
    {
        $this->template = new GroupWelcomeTemplate();
    }

    public function test_constructor_setsDefaultValues(): void
    {
        $template = new GroupWelcomeTemplate();
        
        $this->assertNull($template->getId());
        $this->assertNull($template->getCreatedBy());
        $this->assertNull($template->getUpdatedBy());
        $this->assertNull($template->getAgent());
        $this->assertNull($template->getTemplateId());
        $this->assertTrue($template->isNotify()); // 默认为true
        $this->assertNull($template->getTextContent());
        $this->assertNull($template->getImageMedia());
        $this->assertNull($template->getImagePicUrl());
        $this->assertNull($template->getLinkTitle());
        $this->assertNull($template->getLinkPicUrl());
        $this->assertNull($template->getLinkDesc());
        $this->assertNull($template->getLinkUrl());
        $this->assertNull($template->getMiniprogramTitle());
        $this->assertNull($template->getMiniprogramMedia());
        $this->assertNull($template->getMiniprogramAppId());
        $this->assertNull($template->getMiniprogramPage());
        $this->assertNull($template->getFileMedia());
        $this->assertNull($template->getVideoMedia());
        $this->assertNull($template->isSync());
        $this->assertNull($template->getCreatedFromIp());
        $this->assertNull($template->getUpdatedFromIp());
        $this->assertNull($template->getCreateTime());
        $this->assertNull($template->getUpdateTime());
    }

    public function test_setCreatedBy_withValidString_setsCreatedByCorrectly(): void
    {
        $createdBy = 'admin_user_123';
        
        $result = $this->template->setCreatedBy($createdBy);
        
        $this->assertSame($this->template, $result);
        $this->assertSame($createdBy, $this->template->getCreatedBy());
    }

    public function test_setCreatedBy_withNull_setsNull(): void
    {
        $this->template->setCreatedBy('old_user');
        
        $result = $this->template->setCreatedBy(null);
        
        $this->assertSame($this->template, $result);
        $this->assertNull($this->template->getCreatedBy());
    }

    public function test_setUpdatedBy_withValidString_setsUpdatedByCorrectly(): void
    {
        $updatedBy = 'editor_user_456';
        
        $result = $this->template->setUpdatedBy($updatedBy);
        
        $this->assertSame($this->template, $result);
        $this->assertSame($updatedBy, $this->template->getUpdatedBy());
    }

    public function test_setUpdatedBy_withNull_setsNull(): void
    {
        $this->template->setUpdatedBy('old_editor');
        
        $result = $this->template->setUpdatedBy(null);
        
        $this->assertSame($this->template, $result);
        $this->assertNull($this->template->getUpdatedBy());
    }

    public function test_setAgent_withValidAgent_setsAgentCorrectly(): void
    {
        /** @var AgentInterface&MockObject $agent */
        $agent = $this->createMock(AgentInterface::class);
        
        $result = $this->template->setAgent($agent);
        
        $this->assertSame($this->template, $result);
        $this->assertSame($agent, $this->template->getAgent());
    }

    public function test_setAgent_withNull_setsNull(): void
    {
        /** @var AgentInterface&MockObject $agent */
        $agent = $this->createMock(AgentInterface::class);
        $this->template->setAgent($agent);
        
        $result = $this->template->setAgent(null);
        
        $this->assertSame($this->template, $result);
        $this->assertNull($this->template->getAgent());
    }

    public function test_setTemplateId_withValidId_setsIdCorrectly(): void
    {
        $templateId = 'template_123456_abcdef';
        
        $result = $this->template->setTemplateId($templateId);
        
        $this->assertSame($this->template, $result);
        $this->assertSame($templateId, $this->template->getTemplateId());
    }

    public function test_setTemplateId_withNull_setsNull(): void
    {
        $this->template->setTemplateId('old_template_id');
        
        $result = $this->template->setTemplateId(null);
        
        $this->assertSame($this->template, $result);
        $this->assertNull($this->template->getTemplateId());
    }

    public function test_setTemplateId_withLongId_setsLongId(): void
    {
        $longTemplateId = str_repeat('a', 120); // 最大长度
        
        $result = $this->template->setTemplateId($longTemplateId);
        
        $this->assertSame($this->template, $result);
        $this->assertSame($longTemplateId, $this->template->getTemplateId());
    }

    public function test_setNotify_withTrue_setsTrue(): void
    {
        $result = $this->template->setNotify(true);
        
        $this->assertSame($this->template, $result);
        $this->assertTrue($this->template->isNotify());
    }

    public function test_setNotify_withFalse_setsFalse(): void
    {
        $result = $this->template->setNotify(false);
        
        $this->assertSame($this->template, $result);
        $this->assertFalse($this->template->isNotify());
    }

    public function test_setNotify_withNull_setsNull(): void
    {
        $this->template->setNotify(true);
        
        $result = $this->template->setNotify(null);
        
        $this->assertSame($this->template, $result);
        $this->assertNull($this->template->isNotify());
    }

    public function test_setTextContent_withValidContent_setsContentCorrectly(): void
    {
        $textContent = '欢迎加入我们的产品讨论群！在这里您可以获取最新的产品信息和技术支持。';
        
        $result = $this->template->setTextContent($textContent);
        
        $this->assertSame($this->template, $result);
        $this->assertSame($textContent, $this->template->getTextContent());
    }

    public function test_setTextContent_withNull_setsNull(): void
    {
        $this->template->setTextContent('old content');
        
        $result = $this->template->setTextContent(null);
        
        $this->assertSame($this->template, $result);
        $this->assertNull($this->template->getTextContent());
    }

    public function test_setTextContent_withLongContent_setsLongContent(): void
    {
        $longContent = str_repeat('这是一段很长的欢迎词。', 100); // 长文本
        
        $result = $this->template->setTextContent($longContent);
        
        $this->assertSame($this->template, $result);
        $this->assertSame($longContent, $this->template->getTextContent());
    }

    /**
     * 测试图片相关功能
     */
    public function test_setImageMedia_withValidMedia_setsMediaCorrectly(): void
    {
        /** @var TempMedia&MockObject $imageMedia */
        $imageMedia = $this->createMock(TempMedia::class);
        
        $result = $this->template->setImageMedia($imageMedia);
        
        $this->assertSame($this->template, $result);
        $this->assertSame($imageMedia, $this->template->getImageMedia());
    }

    public function test_setImageMedia_withNull_setsNull(): void
    {
        /** @var TempMedia&MockObject $imageMedia */
        $imageMedia = $this->createMock(TempMedia::class);
        $this->template->setImageMedia($imageMedia);
        
        $result = $this->template->setImageMedia(null);
        
        $this->assertSame($this->template, $result);
        $this->assertNull($this->template->getImageMedia());
    }

    public function test_setImagePicUrl_withValidUrl_setsUrlCorrectly(): void
    {
        $imagePicUrl = 'https://example.com/images/welcome_banner.png';
        
        $result = $this->template->setImagePicUrl($imagePicUrl);
        
        $this->assertSame($this->template, $result);
        $this->assertSame($imagePicUrl, $this->template->getImagePicUrl());
    }

    public function test_setImagePicUrl_withNull_setsNull(): void
    {
        $this->template->setImagePicUrl('https://old.example.com/image.png');
        
        $result = $this->template->setImagePicUrl(null);
        
        $this->assertSame($this->template, $result);
        $this->assertNull($this->template->getImagePicUrl());
    }

    /**
     * 测试链接相关功能
     */
    public function test_setLinkTitle_withValidTitle_setsTitleCorrectly(): void
    {
        $linkTitle = '产品官网';
        
        $result = $this->template->setLinkTitle($linkTitle);
        
        $this->assertSame($this->template, $result);
        $this->assertSame($linkTitle, $this->template->getLinkTitle());
    }

    public function test_setLinkTitle_withNull_setsNull(): void
    {
        $this->template->setLinkTitle('old title');
        
        $result = $this->template->setLinkTitle(null);
        
        $this->assertSame($this->template, $result);
        $this->assertNull($this->template->getLinkTitle());
    }

    public function test_setLinkPicUrl_withValidUrl_setsUrlCorrectly(): void
    {
        $linkPicUrl = 'https://example.com/images/link_thumb.png';
        
        $result = $this->template->setLinkPicUrl($linkPicUrl);
        
        $this->assertSame($this->template, $result);
        $this->assertSame($linkPicUrl, $this->template->getLinkPicUrl());
    }

    public function test_setLinkDesc_withValidDesc_setsDescCorrectly(): void
    {
        $linkDesc = '了解更多产品功能和技术细节，访问我们的官方网站。';
        
        $result = $this->template->setLinkDesc($linkDesc);
        
        $this->assertSame($this->template, $result);
        $this->assertSame($linkDesc, $this->template->getLinkDesc());
    }

    public function test_setLinkUrl_withValidUrl_setsUrlCorrectly(): void
    {
        $linkUrl = 'https://example.com/products';
        
        $result = $this->template->setLinkUrl($linkUrl);
        
        $this->assertSame($this->template, $result);
        $this->assertSame($linkUrl, $this->template->getLinkUrl());
    }

    /**
     * 测试小程序相关功能
     */
    public function test_setMiniprogramTitle_withValidTitle_setsTitleCorrectly(): void
    {
        $miniprogramTitle = '产品小程序';
        
        $result = $this->template->setMiniprogramTitle($miniprogramTitle);
        
        $this->assertSame($this->template, $result);
        $this->assertSame($miniprogramTitle, $this->template->getMiniprogramTitle());
    }

    public function test_setMiniprogramMedia_withValidMedia_setsMediaCorrectly(): void
    {
        /** @var TempMedia&MockObject $miniprogramMedia */
        $miniprogramMedia = $this->createMock(TempMedia::class);
        
        $result = $this->template->setMiniprogramMedia($miniprogramMedia);
        
        $this->assertSame($this->template, $result);
        $this->assertSame($miniprogramMedia, $this->template->getMiniprogramMedia());
    }

    public function test_setMiniprogramAppId_withValidAppId_setsAppIdCorrectly(): void
    {
        $miniprogramAppId = 'wx1234567890abcdef';
        
        $result = $this->template->setMiniprogramAppId($miniprogramAppId);
        
        $this->assertSame($this->template, $result);
        $this->assertSame($miniprogramAppId, $this->template->getMiniprogramAppId());
    }

    public function test_setMiniprogramPage_withValidPage_setsPageCorrectly(): void
    {
        $miniprogramPage = 'pages/index/index?param=value';
        
        $result = $this->template->setMiniprogramPage($miniprogramPage);
        
        $this->assertSame($this->template, $result);
        $this->assertSame($miniprogramPage, $this->template->getMiniprogramPage());
    }

    /**
     * 测试文件和视频媒体
     */
    public function test_setFileMedia_withValidMedia_setsMediaCorrectly(): void
    {
        /** @var TempMedia&MockObject $fileMedia */
        $fileMedia = $this->createMock(TempMedia::class);
        
        $result = $this->template->setFileMedia($fileMedia);
        
        $this->assertSame($this->template, $result);
        $this->assertSame($fileMedia, $this->template->getFileMedia());
    }

    public function test_setVideoMedia_withValidMedia_setsMediaCorrectly(): void
    {
        /** @var TempMedia&MockObject $videoMedia */
        $videoMedia = $this->createMock(TempMedia::class);
        
        $result = $this->template->setVideoMedia($videoMedia);
        
        $this->assertSame($this->template, $result);
        $this->assertSame($videoMedia, $this->template->getVideoMedia());
    }

    /**
     * 测试同步状态
     */
    public function test_setSync_withTrue_setsTrue(): void
    {
        $result = $this->template->setSync(true);
        
        $this->assertSame($this->template, $result);
        $this->assertTrue($this->template->isSync());
    }

    public function test_setSync_withFalse_setsFalse(): void
    {
        $result = $this->template->setSync(false);
        
        $this->assertSame($this->template, $result);
        $this->assertFalse($this->template->isSync());
    }

    public function test_setSync_withNull_setsNull(): void
    {
        $this->template->setSync(true);
        
        $result = $this->template->setSync(null);
        
        $this->assertSame($this->template, $result);
        $this->assertNull($this->template->isSync());
    }

    /**
     * 测试IP地址相关
     */
    public function test_setCreatedFromIp_withValidIp_setsIpCorrectly(): void
    {
        $createdFromIp = '192.168.1.100';
        
        $result = $this->template->setCreatedFromIp($createdFromIp);
        
        $this->assertSame($this->template, $result);
        $this->assertSame($createdFromIp, $this->template->getCreatedFromIp());
    }

    public function test_setCreatedFromIp_withIpv6_setsIpCorrectly(): void
    {
        $createdFromIp = '2001:db8::1';
        
        $result = $this->template->setCreatedFromIp($createdFromIp);
        
        $this->assertSame($this->template, $result);
        $this->assertSame($createdFromIp, $this->template->getCreatedFromIp());
    }

    public function test_setUpdatedFromIp_withValidIp_setsIpCorrectly(): void
    {
        $updatedFromIp = '10.0.0.5';
        
        $result = $this->template->setUpdatedFromIp($updatedFromIp);
        
        $this->assertSame($this->template, $result);
        $this->assertSame($updatedFromIp, $this->template->getUpdatedFromIp());
    }

    /**
     * 测试时间戳
     */
    public function test_setCreateTime_withValidDateTime_setsTimeCorrectly(): void
    {
        $createTime = new \DateTime('2024-01-01 08:00:00');
        
        $this->template->setCreateTime($createTime);
        
        $this->assertSame($createTime, $this->template->getCreateTime());
    }

    public function test_setCreateTime_withNull_setsNull(): void
    {
        $this->template->setCreateTime(new \DateTime());
        
        $this->template->setCreateTime(null);
        
        $this->assertNull($this->template->getCreateTime());
    }

    public function test_setUpdateTime_withValidDateTime_setsTimeCorrectly(): void
    {
        $updateTime = new \DateTime('2024-01-30 18:30:00');
        
        $this->template->setUpdateTime($updateTime);
        
        $this->assertSame($updateTime, $this->template->getUpdateTime());
    }

    public function test_setUpdateTime_withNull_setsNull(): void
    {
        $this->template->setUpdateTime(new \DateTime());
        
        $this->template->setUpdateTime(null);
        
        $this->assertNull($this->template->getUpdateTime());
    }

    /**
     * 测试链式调用
     */
    public function test_chainedSetters_returnSameInstance(): void
    {
        /** @var AgentInterface&MockObject $agent */
        $agent = $this->createMock(AgentInterface::class);
        /** @var TempMedia&MockObject $imageMedia */
        $imageMedia = $this->createMock(TempMedia::class);
        
        $result = $this->template
            ->setCreatedBy('admin_123')
            ->setUpdatedBy('editor_456')
            ->setAgent($agent)
            ->setTemplateId('template_789')
            ->setNotify(true)
            ->setTextContent('欢迎语')
            ->setImageMedia($imageMedia)
            ->setImagePicUrl('https://example.com/image.png')
            ->setLinkTitle('链接标题')
            ->setLinkPicUrl('https://example.com/link.png')
            ->setLinkDesc('链接描述')
            ->setLinkUrl('https://example.com/link')
            ->setMiniprogramTitle('小程序标题')
            ->setMiniprogramAppId('wxapp123')
            ->setMiniprogramPage('pages/index')
            ->setSync(false)
            ->setCreatedFromIp('192.168.1.100')
            ->setUpdatedFromIp('192.168.1.101');
        
        $this->assertSame($this->template, $result);
        $this->assertSame('admin_123', $this->template->getCreatedBy());
        $this->assertSame('editor_456', $this->template->getUpdatedBy());
        $this->assertSame($agent, $this->template->getAgent());
        $this->assertSame('template_789', $this->template->getTemplateId());
        $this->assertTrue($this->template->isNotify());
        $this->assertSame('欢迎语', $this->template->getTextContent());
        $this->assertSame($imageMedia, $this->template->getImageMedia());
        $this->assertSame('https://example.com/image.png', $this->template->getImagePicUrl());
        $this->assertSame('链接标题', $this->template->getLinkTitle());
        $this->assertSame('链接描述', $this->template->getLinkDesc());
        $this->assertFalse($this->template->isSync());
    }

    /**
     * 测试边界场景
     */
    public function test_edgeCases_longStrings(): void
    {
        $maxTemplateId = str_repeat('a', 120);
        $maxImagePicUrl = str_repeat('https://example.com/', 8) . str_repeat('x', 31); // 长URL
        $maxLinkTitle = str_repeat('标题', 64);
        $maxMiniprogramAppId = str_repeat('w', 64);
        $maxCreatedFromIp = str_repeat('2001:db8::', 14) . '1'; // IPv6地址
        
        $this->template->setTemplateId($maxTemplateId);
        $this->template->setImagePicUrl($maxImagePicUrl);
        $this->template->setLinkTitle($maxLinkTitle);
        $this->template->setMiniprogramAppId($maxMiniprogramAppId);
        $this->template->setCreatedFromIp($maxCreatedFromIp);
        
        $this->assertSame($maxTemplateId, $this->template->getTemplateId());
        $this->assertSame($maxImagePicUrl, $this->template->getImagePicUrl());
        $this->assertSame($maxLinkTitle, $this->template->getLinkTitle());
        $this->assertSame($maxMiniprogramAppId, $this->template->getMiniprogramAppId());
        $this->assertSame($maxCreatedFromIp, $this->template->getCreatedFromIp());
    }

    public function test_edgeCases_dateTimeTypes(): void
    {
        // 测试DateTime
        $dateTime = new \DateTime('2024-01-15 12:30:45');
        $this->template->setCreateTime($dateTime);
        $this->assertSame($dateTime, $this->template->getCreateTime());
        
        // 测试DateTimeImmutable
        $dateTimeImmutable = new \DateTimeImmutable('2024-02-20 09:15:30');
        $this->template->setUpdateTime($dateTimeImmutable);
        $this->assertSame($dateTimeImmutable, $this->template->getUpdateTime());
    }

    /**
     * 测试业务逻辑场景
     */
    public function test_businessScenario_textOnlyTemplate(): void
    {
        /** @var AgentInterface&MockObject $agent */
        $agent = $this->createMock(AgentInterface::class);
        
        $createTime = new \DateTime('2024-01-15 10:00:00');
        
        // 模拟纯文本欢迎模板
        $this->template
            ->setAgent($agent)
            ->setTemplateId('text_template_001')
            ->setNotify(true)
            ->setTextContent('欢迎加入我们的技术交流群！请遵守群规，文明交流。')
            ->setSync(true);
        
        $this->template->setCreateTime($createTime);
        $this->template->setCreatedBy('admin_user');
        $this->template->setCreatedFromIp('192.168.1.100');
        
        // 验证纯文本模板状态
        $this->assertNotNull($this->template->getAgent());
        $this->assertNotNull($this->template->getTextContent());
        $this->assertNull($this->template->getImageMedia());
        $this->assertNull($this->template->getLinkTitle());
        $this->assertNull($this->template->getMiniprogramTitle());
        $this->assertTrue($this->template->isNotify());
        $this->assertTrue($this->template->isSync());
    }

    public function test_businessScenario_richMediaTemplate(): void
    {
        /** @var AgentInterface&MockObject $agent */
        $agent = $this->createMock(AgentInterface::class);
        /** @var TempMedia&MockObject $imageMedia */
        $imageMedia = $this->createMock(TempMedia::class);
        /** @var TempMedia&MockObject $miniprogramMedia */
        $miniprogramMedia = $this->createMock(TempMedia::class);
        
        // 模拟富媒体欢迎模板
        $this->template
            ->setAgent($agent)
            ->setTemplateId('rich_template_002')
            ->setNotify(true)
            ->setTextContent('欢迎加入产品体验群！')
            ->setImageMedia($imageMedia)
            ->setImagePicUrl('https://example.com/welcome-banner.png')
            ->setLinkTitle('产品官网')
            ->setLinkDesc('了解更多产品功能')
            ->setLinkUrl('https://example.com/products')
            ->setMiniprogramTitle('产品小程序')
            ->setMiniprogramMedia($miniprogramMedia)
            ->setMiniprogramAppId('wx1234567890abcdef')
            ->setMiniprogramPage('pages/welcome/index')
            ->setSync(false); // 还未同步
        
        $this->template->setCreatedBy('marketing_admin');
        $this->template->setCreatedFromIp('10.0.0.50');
        
        // 验证富媒体模板状态
        $this->assertNotNull($this->template->getTextContent());
        $this->assertNotNull($this->template->getImageMedia());
        $this->assertNotNull($this->template->getLinkTitle());
        $this->assertNotNull($this->template->getMiniprogramTitle());
        $this->assertNotNull($this->template->getMiniprogramAppId());
        $this->assertFalse($this->template->isSync()); // 未同步状态
    }

    public function test_businessScenario_templateSyncFlow(): void
    {
        /** @var AgentInterface&MockObject $agent */
        $agent = $this->createMock(AgentInterface::class);
        
        $createTime = new \DateTime('2024-01-15 10:00:00');
        $updateTime = new \DateTime('2024-01-15 10:30:00');
        
        // 模拟模板同步流程
        $this->template
            ->setAgent($agent)
            ->setTextContent('欢迎加入群聊！')
            ->setSync(false); // 初始未同步
        
        $this->template->setCreateTime($createTime);
        $this->template->setCreatedBy('template_creator');
        
        // 验证初始状态
        $this->assertFalse($this->template->isSync());
        $this->assertNull($this->template->getTemplateId());
        
        // 模拟同步到企业微信后
        $this->template->setTemplateId('synced_template_123');
        $this->template->setSync(true);
        $this->template->setUpdateTime($updateTime);
        $this->template->setUpdatedBy('sync_service');
        
        // 验证同步后状态
        $this->assertTrue($this->template->isSync());
        $this->assertNotNull($this->template->getTemplateId());
        $this->assertSame('synced_template_123', $this->template->getTemplateId());
        $this->assertTrue($updateTime > $createTime);
    }

    public function test_businessScenario_templateVersioning(): void
    {
        $createTime = new \DateTime('2024-01-15 08:00:00');
        $firstUpdateTime = new \DateTime('2024-01-15 10:00:00');
        $secondUpdateTime = new \DateTime('2024-01-15 14:00:00');
        
        // 模拟模板版本变更
        $this->template->setTextContent('原始欢迎语');
        $this->template->setCreateTime($createTime);
        $this->template->setCreatedBy('content_creator');
        $this->template->setCreatedFromIp('192.168.1.100');
        
        // 第一次更新
        $this->template->setTextContent('更新后的欢迎语');
        $this->template->setUpdateTime($firstUpdateTime);
        $this->template->setUpdatedBy('content_editor');
        $this->template->setUpdatedFromIp('192.168.1.101');
        
        // 第二次更新
        $this->template->setTextContent('最终版本的欢迎语');
        $this->template->setUpdateTime($secondUpdateTime);
        $this->template->setUpdatedBy('final_reviewer');
        $this->template->setUpdatedFromIp('192.168.1.102');
        
        // 验证版本变更历史
        $this->assertSame('最终版本的欢迎语', $this->template->getTextContent());
        $this->assertSame('final_reviewer', $this->template->getUpdatedBy());
        $this->assertSame('192.168.1.102', $this->template->getUpdatedFromIp());
        $this->assertTrue($createTime < $firstUpdateTime);
        $this->assertTrue($firstUpdateTime < $secondUpdateTime);
    }

    public function test_businessScenario_notificationSettings(): void
    {
        // 测试通知设置的业务逻辑
        
        // 默认启用通知
        $this->assertTrue($this->template->isNotify());
        
        // 禁用通知（用于内部测试模板）
        $this->template->setNotify(false);
        $this->assertFalse($this->template->isNotify());
        
        // 重新启用通知
        $this->template->setNotify(true);
        $this->assertTrue($this->template->isNotify());
    }
} 
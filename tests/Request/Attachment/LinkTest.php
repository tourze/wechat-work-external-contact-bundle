<?php

namespace WechatWorkExternalContactBundle\Tests\Request\Attachment;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatWorkExternalContactBundle\Request\Attachment\Link;

/**
 * Link 附件测试
 *
 * @internal
 */
#[CoversClass(Link::class)]
#[RunTestsInSeparateProcesses] final class LinkTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void        // 此测试不需要特殊的设置
    {
    }

    public function testInheritance(): void
    {
        // 测试继承关系
        $link = self::getService(Link::class);
        $this->assertInstanceOf(PlainArrayInterface::class, $link);

        // 测试PlainArrayInterface接口的实际功能
        $link->setTitle('测试链接');
        $link->setUrl('https://example.com');
        $link->setDesc('测试链接描述');
        $link->setPicUrl('https://example.com/image.jpg');
        $array = $link->retrievePlainArray();
        $this->assertIsArray($array);
        $this->assertArrayHasKey('msgtype', $array);
        $this->assertArrayHasKey('link', $array);
    }

    public function testTitleSetterAndGetter(): void
    {
        // 测试标题设置和获取
        $link = self::getService(Link::class);
        $title = '企业产品介绍';

        $link->setTitle($title);
        $this->assertSame($title, $link->getTitle());
    }

    public function testTitleWithSpecialCharacters(): void
    {
        // 测试特殊字符标题
        $link = self::getService(Link::class);
        $specialTitle = '产品2024版 - 全新体验！@#$%';
        $link->setTitle($specialTitle);

        $this->assertSame($specialTitle, $link->getTitle());
    }

    public function testTitleWithMaxLength(): void
    {
        // 测试最大长度标题（128字节）
        $link = self::getService(Link::class);
        $maxTitle = str_repeat('标', 42) . '题'; // 约126字节（每个中文字符3字节）
        $link->setTitle($maxTitle);

        $this->assertSame($maxTitle, $link->getTitle());
    }

    public function testUrlSetterAndGetter(): void
    {
        // 测试URL设置和获取
        $link = self::getService(Link::class);
        $url = 'https://company.com/product';

        $link->setUrl($url);
        $this->assertSame($url, $link->getUrl());
    }

    public function testUrlWithComplexUrl(): void
    {
        // 测试复杂URL
        $link = self::getService(Link::class);
        $complexUrl = 'https://api.company.com/v1/product?id=123&category=tech&utm_source=wechat';
        $link->setUrl($complexUrl);

        $this->assertSame($complexUrl, $link->getUrl());
    }

    public function testPicUrlSetterAndGetter(): void
    {
        // 测试图片URL设置和获取
        $link = self::getService(Link::class);
        $picUrl = 'https://cdn.company.com/images/product.jpg';

        $link->setPicUrl($picUrl);
        $this->assertSame($picUrl, $link->getPicUrl());
    }

    public function testPicUrlWithNull(): void
    {
        // 测试null图片URL
        $link = self::getService(Link::class);
        $link->setPicUrl(null);

        $this->assertNull($link->getPicUrl());
    }

    public function testDescSetterAndGetter(): void
    {
        // 测试描述设置和获取
        $link = self::getService(Link::class);
        $desc = '这是一个优秀的产品介绍，包含详细的功能说明和使用指南。';

        $link->setDesc($desc);
        $this->assertSame($desc, $link->getDesc());
    }

    public function testDescWithNull(): void
    {
        // 测试null描述
        $link = self::getService(Link::class);
        $link->setDesc(null);

        $this->assertNull($link->getDesc());
    }

    public function testDescWithMaxLength(): void
    {
        // 测试最大长度描述（512字节）
        $link = self::getService(Link::class);
        $maxDesc = str_repeat('详细描述内容', 25); // 约375字节
        $link->setDesc($maxDesc);

        $this->assertSame($maxDesc, $link->getDesc());
    }

    public function testRetrievePlainArrayWithRequiredFieldsOnly(): void
    {
        // 测试仅必填字段的数组转换
        $link = self::getService(Link::class);
        $title = '产品标题';
        $url = 'https://company.com/product';

        $link->setTitle($title);
        $link->setUrl($url);

        $expected = [
            'msgtype' => 'link',
            'link' => [
                'title' => $title,
                'url' => $url,
            ],
        ];

        $this->assertSame($expected, $link->retrievePlainArray());
    }

    public function testRetrievePlainArrayWithAllFields(): void
    {
        // 测试所有字段的数组转换
        $link = self::getService(Link::class);
        $title = '完整产品介绍';
        $url = 'https://company.com/complete-product';
        $picUrl = 'https://cdn.company.com/product-cover.jpg';
        $desc = '这是一个包含所有字段的完整产品介绍链接。';

        $link->setTitle($title);
        $link->setUrl($url);
        $link->setPicUrl($picUrl);
        $link->setDesc($desc);

        $expected = [
            'msgtype' => 'link',
            'link' => [
                'title' => $title,
                'url' => $url,
                'picurl' => $picUrl,
                'desc' => $desc,
            ],
        ];

        $this->assertSame($expected, $link->retrievePlainArray());
    }

    public function testRetrievePlainArrayWithOnlyPicUrl(): void
    {
        // 测试仅有图片URL的数组转换
        $link = self::getService(Link::class);
        $title = '有图产品';
        $url = 'https://company.com/with-pic';
        $picUrl = 'https://cdn.company.com/pic.jpg';

        $link->setTitle($title);
        $link->setUrl($url);
        $link->setPicUrl($picUrl);

        $expected = [
            'msgtype' => 'link',
            'link' => [
                'title' => $title,
                'url' => $url,
                'picurl' => $picUrl,
            ],
        ];

        $this->assertSame($expected, $link->retrievePlainArray());
    }

    public function testRetrievePlainArrayWithOnlyDesc(): void
    {
        // 测试仅有描述的数组转换
        $link = self::getService(Link::class);
        $title = '有描述产品';
        $url = 'https://company.com/with-desc';
        $desc = '产品详细描述信息';

        $link->setTitle($title);
        $link->setUrl($url);
        $link->setDesc($desc);

        $expected = [
            'msgtype' => 'link',
            'link' => [
                'title' => $title,
                'url' => $url,
                'desc' => $desc,
            ],
        ];

        $this->assertSame($expected, $link->retrievePlainArray());
    }

    public function testRetrievePlainArrayStructure(): void
    {
        // 测试数组结构
        $link = self::getService(Link::class);
        $link->setTitle('structure_test_title');
        $link->setPicUrl('https://example.com/pic.jpg');
        $link->setDesc('structure_test_description');
        $link->setUrl('https://example.com/link');

        $array = $link->retrievePlainArray();

        $this->assertCount(2, $array);
        $this->assertArrayHasKey('msgtype', $array);
        $this->assertArrayHasKey('link', $array);
        $this->assertSame('link', $array['msgtype']);
        $this->assertIsArray($array['link']);
        $this->assertCount(4, $array['link']);
    }

    public function testBusinessScenarioProductIntroduction(): void
    {
        // 测试业务场景：产品介绍链接
        $link = self::getService(Link::class);
        $link->setTitle('2024年新品发布');
        $link->setUrl('https://company.com/products/2024/new-release');
        $link->setPicUrl('https://cdn.company.com/2024-new-product.jpg');
        $link->setDesc('我们隆重推出2024年度旗舰产品，具有革命性的功能和设计。');

        $array = $link->retrievePlainArray();

        $this->assertIsArray($array);
        $this->assertSame('link', $array['msgtype']);
        $this->assertArrayHasKey('link', $array);
        $this->assertIsArray($array['link']);
        $this->assertSame('2024年新品发布', $array['link']['title']);
        $this->assertStringContainsString('2024', $array['link']['url']);
        $this->assertArrayHasKey('picurl', $array['link']);
        $this->assertArrayHasKey('desc', $array['link']);
    }

    public function testBusinessScenarioNewsArticle(): void
    {
        // 测试业务场景：新闻文章链接
        $link = self::getService(Link::class);
        $link->setTitle('行业最新动态');
        $link->setUrl('https://news.company.com/industry-trends-2024');
        $link->setDesc('了解行业最新发展趋势和市场动态。');

        $array = $link->retrievePlainArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('link', $array);
        $this->assertIsArray($array['link']);
        $this->assertSame('行业最新动态', $array['link']['title']);
        $this->assertStringContainsString('news', $array['link']['url']);
        $this->assertArrayNotHasKey('picurl', $array['link']);
        $this->assertArrayHasKey('desc', $array['link']);
    }

    public function testBusinessScenarioSimpleLink(): void
    {
        // 测试业务场景：简单链接
        $link = self::getService(Link::class);
        $link->setTitle('官网首页');
        $link->setUrl('https://company.com');

        $array = $link->retrievePlainArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('link', $array);
        $this->assertIsArray($array['link']);
        $this->assertSame('官网首页', $array['link']['title']);
        $this->assertSame('https://company.com', $array['link']['url']);
        $this->assertArrayNotHasKey('picurl', $array['link']);
        $this->assertArrayNotHasKey('desc', $array['link']);

        // 验证符合企业微信API要求
        $this->assertArrayHasKey('msgtype', $array);
        $this->assertArrayHasKey('link', $array);
    }

    public function testMultipleSetCalls(): void
    {
        // 测试多次设置值
        $link = self::getService(Link::class);

        $link->setTitle('第一个标题');
        $link->setUrl('https://first.com');
        $link->setPicUrl('https://first-pic.com');
        $link->setDesc('第一个描述');

        $this->assertSame('第一个标题', $link->getTitle());
        $this->assertSame('https://first.com', $link->getUrl());
        $this->assertSame('https://first-pic.com', $link->getPicUrl());
        $this->assertSame('第一个描述', $link->getDesc());

        // 重新设置
        $link->setTitle('第二个标题');
        $link->setUrl('https://second.com');
        $link->setPicUrl('https://second-pic.com');
        $link->setDesc('第二个描述');

        $this->assertSame('第二个标题', $link->getTitle());
        $this->assertSame('https://second.com', $link->getUrl());
        $this->assertSame('https://second-pic.com', $link->getPicUrl());
        $this->assertSame('第二个描述', $link->getDesc());

        $array = $link->retrievePlainArray();
        $this->assertIsArray($array);
        $this->assertArrayHasKey('link', $array);
        $this->assertIsArray($array['link']);
        $this->assertSame('第二个标题', $array['link']['title']);
        $this->assertSame('https://second.com', $array['link']['url']);
    }

    public function testResetToNull(): void
    {
        // 测试重置可选字段为null
        $link = self::getService(Link::class);

        $link->setTitle('初始标题');
        $link->setUrl('https://initial.com');
        $link->setPicUrl('https://initial-pic.com');
        $link->setDesc('初始描述');

        // 重置可选字段为null
        $link->setPicUrl(null);
        $link->setDesc(null);

        $this->assertSame('初始标题', $link->getTitle());
        $this->assertSame('https://initial.com', $link->getUrl());
        $this->assertNull($link->getPicUrl());
        $this->assertNull($link->getDesc());

        $array = $link->retrievePlainArray();
        $this->assertIsArray($array);
        $this->assertArrayHasKey('link', $array);
        $this->assertIsArray($array['link']);
        $this->assertArrayHasKey('title', $array['link']);
        $this->assertArrayHasKey('url', $array['link']);
        $this->assertArrayNotHasKey('picurl', $array['link']);
        $this->assertArrayNotHasKey('desc', $array['link']);
    }

    public function testRetrievePlainArrayDoesNotModifyOriginalData(): void
    {
        // 测试获取数组不会修改原始数据
        $link = self::getService(Link::class);
        $originalTitle = '原始标题';
        $originalUrl = 'https://original.com';
        $originalPicUrl = 'https://original-pic.com';
        $originalDesc = '原始描述';

        $link->setTitle($originalTitle);
        $link->setUrl($originalUrl);
        $link->setPicUrl($originalPicUrl);
        $link->setDesc($originalDesc);

        $array1 = $link->retrievePlainArray();
        $array2 = $link->retrievePlainArray();

        // 修改返回的数组不应影响原始数据
        $this->assertIsArray($array1);
        $this->assertArrayHasKey('link', $array1);
        $this->assertIsArray($array1['link']);
        $array1['link']['title'] = '修改后标题';
        $array1['link']['url'] = 'https://modified.com';
        $array1['link']['picurl'] = 'https://modified-pic.com';
        $array1['link']['desc'] = '修改后描述';
        $array1['msgtype'] = 'modified_type';

        $this->assertSame($originalTitle, $link->getTitle());
        $this->assertSame($originalUrl, $link->getUrl());
        $this->assertSame($originalPicUrl, $link->getPicUrl());
        $this->assertSame($originalDesc, $link->getDesc());

        $this->assertIsArray($array2);
        $this->assertArrayHasKey('link', $array2);
        $this->assertIsArray($array2['link']);
        $this->assertSame($originalTitle, $array2['link']['title']);
        $this->assertSame($originalUrl, $array2['link']['url']);
        $this->assertSame($originalPicUrl, $array2['link']['picurl']);
        $this->assertSame($originalDesc, $array2['link']['desc']);
        $this->assertSame('link', $array2['msgtype']);
    }

    public function testImmutableBehavior(): void
    {
        // 测试不可变行为
        $link = self::getService(Link::class);
        $title = '不可变测试';
        $url = 'https://immutable.com';
        $picUrl = 'https://immutable-pic.com';
        $desc = '不可变描述';

        $link->setTitle($title);
        $link->setUrl($url);
        $link->setPicUrl($picUrl);
        $link->setDesc($desc);

        $array = $link->retrievePlainArray();

        // 修改数组不应影响link对象
        $this->assertIsArray($array);
        $this->assertArrayHasKey('link', $array);
        $this->assertIsArray($array['link']);
        $array['link']['title'] = '改变标题';
        $array['link']['url'] = 'https://changed.com';
        $array['link']['picurl'] = 'https://changed-pic.com';
        $array['link']['desc'] = '改变描述';
        $array['msgtype'] = 'changed_type';
        $array['new_key'] = 'new_value';

        $this->assertSame($title, $link->getTitle());
        $this->assertSame($url, $link->getUrl());
        $this->assertSame($picUrl, $link->getPicUrl());
        $this->assertSame($desc, $link->getDesc());

        $newArray = $link->retrievePlainArray();
        $this->assertIsArray($newArray);
        $this->assertArrayHasKey('link', $newArray);
        $this->assertIsArray($newArray['link']);
        $this->assertSame($title, $newArray['link']['title']);
        $this->assertSame($url, $newArray['link']['url']);
        $this->assertSame($picUrl, $newArray['link']['picurl']);
        $this->assertSame($desc, $newArray['link']['desc']);
        $this->assertSame('link', $newArray['msgtype']);
        $this->assertArrayNotHasKey('new_key', $newArray);
    }

    public function testMethodCallsAreIdempotent(): void
    {
        // 测试方法调用是幂等的
        $link = self::getService(Link::class);
        $title = '幂等测试';
        $url = 'https://idempotent.com';
        $picUrl = 'https://idempotent-pic.com';
        $desc = '幂等描述';

        $link->setTitle($title);
        $link->setUrl($url);
        $link->setPicUrl($picUrl);
        $link->setDesc($desc);

        // 多次调用应该返回相同结果
        $title1 = $link->getTitle();
        $title2 = $link->getTitle();
        $this->assertSame($title1, $title2);

        $url1 = $link->getUrl();
        $url2 = $link->getUrl();
        $this->assertSame($url1, $url2);

        $picUrl1 = $link->getPicUrl();
        $picUrl2 = $link->getPicUrl();
        $this->assertSame($picUrl1, $picUrl2);

        $desc1 = $link->getDesc();
        $desc2 = $link->getDesc();
        $this->assertSame($desc1, $desc2);

        $array1 = $link->retrievePlainArray();
        $array2 = $link->retrievePlainArray();
        $this->assertSame($array1, $array2);
    }

    public function testPlainArrayInterfaceImplementation(): void
    {
        // 测试PlainArrayInterface接口实现
        $link = self::getService(Link::class);
        $link->setTitle('interface_test_title');
        $link->setPicUrl('https://example.com/pic.jpg');
        $link->setDesc('interface_test_description');
        $link->setUrl('https://example.com/link');

        $array = $link->retrievePlainArray();
        // 移除冗余检查，直接验证返回的数组
        $this->assertCount(2, $array);
        $this->assertArrayHasKey('msgtype', $array);
        $this->assertArrayHasKey('link', $array);
    }
}

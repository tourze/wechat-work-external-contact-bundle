<?php

namespace WechatWorkExternalContactBundle\Tests\Request\Attachment;

use PHPUnit\Framework\TestCase;
use Tourze\Arrayable\PlainArrayInterface;
use WechatWorkExternalContactBundle\Request\Attachment\BaseAttachment;
use WechatWorkExternalContactBundle\Request\Attachment\Link;

/**
 * Link 附件测试
 */
class LinkTest extends TestCase
{
    public function test_inheritance(): void
    {
        // 测试继承关系
        $link = new Link();
        $this->assertInstanceOf(BaseAttachment::class, $link);
        $this->assertInstanceOf(PlainArrayInterface::class, $link);
    }

    public function test_title_setterAndGetter(): void
    {
        // 测试标题设置和获取
        $link = new Link();
        $title = '企业产品介绍';

        $link->setTitle($title);
        $this->assertSame($title, $link->getTitle());
    }

    public function test_title_withSpecialCharacters(): void
    {
        // 测试特殊字符标题
        $link = new Link();
        $specialTitle = '产品2024版 - 全新体验！@#$%';
        $link->setTitle($specialTitle);

        $this->assertSame($specialTitle, $link->getTitle());
    }

    public function test_title_withMaxLength(): void
    {
        // 测试最大长度标题（128字节）
        $link = new Link();
        $maxTitle = str_repeat('标', 42) . '题'; // 约126字节（每个中文字符3字节）
        $link->setTitle($maxTitle);

        $this->assertSame($maxTitle, $link->getTitle());
    }

    public function test_url_setterAndGetter(): void
    {
        // 测试URL设置和获取
        $link = new Link();
        $url = 'https://company.com/product';

        $link->setUrl($url);
        $this->assertSame($url, $link->getUrl());
    }

    public function test_url_withComplexUrl(): void
    {
        // 测试复杂URL
        $link = new Link();
        $complexUrl = 'https://api.company.com/v1/product?id=123&category=tech&utm_source=wechat';
        $link->setUrl($complexUrl);

        $this->assertSame($complexUrl, $link->getUrl());
    }

    public function test_picUrl_setterAndGetter(): void
    {
        // 测试图片URL设置和获取
        $link = new Link();
        $picUrl = 'https://cdn.company.com/images/product.jpg';

        $link->setPicUrl($picUrl);
        $this->assertSame($picUrl, $link->getPicUrl());
    }

    public function test_picUrl_withNull(): void
    {
        // 测试null图片URL
        $link = new Link();
        $link->setPicUrl(null);

        $this->assertNull($link->getPicUrl());
    }

    public function test_desc_setterAndGetter(): void
    {
        // 测试描述设置和获取
        $link = new Link();
        $desc = '这是一个优秀的产品介绍，包含详细的功能说明和使用指南。';

        $link->setDesc($desc);
        $this->assertSame($desc, $link->getDesc());
    }

    public function test_desc_withNull(): void
    {
        // 测试null描述
        $link = new Link();
        $link->setDesc(null);

        $this->assertNull($link->getDesc());
    }

    public function test_desc_withMaxLength(): void
    {
        // 测试最大长度描述（512字节）
        $link = new Link();
        $maxDesc = str_repeat('详细描述内容', 25); // 约375字节
        $link->setDesc($maxDesc);

        $this->assertSame($maxDesc, $link->getDesc());
    }

    public function test_retrievePlainArray_withRequiredFieldsOnly(): void
    {
        // 测试仅必填字段的数组转换
        $link = new Link();
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

    public function test_retrievePlainArray_withAllFields(): void
    {
        // 测试所有字段的数组转换
        $link = new Link();
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

    public function test_retrievePlainArray_withOnlyPicUrl(): void
    {
        // 测试仅有图片URL的数组转换
        $link = new Link();
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

    public function test_retrievePlainArray_withOnlyDesc(): void
    {
        // 测试仅有描述的数组转换
        $link = new Link();
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

    public function test_retrievePlainArray_structure(): void
    {
        // 测试数组结构
        $link = new Link();
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

    public function test_businessScenario_productIntroduction(): void
    {
        // 测试业务场景：产品介绍链接
        $link = new Link();
        $link->setTitle('2024年新品发布');
        $link->setUrl('https://company.com/products/2024/new-release');
        $link->setPicUrl('https://cdn.company.com/2024-new-product.jpg');
        $link->setDesc('我们隆重推出2024年度旗舰产品，具有革命性的功能和设计。');

        $array = $link->retrievePlainArray();

        $this->assertSame('link', $array['msgtype']);
        $this->assertSame('2024年新品发布', $array['link']['title']);
        $this->assertStringContainsString('2024', $array['link']['url']);
        $this->assertArrayHasKey('picurl', $array['link']);
        $this->assertArrayHasKey('desc', $array['link']);
    }

    public function test_businessScenario_newsArticle(): void
    {
        // 测试业务场景：新闻文章链接
        $link = new Link();
        $link->setTitle('行业最新动态');
        $link->setUrl('https://news.company.com/industry-trends-2024');
        $link->setDesc('了解行业最新发展趋势和市场动态。');

        $array = $link->retrievePlainArray();

        $this->assertSame('行业最新动态', $array['link']['title']);
        $this->assertStringContainsString('news', $array['link']['url']);
        $this->assertArrayNotHasKey('picurl', $array['link']);
        $this->assertArrayHasKey('desc', $array['link']);
    }

    public function test_businessScenario_simpleLink(): void
    {
        // 测试业务场景：简单链接
        $link = new Link();
        $link->setTitle('官网首页');
        $link->setUrl('https://company.com');

        $array = $link->retrievePlainArray();

        $this->assertSame('官网首页', $array['link']['title']);
        $this->assertSame('https://company.com', $array['link']['url']);
        $this->assertArrayNotHasKey('picurl', $array['link']);
        $this->assertArrayNotHasKey('desc', $array['link']);

        // 验证符合企业微信API要求
        $this->assertArrayHasKey('msgtype', $array);
        $this->assertArrayHasKey('link', $array);
    }

    public function test_multipleSetCalls(): void
    {
        // 测试多次设置值
        $link = new Link();

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
        $this->assertSame('第二个标题', $array['link']['title']);
        $this->assertSame('https://second.com', $array['link']['url']);
    }

    public function test_resetToNull(): void
    {
        // 测试重置可选字段为null
        $link = new Link();

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
        $this->assertArrayHasKey('title', $array['link']);
        $this->assertArrayHasKey('url', $array['link']);
        $this->assertArrayNotHasKey('picurl', $array['link']);
        $this->assertArrayNotHasKey('desc', $array['link']);
    }

    public function test_retrievePlainArrayDoesNotModifyOriginalData(): void
    {
        // 测试获取数组不会修改原始数据
        $link = new Link();
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
        $array1['link']['title'] = '修改后标题';
        $array1['link']['url'] = 'https://modified.com';
        $array1['link']['picurl'] = 'https://modified-pic.com';
        $array1['link']['desc'] = '修改后描述';
        $array1['msgtype'] = 'modified_type';

        $this->assertSame($originalTitle, $link->getTitle());
        $this->assertSame($originalUrl, $link->getUrl());
        $this->assertSame($originalPicUrl, $link->getPicUrl());
        $this->assertSame($originalDesc, $link->getDesc());

        $this->assertSame($originalTitle, $array2['link']['title']);
        $this->assertSame($originalUrl, $array2['link']['url']);
        $this->assertSame($originalPicUrl, $array2['link']['picurl']);
        $this->assertSame($originalDesc, $array2['link']['desc']);
        $this->assertSame('link', $array2['msgtype']);
    }

    public function test_immutableBehavior(): void
    {
        // 测试不可变行为
        $link = new Link();
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
        $this->assertSame($title, $newArray['link']['title']);
        $this->assertSame($url, $newArray['link']['url']);
        $this->assertSame($picUrl, $newArray['link']['picurl']);
        $this->assertSame($desc, $newArray['link']['desc']);
        $this->assertSame('link', $newArray['msgtype']);
        $this->assertArrayNotHasKey('new_key', $newArray);
    }

    public function test_methodCallsAreIdempotent(): void
    {
        // 测试方法调用是幂等的
        $link = new Link();
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

    public function test_plainArrayInterfaceImplementation(): void
    {
        // 测试PlainArrayInterface接口实现
        $link = new Link();
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

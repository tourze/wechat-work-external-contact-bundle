<?php

namespace WechatWorkExternalContactBundle\Tests\Request\Attachment;

use PHPUnit\Framework\TestCase;
use Tourze\Arrayable\PlainArrayInterface;
use WechatWorkExternalContactBundle\Request\Attachment\BaseAttachment;
use WechatWorkExternalContactBundle\Request\Attachment\MiniProgram;

/**
 * MiniProgram 附件测试
 */
class MiniProgramTest extends TestCase
{
    public function test_inheritance(): void
    {
        // 测试继承关系
        $miniProgram = new MiniProgram();
        $this->assertInstanceOf(BaseAttachment::class, $miniProgram);
        $this->assertInstanceOf(PlainArrayInterface::class, $miniProgram);
    }

    public function test_title_setterAndGetter(): void
    {
        // 测试标题设置和获取
        $miniProgram = new MiniProgram();
        $title = '企业服务小程序';
        
        $miniProgram->setTitle($title);
        $this->assertSame($title, $miniProgram->getTitle());
    }

    public function test_title_withSpecialCharacters(): void
    {
        // 测试特殊字符标题
        $miniProgram = new MiniProgram();
        $specialTitle = '小程序2024版 - 全新体验！@#$%';
        $miniProgram->setTitle($specialTitle);
        
        $this->assertSame($specialTitle, $miniProgram->getTitle());
    }

    public function test_title_withMaxLength(): void
    {
        // 测试最大长度标题（64字节）
        $miniProgram = new MiniProgram();
        $maxTitle = str_repeat('小', 21) . '程'; // 约63字节（每个中文字符3字节）
        $miniProgram->setTitle($maxTitle);
        
        $this->assertSame($maxTitle, $miniProgram->getTitle());
    }

    public function test_picMediaId_setterAndGetter(): void
    {
        // 测试封面媒体ID设置和获取
        $miniProgram = new MiniProgram();
        $picMediaId = 'miniprogram_cover_media_id_123';
        
        $miniProgram->setPicMediaId($picMediaId);
        $this->assertSame($picMediaId, $miniProgram->getPicMediaId());
    }

    public function test_picMediaId_withSpecialCharacters(): void
    {
        // 测试特殊字符封面媒体ID
        $miniProgram = new MiniProgram();
        $specialPicMediaId = 'cover_abc-123_test@domain.com';
        $miniProgram->setPicMediaId($specialPicMediaId);
        
        $this->assertSame($specialPicMediaId, $miniProgram->getPicMediaId());
    }

    public function test_appId_setterAndGetter(): void
    {
        // 测试AppID设置和获取
        $miniProgram = new MiniProgram();
        $appId = 'wx1234567890abcdef';
        
        $miniProgram->setAppId($appId);
        $this->assertSame($appId, $miniProgram->getAppId());
    }

    public function test_appId_withRealFormat(): void
    {
        // 测试真实格式的AppID
        $miniProgram = new MiniProgram();
        $realAppId = 'wxabcdef1234567890';
        $miniProgram->setAppId($realAppId);
        
        $this->assertSame($realAppId, $miniProgram->getAppId());
    }

    public function test_page_setterAndGetter(): void
    {
        // 测试页面路径设置和获取
        $miniProgram = new MiniProgram();
        $page = 'pages/index/index';
        
        $miniProgram->setPage($page);
        $this->assertSame($page, $miniProgram->getPage());
    }

    public function test_page_withComplexPath(): void
    {
        // 测试复杂页面路径
        $miniProgram = new MiniProgram();
        $complexPage = 'pages/product/detail?id=123&category=electronics';
        $miniProgram->setPage($complexPage);
        
        $this->assertSame($complexPage, $miniProgram->getPage());
    }

    public function test_retrievePlainArray(): void
    {
        // 测试获取普通数组
        $miniProgram = new MiniProgram();
        $title = '测试小程序';
        $picMediaId = 'test_pic_media_id';
        $appId = 'wx0123456789abcdef';
        $page = 'pages/test/test';
        
        $miniProgram->setTitle($title);
        $miniProgram->setPicMediaId($picMediaId);
        $miniProgram->setAppId($appId);
        $miniProgram->setPage($page);
        
        $expected = [
            'msgtype' => 'miniprogram',
            'miniprogram' => [
                'title' => $title,
                'pic_media_id' => $picMediaId,
                'appid' => $appId,
                'page' => $page,
            ],
        ];
        
        $this->assertSame($expected, $miniProgram->retrievePlainArray());
    }

    public function test_retrievePlainArray_structure(): void
    {
        // 测试数组结构
        $miniProgram = new MiniProgram();
        $miniProgram->setTitle('结构测试');
        $miniProgram->setPicMediaId('structure_pic_media');
        $miniProgram->setAppId('wx_structure_test');
        $miniProgram->setPage('pages/structure/test');
        
        $array = $miniProgram->retrievePlainArray();
        
        $this->assertIsArray($array);
        $this->assertCount(2, $array);
        $this->assertArrayHasKey('msgtype', $array);
        $this->assertArrayHasKey('miniprogram', $array);
        $this->assertSame('miniprogram', $array['msgtype']);
        $this->assertIsArray($array['miniprogram']);
        $this->assertCount(4, $array['miniprogram']);
        $this->assertArrayHasKey('title', $array['miniprogram']);
        $this->assertArrayHasKey('pic_media_id', $array['miniprogram']);
        $this->assertArrayHasKey('appid', $array['miniprogram']);
        $this->assertArrayHasKey('page', $array['miniprogram']);
    }

    public function test_businessScenario_productCatalog(): void
    {
        // 测试业务场景：产品目录小程序
        $miniProgram = new MiniProgram();
        $miniProgram->setTitle('企业产品目录');
        $miniProgram->setPicMediaId('product_catalog_cover_media');
        $miniProgram->setAppId('wx_product_catalog_2024');
        $miniProgram->setPage('pages/catalog/index');
        
        $array = $miniProgram->retrievePlainArray();
        
        $this->assertSame('miniprogram', $array['msgtype']);
        $this->assertSame('企业产品目录', $array['miniprogram']['title']);
        $this->assertStringContainsString('catalog', $array['miniprogram']['appid']);
        $this->assertStringContainsString('catalog', $array['miniprogram']['page']);
        
        // 验证符合企业微信API要求
        $this->assertArrayHasKey('msgtype', $array);
        $this->assertArrayHasKey('miniprogram', $array);
    }

    public function test_businessScenario_customerService(): void
    {
        // 测试业务场景：客服小程序
        $miniProgram = new MiniProgram();
        $miniProgram->setTitle('在线客服');
        $miniProgram->setPicMediaId('customer_service_cover');
        $miniProgram->setAppId('wx_customer_service');
        $miniProgram->setPage('pages/service/chat?user_id=12345');
        
        $array = $miniProgram->retrievePlainArray();
        
        $this->assertSame('在线客服', $array['miniprogram']['title']);
        $this->assertStringContainsString('service', $array['miniprogram']['appid']);
        $this->assertStringContainsString('chat', $array['miniprogram']['page']);
        $this->assertStringContainsString('user_id', $array['miniprogram']['page']);
    }

    public function test_businessScenario_welcomePage(): void
    {
        // 测试业务场景：欢迎页面小程序
        $miniProgram = new MiniProgram();
        $miniProgram->setTitle('欢迎加入企业');
        $miniProgram->setPicMediaId('welcome_cover_520x416');
        $miniProgram->setAppId('wx_company_welcome');
        $miniProgram->setPage('pages/welcome/index');
        
        $array = $miniProgram->retrievePlainArray();
        
        $this->assertSame('欢迎加入企业', $array['miniprogram']['title']);
        $this->assertStringContainsString('520x416', $array['miniprogram']['pic_media_id']);
        $this->assertStringContainsString('welcome', $array['miniprogram']['appid']);
        $this->assertSame('pages/welcome/index', $array['miniprogram']['page']);
    }

    public function test_multipleSetCalls(): void
    {
        // 测试多次设置值
        $miniProgram = new MiniProgram();
        
        $miniProgram->setTitle('第一个标题');
        $miniProgram->setPicMediaId('first_pic_media');
        $miniProgram->setAppId('wx_first_app');
        $miniProgram->setPage('pages/first/index');
        
        $this->assertSame('第一个标题', $miniProgram->getTitle());
        $this->assertSame('first_pic_media', $miniProgram->getPicMediaId());
        $this->assertSame('wx_first_app', $miniProgram->getAppId());
        $this->assertSame('pages/first/index', $miniProgram->getPage());
        
        // 重新设置
        $miniProgram->setTitle('第二个标题');
        $miniProgram->setPicMediaId('second_pic_media');
        $miniProgram->setAppId('wx_second_app');
        $miniProgram->setPage('pages/second/index');
        
        $this->assertSame('第二个标题', $miniProgram->getTitle());
        $this->assertSame('second_pic_media', $miniProgram->getPicMediaId());
        $this->assertSame('wx_second_app', $miniProgram->getAppId());
        $this->assertSame('pages/second/index', $miniProgram->getPage());
        
        $array = $miniProgram->retrievePlainArray();
        $this->assertSame('第二个标题', $array['miniprogram']['title']);
        $this->assertSame('second_pic_media', $array['miniprogram']['pic_media_id']);
        $this->assertSame('wx_second_app', $array['miniprogram']['appid']);
        $this->assertSame('pages/second/index', $array['miniprogram']['page']);
    }

    public function test_retrievePlainArrayDoesNotModifyOriginalData(): void
    {
        // 测试获取数组不会修改原始数据
        $miniProgram = new MiniProgram();
        $originalTitle = '原始标题';
        $originalPicMediaId = 'original_pic_media';
        $originalAppId = 'wx_original_app';
        $originalPage = 'pages/original/index';
        
        $miniProgram->setTitle($originalTitle);
        $miniProgram->setPicMediaId($originalPicMediaId);
        $miniProgram->setAppId($originalAppId);
        $miniProgram->setPage($originalPage);
        
        $array1 = $miniProgram->retrievePlainArray();
        $array2 = $miniProgram->retrievePlainArray();
        
        // 修改返回的数组不应影响原始数据
        $array1['miniprogram']['title'] = '修改后标题';
        $array1['miniprogram']['pic_media_id'] = 'modified_pic_media';
        $array1['miniprogram']['appid'] = 'wx_modified_app';
        $array1['miniprogram']['page'] = 'pages/modified/index';
        $array1['msgtype'] = 'modified_type';
        
        $this->assertSame($originalTitle, $miniProgram->getTitle());
        $this->assertSame($originalPicMediaId, $miniProgram->getPicMediaId());
        $this->assertSame($originalAppId, $miniProgram->getAppId());
        $this->assertSame($originalPage, $miniProgram->getPage());
        
        $this->assertSame($originalTitle, $array2['miniprogram']['title']);
        $this->assertSame($originalPicMediaId, $array2['miniprogram']['pic_media_id']);
        $this->assertSame($originalAppId, $array2['miniprogram']['appid']);
        $this->assertSame($originalPage, $array2['miniprogram']['page']);
        $this->assertSame('miniprogram', $array2['msgtype']);
    }

    public function test_immutableBehavior(): void
    {
        // 测试不可变行为
        $miniProgram = new MiniProgram();
        $title = '不可变测试';
        $picMediaId = 'immutable_pic_media';
        $appId = 'wx_immutable_app';
        $page = 'pages/immutable/test';
        
        $miniProgram->setTitle($title);
        $miniProgram->setPicMediaId($picMediaId);
        $miniProgram->setAppId($appId);
        $miniProgram->setPage($page);
        
        $array = $miniProgram->retrievePlainArray();
        
        // 修改数组不应影响miniProgram对象
        $array['miniprogram']['title'] = '改变标题';
        $array['miniprogram']['pic_media_id'] = 'changed_pic_media';
        $array['miniprogram']['appid'] = 'wx_changed_app';
        $array['miniprogram']['page'] = 'pages/changed/test';
        $array['msgtype'] = 'changed_type';
        $array['new_key'] = 'new_value';
        
        $this->assertSame($title, $miniProgram->getTitle());
        $this->assertSame($picMediaId, $miniProgram->getPicMediaId());
        $this->assertSame($appId, $miniProgram->getAppId());
        $this->assertSame($page, $miniProgram->getPage());
        
        $newArray = $miniProgram->retrievePlainArray();
        $this->assertSame($title, $newArray['miniprogram']['title']);
        $this->assertSame($picMediaId, $newArray['miniprogram']['pic_media_id']);
        $this->assertSame($appId, $newArray['miniprogram']['appid']);
        $this->assertSame($page, $newArray['miniprogram']['page']);
        $this->assertSame('miniprogram', $newArray['msgtype']);
        $this->assertArrayNotHasKey('new_key', $newArray);
    }

    public function test_methodCallsAreIdempotent(): void
    {
        // 测试方法调用是幂等的
        $miniProgram = new MiniProgram();
        $title = '幂等测试';
        $picMediaId = 'idempotent_pic_media';
        $appId = 'wx_idempotent_app';
        $page = 'pages/idempotent/test';
        
        $miniProgram->setTitle($title);
        $miniProgram->setPicMediaId($picMediaId);
        $miniProgram->setAppId($appId);
        $miniProgram->setPage($page);
        
        // 多次调用应该返回相同结果
        $title1 = $miniProgram->getTitle();
        $title2 = $miniProgram->getTitle();
        $this->assertSame($title1, $title2);
        
        $picMediaId1 = $miniProgram->getPicMediaId();
        $picMediaId2 = $miniProgram->getPicMediaId();
        $this->assertSame($picMediaId1, $picMediaId2);
        
        $appId1 = $miniProgram->getAppId();
        $appId2 = $miniProgram->getAppId();
        $this->assertSame($appId1, $appId2);
        
        $page1 = $miniProgram->getPage();
        $page2 = $miniProgram->getPage();
        $this->assertSame($page1, $page2);
        
        $array1 = $miniProgram->retrievePlainArray();
        $array2 = $miniProgram->retrievePlainArray();
        $this->assertSame($array1, $array2);
    }

    public function test_plainArrayInterfaceImplementation(): void
    {
        // 测试PlainArrayInterface接口实现
        $miniProgram = new MiniProgram();
        $miniProgram->setTitle('接口测试');
        $miniProgram->setPicMediaId('interface_pic_media');
        $miniProgram->setAppId('wx_interface_test');
        $miniProgram->setPage('pages/interface/test');
        
        $this->assertTrue(method_exists($miniProgram, 'retrievePlainArray'));
        $this->assertTrue(is_callable([$miniProgram, 'retrievePlainArray']));
        
        $array = $miniProgram->retrievePlainArray();
        $this->assertIsArray($array);
    }
} 
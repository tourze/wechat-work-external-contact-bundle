<?php

namespace WechatWorkExternalContactBundle\Tests\Request\Attachment;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatWorkExternalContactBundle\Request\Attachment\MiniProgram;

/**
 * MiniProgram 附件测试
 *
 * @internal
 */
#[CoversClass(MiniProgram::class)]
#[RunTestsInSeparateProcesses] final class MiniProgramTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void        // 此测试不需要特殊的设置
    {
    }

    public function testInheritance(): void
    {
        // 测试继承关系
        $miniProgram = self::getService(MiniProgram::class);
        $this->assertInstanceOf(PlainArrayInterface::class, $miniProgram);

        // 测试PlainArrayInterface接口的实际功能
        $miniProgram->setTitle('测试小程序');
        $miniProgram->setAppId('test_app_id');
        $miniProgram->setPage('pages/index/index');
        $miniProgram->setPicMediaId('test_thumb_media_id');
        $array = $miniProgram->retrievePlainArray();
        $this->assertIsArray($array);
        $this->assertArrayHasKey('msgtype', $array);
        $this->assertArrayHasKey('miniprogram', $array);
    }

    public function testTitleSetterAndGetter(): void
    {
        // 测试标题设置和获取
        $miniProgram = self::getService(MiniProgram::class);
        $title = '企业服务小程序';

        $miniProgram->setTitle($title);
        $this->assertSame($title, $miniProgram->getTitle());
    }

    public function testTitleWithSpecialCharacters(): void
    {
        // 测试特殊字符标题
        $miniProgram = self::getService(MiniProgram::class);
        $specialTitle = '小程序2024版 - 全新体验！@#$%';
        $miniProgram->setTitle($specialTitle);

        $this->assertSame($specialTitle, $miniProgram->getTitle());
    }

    public function testTitleWithMaxLength(): void
    {
        // 测试最大长度标题（64字节）
        $miniProgram = self::getService(MiniProgram::class);
        $maxTitle = str_repeat('小', 21) . '程'; // 约63字节（每个中文字符3字节）
        $miniProgram->setTitle($maxTitle);

        $this->assertSame($maxTitle, $miniProgram->getTitle());
    }

    public function testPicMediaIdSetterAndGetter(): void
    {
        // 测试封面媒体ID设置和获取
        $miniProgram = self::getService(MiniProgram::class);
        $picMediaId = 'miniprogram_cover_media_id_123';

        $miniProgram->setPicMediaId($picMediaId);
        $this->assertSame($picMediaId, $miniProgram->getPicMediaId());
    }

    public function testPicMediaIdWithSpecialCharacters(): void
    {
        // 测试特殊字符封面媒体ID
        $miniProgram = self::getService(MiniProgram::class);
        $specialPicMediaId = 'cover_abc-123_test@domain.com';
        $miniProgram->setPicMediaId($specialPicMediaId);

        $this->assertSame($specialPicMediaId, $miniProgram->getPicMediaId());
    }

    public function testAppIdSetterAndGetter(): void
    {
        // 测试AppID设置和获取
        $miniProgram = self::getService(MiniProgram::class);
        $appId = 'wx1234567890abcdef';

        $miniProgram->setAppId($appId);
        $this->assertSame($appId, $miniProgram->getAppId());
    }

    public function testAppIdWithRealFormat(): void
    {
        // 测试真实格式的AppID
        $miniProgram = self::getService(MiniProgram::class);
        $realAppId = 'wxabcdef1234567890';
        $miniProgram->setAppId($realAppId);

        $this->assertSame($realAppId, $miniProgram->getAppId());
    }

    public function testPageSetterAndGetter(): void
    {
        // 测试页面路径设置和获取
        $miniProgram = self::getService(MiniProgram::class);
        $page = 'pages/index/index';

        $miniProgram->setPage($page);
        $this->assertSame($page, $miniProgram->getPage());
    }

    public function testPageWithComplexPath(): void
    {
        // 测试复杂页面路径
        $miniProgram = self::getService(MiniProgram::class);
        $complexPage = 'pages/product/detail?id=123&category=electronics';
        $miniProgram->setPage($complexPage);

        $this->assertSame($complexPage, $miniProgram->getPage());
    }

    public function testRetrievePlainArray(): void
    {
        // 测试获取普通数组
        $miniProgram = self::getService(MiniProgram::class);
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

    public function testRetrievePlainArrayStructure(): void
    {
        // 测试数组结构
        $miniProgram = self::getService(MiniProgram::class);
        $miniProgram->setTitle('structure_test_title');
        $miniProgram->setPicMediaId('structure_test_pic_media');
        $miniProgram->setAppId('structure_test_app_id');
        $miniProgram->setPage('structure_test_page');

        $array = $miniProgram->retrievePlainArray();

        $this->assertCount(2, $array);
        $this->assertArrayHasKey('msgtype', $array);
        $this->assertArrayHasKey('miniprogram', $array);
        $this->assertSame('miniprogram', $array['msgtype']);
        $this->assertIsArray($array['miniprogram']);
        $this->assertCount(4, $array['miniprogram']);
    }

    public function testBusinessScenarioProductCatalog(): void
    {
        // 测试业务场景：产品目录小程序
        $miniProgram = self::getService(MiniProgram::class);
        $miniProgram->setTitle('企业产品目录');
        $miniProgram->setPicMediaId('product_catalog_cover_media');
        $miniProgram->setAppId('wx_product_catalog_2024');
        $miniProgram->setPage('pages/catalog/index');

        $array = $miniProgram->retrievePlainArray();

        $this->assertArrayHasKey('msgtype', $array);
        $this->assertArrayHasKey('miniprogram', $array);
        $this->assertSame('miniprogram', $array['msgtype']);
        $this->assertIsArray($array['miniprogram']);
        $this->assertArrayHasKey('title', $array['miniprogram']);
        $this->assertSame('企业产品目录', $array['miniprogram']['title']);
        $this->assertArrayHasKey('appid', $array['miniprogram']);
        $this->assertStringContainsString('catalog', $array['miniprogram']['appid']);
        $this->assertArrayHasKey('page', $array['miniprogram']);
        $this->assertStringContainsString('catalog', $array['miniprogram']['page']);
    }

    public function testBusinessScenarioCustomerService(): void
    {
        // 测试业务场景：客服小程序
        $miniProgram = self::getService(MiniProgram::class);
        $miniProgram->setTitle('在线客服');
        $miniProgram->setPicMediaId('customer_service_cover');
        $miniProgram->setAppId('wx_customer_service');
        $miniProgram->setPage('pages/service/chat?user_id=12345');

        $array = $miniProgram->retrievePlainArray();

        $this->assertArrayHasKey('miniprogram', $array);
        $this->assertIsArray($array['miniprogram']);
        $this->assertArrayHasKey('title', $array['miniprogram']);
        $this->assertSame('在线客服', $array['miniprogram']['title']);
        $this->assertArrayHasKey('appid', $array['miniprogram']);
        $this->assertStringContainsString('service', $array['miniprogram']['appid']);
        $this->assertArrayHasKey('page', $array['miniprogram']);
        $this->assertStringContainsString('chat', $array['miniprogram']['page']);
        $this->assertStringContainsString('user_id', $array['miniprogram']['page']);
    }

    public function testBusinessScenarioWelcomePage(): void
    {
        // 测试业务场景：欢迎页面小程序
        $miniProgram = self::getService(MiniProgram::class);
        $miniProgram->setTitle('欢迎加入企业');
        $miniProgram->setPicMediaId('welcome_cover_520x416');
        $miniProgram->setAppId('wx_company_welcome');
        $miniProgram->setPage('pages/welcome/index');

        $array = $miniProgram->retrievePlainArray();

        $this->assertArrayHasKey('miniprogram', $array);
        $this->assertIsArray($array['miniprogram']);
        $this->assertArrayHasKey('title', $array['miniprogram']);
        $this->assertSame('欢迎加入企业', $array['miniprogram']['title']);
        $this->assertArrayHasKey('pic_media_id', $array['miniprogram']);
        $this->assertStringContainsString('520x416', $array['miniprogram']['pic_media_id']);
        $this->assertArrayHasKey('appid', $array['miniprogram']);
        $this->assertStringContainsString('welcome', $array['miniprogram']['appid']);
        $this->assertArrayHasKey('page', $array['miniprogram']);
        $this->assertSame('pages/welcome/index', $array['miniprogram']['page']);
    }

    public function testMultipleSetCalls(): void
    {
        // 测试多次设置值
        $miniProgram = self::getService(MiniProgram::class);

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
        $this->assertArrayHasKey('miniprogram', $array);
        $this->assertIsArray($array['miniprogram']);
        $this->assertArrayHasKey('title', $array['miniprogram']);
        $this->assertSame('第二个标题', $array['miniprogram']['title']);
        $this->assertArrayHasKey('pic_media_id', $array['miniprogram']);
        $this->assertSame('second_pic_media', $array['miniprogram']['pic_media_id']);
        $this->assertArrayHasKey('appid', $array['miniprogram']);
        $this->assertSame('wx_second_app', $array['miniprogram']['appid']);
        $this->assertArrayHasKey('page', $array['miniprogram']);
        $this->assertSame('pages/second/index', $array['miniprogram']['page']);
    }

    public function testRetrievePlainArrayDoesNotModifyOriginalData(): void
    {
        // 测试获取数组不会修改原始数据
        $miniProgram = self::getService(MiniProgram::class);
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
        $this->assertArrayHasKey('miniprogram', $array1);
        $this->assertIsArray($array1['miniprogram']);
        $array1['miniprogram']['title'] = '修改后标题';
        $array1['miniprogram']['pic_media_id'] = 'modified_pic_media';
        $array1['miniprogram']['appid'] = 'wx_modified_app';
        $array1['miniprogram']['page'] = 'pages/modified/index';
        $array1['msgtype'] = 'modified_type';

        $this->assertSame($originalTitle, $miniProgram->getTitle());
        $this->assertSame($originalPicMediaId, $miniProgram->getPicMediaId());
        $this->assertSame($originalAppId, $miniProgram->getAppId());
        $this->assertSame($originalPage, $miniProgram->getPage());

        $this->assertArrayHasKey('miniprogram', $array2);
        $this->assertIsArray($array2['miniprogram']);
        $this->assertArrayHasKey('title', $array2['miniprogram']);
        $this->assertSame($originalTitle, $array2['miniprogram']['title']);
        $this->assertArrayHasKey('pic_media_id', $array2['miniprogram']);
        $this->assertSame($originalPicMediaId, $array2['miniprogram']['pic_media_id']);
        $this->assertArrayHasKey('appid', $array2['miniprogram']);
        $this->assertSame($originalAppId, $array2['miniprogram']['appid']);
        $this->assertArrayHasKey('page', $array2['miniprogram']);
        $this->assertSame($originalPage, $array2['miniprogram']['page']);
        $this->assertArrayHasKey('msgtype', $array2);
        $this->assertSame('miniprogram', $array2['msgtype']);
    }

    public function testImmutableBehavior(): void
    {
        // 测试不可变行为
        $miniProgram = self::getService(MiniProgram::class);
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
        $this->assertArrayHasKey('miniprogram', $array);
        $this->assertIsArray($array['miniprogram']);
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
        $this->assertArrayHasKey('miniprogram', $newArray);
        $this->assertIsArray($newArray['miniprogram']);
        $this->assertArrayHasKey('title', $newArray['miniprogram']);
        $this->assertSame($title, $newArray['miniprogram']['title']);
        $this->assertArrayHasKey('pic_media_id', $newArray['miniprogram']);
        $this->assertSame($picMediaId, $newArray['miniprogram']['pic_media_id']);
        $this->assertArrayHasKey('appid', $newArray['miniprogram']);
        $this->assertSame($appId, $newArray['miniprogram']['appid']);
        $this->assertArrayHasKey('page', $newArray['miniprogram']);
        $this->assertSame($page, $newArray['miniprogram']['page']);
        $this->assertArrayHasKey('msgtype', $newArray);
        $this->assertSame('miniprogram', $newArray['msgtype']);
        $this->assertArrayNotHasKey('new_key', $newArray);
    }

    public function testMethodCallsAreIdempotent(): void
    {
        // 测试方法调用是幂等的
        $miniProgram = self::getService(MiniProgram::class);
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

    public function testPlainArrayInterfaceImplementation(): void
    {
        // 测试PlainArrayInterface接口实现
        $miniProgram = self::getService(MiniProgram::class);
        $miniProgram->setTitle('interface_test_title');
        $miniProgram->setPicMediaId('interface_test_pic_media');
        $miniProgram->setAppId('interface_test_app_id');
        $miniProgram->setPage('interface_test_page');

        $array = $miniProgram->retrievePlainArray();
        // 移除冗余检查，直接验证返回的数组
        $this->assertCount(2, $array);
        $this->assertArrayHasKey('msgtype', $array);
        $this->assertArrayHasKey('miniprogram', $array);
    }
}

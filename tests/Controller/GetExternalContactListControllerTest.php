<?php

namespace WechatWorkExternalContactBundle\Tests\Controller;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;
use WechatWorkExternalContactBundle\Controller\GetExternalContactListController;

/**
 * @internal
 */
#[CoversClass(GetExternalContactListController::class)]
#[RunTestsInSeparateProcesses]
final class GetExternalContactListControllerTest extends AbstractWebTestCase
{
    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        $client = self::createClient();
        $client->catchExceptions(false);

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request($method, '/wechat/work/test/get_external_contact_list');
    }

    #[Test]
    public function testGetExternalContactListRequireUserId(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(true);

        $client->request('GET', '/wechat/work/test/get_external_contact_list');

        // 通过客户端直接获取响应状态码，避免依赖断言框架的客户端管理
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    #[Test]
    public function testGetExternalContactListWithValidUserId(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(true);

        $client->request('GET', '/wechat/work/test/get_external_contact_list', [
            'userId' => 'test_user_id',
            'corpId' => 'test_corp_id',
        ]);

        $response = $client->getResponse();

        // 通过客户端直接获取响应状态码，避免依赖断言框架的客户端管理
        // 当数据库为空时，agent 为 null，但请求仍能正常处理，返回 200
        $this->assertEquals(200, $response->getStatusCode());

        // 验证响应内容格式正确
        $responseContent = $response->getContent();
        $this->assertNotFalse($responseContent);
        $content = json_decode($responseContent, true);
        $this->assertIsArray($content);
    }

    #[Test]
    public function testGetExternalContactListWithEmptyUserId(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(true);

        $client->request('GET', '/wechat/work/test/get_external_contact_list', [
            'userId' => '',
            'corpId' => 'test_corp_id',
        ]);

        // 通过客户端直接获取响应状态码，避免依赖断言框架的客户端管理
        // 空字符串仍然是有效的字符串，所以控制器会继续处理，返回 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // 验证响应内容格式正确
        $responseContent = $client->getResponse()->getContent();
        $this->assertNotFalse($responseContent);
        $content = json_decode($responseContent, true);
        $this->assertIsArray($content);
    }

    #[Test]
    public function testUnauthenticatedAccess(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(true);

        $client->request('GET', '/wechat/work/test/get_external_contact_list');

        // 通过客户端直接获取响应状态码，避免依赖断言框架的客户端管理
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}

<?php

namespace WechatWorkExternalContactBundle\Tests\Controller;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;
use WechatWorkExternalContactBundle\Controller\SendWelcomeMessageController;

/**
 * @internal
 */
#[CoversClass(SendWelcomeMessageController::class)]
#[RunTestsInSeparateProcesses]
final class SendWelcomeMessageControllerTest extends AbstractWebTestCase
{
    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        $client = self::createClient();
        $client->catchExceptions(false);

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request($method, '/wechat/work/test/send_welcome_msg');
    }

    #[Test]
    public function testSendWelcomeMessageRequireWelcomeCode(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(true);

        $client->request('GET', '/wechat/work/test/send_welcome_msg');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    #[Test]
    public function testSendWelcomeMessageWithValidWelcomeCode(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(true);

        $client->request('GET', '/wechat/work/test/send_welcome_msg', [
            'welcomeCode' => 'test_welcome_code',
            'corpId' => 'test_corp_id',
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // 验证响应内容格式正确
        $responseContent = $client->getResponse()->getContent();
        $this->assertNotFalse($responseContent);
        $content = json_decode($responseContent, true);
        $this->assertIsArray($content);
    }

    #[Test]
    public function testSendWelcomeMessageWithEmptyWelcomeCode(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(true);

        $client->request('GET', '/wechat/work/test/send_welcome_msg', [
            'welcomeCode' => '',
            'corpId' => 'test_corp_id',
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    #[Test]
    public function testUnauthenticatedAccess(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(true);

        $client->request('GET', '/wechat/work/test/send_welcome_msg');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}

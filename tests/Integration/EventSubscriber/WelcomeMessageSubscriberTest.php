<?php

namespace WechatWorkExternalContactBundle\Tests\Integration\EventSubscriber;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Tourze\TextManageBundle\Service\TextFormatter;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkBundle\Service\WorkService;
use WechatWorkExternalContactBundle\EventSubscriber\WelcomeMessageSubscriber;
use WechatWorkExternalContactBundle\Request\SendWelcomeMessageRequest;
use WechatWorkServerBundle\Event\WechatWorkServerMessageRequestEvent;
use WechatWorkServerBundle\Entity\ServerMessage;

/**
 * WelcomeMessageSubscriber 集成测试
 *
 * 测试欢迎消息订阅器的所有功能
 */
class WelcomeMessageSubscriberTest extends TestCase
{
    private WelcomeMessageSubscriber $subscriber;
    private MockObject|WorkService $workService;
    private MockObject|LoggerInterface $logger;
    private MockObject|TextFormatter $textFormatter;

    protected function setUp(): void
    {
        $this->workService = $this->createMock(WorkService::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->textFormatter = $this->createMock(TextFormatter::class);

        $this->subscriber = new WelcomeMessageSubscriber(
            $this->workService,
            $this->logger,
            $this->textFormatter
        );
    }

    public function test_onServerMessageRequest_withoutWelcomeCode_returnsEarly(): void
    {
        $message = new ServerMessage();
        $rawData = ['UserID' => 'user123'];
        $message->setRawData($rawData);

        $event = new WechatWorkServerMessageRequestEvent();
        $event->setMessage($message);

        $this->workService->expects($this->never())
            ->method('request');

        $this->subscriber->onServerMessageRequest($event);
    }

    public function test_onServerMessageRequest_withEmptyWelcomeText_returnsEarly(): void
    {
        $agent = $this->createMock(AgentInterface::class);
        $agent->method('getWelcomeText')->willReturn('');

        $message = new ServerMessage();
        $rawData = ['WelcomeCode' => 'welcome123'];
        $message->setRawData($rawData);
        $message->setAgent($agent);

        $event = new WechatWorkServerMessageRequestEvent();
        $event->setMessage($message);

        $this->workService->expects($this->never())
            ->method('request');

        $this->subscriber->onServerMessageRequest($event);
    }

    public function test_onServerMessageRequest_withNullWelcomeText_returnsEarly(): void
    {
        $agent = $this->createMock(AgentInterface::class);
        $agent->method('getWelcomeText')->willReturn(null);

        $message = new ServerMessage();
        $rawData = ['WelcomeCode' => 'welcome123'];
        $message->setRawData($rawData);
        $message->setAgent($agent);

        $event = new WechatWorkServerMessageRequestEvent();
        $event->setMessage($message);

        $this->workService->expects($this->never())
            ->method('request');

        $this->subscriber->onServerMessageRequest($event);
    }

    public function test_onServerMessageRequest_withValidData_sendsWelcomeMessage(): void
    {
        $agent = $this->createMock(AgentInterface::class);
        $agent->method('getWelcomeText')->willReturn('欢迎 {name}！');

        $message = new ServerMessage();
        $rawData = [
            'WelcomeCode' => 'welcome123',
            'name' => '张三',
        ];
        $message->setRawData($rawData);
        $message->setAgent($agent);

        $event = new WechatWorkServerMessageRequestEvent();
        $event->setMessage($message);

        $this->textFormatter->expects($this->once())
            ->method('formatText')
            ->with('欢迎 {name}！', ['message' => $rawData])
            ->willReturn('欢迎 张三！');

        $capturedRequest = null;
        $this->workService->expects($this->once())
            ->method('request')
            ->with($this->callback(function (SendWelcomeMessageRequest $request) use (&$capturedRequest) {
                $capturedRequest = $request;
                return $request->getWelcomeCode() === 'welcome123'
                    && $request->getTextContent() === '欢迎 张三！';
            }));

        $this->subscriber->onServerMessageRequest($event);

        $this->assertNotNull($capturedRequest);
        $this->assertSame($agent, $capturedRequest->getAgent());
        $this->assertSame('welcome123', $capturedRequest->getWelcomeCode());
        $this->assertSame('欢迎 张三！', $capturedRequest->getTextContent());
    }

    public function test_onServerMessageRequest_withException_logsError(): void
    {
        $agent = $this->createMock(AgentInterface::class);
        $agent->method('getWelcomeText')->willReturn('欢迎！');

        $message = new ServerMessage();
        $rawData = ['WelcomeCode' => 'welcome123'];
        $message->setRawData($rawData);
        $message->setAgent($agent);

        $event = new WechatWorkServerMessageRequestEvent();
        $event->setMessage($message);

        $this->textFormatter->expects($this->once())
            ->method('formatText')
            ->willReturn('欢迎！');

        $exception = new \RuntimeException('发送失败');

        $this->workService->expects($this->once())
            ->method('request')
            ->willThrowException($exception);

        $this->logger->expects($this->once())
            ->method('error')
            ->with(
                '发送欢迎语时发生异常',
                $this->callback(function (array $context) use ($exception) {
                    return $context['exception'] === $exception
                        && $context['request'] instanceof SendWelcomeMessageRequest;
                })
            );

        $this->subscriber->onServerMessageRequest($event);
    }

    public function test_onServerMessageRequest_withComplexWelcomeText(): void
    {
        $agent = $this->createMock(AgentInterface::class);
        $agent->method('getWelcomeText')->willReturn('欢迎 {user.name}，您的代码是 {WelcomeCode}');

        $message = new ServerMessage();
        $rawData = [
            'WelcomeCode' => 'ABC123',
            'user' => ['name' => '李四'],
        ];
        $message->setRawData($rawData);
        $message->setAgent($agent);

        $event = new WechatWorkServerMessageRequestEvent();
        $event->setMessage($message);

        $this->textFormatter->expects($this->once())
            ->method('formatText')
            ->with(
                '欢迎 {user.name}，您的代码是 {WelcomeCode}',
                ['message' => $rawData]
            )
            ->willReturn('欢迎 李四，您的代码是 ABC123');

        $this->workService->expects($this->once())
            ->method('request')
            ->with($this->callback(function (SendWelcomeMessageRequest $request) {
                return $request->getTextContent() === '欢迎 李四，您的代码是 ABC123';
            }));

        $this->subscriber->onServerMessageRequest($event);
    }

    public function test_onServerMessageRequest_withLongWelcomeText(): void
    {
        $longText = str_repeat('欢迎您！', 100);
        $agent = $this->createMock(AgentInterface::class);
        $agent->method('getWelcomeText')->willReturn($longText);

        $message = new ServerMessage();
        $rawData = ['WelcomeCode' => 'welcome123'];
        $message->setRawData($rawData);
        $message->setAgent($agent);

        $event = new WechatWorkServerMessageRequestEvent();
        $event->setMessage($message);

        $this->textFormatter->expects($this->once())
            ->method('formatText')
            ->willReturn($longText);

        $this->workService->expects($this->once())
            ->method('request')
            ->with($this->callback(function (SendWelcomeMessageRequest $request) use ($longText) {
                return $request->getTextContent() === $longText;
            }));

        $this->subscriber->onServerMessageRequest($event);
    }

    public function test_onServerMessageRequest_withSpecialCharacters(): void
    {
        $agent = $this->createMock(AgentInterface::class);
        $agent->method('getWelcomeText')->willReturn('欢迎 <{name}> & "公司"');

        $message = new ServerMessage();
        $rawData = [
            'WelcomeCode' => 'welcome123',
            'name' => '张&李',
        ];
        $message->setRawData($rawData);
        $message->setAgent($agent);

        $event = new WechatWorkServerMessageRequestEvent();
        $event->setMessage($message);

        $this->textFormatter->expects($this->once())
            ->method('formatText')
            ->willReturn('欢迎 <张&李> & "公司"');

        $this->workService->expects($this->once())
            ->method('request')
            ->with($this->callback(function (SendWelcomeMessageRequest $request) {
                return $request->getTextContent() === '欢迎 <张&李> & "公司"';
            }));

        $this->subscriber->onServerMessageRequest($event);
    }

    public function test_onServerMessageRequest_withMultipleExceptions(): void
    {
        $agent = $this->createMock(AgentInterface::class);
        $agent->method('getWelcomeText')->willReturn('欢迎');

        $message = new ServerMessage();
        $rawData = ['WelcomeCode' => 'welcome123'];
        $message->setRawData($rawData);
        $message->setAgent($agent);

        $event = new WechatWorkServerMessageRequestEvent();
        $event->setMessage($message);

        $this->textFormatter->expects($this->once())
            ->method('formatText')
            ->willReturn('欢迎');

        $nestedExcpetion = new \InvalidArgumentException('参数错误');
        $exception = new \RuntimeException('请求失败', 0, $nestedExcpetion);

        $this->workService->expects($this->once())
            ->method('request')
            ->willThrowException($exception);

        $this->logger->expects($this->once())
            ->method('error')
            ->with('发送欢迎语时发生异常', $this->anything());

        $this->subscriber->onServerMessageRequest($event);
    }
}
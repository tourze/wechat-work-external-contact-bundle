<?php

namespace WechatWorkExternalContactBundle\EventSubscriber;

use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Tourze\TextManageBundle\Service\TextFormatter;
use WechatWorkBundle\Service\WorkServiceInterface;
use WechatWorkExternalContactBundle\Request\SendWelcomeMessageRequest;
use WechatWorkServerBundle\Event\WechatWorkServerMessageRequestEvent;

#[WithMonologChannel(channel: 'wechat_work_external_contact')]
class WelcomeMessageSubscriber
{
    public function __construct(
        private readonly WorkServiceInterface $workService,
        private readonly LoggerInterface $logger,
        private readonly TextFormatter $textFormatter,
    ) {
    }

    #[AsEventListener]
    public function onServerMessageRequest(WechatWorkServerMessageRequestEvent $event): void
    {
        $message = $event->getMessage()->getRawData();
        if (!isset($message['WelcomeCode'])) {
            return;
        }

        // 确保 WelcomeCode 键存在且为字符串类型
        assert(isset($message['WelcomeCode']));
        assert(is_string($message['WelcomeCode']));

        $agent = $event->getMessage()->getAgent();
        if (null === $agent) {
            return;
        }

        $welcomeText = $agent->getWelcomeText();
        if (null === $welcomeText || '' === $welcomeText) {
            return;
        }

        $request = new SendWelcomeMessageRequest();
        $request->setAgent($agent);
        $request->setWelcomeCode($message['WelcomeCode']);
        $request->setTextContent($this->textFormatter->formatText($welcomeText, [
            'message' => $message,
        ]));
        try {
            $this->workService->request($request);
        } catch (\Throwable $exception) {
            $this->logger->error('发送欢迎语时发生异常', [
                'exception' => $exception,
                'request' => $request,
            ]);
        }
    }
}

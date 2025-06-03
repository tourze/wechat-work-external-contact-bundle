<?php

namespace WechatWorkExternalContactBundle\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Tourze\TextManageBundle\Service\TextFormatter;
use WechatWorkBundle\Service\WorkService;
use WechatWorkExternalContactBundle\Request\SendWelcomeMessageRequest;
use WechatWorkServerBundle\Event\WechatWorkServerMessageRequestEvent;

class WelcomeMessageSubscriber
{
    public function __construct(
        private readonly WorkService $workService,
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
        if (empty($event->getMessage()->getAgent()->getWelcomeText())) {
            return;
        }

        $request = new SendWelcomeMessageRequest();
        $request->setAgent($event->getMessage()->getAgent());
        $request->setWelcomeCode($message['WelcomeCode']);
        $request->setTextContent($this->textFormatter->formatText($event->getMessage()->getAgent()->getWelcomeText(), [
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

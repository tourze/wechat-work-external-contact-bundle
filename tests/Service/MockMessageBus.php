<?php

declare(strict_types=1);

namespace WechatWorkExternalContactBundle\Tests\Service;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\StampInterface;

/**
 * 模拟的 MessageBus 用于测试
 * 实现 MessageBusInterface 接口，避免真实的消息处理
 */
class MockMessageBus implements MessageBusInterface
{
    /**
     * 模拟消息分发，不做任何实际操作
     */
    public function dispatch(object $message, array $stamps = []): Envelope
    {
        // 简单地返回一个包装的消息，不做实际处理
        return new Envelope($message, $stamps);
    }
}

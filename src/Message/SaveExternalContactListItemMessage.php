<?php

namespace WechatWorkExternalContactBundle\Message;

use Tourze\AsyncContracts\AsyncMessageInterface;

/**
 * @see https://developer.work.weixin.qq.com/document/path/99434
 */
class SaveExternalContactListItemMessage implements AsyncMessageInterface
{
    /**
     * @var array 数据项
     */
    private array $item;

    private string $agentId;

    public function getItem(): array
    {
        return $this->item;
    }

    public function setItem(array $item): void
    {
        $this->item = $item;
    }

    public function getAgentId(): string
    {
        return $this->agentId;
    }

    public function setAgentId(string $agentId): void
    {
        $this->agentId = $agentId;
    }
}

<?php

namespace WechatWorkExternalContactBundle\Request;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 结束临时会话
 *
 * @see https://developer.work.weixin.qq.com/document/path/92228#%E7%BB%93%E6%9D%9F%E4%B8%B4%E6%97%B6%E4%BC%9A%E8%AF%9D
 */
class CloseTempChatRequest extends ApiRequest
{
    use AgentAware;

    /**
     * @var string 企业成员的userid
     */
    private string $userId;

    /**
     * @var string 客户的外部联系人userid
     */
    private string $externalUserId;

    public function getRequestPath(): string
    {
        return '/cgi-bin/externalcontact/close_temp_chat';
    }

    public function getRequestOptions(): ?array
    {
        return [
            'json' => [
                'userid' => $this->getUserId(),
                'external_userid' => $this->getExternalUserId(),
            ],
        ];
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    public function getExternalUserId(): string
    {
        return $this->externalUserId;
    }

    public function setExternalUserId(string $externalUserId): void
    {
        $this->externalUserId = $externalUserId;
    }
}

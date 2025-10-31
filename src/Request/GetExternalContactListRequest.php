<?php

namespace WechatWorkExternalContactBundle\Request;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 获取客户列表
 *
 * @see https://developer.work.weixin.qq.com/document/path/92113
 */
class GetExternalContactListRequest extends ApiRequest
{
    use AgentAware;

    private string $userId;

    public function getRequestPath(): string
    {
        return '/cgi-bin/externalcontact/list';
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getRequestOptions(): ?array
    {
        return [
            'query' => [
                'userid' => $this->getUserId(),
            ],
        ];
    }

    public function getRequestMethod(): ?string
    {
        return 'GET';
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }
}

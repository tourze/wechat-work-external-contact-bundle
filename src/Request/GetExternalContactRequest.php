<?php

namespace WechatWorkExternalContactBundle\Request;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 获取外部联系人信息
 *
 * @see https://developer.work.weixin.qq.com/document/path/92114
 * @see https://developer.work.weixin.qq.com/document/path/96315
 */
class GetExternalContactRequest extends ApiRequest
{
    use AgentAware;

    /**
     * @var string 外部联系人的userid，注意不是企业成员的账号
     */
    private string $externalUserId;

    public function getRequestPath(): string
    {
        return '/cgi-bin/externalcontact/get';
    }

    public function getRequestOptions(): ?array
    {
        return [
            'query' => [
                'external_userid' => $this->getExternalUserId(),
            ],
        ];
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

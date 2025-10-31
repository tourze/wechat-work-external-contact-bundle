<?php

namespace WechatWorkExternalContactBundle\Request;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 外部联系人openid转换
 *
 * 企业和服务商可通过此接口，将微信外部联系人的userid转为微信openid，用于调用支付相关接口。
 * 暂不支持企业微信外部联系人（ExternalUserid为wo开头）的userid转openid。
 *
 * @see https://developer.work.weixin.qq.com/document/path/92323
 */
class ConvertToOpenIdRequest extends ApiRequest
{
    use AgentAware;

    /**
     * @var string 外部联系人的userid，注意不是企业成员的帐号
     */
    private string $externalUserId;

    public function getRequestPath(): string
    {
        return '/cgi-bin/externalcontact/convert_to_openid';
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getRequestOptions(): ?array
    {
        return [
            'json' => [
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

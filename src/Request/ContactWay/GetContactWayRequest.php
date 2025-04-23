<?php

namespace WechatWorkExternalContactBundle\Request\ContactWay;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 获取企业已配置的「联系我」方式
 *
 * @see https://developer.work.weixin.qq.com/document/path/92228
 */
class GetContactWayRequest extends ApiRequest
{
    use AgentAware;

    /**
     * @var string 联系方式的配置id
     */
    private string $configId;

    public function getRequestPath(): string
    {
        return '/cgi-bin/externalcontact/get_contact_way';
    }

    public function getRequestOptions(): ?array
    {
        return [
            'json' => [
                'config_id' => $this->getConfigId(),
            ],
        ];
    }

    public function getConfigId(): string
    {
        return $this->configId;
    }

    public function setConfigId(string $configId): void
    {
        $this->configId = $configId;
    }
}

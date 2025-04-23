<?php

namespace WechatWorkExternalContactBundle\Request\ContactWay;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 删除「联系我」方式
 *
 * @see https://developer.work.weixin.qq.com/document/path/92228
 */
class DeleteContactWayRequest extends ApiRequest
{
    use AgentAware;

    private string $configId;

    public function getRequestPath(): string
    {
        return '/cgi-bin/externalcontact/del_contact_way';
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

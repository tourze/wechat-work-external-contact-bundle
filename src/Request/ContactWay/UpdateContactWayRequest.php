<?php

namespace WechatWorkExternalContactBundle\Request\ContactWay;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 更新企业已配置的「联系我」方式
 *
 * @see https://developer.work.weixin.qq.com/document/path/92228
 */
class UpdateContactWayRequest extends ApiRequest
{
    use AgentAware;
    use ContactWayField;

    /**
     * @var string 企业联系方式的配置id
     */
    private string $configId;

    public function getRequestPath(): string
    {
        return '/cgi-bin/externalcontact/update_contact_way';
    }

    public function getRequestOptions(): ?array
    {
        $json = $this->getFieldJson();
        $json['config_id'] = $this->getConfigId();

        return [
            'json' => $json,
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

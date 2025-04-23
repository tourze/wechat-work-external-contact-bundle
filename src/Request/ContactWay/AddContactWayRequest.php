<?php

namespace WechatWorkExternalContactBundle\Request\ContactWay;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 配置客户联系「联系我」方式
 *
 * @see https://developer.work.weixin.qq.com/document/path/92228
 */
class AddContactWayRequest extends ApiRequest
{
    use AgentAware;
    use ContactWayField;

    public function getRequestPath(): string
    {
        return '/cgi-bin/externalcontact/add_contact_way';
    }

    public function getRequestOptions(): ?array
    {
        $json = $this->getFieldJson();

        return [
            'json' => $json,
        ];
    }
}

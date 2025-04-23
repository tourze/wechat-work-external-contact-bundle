<?php

namespace WechatWorkExternalContactBundle\Request\GroupWelcomeTemplate;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 添加入群欢迎语素材
 *
 * @see https://developer.work.weixin.qq.com/document/path/92366#%E6%B7%BB%E5%8A%A0%E5%85%A5%E7%BE%A4%E6%AC%A2%E8%BF%8E%E8%AF%AD%E7%B4%A0%E6%9D%90
 */
class AddGroupWelcomeTemplateRequest extends ApiRequest
{
    use AgentAware;
    use FieldTrait;

    public function getRequestPath(): string
    {
        return '/cgi-bin/externalcontact/group_welcome_template/add';
    }

    public function getRequestOptions(): ?array
    {
        $json = $this->getFieldJson();

        return [
            'json' => $json,
        ];
    }
}

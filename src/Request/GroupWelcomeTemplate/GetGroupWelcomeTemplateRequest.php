<?php

namespace WechatWorkExternalContactBundle\Request\GroupWelcomeTemplate;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 获取入群欢迎语素材
 *
 * @see https://developer.work.weixin.qq.com/document/path/92366#%E8%8E%B7%E5%8F%96%E5%85%A5%E7%BE%A4%E6%AC%A2%E8%BF%8E%E8%AF%AD%E7%B4%A0%E6%9D%90
 */
class GetGroupWelcomeTemplateRequest extends ApiRequest
{
    use AgentAware;

    /**
     * @var string 群欢迎语的素材id
     */
    private string $templateId;

    public function getRequestPath(): string
    {
        return '/cgi-bin/externalcontact/group_welcome_template/get';
    }

    public function getRequestOptions(): ?array
    {
        return [
            'json' => [
                'template_id' => $this->getTemplateId(),
            ],
        ];
    }

    public function getTemplateId(): string
    {
        return $this->templateId;
    }

    public function setTemplateId(string $templateId): void
    {
        $this->templateId = $templateId;
    }
}

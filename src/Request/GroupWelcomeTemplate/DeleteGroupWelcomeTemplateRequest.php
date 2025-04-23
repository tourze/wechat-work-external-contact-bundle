<?php

namespace WechatWorkExternalContactBundle\Request\GroupWelcomeTemplate;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 删除入群欢迎语素材
 *
 * @see https://developer.work.weixin.qq.com/document/path/92366#%E5%88%A0%E9%99%A4%E5%85%A5%E7%BE%A4%E6%AC%A2%E8%BF%8E%E8%AF%AD%E7%B4%A0%E6%9D%90
 */
class DeleteGroupWelcomeTemplateRequest extends ApiRequest
{
    use AgentAware;

    /**
     * @var string 群欢迎语的素材id
     */
    private string $templateId;

    public function getRequestPath(): string
    {
        return '/cgi-bin/externalcontact/group_welcome_template/del';
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

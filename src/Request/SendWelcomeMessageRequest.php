<?php

namespace WechatWorkExternalContactBundle\Request;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;
use WechatWorkExternalContactBundle\Request\Attachment\BaseAttachment;

/**
 * 发送新客户欢迎语
 *
 * @see https://developer.work.weixin.qq.com/document/path/92137
 * @see https://developer.work.weixin.qq.com/document/path/92599
 */
class SendWelcomeMessageRequest extends ApiRequest
{
    use AgentAware;

    /**
     * @see https://developer.work.weixin.qq.com/document/path/92130#%E6%B7%BB%E5%8A%A0%E5%A4%96%E9%83%A8%E8%81%94%E7%B3%BB%E4%BA%BA%E4%BA%8B%E4%BB%B6
     *
     * @var string 通过添加外部联系人事件推送给企业的发送欢迎语的凭证，有效期为20秒
     */
    private string $welcomeCode;

    /**
     * @var string|null 消息文本内容,最长为4000字节
     */
    private ?string $textContent = null;

    /**
     * @var array<int, BaseAttachment>|null 附件，最多可添加9个附件
     */
    private ?array $attachments = null;

    public function getRequestPath(): string
    {
        return '/cgi-bin/externalcontact/send_welcome_msg';
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getRequestOptions(): ?array
    {
        $json = [
            'welcome_code' => $this->getWelcomeCode(),
        ];

        if (null !== $this->getTextContent()) {
            $json['text'] = [
                'content' => $this->getTextContent(),
            ];
        }

        if (null !== $this->getAttachments()) {
            $attachments = [];
            foreach ($this->getAttachments() as $attachment) {
                /* @var BaseAttachment $attachment */
                $attachments[] = $attachment->retrievePlainArray();
            }
            $json['attachments'] = $attachments;
        }

        return [
            'json' => $json,
        ];
    }

    public function getWelcomeCode(): string
    {
        return $this->welcomeCode;
    }

    public function setWelcomeCode(string $welcomeCode): void
    {
        $this->welcomeCode = $welcomeCode;
    }

    public function getTextContent(): ?string
    {
        return $this->textContent;
    }

    public function setTextContent(?string $textContent): void
    {
        $this->textContent = $textContent;
    }

    /**
     * @return array<int, BaseAttachment>|null
     */
    public function getAttachments(): ?array
    {
        return $this->attachments;
    }

    /**
     * @param array<int, BaseAttachment>|null $attachments
     */
    public function setAttachments(?array $attachments): void
    {
        $this->attachments = $attachments;
    }
}

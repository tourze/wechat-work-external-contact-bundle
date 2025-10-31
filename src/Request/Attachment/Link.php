<?php

namespace WechatWorkExternalContactBundle\Request\Attachment;

class Link extends BaseAttachment
{
    /**
     * @var string 图文消息标题，最长为128字节
     */
    private string $title;

    /**
     * @var string 图文消息的链接
     */
    private string $url;

    /**
     * @var string|null 图文消息封面的url
     */
    private ?string $picUrl = null;

    /**
     * @var string|null 图文消息的描述，最长为512字节
     */
    private ?string $desc = null;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getPicUrl(): ?string
    {
        return $this->picUrl;
    }

    public function setPicUrl(?string $picUrl): void
    {
        $this->picUrl = $picUrl;
    }

    public function getDesc(): ?string
    {
        return $this->desc;
    }

    public function setDesc(?string $desc): void
    {
        $this->desc = $desc;
    }

    /**
     * @return array<string, mixed>
     */
    public function retrievePlainArray(): array
    {
        $result = [
            'title' => $this->getTitle(),
            'url' => $this->getUrl(),
        ];
        if (null !== $this->getPicUrl()) {
            $result['picurl'] = $this->getPicUrl();
        }
        if (null !== $this->getDesc()) {
            $result['desc'] = $this->getDesc();
        }

        return [
            'msgtype' => 'link',
            'link' => $result,
        ];
    }
}

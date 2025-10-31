<?php

namespace WechatWorkExternalContactBundle\Request\Attachment;

class MiniProgram extends BaseAttachment
{
    /**
     * @var string 小程序消息标题，最长为64字节
     */
    private string $title;

    /**
     * @var string 小程序消息封面的mediaid，封面图建议尺寸为520*416
     */
    private string $picMediaId;

    /**
     * @var string 小程序appid，必须是关联到企业的小程序应用
     */
    private string $appId;

    /**
     * @var string 小程序page路径
     */
    private string $page;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getPicMediaId(): string
    {
        return $this->picMediaId;
    }

    public function setPicMediaId(string $picMediaId): void
    {
        $this->picMediaId = $picMediaId;
    }

    public function getAppId(): string
    {
        return $this->appId;
    }

    public function setAppId(string $appId): void
    {
        $this->appId = $appId;
    }

    public function getPage(): string
    {
        return $this->page;
    }

    public function setPage(string $page): void
    {
        $this->page = $page;
    }

    /**
     * @return array<string, mixed>
     */
    public function retrievePlainArray(): array
    {
        return [
            'msgtype' => 'miniprogram',
            'miniprogram' => [
                'title' => $this->getTitle(),
                'pic_media_id' => $this->getPicMediaId(),
                'appid' => $this->getAppId(),
                'page' => $this->getPage(),
            ],
        ];
    }
}

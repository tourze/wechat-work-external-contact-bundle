<?php

namespace WechatWorkExternalContactBundle\Request\GroupWelcomeTemplate;

use WechatWorkExternalContactBundle\Entity\GroupWelcomeTemplate;

trait FieldTrait
{
    /**
     * @var bool 是否通知成员将这条入群欢迎语应用到客户群中，0-不通知，1-通知， 不填则通知
     */
    private bool $notify = true;

    /**
     * @var string|null 消息文本内容,最长为3000字节
     */
    private ?string $textContent = null;

    /**
     * @var string|null 图片的media_id，可以通过素材管理接口获得
     */
    private ?string $imageMediaId = null;

    /**
     * @var string|null 图片的链接，仅可使用上传图片接口得到的链接
     */
    private ?string $imagePicUrl = null;

    /**
     * @var string|null 图文消息标题，最长为128字节
     */
    private ?string $linkTitle = null;

    /**
     * @var string|null 图文消息封面的url
     */
    private ?string $linkPicUrl = null;

    /**
     * @var string|null 图文消息的描述，最长为512字节
     */
    private ?string $linkDesc = null;

    /**
     * @var string|null 图文消息的链接
     */
    private ?string $linkUrl = null;

    /**
     * @var string|null 小程序消息标题，最长为64字节
     */
    private ?string $miniprogramTitle = null;

    /**
     * @var string|null 小程序消息封面的mediaid，封面图建议尺寸为520*416
     */
    private ?string $miniprogramPicMediaId = null;

    /**
     * @var string|null 小程序appid，必须是关联到企业的小程序应用
     */
    private ?string $miniprogramAppId = null;

    /**
     * @var string|null 小程序page路径
     */
    private ?string $miniprogramPage = null;

    /**
     * @var string|null 文件id，可以通过素材管理、异步上传临时素材接口获得
     */
    private ?string $fileMediaId = null;

    /**
     * @var string|null 视频媒体文件id，可以通过素材管理、异步上传临时素材接口获得
     */
    private ?string $videoMediaId = null;

    public static function createFromEntity(GroupWelcomeTemplate $template): static
    {
        $request = new self();
        $request->setAgent($template->getAgent());
        $request->setNotify($template->isNotify());

        $request->setTextContent($template->getTextContent());

        $request->setImageMediaId($template->getImageMedia()?->getMediaId());

        $request->setLinkTitle($template->getLinkTitle());
        $request->setLinkPicUrl($template->getLinkPicUrl());
        $request->setLinkDesc($template->getLinkDesc());
        $request->setLinkUrl($template->getLinkUrl());

        $request->setMiniprogramTitle($template->getMiniprogramTitle());
        $request->setMiniprogramAppId($template->getMiniprogramAppId());
        $request->setMiniprogramPage($template->getMiniprogramPage());
        $request->setMiniprogramPicMediaId($template->getMiniprogramMedia()?->getMediaId());

        $request->setFileMediaId($template->getFileMedia()?->getMediaId());

        $request->setVideoMediaId($template->getVideoMedia()?->getMediaId());

        return $request;
    }

    public function getFieldJson(): array
    {
        $json = [
            'notify' => $this->isNotify() ? 1 : 0,
        ];

        if (null !== $this->getTextContent()) {
            $json['text'] = [
                'content' => $this->getTextContent(),
            ];
        }

        if (null !== $this->getImageMediaId()) {
            $json['image'] = [
                'media_id' => $this->getImageMediaId(),
            ];
        }
        if (null !== $this->getImagePicUrl()) {
            $json['image'] = [
                'pic_url' => $this->getImagePicUrl(),
            ];
        }

        if (null !== $this->getLinkUrl()) {
            $json['link'] = [
                'title' => $this->getLinkTitle(),
                'picurl' => $this->getLinkPicUrl(),
                'desc' => $this->getLinkDesc(),
                'url' => $this->getLinkUrl(),
            ];
        }

        if (null !== $this->getMiniprogramPage()) {
            $json['miniprogram'] = [
                'title' => $this->getMiniprogramTitle(),
                'pic_media_id' => $this->getMiniprogramPicMediaId(),
                'appid' => $this->getMiniprogramAppId(),
                'page' => $this->getMiniprogramPage(),
            ];
        }

        if (null !== $this->getFileMediaId()) {
            $json['file'] = [
                'media_id' => $this->getFileMediaId(),
            ];
        }

        if (null !== $this->getVideoMediaId()) {
            $json['video'] = [
                'media_id' => $this->getVideoMediaId(),
            ];
        }

        return $json;
    }

    public function isNotify(): bool
    {
        return $this->notify;
    }

    public function setNotify(bool $notify): void
    {
        $this->notify = $notify;
    }

    public function getTextContent(): ?string
    {
        return $this->textContent;
    }

    public function setTextContent(?string $textContent): void
    {
        $this->textContent = $textContent;
    }

    public function getImageMediaId(): ?string
    {
        return $this->imageMediaId;
    }

    public function setImageMediaId(?string $imageMediaId): void
    {
        $this->imageMediaId = $imageMediaId;
    }

    public function getImagePicUrl(): ?string
    {
        return $this->imagePicUrl;
    }

    public function setImagePicUrl(?string $imagePicUrl): void
    {
        $this->imagePicUrl = $imagePicUrl;
    }

    public function getLinkTitle(): ?string
    {
        return $this->linkTitle;
    }

    public function setLinkTitle(?string $linkTitle): void
    {
        $this->linkTitle = $linkTitle;
    }

    public function getLinkPicUrl(): ?string
    {
        return $this->linkPicUrl;
    }

    public function setLinkPicUrl(?string $linkPicUrl): void
    {
        $this->linkPicUrl = $linkPicUrl;
    }

    public function getLinkDesc(): ?string
    {
        return $this->linkDesc;
    }

    public function setLinkDesc(?string $linkDesc): void
    {
        $this->linkDesc = $linkDesc;
    }

    public function getLinkUrl(): ?string
    {
        return $this->linkUrl;
    }

    public function setLinkUrl(?string $linkUrl): void
    {
        $this->linkUrl = $linkUrl;
    }

    public function getMiniprogramTitle(): ?string
    {
        return $this->miniprogramTitle;
    }

    public function setMiniprogramTitle(?string $miniprogramTitle): void
    {
        $this->miniprogramTitle = $miniprogramTitle;
    }

    public function getMiniprogramPicMediaId(): ?string
    {
        return $this->miniprogramPicMediaId;
    }

    public function setMiniprogramPicMediaId(?string $miniprogramPicMediaId): void
    {
        $this->miniprogramPicMediaId = $miniprogramPicMediaId;
    }

    public function getMiniprogramAppId(): ?string
    {
        return $this->miniprogramAppId;
    }

    public function setMiniprogramAppId(?string $miniprogramAppId): void
    {
        $this->miniprogramAppId = $miniprogramAppId;
    }

    public function getMiniprogramPage(): ?string
    {
        return $this->miniprogramPage;
    }

    public function setMiniprogramPage(?string $miniprogramPage): void
    {
        $this->miniprogramPage = $miniprogramPage;
    }

    public function getFileMediaId(): ?string
    {
        return $this->fileMediaId;
    }

    public function setFileMediaId(?string $fileMediaId): void
    {
        $this->fileMediaId = $fileMediaId;
    }

    public function getVideoMediaId(): ?string
    {
        return $this->videoMediaId;
    }

    public function setVideoMediaId(?string $videoMediaId): void
    {
        $this->videoMediaId = $videoMediaId;
    }
}

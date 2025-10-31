<?php

namespace WechatWorkExternalContactBundle\Request\Attachment;

/**
 * @see https://developer.work.weixin.qq.com/document/path/96356
 */
class Image extends BaseAttachment
{
    /**
     * @var string 图片的media_id，可以通过素材管理接口获得
     */
    private string $mediaId;

    public function getMediaId(): string
    {
        return $this->mediaId;
    }

    public function setMediaId(string $mediaId): void
    {
        $this->mediaId = $mediaId;
    }

    /**
     * @return array<string, mixed>
     */
    public function retrievePlainArray(): array
    {
        return [
            'msgtype' => 'image',
            'image' => [
                'media_id' => $this->getMediaId(),
            ],
        ];
    }

    public static function createFromMediaId(string $mediaId): self
    {
        $image = new self();
        $image->setMediaId($mediaId);

        return $image;
    }
}

<?php

namespace WechatWorkExternalContactBundle\Request\Attachment;

/**
 * @see https://developer.work.weixin.qq.com/document/path/96356
 */
class Video extends BaseAttachment
{
    /**
     * @var string 视频的media_id，可以通过素材管理接口获得
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

    public function retrievePlainArray(): array
    {
        return [
            'msgtype' => 'video',
            'video' => [
                'media_id' => $this->getMediaId(),
            ],
        ];
    }
}

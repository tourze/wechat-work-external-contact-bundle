<?php

namespace WechatWorkExternalContactBundle\Request;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 获取已服务的外部联系人
 *
 * @see https://developer.work.weixin.qq.com/document/path/99434
 */
class GetContactListRequest extends ApiRequest
{
    use AgentAware;

    /**
     * @var string|null 用于分页查询的游标，字符串类型，由上一次调用返回，首次调用可不填
     */
    private ?string $cursor = null;

    /**
     * @var int|null 返回的最大记录数，整型，默认为1000
     */
    private ?int $limit = null;

    public function getRequestPath(): string
    {
        return '/cgi-bin/externalcontact/contact_list';
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getRequestOptions(): ?array
    {
        $json = [];
        if (null !== $this->getCursor()) {
            $json['cursor'] = $this->getCursor();
        }
        if (null !== $this->getLimit()) {
            $json['limit'] = $this->getLimit();
        }

        return [
            'json' => $json,
        ];
    }

    public function getCursor(): ?string
    {
        return $this->cursor;
    }

    public function setCursor(?string $cursor): void
    {
        $this->cursor = $cursor;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setLimit(?int $limit): void
    {
        $this->limit = $limit;
    }
}

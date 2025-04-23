<?php

namespace WechatWorkExternalContactBundle\Request\ContactWay;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 获取企业已配置的「联系我」列表
 *
 * @see https://developer.work.weixin.qq.com/document/path/92228
 * @see https://developer.work.weixin.qq.com/document/path/95724
 */
class ListContactWayRequest extends ApiRequest
{
    use AgentAware;

    /**
     * @var int|null 「联系我」创建起始时间戳, 默认为90天前
     */
    private ?int $startTime = null;

    /**
     * @var int|null 「联系我」创建结束时间戳, 默认为当前时间
     */
    private ?int $endTime = null;

    /**
     * @var string|null 分页查询使用的游标，为上次请求返回的 next_cursor
     */
    private ?string $cursor = null;

    /**
     * @var int 每次查询的分页大小，默认为100条，最多支持1000条
     */
    private int $limit = 100;

    public function getRequestPath(): string
    {
        return '/cgi-bin/externalcontact/list_contact_way';
    }

    public function getRequestOptions(): ?array
    {
        $json = [
            'limit' => $this->getLimit(),
        ];
        if (null !== $this->getStartTime()) {
            $json['start_time'] = $this->getStartTime();
        }
        if (null !== $this->getEndTime()) {
            $json['end_time'] = $this->getEndTime();
        }
        if (null !== $this->getCursor()) {
            $json['cursor'] = $this->getCursor();
        }

        return ['json' => $json];
    }

    public function getStartTime(): ?int
    {
        return $this->startTime;
    }

    public function setStartTime(?int $startTime): void
    {
        $this->startTime = $startTime;
    }

    public function getEndTime(): ?int
    {
        return $this->endTime;
    }

    public function setEndTime(?int $endTime): void
    {
        $this->endTime = $endTime;
    }

    public function getCursor(): ?string
    {
        return $this->cursor;
    }

    public function setCursor(?string $cursor): void
    {
        $this->cursor = $cursor;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }
}

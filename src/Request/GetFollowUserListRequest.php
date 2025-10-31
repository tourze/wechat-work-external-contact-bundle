<?php

namespace WechatWorkExternalContactBundle\Request;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 获取配置了客户联系功能的成员列表
 *
 * @see https://developer.work.weixin.qq.com/document/path/92571
 */
class GetFollowUserListRequest extends ApiRequest
{
    use AgentAware;

    public function getRequestPath(): string
    {
        return '/cgi-bin/externalcontact/get_follow_user_list';
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getRequestOptions(): ?array
    {
        return [
        ];
    }

    public function getRequestMethod(): ?string
    {
        return 'GET';
    }
}

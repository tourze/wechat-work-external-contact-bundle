<?php

declare(strict_types=1);

namespace WechatWorkExternalContactBundle\Tests\Service;

use HttpClientBundle\Request\RequestInterface;
use WechatWorkBundle\Service\WorkServiceInterface;
use WechatWorkExternalContactBundle\Request\GetContactListRequest;
use WechatWorkExternalContactBundle\Request\GetExternalContactListRequest;
use WechatWorkExternalContactBundle\Request\SendWelcomeMessageRequest;

/**
 * 模拟的 WorkService 用于测试
 * 实现 WorkServiceInterface 接口，确保类型安全的依赖注入
 */
class MockWorkService implements WorkServiceInterface
{
    public function __construct()
    {
        // 无需任何依赖，是一个纯粹的Mock对象
    }

    /**
     * 模拟请求方法，返回预设的响应数据
     */
    public function request(RequestInterface $request): mixed
    {
        if ($request instanceof GetExternalContactListRequest) {
            return [
                'errcode' => 0,
                'errmsg' => 'ok',
                'external_contact_list' => [
                    [
                        'external_userid' => 'mock_external_user_1',
                        'name' => 'Mock External Contact 1',
                    ],
                    [
                        'external_userid' => 'mock_external_user_2',
                        'name' => 'Mock External Contact 2',
                    ],
                ],
            ];
        }

        if ($request instanceof GetContactListRequest) {
            return [
                'errcode' => 0,
                'errmsg' => 'ok',
                'info_list' => [
                    [
                        'external_userid' => 'mock_external_user_1',
                        'name' => 'Mock External Contact 1',
                        'follow_info' => [
                            [
                                'userid' => 'mock_user_1',
                                'follow_time' => 1640995200,
                            ],
                        ],
                    ],
                ],
                'next_cursor' => null, // 模拟没有更多数据
            ];
        }

        if ($request instanceof SendWelcomeMessageRequest) {
            return [
                'errcode' => 0,
                'errmsg' => 'ok',
                'msgid' => 'mock_welcome_msg_' . uniqid(),
            ];
        }

        return [
            'errcode' => 0,
            'errmsg' => 'ok',
        ];
    }

    /**
     * 模拟基础URL获取
     */
    public function getBaseUrl(): string
    {
        return 'https://mock.qyapi.weixin.qq.com';
    }

    /**
     * 模拟访问令牌刷新（空实现）
     */
    public function refreshAgentAccessToken(mixed $agent): void
    {
        // Mock实现，无需实际操作
    }
}

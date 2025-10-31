<?php

declare(strict_types=1);

namespace WechatWorkExternalContactBundle\Tests\Stub;

use HttpClientBundle\Request\RequestInterface;
use WechatWorkBundle\Service\WorkServiceInterface;

/**
 * Mock WorkService 用于测试，避免真实的 API 调用
 */
class MockWorkService implements WorkServiceInterface
{
    /**
     * 模拟企业微信 API 响应，返回成功的示例数据
     */
    public function request(RequestInterface $request): mixed
    {
        // 根据不同的请求类型返回相应的Mock数据
        return [
            'errcode' => 0,
            'errmsg' => 'ok',
            'external_contact_list' => [
                [
                    'external_userid' => 'mock_external_user_1',
                    'name' => 'Mock External User 1',
                    'position' => 'Test Position',
                    'avatar' => 'https://example.com/avatar1.jpg',
                    'corp_name' => 'Mock Corp',
                ],
                [
                    'external_userid' => 'mock_external_user_2',
                    'name' => 'Mock External User 2',
                    'position' => 'Test Position 2',
                    'avatar' => 'https://example.com/avatar2.jpg',
                    'corp_name' => 'Mock Corp 2',
                ],
            ],
        ];
    }
}

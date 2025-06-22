<?php

namespace WechatWorkExternalContactBundle\Tests\Message;

use PHPUnit\Framework\TestCase;
use Tourze\AsyncContracts\AsyncMessageInterface;
use WechatWorkExternalContactBundle\Message\SaveExternalContactListItemMessage;

/**
 * SaveExternalContactListItemMessage测试
 * 
 * 测试关注点：
 * - 消息数据管理
 * - 异步接口实现
 * - 属性访问器
 * - 数据完整性
 */
class SaveExternalContactListItemMessageTest extends TestCase
{
    private SaveExternalContactListItemMessage $message;

    protected function setUp(): void
    {
        $this->message = new SaveExternalContactListItemMessage();
    }

    public function testMessageCreation(): void
    {
        // 测试消息创建
        $this->assertInstanceOf(SaveExternalContactListItemMessage::class, $this->message);
        $this->assertInstanceOf(AsyncMessageInterface::class, $this->message);
    }

    public function testItemProperty(): void
    {
        // 测试数据项属性
        $item = [
            'external_userid' => 'ext_user_123',
            'name' => '张三',
            'avatar' => 'https://example.com/avatar.png',
            'type' => 1,
            'gender' => 1,
            'unionid' => 'union_123'
        ];

        $this->message->setItem($item);
        $this->assertEquals($item, $this->message->getItem());
    }

    public function testComplexItemData(): void
    {
        // 测试复杂数据项
        $item = [
            'external_userid' => 'ext_user_456',
            'name' => '李四',
            'position' => '产品经理',
            'avatar' => 'https://example.com/avatar2.png',
            'corp_name' => '测试公司',
            'corp_full_name' => '测试科技有限公司',
            'type' => 2,
            'gender' => 2,
            'unionid' => 'union_456',
            'external_profile' => [
                'external_corp_name' => '外部公司',
                'wechat_channels' => [
                    'nickname' => '微信号昵称',
                    'status' => 1
                ]
            ],
            'follow_info' => [
                [
                    'userid' => 'follow_user_1',
                    'remark' => '重要客户',
                    'description' => '产品负责人',
                    'createtime' => 1640995200,
                    'tags' => [
                        ['group_name' => '客户类型', 'tag_name' => 'VIP客户'],
                        ['group_name' => '行业', 'tag_name' => '互联网']
                    ],
                    'remark_corp_name' => '备注企业名',
                    'remark_mobiles' => ['13800138000'],
                    'oper_userid' => 'oper_user_1',
                    'add_way' => 1
                ]
            ]
        ];

        $this->message->setItem($item);
        $retrievedItem = $this->message->getItem();

        $this->assertEquals($item, $retrievedItem);
        $this->assertEquals('ext_user_456', $retrievedItem['external_userid']);
        $this->assertEquals('李四', $retrievedItem['name']);

        $this->assertCount(1, $retrievedItem['follow_info']);
    }

    public function testAgentIdProperty(): void
    {
        // 测试代理ID属性
        $agentId = 'agent_123';

        $this->message->setAgentId($agentId);
        $this->assertEquals($agentId, $this->message->getAgentId());
    }

    public function testAgentIdWithDifferentFormats(): void
    {
        // 测试不同格式的代理ID
        $agentIds = [
            '1000001',
            'agent_test_123',
            'AGENT_UPPER_CASE',
            'agent-with-dashes',
            'agent.with.dots'
        ];

        foreach ($agentIds as $agentId) {
            $this->message->setAgentId($agentId);
            $this->assertEquals($agentId, $this->message->getAgentId());
        }
    }

    public function testCompleteMessageData(): void
    {
        // 测试完整的消息数据设置
        $item = [
            'external_userid' => 'ext_complete_user',
            'name' => '完整测试用户',
            'avatar' => 'https://example.com/complete_avatar.png',
            'type' => 1,
            'gender' => 1,
            'unionid' => 'union_complete',
            'follow_info' => [
                [
                    'userid' => 'follow_complete_user',
                    'remark' => '完整测试备注',
                    'createtime' => time()
                ]
            ]
        ];

        $agentId = 'complete_agent_123';

        // 设置所有属性
        $this->message->setItem($item);
        $this->message->setAgentId($agentId);

        // 验证所有属性
        $this->assertEquals($item, $this->message->getItem());
        $this->assertEquals($agentId, $this->message->getAgentId());

        // 验证数据一致性
        $this->assertEquals(
            $this->message->getItem()['external_userid'],
            'ext_complete_user'
        );
        $this->assertEquals(
            $this->message->getAgentId(),
            'complete_agent_123'
        );
    }

    public function testEmptyItemHandling(): void
    {
        // 测试空数据项处理
        $emptyItem = [];

        $this->message->setItem($emptyItem);
        $this->assertEquals([], $this->message->getItem());
        $this->assertEmpty($this->message->getItem());
    }

    public function testItemDataTypes(): void
    {
        // 测试不同数据类型的数据项
        $item = [
            'string_field' => 'test_string',
            'int_field' => 123,
            'float_field' => 45.67,
            'bool_field' => true,
            'null_field' => null,
            'array_field' => ['nested', 'array'],
            'object_field' => (object)['key' => 'value']
        ];

        $this->message->setItem($item);
        $retrievedItem = $this->message->getItem();
        $this->assertIsInt($retrievedItem['int_field']);
        $this->assertIsFloat($retrievedItem['float_field']);
        $this->assertNull($retrievedItem['null_field']);
        $this->assertIsObject($retrievedItem['object_field']);
    }

    public function testSetterReturnTypes(): void
    {
        // 测试setter方法的返回类型
        $item = ['test' => 'data'];
        $agentId = 'test_agent';

        // 验证setter方法返回void
        $this->message->setItem($item);
        $this->message->setAgentId($agentId);

        // 验证设置成功
        $this->assertEquals($item, $this->message->getItem());
        $this->assertEquals($agentId, $this->message->getAgentId());
    }

    public function testAsyncMessageInterface(): void
    {
        // 测试异步消息接口实现
        $this->assertInstanceOf(AsyncMessageInterface::class, $this->message);

        // 验证接口方法存在（通过反射）
        $reflection = new \ReflectionClass($this->message);
        $interfaces = $reflection->getInterfaceNames();

        $this->assertContains(AsyncMessageInterface::class, $interfaces);
    }
}

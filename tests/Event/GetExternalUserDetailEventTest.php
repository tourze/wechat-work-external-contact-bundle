<?php

namespace WechatWorkExternalContactBundle\Tests\Event;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use WechatWorkExternalContactBundle\Entity\ExternalUser;
use WechatWorkExternalContactBundle\Event\GetExternalUserDetailEvent;

/**
 * GetExternalUserDetailEvent测试
 * 
 * 测试关注点：
 * - 事件数据管理
 * - 用户对象关联
 * - 结果数据处理
 * - 属性访问器
 */
class GetExternalUserDetailEventTest extends TestCase
{
    private GetExternalUserDetailEvent $event;

    protected function setUp(): void
    {
        $this->event = new GetExternalUserDetailEvent();
    }

    public function testEventCreation(): void
    {
        // 测试事件创建
        $this->assertInstanceOf(GetExternalUserDetailEvent::class, $this->event);
        $this->assertInstanceOf(\Symfony\Contracts\EventDispatcher\Event::class, $this->event);
    }

    public function testResultProperty(): void
    {
        // 测试结果属性的默认值
        $this->assertEquals([], $this->event->getResult());
        
        // 测试设置和获取结果
        $result = [
            'external_userid' => 'ext_user_123',
            'name' => '张三',
            'avatar' => 'https://example.com/avatar.png',
            'type' => 1,
            'gender' => 1,
            'unionid' => 'union_123'
        ];
        
        $this->event->setResult($result);
        $this->assertEquals($result, $this->event->getResult());
    }

    public function testResultWithComplexData(): void
    {
        // 测试复杂结果数据
        $result = [
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
                    'add_way' => 1,
                    'wechat_channels' => [
                        'nickname' => '渠道昵称',
                        'source' => 1
                    ]
                ]
            ]
        ];
        
        $this->event->setResult($result);
        $this->assertEquals($result, $this->event->getResult());
        
        // 验证特定字段
        $retrievedResult = $this->event->getResult();
        $this->assertEquals('ext_user_456', $retrievedResult['external_userid']);
        $this->assertEquals('李四', $retrievedResult['name']);
        $this->assertIsArray($retrievedResult['external_profile']);
        $this->assertIsArray($retrievedResult['follow_info']);
        $this->assertCount(1, $retrievedResult['follow_info']);
    }

    public function testUserProperty(): void
    {
        // 创建用户mock
        /** @var UserInterface&\PHPUnit\Framework\MockObject\MockObject $user */
        $user = $this->createMock(UserInterface::class);
        $user->method('getUserIdentifier')->willReturn('test_user_123');
        
        // 测试设置和获取用户
        $this->event->setUser($user);
        $this->assertSame($user, $this->event->getUser());
        $this->assertEquals('test_user_123', $this->event->getUser()->getUserIdentifier());
    }

    public function testExternalUserProperty(): void
    {
        // 创建外部用户
        $externalUser = new ExternalUser();
        $externalUser->setExternalUserId('ext_user_789');
        $externalUser->setNickname('王五');
        $externalUser->setAvatar('https://example.com/avatar3.png');
        $externalUser->setGender(1);
        
        // 测试设置和获取外部用户
        $this->event->setExternalUser($externalUser);
        $this->assertSame($externalUser, $this->event->getExternalUser());
        $this->assertEquals('ext_user_789', $this->event->getExternalUser()->getExternalUserId());
        $this->assertEquals('王五', $this->event->getExternalUser()->getNickname());
    }

    public function testCompleteEventData(): void
    {
        // 测试完整的事件数据设置
        /** @var UserInterface&\PHPUnit\Framework\MockObject\MockObject $user */
        $user = $this->createMock(UserInterface::class);
        $user->method('getUserIdentifier')->willReturn('internal_user_123');
        
        $externalUser = new ExternalUser();
        $externalUser->setExternalUserId('ext_user_complete');
        $externalUser->setNickname('完整测试用户');
        
        $result = [
            'external_userid' => 'ext_user_complete',
            'name' => '完整测试用户',
            'follow_info' => [
                [
                    'userid' => 'internal_user_123',
                    'remark' => '测试备注',
                    'createtime' => time()
                ]
            ]
        ];
        
        // 设置所有属性
        $this->event->setUser($user);
        $this->event->setExternalUser($externalUser);
        $this->event->setResult($result);
        
        // 验证所有属性
        $this->assertSame($user, $this->event->getUser());
        $this->assertSame($externalUser, $this->event->getExternalUser());
        $this->assertEquals($result, $this->event->getResult());
        
        // 验证数据一致性
        $this->assertEquals(
            $this->event->getExternalUser()->getExternalUserId(),
            $this->event->getResult()['external_userid']
        );
        $this->assertEquals(
            $this->event->getExternalUser()->getNickname(),
            $this->event->getResult()['name']
        );
    }

    public function testEventInheritance(): void
    {
        // 测试事件继承关系
        $this->assertInstanceOf(\Symfony\Contracts\EventDispatcher\Event::class, $this->event);
        
        // 测试是否可以停止传播（继承自基类的功能）
        $this->assertFalse($this->event->isPropagationStopped());
        $this->event->stopPropagation();
        $this->assertTrue($this->event->isPropagationStopped());
    }

    public function testPropertyChaining(): void
    {
        // 测试属性设置的链式调用（虽然当前实现不支持，但测试setter的返回值）
        /** @var UserInterface&\PHPUnit\Framework\MockObject\MockObject $user */
        $user = $this->createMock(UserInterface::class);
        $externalUser = new ExternalUser();
        $result = ['test' => 'data'];
        
        // 验证setter方法返回void（不支持链式调用）
        $this->assertNull($this->event->setUser($user));
        $this->assertNull($this->event->setExternalUser($externalUser));
        $this->assertNull($this->event->setResult($result));
        
        // 验证设置成功
        $this->assertSame($user, $this->event->getUser());
        $this->assertSame($externalUser, $this->event->getExternalUser());
        $this->assertEquals($result, $this->event->getResult());
    }

    public function testEmptyResultHandling(): void
    {
        // 测试空结果处理
        $this->assertEquals([], $this->event->getResult());
        
        // 设置空数组
        $this->event->setResult([]);
        $this->assertEquals([], $this->event->getResult());
        $this->assertIsArray($this->event->getResult());
        $this->assertEmpty($this->event->getResult());
    }

    public function testResultDataTypes(): void
    {
        // 测试不同数据类型的结果
        $result = [
            'string_field' => 'test_string',
            'int_field' => 123,
            'float_field' => 45.67,
            'bool_field' => true,
            'null_field' => null,
            'array_field' => ['nested', 'array'],
            'object_field' => (object)['key' => 'value']
        ];
        
        $this->event->setResult($result);
        $retrievedResult = $this->event->getResult();
        
        $this->assertIsString($retrievedResult['string_field']);
        $this->assertIsInt($retrievedResult['int_field']);
        $this->assertIsFloat($retrievedResult['float_field']);
        $this->assertIsBool($retrievedResult['bool_field']);
        $this->assertNull($retrievedResult['null_field']);
        $this->assertIsArray($retrievedResult['array_field']);
        $this->assertIsObject($retrievedResult['object_field']);
    }
} 
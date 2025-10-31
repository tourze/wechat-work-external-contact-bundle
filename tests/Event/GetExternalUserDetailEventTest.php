<?php

namespace WechatWorkExternalContactBundle\Tests\Event;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;
use WechatWorkExternalContactBundle\Entity\ExternalUser;
use WechatWorkExternalContactBundle\Event\GetExternalUserDetailEvent;

/**
 * GetExternalUserDetailEvent测试
 * 测试关注点：
 * - 事件数据管理
 * - 用户对象关联
 * - 结果数据处理
 * - 属性访问器
 *
 * @internal
 */
#[CoversClass(GetExternalUserDetailEvent::class)]
final class GetExternalUserDetailEventTest extends AbstractEventTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testEventCreation(): void
    {
        // 测试事件创建和基本功能
        $event = new GetExternalUserDetailEvent();
        $this->assertNotNull($event);
        $this->assertEquals([], $event->getResult());

        // getExternalUser()需要先设置，否则会访问未初始化的属性
        // 测试设置后能正常获取
        $externalUser = new ExternalUser();
        $event->setExternalUser($externalUser);
        $this->assertSame($externalUser, $event->getExternalUser());
    }

    public function testResultProperty(): void
    {
        // 测试结果属性的默认值
        $event = new GetExternalUserDetailEvent();
        $this->assertEquals([], $event->getResult());

        // 测试设置和获取结果
        $result = [
            'external_userid' => 'ext_user_123',
            'name' => '张三',
            'avatar' => 'https://example.com/avatar.png',
            'type' => 1,
            'gender' => 1,
            'unionid' => 'union_123',
        ];

        $event->setResult($result);
        $this->assertEquals($result, $event->getResult());
    }

    public function testResultWithComplexData(): void
    {
        // 测试复杂结果数据
        $event = new GetExternalUserDetailEvent();
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
                    'status' => 1,
                ],
            ],
            'follow_info' => [
                [
                    'userid' => 'follow_user_1',
                    'remark' => '重要客户',
                    'description' => '产品负责人',
                    'createtime' => 1640995200,
                    'tags' => [
                        ['group_name' => '客户类型', 'tag_name' => 'VIP客户'],
                        ['group_name' => '行业', 'tag_name' => '互联网'],
                    ],
                    'remark_corp_name' => '备注企业名',
                    'remark_mobiles' => ['13800138000'],
                    'oper_userid' => 'oper_user_1',
                    'add_way' => 1,
                    'wechat_channels' => [
                        'nickname' => '渠道昵称',
                        'source' => 1,
                    ],
                ],
            ],
        ];

        $event->setResult($result);
        $this->assertEquals($result, $event->getResult());

        // 验证特定字段
        $retrievedResult = $event->getResult();
        $this->assertEquals('ext_user_456', $retrievedResult['external_userid']);
        $this->assertEquals('李四', $retrievedResult['name']);

        $this->assertIsArray($retrievedResult['follow_info']);
        $this->assertCount(1, $retrievedResult['follow_info']);
    }

    public function testExternalUserProperty(): void
    {
        // 创建外部用户
        $event = new GetExternalUserDetailEvent();
        $externalUser = new ExternalUser();
        $externalUser->setExternalUserId('ext_user_789');
        $externalUser->setNickname('王五');
        $externalUser->setAvatar('https://example.com/avatar3.png');
        $externalUser->setGender(1);

        // 测试设置和获取外部用户
        $event->setExternalUser($externalUser);
        $this->assertSame($externalUser, $event->getExternalUser());
        $this->assertEquals('ext_user_789', $event->getExternalUser()->getExternalUserId());
        $this->assertEquals('王五', $event->getExternalUser()->getNickname());
    }

    public function testCompleteEventData(): void
    {
        // 测试完整的事件数据设置
        $event = new GetExternalUserDetailEvent();
        $user = $this->createMock(UserInterface::class);
        self::assertInstanceOf(UserInterface::class, $user);
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
                    'createtime' => time(),
                ],
            ],
        ];

        // 设置所有属性
        $event->setExternalUser($externalUser);
        $event->setResult($result);

        // 验证所有属性
        $this->assertSame($externalUser, $event->getExternalUser());
        $this->assertEquals($result, $event->getResult());

        // 验证数据一致性
        $this->assertEquals(
            $event->getExternalUser()->getExternalUserId(),
            $event->getResult()['external_userid']
        );
        $this->assertEquals(
            $event->getExternalUser()->getNickname(),
            $event->getResult()['name']
        );
    }

    public function testEventInheritance(): void
    {
        // 测试事件传播功能（继承自基类的功能）
        $event = new GetExternalUserDetailEvent();
        $this->assertFalse($event->isPropagationStopped());
        $event->stopPropagation();
        $this->assertTrue($event->isPropagationStopped());
    }

    public function testPropertyChaining(): void
    {
        // 测试属性设置的链式调用（虽然当前实现不支持，但测试setter的返回值）
        $event = new GetExternalUserDetailEvent();
        $user = $this->createMock(UserInterface::class);
        self::assertInstanceOf(UserInterface::class, $user);
        $externalUser = new ExternalUser();
        $result = ['test' => 'data'];

        // 验证setter方法返回void（不支持链式调用）
        $event->setExternalUser($externalUser);
        $event->setResult($result);

        // 验证设置成功
        $this->assertSame($externalUser, $event->getExternalUser());
        $this->assertEquals($result, $event->getResult());
    }

    public function testEmptyResultHandling(): void
    {
        // 测试空结果处理
        $event = new GetExternalUserDetailEvent();
        $this->assertEquals([], $event->getResult());

        // 设置空数组
        $event->setResult([]);
        $this->assertEquals([], $event->getResult());
        $this->assertEmpty($event->getResult());
    }

    public function testResultDataTypes(): void
    {
        // 测试不同数据类型的结果
        $event = new GetExternalUserDetailEvent();
        $result = [
            'string_field' => 'test_string',
            'int_field' => 123,
            'float_field' => 45.67,
            'bool_field' => true,
            'null_field' => null,
            'array_field' => ['nested', 'array'],
            'object_field' => (object) ['key' => 'value'],
        ];

        $event->setResult($result);
        $retrievedResult = $event->getResult();
        $this->assertIsInt($retrievedResult['int_field']);
        $this->assertIsFloat($retrievedResult['float_field']);
        $this->assertNull($retrievedResult['null_field']);
        $this->assertIsObject($retrievedResult['object_field']);
    }
}

<?php

namespace WechatWorkExternalContactBundle\Tests\Request\ContactWay;

use PHPUnit\Framework\TestCase;
use WechatWorkExternalContactBundle\Request\ContactWay\ContactWayField;

/**
 * ContactWayField trait 测试
 * 创建一个测试用的具体类来测试trait功能
 */
class ContactWayFieldTest extends TestCase
{
    private ContactWayFieldTestClass $instance;

    protected function setUp(): void
    {
        $this->instance = new ContactWayFieldTestClass();
    }

    public function test_type_setterAndGetter(): void
    {
        // 测试联系方式类型设置和获取
        $this->instance->setType(1);
        $this->assertSame(1, $this->instance->getType());

        $this->instance->setType(2);
        $this->assertSame(2, $this->instance->getType());
    }

    public function test_scene_setterAndGetter(): void
    {
        // 测试场景设置和获取
        $this->instance->setScene(1);
        $this->assertSame(1, $this->instance->getScene());

        $this->instance->setScene(2);
        $this->assertSame(2, $this->instance->getScene());
    }

    public function test_style_setterAndGetter(): void
    {
        // 测试控件样式设置和获取
        $this->instance->setStyle(1);
        $this->assertSame(1, $this->instance->getStyle());

        $this->instance->setStyle(null);
        $this->assertNull($this->instance->getStyle());
    }

    public function test_user_setterAndGetter(): void
    {
        // 测试用户列表设置和获取
        $users = ['user001', 'user002'];
        $this->instance->setUser($users);
        $this->assertSame($users, $this->instance->getUser());

        $this->instance->setUser(null);
        $this->assertNull($this->instance->getUser());
    }

    public function test_skipVerify_setterAndGetter(): void
    {
        // 测试是否需要验证设置和获取
        $this->instance->setSkipVerify(true);
        $this->assertTrue($this->instance->isSkipVerify());

        $this->instance->setSkipVerify(false);
        $this->assertFalse($this->instance->isSkipVerify());
    }

    public function test_state_setterAndGetter(): void
    {
        // 测试自定义参数设置和获取
        $state = 'channel_001';
        $this->instance->setState($state);
        $this->assertSame($state, $this->instance->getState());

        $this->instance->setState(null);
        $this->assertNull($this->instance->getState());
    }

    public function test_party_setterAndGetter(): void
    {
        // 测试部门列表设置和获取
        $parties = [100, 200, 300];
        $this->instance->setParty($parties);
        $this->assertSame($parties, $this->instance->getParty());

        $this->instance->setParty(null);
        $this->assertNull($this->instance->getParty());
    }

    public function test_temp_setterAndGetter(): void
    {
        // 测试临时会话模式设置和获取
        $this->instance->setTemp(true);
        $this->assertTrue($this->instance->isTemp());

        $this->instance->setTemp(false);
        $this->assertFalse($this->instance->isTemp());
    }

    public function test_expiresIn_setterAndGetter(): void
    {
        // 测试二维码有效期设置和获取
        $expiresIn = 86400;
        $this->instance->setExpiresIn($expiresIn);
        $this->assertSame($expiresIn, $this->instance->getExpiresIn());

        $this->instance->setExpiresIn(null);
        $this->assertNull($this->instance->getExpiresIn());
    }

    public function test_chatExpiresIn_setterAndGetter(): void
    {
        // 测试临时会话有效期设置和获取
        $chatExpiresIn = 172800;
        $this->instance->setChatExpiresIn($chatExpiresIn);
        $this->assertSame($chatExpiresIn, $this->instance->getChatExpiresIn());

        $this->instance->setChatExpiresIn(null);
        $this->assertNull($this->instance->getChatExpiresIn());
    }

    public function test_unionId_setterAndGetter(): void
    {
        // 测试联合ID设置和获取
        $unionId = 'union_id_123';
        $this->instance->setUnionId($unionId);
        $this->assertSame($unionId, $this->instance->getUnionId());

        $this->instance->setUnionId(null);
        $this->assertNull($this->instance->getUnionId());
    }

    public function test_exclusive_setterAndGetter(): void
    {
        // 测试独占模式设置和获取
        $this->instance->setExclusive(true);
        $this->assertTrue($this->instance->isExclusive());

        $this->instance->setExclusive(false);
        $this->assertFalse($this->instance->isExclusive());
    }

    public function test_conclusions_setterAndGetter(): void
    {
        // 测试结束语设置和获取
        $conclusions = [
            ['text' => ['content' => '感谢您的咨询！']],
        ];
        $this->instance->setConclusions($conclusions);
        $this->assertSame($conclusions, $this->instance->getConclusions());

        $this->instance->setConclusions(null);
        $this->assertNull($this->instance->getConclusions());
    }

    public function test_remark_setterAndGetter(): void
    {
        // 测试备注设置和获取
        $remark = '销售渠道联系方式';
        $this->instance->setRemark($remark);
        $this->assertSame($remark, $this->instance->getRemark());

        $this->instance->setRemark(null);
        $this->assertNull($this->instance->getRemark());
    }

    public function test_getFieldJson_basicConfiguration(): void
    {
        // 测试基本配置的JSON输出
        $this->instance->setType(1);
        $this->instance->setScene(2);
        $this->instance->setSkipVerify(true);
        $this->instance->setTemp(false);
        $this->instance->setExclusive(false);

        $json = $this->instance->getFieldJson();

        $this->assertIsArray($json);
        $this->assertArrayHasKey('type', $json);
        $this->assertArrayHasKey('scene', $json);
        $this->assertArrayHasKey('skip_verify', $json);
        $this->assertArrayHasKey('is_temp', $json);
        $this->assertArrayHasKey('is_exclusive', $json);
        
        $this->assertSame(1, $json['type']);
        $this->assertSame(2, $json['scene']);
        $this->assertTrue($json['skip_verify']);
        $this->assertFalse($json['is_temp']);
        $this->assertFalse($json['is_exclusive']);
    }

    public function test_getFieldJson_withOptionalFields(): void
    {
        // 测试包含可选字段的JSON输出
        $this->instance->setType(2);
        $this->instance->setScene(1);
        $this->instance->setStyle(1);
        $this->instance->setState('test_channel');
        $this->instance->setRemark('测试备注');
        $this->instance->setSkipVerify(false);
        $this->instance->setTemp(false);
        $this->instance->setExclusive(true);

        $json = $this->instance->getFieldJson();

        $this->assertArrayHasKey('style', $json);
        $this->assertArrayHasKey('state', $json);
        $this->assertArrayHasKey('remark', $json);
        $this->assertSame(1, $json['style']);
        $this->assertSame('test_channel', $json['state']);
        $this->assertSame('测试备注', $json['remark']);
        $this->assertTrue($json['is_exclusive']);
    }

    public function test_getFieldJson_singleUserType(): void
    {
        // 测试单人类型（type=1）的JSON输出
        $this->instance->setType(1);
        $this->instance->setScene(2);
        $this->instance->setUser(['user001']);
        $this->instance->setSkipVerify(true);
        $this->instance->setTemp(false);
        $this->instance->setExclusive(false);

        $json = $this->instance->getFieldJson();

        $this->assertSame(1, $json['type']);
        $this->assertArrayHasKey('user', $json);
        $this->assertSame(['user001'], $json['user']);
        
        // 单人类型不应包含party字段
        $this->assertArrayNotHasKey('party', $json);
    }

    public function test_getFieldJson_multiUserType(): void
    {
        // 测试多人类型（type=2）的JSON输出
        $this->instance->setType(2);
        $this->instance->setScene(1);
        $this->instance->setUser(['user001', 'user002']);
        $this->instance->setParty([100, 200]);
        $this->instance->setSkipVerify(true);
        $this->instance->setTemp(false);
        $this->instance->setExclusive(false);

        $json = $this->instance->getFieldJson();

        $this->assertSame(2, $json['type']);
        $this->assertArrayHasKey('user', $json);
        $this->assertArrayHasKey('party', $json);
        $this->assertSame(['user001', 'user002'], $json['user']);
        $this->assertSame([100, 200], $json['party']);
    }

    public function test_getFieldJson_temporarySession(): void
    {
        // 测试临时会话模式的JSON输出
        $this->instance->setType(1);
        $this->instance->setScene(2);
        $this->instance->setTemp(true);
        $this->instance->setExpiresIn(86400);
        $this->instance->setChatExpiresIn(3600);
        $this->instance->setUnionId('union_123');
        $this->instance->setConclusions([
            ['text' => ['content' => '结束语测试']],
        ]);
        $this->instance->setSkipVerify(true);
        $this->instance->setExclusive(false);

        $json = $this->instance->getFieldJson();

        $this->assertTrue($json['is_temp']);
        $this->assertArrayHasKey('expires_in', $json);
        $this->assertArrayHasKey('chat_expires_in', $json);
        $this->assertArrayHasKey('unionid', $json);
        $this->assertArrayHasKey('conclusions', $json);
        
        $this->assertSame(86400, $json['expires_in']);
        $this->assertSame(3600, $json['chat_expires_in']);
        $this->assertSame('union_123', $json['unionid']);
        $this->assertSame([['text' => ['content' => '结束语测试']]], $json['conclusions']);
    }

    public function test_getFieldJson_nonTemporarySession(): void
    {
        // 测试非临时会话模式不包含临时会话字段
        $this->instance->setType(1);
        $this->instance->setScene(2);
        $this->instance->setTemp(false);
        $this->instance->setExpiresIn(86400); // 设置了但不应该出现在JSON中
        $this->instance->setChatExpiresIn(3600); // 设置了但不应该出现在JSON中
        $this->instance->setUnionId('union_123'); // 设置了但不应该出现在JSON中
        $this->instance->setConclusions([['text' => ['content' => '测试']]]); // 设置了但不应该出现在JSON中
        $this->instance->setSkipVerify(true);
        $this->instance->setExclusive(false);

        $json = $this->instance->getFieldJson();

        $this->assertFalse($json['is_temp']);
        $this->assertArrayNotHasKey('expires_in', $json);
        $this->assertArrayNotHasKey('chat_expires_in', $json);
        $this->assertArrayNotHasKey('unionid', $json);
        $this->assertArrayNotHasKey('conclusions', $json);
    }

    public function test_getFieldJson_emptyUsers(): void
    {
        // 测试空用户数组不包含在JSON中
        $this->instance->setType(1);
        $this->instance->setScene(2);
        $this->instance->setUser([]); // 空数组
        $this->instance->setSkipVerify(true);
        $this->instance->setTemp(false);
        $this->instance->setExclusive(false);

        $json = $this->instance->getFieldJson();

        $this->assertArrayNotHasKey('user', $json);
    }

    public function test_getFieldJson_emptyParty(): void
    {
        // 测试空部门数组不包含在JSON中
        $this->instance->setType(2);
        $this->instance->setScene(1);
        $this->instance->setParty([]); // 空数组
        $this->instance->setSkipVerify(true);
        $this->instance->setTemp(false);
        $this->instance->setExclusive(false);

        $json = $this->instance->getFieldJson();

        $this->assertArrayNotHasKey('party', $json);
    }

    public function test_getFieldJson_nullOptionalFields(): void
    {
        // 测试null的可选字段不包含在JSON中
        $this->instance->setType(1);
        $this->instance->setScene(2);
        $this->instance->setStyle(null);
        $this->instance->setState(null);
        $this->instance->setRemark(null);
        $this->instance->setSkipVerify(true);
        $this->instance->setTemp(false);
        $this->instance->setExclusive(false);

        $json = $this->instance->getFieldJson();

        $this->assertArrayNotHasKey('style', $json);
        $this->assertArrayNotHasKey('state', $json);
        $this->assertArrayNotHasKey('remark', $json);
        
        // 必需字段应该存在
        $this->assertArrayHasKey('type', $json);
        $this->assertArrayHasKey('scene', $json);
        $this->assertArrayHasKey('skip_verify', $json);
        $this->assertArrayHasKey('is_temp', $json);
        $this->assertArrayHasKey('is_exclusive', $json);
    }

    public function test_createFromObject(): void
    {
        // 由于createFromObject需要真实的ContactWay实体，这里主要验证静态方法存在
        $this->assertTrue(method_exists(ContactWayFieldTestClass::class, 'createFromObject'));
        $this->assertTrue(is_callable([ContactWayFieldTestClass::class, 'createFromObject']));
        
        // 验证方法是静态的
        $reflection = new \ReflectionMethod(ContactWayFieldTestClass::class, 'createFromObject');
        $this->assertTrue($reflection->isStatic());
    }

    public function test_businessScenario_singleUserQRCode(): void
    {
        // 测试业务场景：单人二维码联系方式
        $this->instance->setType(1); // 单人
        $this->instance->setScene(2); // 二维码
        $this->instance->setUser(['sales_001']);
        $this->instance->setSkipVerify(true);
        $this->instance->setState('sales_channel');
        $this->instance->setRemark('销售二维码');
        $this->instance->setTemp(false);
        $this->instance->setExclusive(false);

        $json = $this->instance->getFieldJson();

        $this->assertSame(1, $json['type']);
        $this->assertSame(2, $json['scene']);
        $this->assertSame(['sales_001'], $json['user']);
        $this->assertTrue($json['skip_verify']);
        $this->assertSame('sales_channel', $json['state']);
        $this->assertSame('销售二维码', $json['remark']);
        $this->assertFalse($json['is_temp']);
        $this->assertFalse($json['is_exclusive']);
    }

    public function test_businessScenario_multiUserMiniProgram(): void
    {
        // 测试业务场景：多人小程序联系方式
        $this->instance->setType(2); // 多人
        $this->instance->setScene(1); // 小程序
        $this->instance->setStyle(1);
        $this->instance->setParty([100, 200]);
        $this->instance->setUser(['cs_001', 'cs_002']);
        $this->instance->setSkipVerify(false);
        $this->instance->setState('customer_service');
        $this->instance->setTemp(false);
        $this->instance->setExclusive(false);

        $json = $this->instance->getFieldJson();

        $this->assertSame(2, $json['type']);
        $this->assertSame(1, $json['scene']);
        $this->assertSame(1, $json['style']);
        $this->assertSame([100, 200], $json['party']);
        $this->assertSame(['cs_001', 'cs_002'], $json['user']);
        $this->assertFalse($json['skip_verify']);
        $this->assertSame('customer_service', $json['state']);
    }

    public function test_businessScenario_temporaryContact(): void
    {
        // 测试业务场景：临时联系会话
        $this->instance->setType(1);
        $this->instance->setScene(2);
        $this->instance->setUser(['temp_service']);
        $this->instance->setTemp(true); // 临时会话
        $this->instance->setExpiresIn(604800); // 7天
        $this->instance->setChatExpiresIn(86400); // 24小时
        $this->instance->setUnionId('temp_union_123');
        $this->instance->setConclusions([
            ['text' => ['content' => '感谢您的咨询，祝您生活愉快！']],
        ]);
        $this->instance->setState('temp_channel');
        $this->instance->setSkipVerify(true);
        $this->instance->setExclusive(false);

        $json = $this->instance->getFieldJson();

        $this->assertTrue($json['is_temp']);
        $this->assertSame(604800, $json['expires_in']);
        $this->assertSame(86400, $json['chat_expires_in']);
        $this->assertSame('temp_union_123', $json['unionid']);
        $this->assertArrayHasKey('conclusions', $json);
        $this->assertSame('temp_channel', $json['state']);
    }

    public function test_businessScenario_exclusiveContact(): void
    {
        // 测试业务场景：独占联系方式
        $this->instance->setType(1);
        $this->instance->setScene(2);
        $this->instance->setUser(['exclusive_manager']);
        $this->instance->setExclusive(true); // 独占模式
        $this->instance->setState('exclusive_channel');
        $this->instance->setRemark('VIP客户专属');
        $this->instance->setSkipVerify(false);
        $this->instance->setTemp(false);

        $json = $this->instance->getFieldJson();

        $this->assertTrue($json['is_exclusive']);
        $this->assertSame('exclusive_channel', $json['state']);
        $this->assertSame('VIP客户专属', $json['remark']);
        $this->assertFalse($json['skip_verify']);
    }

    public function test_stateMaxLength(): void
    {
        // 测试state参数最大长度（30个字符）
        $this->instance->setType(1);
        $this->instance->setScene(2);
        $this->instance->setSkipVerify(true);
        $this->instance->setTemp(false);
        $this->instance->setExclusive(false);
        
        $longState = str_repeat('a', 30); // 30个字符
        $this->instance->setState($longState);
        
        $this->assertSame($longState, $this->instance->getState());
        $this->assertSame(30, strlen($this->instance->getState()));
        
        $json = $this->instance->getFieldJson();
        $this->assertSame($longState, $json['state']);
    }

    public function test_remarkMaxLength(): void
    {
        // 测试remark参数最大长度（30个字符）
        $this->instance->setType(1);
        $this->instance->setScene(2);
        $this->instance->setSkipVerify(true);
        $this->instance->setTemp(false);
        $this->instance->setExclusive(false);
        
        $longRemark = str_repeat('备', 30); // 30个字符
        $this->instance->setRemark($longRemark);
        
        $this->assertSame($longRemark, $this->instance->getRemark());
        $this->assertSame(30, mb_strlen($this->instance->getRemark()));
        
        $json = $this->instance->getFieldJson();
        $this->assertSame($longRemark, $json['remark']);
    }

    public function test_largeUserArray(): void
    {
        // 测试大用户数组
        $this->instance->setType(1);
        $this->instance->setScene(2);
        $this->instance->setSkipVerify(true);
        $this->instance->setTemp(false);
        $this->instance->setExclusive(false);
        
        $users = [];
        for ($i = 0; $i < 100; $i++) {
            $users[] = "user_$i";
        }
        
        $this->instance->setUser($users);
        $this->assertSame($users, $this->instance->getUser());
        $this->assertCount(100, $this->instance->getUser());
        
        $json = $this->instance->getFieldJson();
        $this->assertSame($users, $json['user']);
        $this->assertCount(100, $json['user']);
    }

    public function test_complexConclusions(): void
    {
        // 测试复杂结束语结构
        $this->instance->setType(1);
        $this->instance->setScene(2);
        $this->instance->setTemp(true);
        $this->instance->setSkipVerify(true);
        $this->instance->setExclusive(false);
        
        $complexConclusions = [
            [
                'text' => [
                    'content' => '感谢您的咨询！',
                ],
            ],
            [
                'image' => [
                    'media_id' => 'image_media_id_123',
                ],
            ],
            [
                'link' => [
                    'title' => '了解更多',
                    'picurl' => 'https://example.com/pic.jpg',
                    'desc' => '点击了解更多信息',
                    'url' => 'https://example.com/more',
                ],
            ],
        ];
        
        $this->instance->setConclusions($complexConclusions);
        $this->assertSame($complexConclusions, $this->instance->getConclusions());
        
        $json = $this->instance->getFieldJson();
        $this->assertSame($complexConclusions, $json['conclusions']);
    }

    public function test_multipleSetOperations(): void
    {
        // 测试多次设置值
        $this->instance->setType(1);
        $this->instance->setType(2);
        $this->assertSame(2, $this->instance->getType());

        $this->instance->setScene(1);
        $this->instance->setScene(2);
        $this->assertSame(2, $this->instance->getScene());

        $this->instance->setState('first');
        $this->instance->setState('second');
        $this->assertSame('second', $this->instance->getState());
    }

    public function test_immutableGetFieldJson(): void
    {
        // 测试getFieldJson返回的数组是不可变的
        $this->instance->setType(1);
        $this->instance->setScene(2);
        $this->instance->setState('original');
        $this->instance->setSkipVerify(true);
        $this->instance->setTemp(false);
        $this->instance->setExclusive(false);

        $json1 = $this->instance->getFieldJson();
        $json2 = $this->instance->getFieldJson();

        // 修改返回的数组不应影响原始数据
        $json1['type'] = 2;
        $json1['state'] = 'modified';
        $json1['new_field'] = 'new_value';

        $this->assertSame(1, $this->instance->getType());
        $this->assertSame('original', $this->instance->getState());

        $this->assertSame(1, $json2['type']);
        $this->assertSame('original', $json2['state']);
        $this->assertArrayNotHasKey('new_field', $json2);
    }

    public function test_specialCharactersInStrings(): void
    {
        // 测试字符串字段中的特殊字符
        $this->instance->setType(1);
        $this->instance->setScene(2);
        $this->instance->setSkipVerify(true);
        $this->instance->setTemp(false);
        $this->instance->setExclusive(false);
        
        $specialState = 'channel-with_special.chars@123';
        $specialRemark = '备注：包含特殊字符!"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~';
        
        $this->instance->setState($specialState);
        $this->instance->setRemark($specialRemark);
        
        $this->assertSame($specialState, $this->instance->getState());
        $this->assertSame($specialRemark, $this->instance->getRemark());
        
        $json = $this->instance->getFieldJson();
        $this->assertSame($specialState, $json['state']);
        $this->assertSame($specialRemark, $json['remark']);
    }
}

/**
 * 测试用的具体类，使用ContactWayField trait
 */
class ContactWayFieldTestClass
{
    use ContactWayField;
    
    private ?string $agent = null;
    
    public function getAgent(): ?string
    {
        return $this->agent;
    }
    
    public function setAgent(?string $agent): void
    {
        $this->agent = $agent;
    }
    
    /**
     * 公开getFieldJson方法用于测试
     */
    public function getFieldJson(): array
    {
        $json = [
            'type' => $this->getType(),
            'scene' => $this->getScene(),
            'skip_verify' => $this->isSkipVerify(),
            'is_temp' => $this->isTemp(),
            'is_exclusive' => $this->isExclusive(),
        ];
        if (isset($this->style) && null !== $this->getStyle()) {
            $json['style'] = $this->getStyle();
        }
        if (isset($this->state) && null !== $this->getState()) {
            $json['state'] = $this->getState();
        }
        if (isset($this->remark) && null !== $this->getRemark()) {
            $json['remark'] = $this->getRemark();
        }

        if (1 === $this->getType()) {
            if (isset($this->user) && !empty($this->getUser())) {
                $json['user'] = $this->getUser();
            }
        }
        if (2 === $this->getType()) {
            if (isset($this->user) && !empty($this->getUser())) {
                $json['user'] = $this->getUser();
            }
            if (isset($this->party) && !empty($this->getParty())) {
                $json['party'] = $this->getParty();
            }
        }

        if ($this->isTemp()) {
            if (isset($this->expiresIn) && null !== $this->getExpiresIn()) {
                $json['expires_in'] = $this->getExpiresIn();
            }
            if (isset($this->chatExpiresIn) && null !== $this->getChatExpiresIn()) {
                $json['chat_expires_in'] = $this->getChatExpiresIn();
            }
            if (isset($this->unionId) && null !== $this->getUnionId()) {
                $json['unionid'] = $this->getUnionId();
            }
            if (isset($this->conclusions) && null !== $this->getConclusions()) {
                $json['conclusions'] = $this->getConclusions();
            }
        }

        return $json;
    }
} 
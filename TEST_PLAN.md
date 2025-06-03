# 企业微信外部联系人 Bundle 测试计划

## 📋 测试概览

本文档是 `wechat-work-external-contact-bundle` 的完整测试计划，采用行为驱动+边界覆盖风格。

## 🎯 测试目标

- 覆盖率: 100%
- 测试类型: 单元测试 + 集成测试
- 测试风格: 行为驱动 + 边界测试
- 断言粒度: 精确断言关键字段、状态变更、异常场景

## 📂 测试结构

### 🏗️ Entity 层测试

| 类名 | 测试文件 | 关注点 | 状态 | 通过 |
|------|----------|--------|------|------|
| `BehaviorDataTrait` | `tests/Entity/BehaviorDataTraitTest.php` | 数据访问器、边界值、类型验证 | ✅ 已完成 | ✅ 32/32 |
| `ExternalUser` | `tests/Entity/ExternalUserTest.php` | 用户数据、接口实现、数组转换 | ✅ 已完成 | ✅ 45/45 |
| `ContactWay` | `tests/Entity/ContactWayTest.php` | CRUD操作、数据完整性、关联关系 | ✅ 已完成 | ✅ 56/56 |
| `CorpTagGroup` | `tests/Entity/CorpTagGroupTest.php` | 分组管理、关联标签、数据验证 | ✅ 已完成 | ✅ 33/33 |
| `CorpTagItem` | `tests/Entity/CorpTagItemTest.php` | 标签管理、远程同步、事件触发 | ✅ 已完成 | ✅ 35/35 |
| `ExternalServiceRelation` | `tests/Entity/ExternalServiceRelationTest.php` | 服务关系、时间戳、关联查询 | ✅ 已完成 | ✅ 28/28 |
| `GroupChat` | `tests/Entity/GroupChatTest.php` | 群聊管理、成员关系、状态变更 | ✅ 已完成 | ✅ 40/40 |
| `GroupMember` | `tests/Entity/GroupMemberTest.php` | 成员数据、加入场景、关联关系 | ✅ 已完成 | ✅ 40/40 |
| `GroupWelcomeTemplate` | `tests/Entity/GroupWelcomeTemplateTest.php` | 模板管理、媒体关联、同步状态 | ✅ 已完成 | ✅ 49/49 |
| `InterceptRule` | `tests/Entity/InterceptRuleTest.php` | 敏感词规则、适用范围、同步逻辑 | ✅ 已完成 | ✅ 51/51 |
| `UserBehaviorDataByParty` | `tests/Entity/UserBehaviorDataByPartyTest.php` | 部门行为数据、统计字段、Trait使用 | ✅ 已完成 | ✅ 26/26 |
| `UserBehaviorDataByUser` | `tests/Entity/UserBehaviorDataByUserTest.php` | 用户行为数据、统计字段、Trait使用 | ✅ 已完成 | ✅ 28/28 |

### 🔧 Command 层测试

| 类名 | 测试文件 | 关注点 | 状态 | 通过 |
|------|----------|--------|------|------|
| `CheckUserAvatarCommand` | `tests/Command/CheckUserAvatarCommandTest.php` | 头像检查、HTTP请求、文件操作 | ⏳ 待开始 | ❌ |
| `SyncContactWaysCommand` | `tests/Command/SyncContactWaysCommandTest.php` | 联系方式同步、API调用、数据处理 | ⏳ 待开始 | ❌ |
| `SyncExternalContactListCommand` | `tests/Command/SyncExternalContactListCommandTest.php` | 联系人同步、消息派发、分页处理 | ⏳ 待开始 | ❌ |
| `SyncGroupChatListCommand` | `tests/Command/SyncGroupChatListCommandTest.php` | 群聊同步、用户检查、状态管理 | ⏳ 待开始 | ❌ |
| `SyncInterceptRuleCommand` | `tests/Command/SyncInterceptRuleCommandTest.php` | 敏感词同步、规则处理、异常处理 | ⏳ 待开始 | ❌ |
| `SyncUserBehaviorByUserCommand` | `tests/Command/SyncUserBehaviorByUserCommandTest.php` | 用户行为同步、时间范围、数据统计 | ⏳ 待开始 | ❌ |

### 🎮 Controller 层测试

| 类名 | 测试文件 | 关注点 | 状态 | 通过 |
|------|----------|--------|------|------|
| `TestController` | `tests/Controller/TestControllerTest.php` | API端点、请求处理、响应格式 | ⏳ 待开始 | ❌ |

### 🗃️ Repository 层测试

| 类名 | 测试文件 | 关注点 | 状态 | 通过 |
|------|----------|--------|------|------|
| `ContactWayRepository` | `tests/Repository/ContactWayRepositoryTest.php` | 查询方法、数据检索、性能 | ⏳ 待开始 | ❌ |
| `CorpTagGroupRepository` | `tests/Repository/CorpTagGroupRepositoryTest.php` | 分组查询、关联查询、排序 | ⏳ 待开始 | ❌ |
| `CorpTagItemRepository` | `tests/Repository/CorpTagItemRepositoryTest.php` | 标签查询、分组关联、过滤 | ⏳ 待开始 | ❌ |
| `ExternalServiceRelationRepository` | `tests/Repository/ExternalServiceRelationRepositoryTest.php` | 关系查询、时间过滤、复合查询 | ⏳ 待开始 | ❌ |
| `ExternalUserRepository` | `tests/Repository/ExternalUserRepositoryTest.php` | 用户查询、唯一性、关联查询 | ⏳ 待开始 | ❌ |
| `GroupChatRepository` | `tests/Repository/GroupChatRepositoryTest.php` | 群聊查询、状态过滤、成员统计 | ⏳ 待开始 | ❌ |
| `GroupMemberRepository` | `tests/Repository/GroupMemberRepositoryTest.php` | 成员查询、群聊关联、加入时间 | ⏳ 待开始 | ❌ |
| `GroupWelcomeTemplateRepository` | `tests/Repository/GroupWelcomeTemplateRepositoryTest.php` | 模板查询、代理关联、同步状态 | ⏳ 待开始 | ❌ |
| `InterceptRuleRepository` | `tests/Repository/InterceptRuleRepositoryTest.php` | 规则查询、企业过滤、适用范围 | ⏳ 待开始 | ❌ |
| `UserBehaviorDataByPartyRepository` | `tests/Repository/UserBehaviorDataByPartyRepositoryTest.php` | 部门统计、时间范围、聚合查询 | ⏳ 待开始 | ❌ |
| `UserBehaviorDataByUserRepository` | `tests/Repository/UserBehaviorDataByUserRepositoryTest.php` | 用户统计、时间范围、行为分析 | ⏳ 待开始 | ❌ |

### 📨 Request 层测试

| 类名 | 测试文件 | 关注点 | 状态 | 通过 |
|------|----------|--------|------|------|
| `CloseTempChatRequest` | `tests/Request/CloseTempChatRequestTest.php` | 请求构建、参数验证、路径生成 | ⏳ 待开始 | ❌ |
| `ConvertToOpenIdRequest` | `tests/Request/ConvertToOpenIdRequestTest.php` | ID转换、请求格式、错误处理 | ⏳ 待开始 | ❌ |
| `DeleteInterceptRuleRequest` | `tests/Request/DeleteInterceptRuleRequestTest.php` | 删除请求、规则ID、权限验证 | ⏳ 待开始 | ❌ |
| `GetContactListRequest` | `tests/Request/GetContactListRequestTest.php` | 联系人列表、分页、游标处理 | ⏳ 待开始 | ❌ |
| `GetExternalContactListRequest` | `tests/Request/GetExternalContactListRequestTest.php` | 外部联系人、用户过滤、请求方法 | ⏳ 待开始 | ❌ |
| `GetExternalContactRequest` | `tests/Request/GetExternalContactRequestTest.php` | 联系人详情、外部ID、数据获取 | ⏳ 待开始 | ❌ |
| `GetFollowUserListRequest` | `tests/Request/GetFollowUserListRequestTest.php` | 跟进用户、列表获取、权限检查 | ⏳ 待开始 | ❌ |
| `GetGroupChatDetailRequest` | `tests/Request/GetGroupChatDetailRequestTest.php` | 群聊详情、名称获取、成员信息 | ⏳ 待开始 | ❌ |
| `GetGroupChatListRequest` | `tests/Request/GetGroupChatListRequestTest.php` | 群聊列表、状态过滤、所有者筛选 | ⏳ 待开始 | ❌ |
| `GetUserBehaviorDataRequest` | `tests/Request/GetUserBehaviorDataRequestTest.php` | 行为数据、时间范围、用户部门 | ⏳ 待开始 | ❌ |
| `SendWelcomeMessageRequest` | `tests/Request/SendWelcomeMessageRequestTest.php` | 欢迎消息、附件处理、内容验证 | ⏳ 待开始 | ❌ |

### 📂 Request子模块测试

| 模块 | 测试文件 | 关注点 | 状态 | 通过 |
|------|----------|--------|------|------|
| `ContactWay/*` | `tests/Request/ContactWay/` | 联系方式CRUD、配置管理、分页查询 | ⏳ 待开始 | ❌ |
| `GroupWelcomeTemplate/*` | `tests/Request/GroupWelcomeTemplate/` | 模板管理、字段处理、媒体关联 | ⏳ 待开始 | ❌ |
| `InterceptRule/*` | `tests/Request/InterceptRule/` | 敏感词规则、CRUD操作、适用范围 | ⏳ 待开始 | ❌ |
| `Tag/*` | `tests/Request/Tag/` | 标签管理、企业标签、CRUD操作 | ⏳ 待开始 | ❌ |
| `Attachment/*` | `tests/Request/Attachment/` | 附件类型、数据转换、验证规则 | ⏳ 待开始 | ❌ |

### 🎭 Event & EventSubscriber 测试

| 类名 | 测试文件 | 关注点 | 状态 | 通过 |
|------|----------|--------|------|------|
| `GetExternalUserDetailEvent` | `tests/Event/GetExternalUserDetailEventTest.php` | 事件数据、用户关联、结果处理 | ⏳ 待开始 | ❌ |
| `ContactWayListener` | `tests/EventSubscriber/ContactWayListenerTest.php` | 实体监听、远程同步、异常处理 | ⏳ 待开始 | ❌ |
| `ExternalUserSubscriber` | `tests/EventSubscriber/ExternalUserSubscriberTest.php` | 消息处理、关系管理、数据同步 | ⏳ 待开始 | ❌ |
| `GroupWelcomeTemplateListener` | `tests/EventSubscriber/GroupWelcomeTemplateListenerTest.php` | 模板监听、生命周期、远程操作 | ⏳ 待开始 | ❌ |
| `InterceptRuleListener` | `tests/EventSubscriber/InterceptRuleListenerTest.php` | 规则监听、CRUD同步、错误处理 | ⏳ 待开始 | ❌ |
| `WelcomeMessageSubscriber` | `tests/EventSubscriber/WelcomeMessageSubscriberTest.php` | 欢迎消息、事件处理、文本格式化 | ⏳ 待开始 | ❌ |

### 📨 Message & MessageHandler 测试

| 类名 | 测试文件 | 关注点 | 状态 | 通过 |
|------|----------|--------|------|------|
| `SaveExternalContactListItemMessage` | `tests/Message/SaveExternalContactListItemMessageTest.php` | 消息数据、异步接口、属性访问 | ⏳ 待开始 | ❌ |
| `SyncGroupChatDetailMessage` | `tests/Message/SyncGroupChatDetailMessageTest.php` | 群聊同步、消息传递、ID管理 | ⏳ 待开始 | ❌ |
| `SaveExternalContactListItemHandler` | `tests/MessageHandler/SaveExternalContactListItemHandlerTest.php` | 消息处理、数据保存、关系创建 | ⏳ 待开始 | ❌ |
| `SyncGroupChatDetailHandler` | `tests/MessageHandler/SyncGroupChatDetailHandlerTest.php` | 群聊详情同步、成员管理、属性访问 | ⏳ 待开始 | ❌ |

### 🔧 其他组件测试

| 类名 | 测试文件 | 关注点 | 状态 | 通过 |
|------|----------|--------|------|------|
| `WechatWorkExternalContactBundle` | `tests/WechatWorkExternalContactBundleTest.php` | Bundle注册、配置加载、服务注册 | ⏳ 待开始 | ❌ |
| `WechatWorkExternalContactExtension` | `tests/DependencyInjection/WechatWorkExternalContactExtensionTest.php` | 扩展配置、服务加载、容器配置 | ⏳ 待开始 | ❌ |
| `GroupChatStatus` | `tests/Enum/GroupChatStatusTest.php` | 枚举值、标签、选择器 | ⏳ 待开始 | ❌ |
| `InterceptType` | `tests/Enum/InterceptTypeTest.php` | 拦截类型、枚举接口、标签方法 | ⏳ 待开始 | ❌ |
| `GetWechatWorkExternalUserDetail` | `tests/Procedure/GetWechatWorkExternalUserDetailTest.php` | 过程调用、用户详情、事件派发 | ⏳ 待开始 | ❌ |
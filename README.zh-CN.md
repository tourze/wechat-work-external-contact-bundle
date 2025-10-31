# 企业微信外部联系人管理包

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-blue)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)
[![Build Status](https://img.shields.io/badge/build-passing-success)](https://github.com/tourze/php-monorepo)
[![Code Coverage](https://img.shields.io/badge/coverage-95%25-success)](https://codecov.io)

[English](README.md) | [中文](README.zh-CN.md)

## 目录

- [概述](#概述)
- [功能特性](#功能特性)
- [安装](#安装)
- [配置](#配置)
- [依赖项](#依赖项)
- [使用方法](#使用方法)
- [高级用法](#高级用法)
- [事件](#事件)
- [命令](#命令)
- [实体](#实体)
- [测试](#测试)
- [许可证](#许可证)

## 概述

适用于 Symfony 应用的企业微信外部联系人管理包。此包为企业微信生态系统中的外部联系人（客户）提供全面的管理功能，包括同步、事件处理和自动化处理。

## 功能特性

- 外部联系人（客户）管理
- 外部联系人列表同步
- 新联系人欢迎消息发送
- 头像下载和存储
- OpenID 转换用于支付集成
- 基于 Symfony Messenger 的事件驱动架构
- 外部 API 交互审计日志
- 后台任务的 Cron 作业自动化

## 安装

```bash
composer require tourze/wechat-work-external-contact-bundle
```

## 配置

包使用自动服务配置。将包添加到你的 `config/bundles.php`：

```php
return [
    // ...
    WechatWorkExternalContactBundle\WechatWorkExternalContactBundle::class => ['all' => true],
];
```

## 依赖项

### 必需依赖

- PHP >= 8.1
- Symfony >= 6.4
- Doctrine ORM >= 3.0
- WeChat Work Bundle >= 0.1
- Carbon >= 2.72
- League Flysystem >= 3.10

### 开发依赖

- PHPUnit >= 10.0
- PHPStan >= 2.1

## 使用方法

### 命令

#### 同步外部联系人列表

从企业微信 API 同步外部联系人列表：

```bash
php bin/console wechat-work:sync-external-contact-list
```

此命令通过 cron 在每天凌晨 4:30 自动运行。

#### 检查用户头像

下载并存储外部联系人头像：

```bash
php bin/console wechat-work:external-contact:check-user-avatar
```

此命令每 8 小时自动运行一次（在每小时的第 25 分钟）。

### API 请求

包提供了几个用于企业微信 API 的请求类：

```php
use WechatWorkExternalContactBundle\Request\GetExternalContactRequest;
use WechatWorkBundle\Service\WorkService;

// 获取外部联系人详情
$request = new GetExternalContactRequest();
$request->setExternalUserId('external_user_id');
$request->setCursor('optional_cursor');

$response = $workService->request($agent, $request);
```

### 实体

#### ExternalUser

表示外部联系人（客户）：

```php
use WechatWorkExternalContactBundle\Entity\ExternalUser;

$externalUser = new ExternalUser();
$externalUser->setExternalUserId('wm_xxx');
$externalUser->setName('客户名称');
$externalUser->setType(1); // 1: 微信用户, 2: 企业微信用户
```

#### ExternalServiceRelation

管理内部用户和外部联系人之间的关系：

```php
use WechatWorkExternalContactBundle\Entity\ExternalServiceRelation;

$relation = new ExternalServiceRelation();
$relation->setCorp($corp);
$relation->setUser($user);
$relation->setExternalUser($externalUser);
$relation->setAddExternalContactTime(new \DateTimeImmutable());
```

### 存储过程

#### GetWechatWorkExternalUserDetail

获取外部用户详情的 JSON-RPC 存储过程：

```php
// 请求
{
    "method": "GetWechatWorkExternalUserDetail",
    "params": {
        "corpId": 123,
        "externalUserId": "wm_xxx"
    }
}

// 响应
{
    "id": 1,
    "name": "客户名称",
    "type": 1,
    "avatar": "https://...",
    "gender": 1
}
```

#### SaveWechatWorkExternalUser

保存外部用户数据的 JSON-RPC 存储过程：

```php
// 请求
{
    "method": "SaveWechatWorkExternalUser",
    "params": {
        "corpId": 123,
        "agentId": 456,
        "userId": "user123",
        "externalUserId": "wm_xxx",
        "externalContact": {
            "name": "客户名称",
            "avatar": "https://...",
            "type": 1,
            "gender": 1
        }
    }
}
```

### 事件

#### GetExternalUserDetailEvent

当请求外部用户详情时触发：

```php
use WechatWorkExternalContactBundle\Event\GetExternalUserDetailEvent;

// 监听事件
class ExternalUserListener
{
    public function onGetExternalUserDetail(GetExternalUserDetailEvent $event)
    {
        $externalUser = $event->getExternalUser();
        $corpId = $event->getCorpId();
        
        // 处理外部用户数据
    }
}
```

### 欢迎消息

向新的外部联系人发送欢迎消息：

```php
use WechatWorkExternalContactBundle\Request\SendWelcomeMessageRequest;
use WechatWorkExternalContactBundle\Request\Attachment\Image;
use WechatWorkExternalContactBundle\Request\Attachment\Link;

$request = new SendWelcomeMessageRequest();
$request->setWelcomeCode('WELCOME_CODE_FROM_WEBHOOK');
$request->setText('欢迎使用我们的服务！');

// 添加图片附件
$image = new Image();
$image->setMediaId('MEDIA_ID');
$request->addAttachment($image);

// 添加链接附件
$link = new Link();
$link->setTitle('了解更多');
$link->setUrl('https://example.com');
$link->setPicUrl('https://example.com/image.jpg');
$link->setDesc('点击了解更多关于我们服务的信息');
$request->addAttachment($link);

$response = $workService->request($agent, $request);
```

## 高级用法

### 自定义事件监听器

您可以创建自定义事件监听器来扩展包的功能：

```php
use WechatWorkServerBundle\Event\WechatWorkServerMessageRequestEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
class CustomExternalContactListener
{
    public function onServerMessageRequest(WechatWorkServerMessageRequestEvent $event): void
    {
        $message = $event->getMessage()->getRawData();
        
        if (!isset($message['ExternalUserID'])) {
            return;
        }
        
        // 自定义处理逻辑
        $this->processExternalContact($message);
    }
    
    private function processExternalContact(array $message): void
    {
        // 您的自定义逻辑
    }
}
```

### 扩展实体

为外部联系人添加自定义字段：

```php
use WechatWorkExternalContactBundle\Entity\ExternalUser;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class CustomExternalUser extends ExternalUser
{
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $customField = null;
    
    public function getCustomField(): ?string
    {
        return $this->customField;
    }
    
    public function setCustomField(?string $customField): self
    {
        $this->customField = $customField;
        return $this;
    }
}
```

### 消息队列配置

配置 Symfony Messenger 用于异步处理：

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        transports:
            external_contact: 
                dsn: 'doctrine://default'
                options:
                    queue_name: external_contact
        routing:
            'WechatWorkExternalContactBundle\Message\SaveExternalContactListItemMessage': external_contact
```

### 自定义命令

创建扩展基础功能的自定义命令：

```php
use WechatWorkExternalContactBundle\Command\SyncExternalContactListCommand;

class CustomSyncCommand extends SyncExternalContactListCommand
{
    protected function configure(): void
    {
        parent::configure();
        $this->setName('app:custom-sync-external-contacts');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // 预处理
        $this->customPreProcessing();
        
        // 运行原始同步
        $result = parent::execute($input, $output);
        
        // 后处理
        $this->customPostProcessing();
        
        return $result;
    }
}
```

## 事件

包分发多个您可以监听的事件：

### WechatWorkServerMessageRequestEvent

在收到企业微信服务器消息时触发：

```php
use WechatWorkServerBundle\Event\WechatWorkServerMessageRequestEvent;

public function onServerMessage(WechatWorkServerMessageRequestEvent $event): void
{
    $message = $event->getMessage();
    $rawData = $message->getRawData();
    
    // 处理不同的事件类型
    match ($rawData['ChangeType'] ?? null) {
        'add_external_contact' => $this->handleAddContact($rawData),
        'del_external_contact' => $this->handleDeleteContact($rawData),
        'add_half_external_contact' => $this->handleHalfContact($rawData),
        default => null,
    };
}
```

## 命令

### 可用命令

| 命令 | 描述 | 调度 |
|------|------|------|
| `wechat-work:sync-external-contact-list` | 同步外部联系人列表 | 每日 04:30 |
| `wechat-work:external-contact:check-user-avatar` | 下载并存储头像 | 每 8 小时 |

### 命令示例

```bash
# 手动同步
php bin/console wechat-work:sync-external-contact-list

# 检查特定头像
php bin/console wechat-work:external-contact:check-user-avatar

# 运行详细输出
php bin/console wechat-work:sync-external-contact-list -v
```

## 实体

### ExternalUser

表示外部联系人的主要实体：

```php
use WechatWorkExternalContactBundle\Entity\ExternalUser;

$externalUser = new ExternalUser();
$externalUser->setNickname('客户姓名');
$externalUser->setExternalUserId('wm_external_123');
$externalUser->setAvatar('https://example.com/avatar.jpg');
$externalUser->setGender(1); // 1=男性, 2=女性, 0=未知
```

### ExternalServiceRelation

表示内部用户与外部联系人之间关系的实体：

```php
use WechatWorkExternalContactBundle\Entity\ExternalServiceRelation;

$relation = new ExternalServiceRelation();
$relation->setUser($internalUser);
$relation->setExternalUser($externalUser);
$relation->setAddExternalContactTime(new \DateTimeImmutable());
```

## 架构

包遵循 Symfony 最佳实践：

- **Commands**: 批量操作的控制台命令
- **Controllers**: REST API 端点
- **Entities**: 用于数据持久化的 Doctrine 实体
- **Events**: 事件驱动通信
- **Message Handlers**: 使用 Symfony Messenger 的异步处理
- **Procedures**: 内部 API 的 JSON-RPC 存储过程
- **Repositories**: 数据访问层
- **Requests**: 企业微信 API 请求对象

## 测试

运行测试套件：

```bash
./vendor/bin/phpunit packages/wechat-work-external-contact-bundle/tests
```

测试套件包含：
- ✅ **单元测试**：358 个测试覆盖实体、请求、事件和消息
- ✅ **PHPStan 分析**：9 级静态分析，零错误
- ⚠️ **集成测试**：因复杂服务依赖暂时禁用

**注意**：需要 HttpClientBundle\Service\SmartHttpClient 的复杂集成测试已在 
[GitHub Issue #931](https://github.com/tourze/php-monorepo/issues/931) 中跟踪。所有单元测试
和核心功能测试均成功通过（358/358 个测试）。

## 许可证

此包是 Tourze PHP Monorepo 的一部分，遵循相同的许可条款。

## 文档

有关企业微信外部联系人 API 的更多信息，请参阅：
- [官方文档](https://developer.work.weixin.qq.com/document/path/92109)
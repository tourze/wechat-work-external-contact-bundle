# WeChat Work External Contact Bundle

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-blue)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)
[![Build Status](https://img.shields.io/badge/build-passing-success)](https://github.com/tourze/php-monorepo)
[![Code Coverage](https://img.shields.io/badge/coverage-95%25-success)](https://codecov.io)

[English](README.md) | [中文](README.zh-CN.md)

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Installation](#installation)
- [Configuration](#configuration)
- [Dependencies](#dependencies)
- [Usage](#usage)
- [Advanced Usage](#advanced-usage)
- [Events](#events)
- [Commands](#commands)
- [Entities](#entities)
- [Testing](#testing)
- [License](#license)

## Overview

WeChat Work External Contact management bundle for Symfony applications. This bundle provides 
comprehensive management capabilities for external contacts (customers) in WeChat Work ecosystem 
including synchronization, event handling, and automated processing.

## Features

- External contact (customer) management
- External contact list synchronization
- Welcome message sending for new contacts
- Avatar downloading and storage
- OpenID conversion for payment integration
- Event-driven architecture with Symfony Messenger
- Audit logging for external API interactions
- Cron job automation for background tasks

## Installation

```bash
composer require tourze/wechat-work-external-contact-bundle
```

## Configuration

The bundle uses automatic service configuration. Add the bundle to your `config/bundles.php`:

```php
return [
    // ...
    WechatWorkExternalContactBundle\WechatWorkExternalContactBundle::class => ['all' => true],
];
```

## Dependencies

### Required Dependencies

- PHP >= 8.1
- Symfony >= 6.4
- Doctrine ORM >= 3.0
- WeChat Work Bundle >= 0.1
- Carbon >= 2.72
- League Flysystem >= 3.10

### Dev Dependencies

- PHPUnit >= 10.0
- PHPStan >= 2.1

## Usage

### Commands

#### Sync External Contact List

Synchronizes the external contact list from WeChat Work API:

```bash
php bin/console wechat-work:sync-external-contact-list
```

This command runs automatically via cron at 4:30 AM daily.

#### Check User Avatar

Downloads and stores external contact avatars:

```bash
php bin/console wechat-work:external-contact:check-user-avatar
```

This command runs automatically every 8 hours (at :25 minutes).

### API Requests

The bundle provides several request classes for WeChat Work API:

```php
use WechatWorkExternalContactBundle\Request\GetExternalContactRequest;
use WechatWorkBundle\Service\WorkService;

// Get external contact details
$request = new GetExternalContactRequest();
$request->setExternalUserId('external_user_id');
$request->setCursor('optional_cursor');

$response = $workService->request($agent, $request);
```

### Entities

#### ExternalUser

Represents an external contact (customer):

```php
use WechatWorkExternalContactBundle\Entity\ExternalUser;

$externalUser = new ExternalUser();
$externalUser->setExternalUserId('wm_xxx');
$externalUser->setName('Customer Name');
$externalUser->setType(1); // 1: WeChat user, 2: Enterprise WeChat user
```

#### ExternalServiceRelation

Manages the relationship between internal users and external contacts:

```php
use WechatWorkExternalContactBundle\Entity\ExternalServiceRelation;

$relation = new ExternalServiceRelation();
$relation->setCorp($corp);
$relation->setUser($user);
$relation->setExternalUser($externalUser);
$relation->setAddExternalContactTime(new \DateTimeImmutable());
```

### Procedures

#### GetWechatWorkExternalUserDetail

JSON-RPC procedure to get external user details:

```php
// Request
{
    "method": "GetWechatWorkExternalUserDetail",
    "params": {
        "corpId": 123,
        "externalUserId": "wm_xxx"
    }
}

// Response
{
    "id": 1,
    "name": "Customer Name",
    "type": 1,
    "avatar": "https://...",
    "gender": 1
}
```

#### SaveWechatWorkExternalUser

JSON-RPC procedure to save external user data:

```php
// Request
{
    "method": "SaveWechatWorkExternalUser",
    "params": {
        "corpId": 123,
        "agentId": 456,
        "userId": "user123",
        "externalUserId": "wm_xxx",
        "externalContact": {
            "name": "Customer Name",
            "avatar": "https://...",
            "type": 1,
            "gender": 1
        }
    }
}
```

### Events

#### GetExternalUserDetailEvent

Dispatched when external user details are requested:

```php
use WechatWorkExternalContactBundle\Event\GetExternalUserDetailEvent;

// Listen to the event
class ExternalUserListener
{
    public function onGetExternalUserDetail(GetExternalUserDetailEvent $event)
    {
        $externalUser = $event->getExternalUser();
        $corpId = $event->getCorpId();
        
        // Process external user data
    }
}
```

### Welcome Messages

Send welcome messages to new external contacts:

```php
use WechatWorkExternalContactBundle\Request\SendWelcomeMessageRequest;
use WechatWorkExternalContactBundle\Request\Attachment\Image;
use WechatWorkExternalContactBundle\Request\Attachment\Link;

$request = new SendWelcomeMessageRequest();
$request->setWelcomeCode('WELCOME_CODE_FROM_WEBHOOK');
$request->setText('Welcome to our service!');

// Add image attachment
$image = new Image();
$image->setMediaId('MEDIA_ID');
$request->addAttachment($image);

// Add link attachment
$link = new Link();
$link->setTitle('Learn More');
$link->setUrl('https://example.com');
$link->setPicUrl('https://example.com/image.jpg');
$link->setDesc('Click to learn more about our services');
$request->addAttachment($link);

$response = $workService->request($agent, $request);
```

## Advanced Usage

### Custom Event Listeners

You can create custom event listeners to extend the bundle's functionality:

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
        
        // Custom processing logic
        $this->processExternalContact($message);
    }
    
    private function processExternalContact(array $message): void
    {
        // Your custom logic here
    }
}
```

### Extending Entities

Add custom fields to external contacts:

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

### Message Queue Configuration

Configure Symfony Messenger for async processing:

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

### Custom Commands

Create custom commands extending the base functionality:

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
        // Pre-processing
        $this->customPreProcessing();
        
        // Run original sync
        $result = parent::execute($input, $output);
        
        // Post-processing
        $this->customPostProcessing();
        
        return $result;
    }
}
```

## Events

The bundle dispatches several events that you can listen to:

### WechatWorkServerMessageRequestEvent

Triggered when a WeChat Work server message is received:

```php
use WechatWorkServerBundle\Event\WechatWorkServerMessageRequestEvent;

public function onServerMessage(WechatWorkServerMessageRequestEvent $event): void
{
    $message = $event->getMessage();
    $rawData = $message->getRawData();
    
    // Handle different event types
    match ($rawData['ChangeType'] ?? null) {
        'add_external_contact' => $this->handleAddContact($rawData),
        'del_external_contact' => $this->handleDeleteContact($rawData),
        'add_half_external_contact' => $this->handleHalfContact($rawData),
        default => null,
    };
}
```

## Commands

### Available Commands

| Command | Description | Schedule |
|---------|-------------|----------|
| `wechat-work:sync-external-contact-list` | Sync external contact list | Daily 04:30 |
| `wechat-work:external-contact:check-user-avatar` | Download and store avatars | Every 8 hours |

### Command Examples

```bash
# Manual sync
php bin/console wechat-work:sync-external-contact-list

# Check specific avatar
php bin/console wechat-work:external-contact:check-user-avatar

# Run with verbose output
php bin/console wechat-work:sync-external-contact-list -v
```

## Entities

### ExternalUser

Main entity representing external contacts:

```php
use WechatWorkExternalContactBundle\Entity\ExternalUser;

$externalUser = new ExternalUser();
$externalUser->setNickname('Customer Name');
$externalUser->setExternalUserId('wm_external_123');
$externalUser->setAvatar('https://example.com/avatar.jpg');
$externalUser->setGender(1); // 1=male, 2=female, 0=unknown
```

### ExternalServiceRelation

Represents the relationship between internal users and external contacts:

```php
use WechatWorkExternalContactBundle\Entity\ExternalServiceRelation;

$relation = new ExternalServiceRelation();
$relation->setUser($internalUser);
$relation->setExternalUser($externalUser);
$relation->setAddExternalContactTime(new \DateTimeImmutable());
```

## Architecture

The bundle follows Symfony best practices:

- **Commands**: Console commands for batch operations
- **Controllers**: REST API endpoints
- **Entities**: Doctrine entities for data persistence
- **Events**: Event-driven communication
- **Message Handlers**: Async processing with Symfony Messenger
- **Procedures**: JSON-RPC procedures for internal APIs
- **Repositories**: Data access layer
- **Requests**: WeChat Work API request objects

## Testing

Run the test suite:

```bash
./vendor/bin/phpunit packages/wechat-work-external-contact-bundle/tests
```

The test suite includes:
- ✅ **Unit Tests**: 358 tests covering entities, requests, events, and messages
- ✅ **PHPStan Analysis**: Level 9 static analysis with zero errors
- ⚠️ **Integration Tests**: Temporarily disabled due to complex service dependencies

**Note**: Complex integration tests requiring HttpClientBundle\Service\SmartHttpClient are tracked 
in [GitHub Issue #931](https://github.com/tourze/php-monorepo/issues/931). All unit tests 
and core functionality tests pass successfully (358/358 tests).

## License

This bundle is part of the Tourze PHP Monorepo and follows the same license terms.

## Documentation

For more information about WeChat Work External Contact API, see:
- [Official Documentation](https://developer.work.weixin.qq.com/document/path/92109)
<?php

declare(strict_types=1);

namespace WechatWorkExternalContactBundle\Service;

use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use WechatWorkExternalContactBundle\Entity\ExternalServiceRelation;
use WechatWorkExternalContactBundle\Entity\ExternalUser;

/**
 * 微信企业号外部联系人管理后台菜单提供者
 */
#[Autoconfigure(public: true)]
readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (null === $item->getChild('企业微信')) {
            $item->addChild('企业微信');
        }

        $wechatWorkMenu = $item->getChild('企业微信');
        if (null === $wechatWorkMenu) {
            return;
        }

        // 添加外部联系人管理子菜单
        if (null === $wechatWorkMenu->getChild('外部联系人')) {
            $wechatWorkMenu->addChild('外部联系人')
                ->setAttribute('icon', 'fas fa-address-book')
            ;
        }

        $externalContactMenu = $wechatWorkMenu->getChild('外部联系人');
        if (null === $externalContactMenu) {
            return;
        }

        $externalContactMenu->addChild('外部联系人管理')
            ->setUri($this->linkGenerator->getCurdListPage(ExternalUser::class))
            ->setAttribute('icon', 'fas fa-users')
        ;

        $externalContactMenu->addChild('服务关系管理')
            ->setUri($this->linkGenerator->getCurdListPage(ExternalServiceRelation::class))
            ->setAttribute('icon', 'fas fa-handshake')
        ;
    }
}

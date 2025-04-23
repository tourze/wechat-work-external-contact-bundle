<?php

namespace WechatWorkExternalContactBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DoctrineEnhanceBundle\Traits\PrimaryKeyAware;
use DoctrineEnhanceBundle\Traits\TimestampableAware;
use Tourze\EasyAdmin\Attribute\Action\Exportable;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;
use WechatWorkExternalContactBundle\Repository\UserBehaviorDataByUserRepository;
use WechatWorkStaffBundle\Entity\User;

/**
 * @see https://developer.work.weixin.qq.com/document/path/92132
 */
#[AsPermission(title: '联系客户统计数据x单用户')]
#[Exportable]
#[ORM\Entity(repositoryClass: UserBehaviorDataByUserRepository::class)]
#[ORM\Table(name: 'wechat_work_behavior_data_by_user', options: ['comment' => '联系客户统计数据x单用户'])]
class UserBehaviorDataByUser
{
    use PrimaryKeyAware;
    use TimestampableAware;
    use BehaviorDataTrait;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}

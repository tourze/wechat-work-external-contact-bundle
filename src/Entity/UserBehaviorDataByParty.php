<?php

namespace WechatWorkExternalContactBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DoctrineEnhanceBundle\Traits\PrimaryKeyAware;
use DoctrineEnhanceBundle\Traits\TimestampableAware;
use Tourze\EasyAdmin\Attribute\Action\Exportable;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;
use WechatWorkExternalContactBundle\Repository\UserBehaviorDataByPartyRepository;
use WechatWorkStaffBundle\Entity\Department;

/**
 * @see https://developer.work.weixin.qq.com/document/path/92132
 */
#[AsPermission(title: '联系客户统计数据x单部门')]
#[Exportable]
#[ORM\Entity(repositoryClass: UserBehaviorDataByPartyRepository::class)]
#[ORM\Table(name: 'wechat_work_behavior_data_by_party', options: ['comment' => '联系客户统计数据x单部门'])]
class UserBehaviorDataByParty
{
    use PrimaryKeyAware;
    use TimestampableAware;
    use BehaviorDataTrait;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Department $party = null;

    public function getParty(): ?Department
    {
        return $this->party;
    }

    public function setParty(?Department $party): static
    {
        $this->party = $party;

        return $this;
    }
}

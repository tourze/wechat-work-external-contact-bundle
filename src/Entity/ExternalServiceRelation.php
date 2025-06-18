<?php

namespace WechatWorkExternalContactBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;
use Tourze\WechatWorkContracts\CorpInterface;
use Tourze\WechatWorkContracts\UserInterface;
use WechatWorkExternalContactBundle\Repository\ExternalServiceRelationRepository;

/**
 * @see https://developer.work.weixin.qq.com/document/path/92277
 */
#[AsPermission(title: '外部联系人服务关系')]
#[ORM\Entity(repositoryClass: ExternalServiceRelationRepository::class)]
#[ORM\Table(name: 'wechat_work_external_service_relation', options: ['comment' => '外部联系人服务关系'])]
#[ORM\UniqueConstraint(name: 'wechat_work_external_service_relation_idx_uniq', columns: ['user_id', 'external_user_id'])]
class ExternalServiceRelation
{
    use TimestampableAware;
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ListColumn(title: '所属企业')]
    #[ORM\ManyToOne(targetEntity: CorpInterface::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?CorpInterface $corp = null;

    #[ORM\ManyToOne]
    private ?UserInterface $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?ExternalUser $externalUser = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '成员添加外部联系人时间'])]
    private ?\DateTimeInterface $addExternalContactTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '外部联系人主动添加时间'])]
    private ?\DateTimeInterface $addHalfExternalContactTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '成员删除外部联系人时间'])]
    private ?\DateTimeInterface $delExternalContactTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '成员被外部联系人删除时间'])]
    private ?\DateTimeInterface $delFollowUserTime = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCorp(): ?CorpInterface
    {
        return $this->corp;
    }

    public function setCorp(?CorpInterface $corp): self
    {
        $this->corp = $corp;

        return $this;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getExternalUser(): ?ExternalUser
    {
        return $this->externalUser;
    }

    public function setExternalUser(?ExternalUser $externalUser): static
    {
        $this->externalUser = $externalUser;

        return $this;
    }

    public function getAddExternalContactTime(): ?\DateTimeInterface
    {
        return $this->addExternalContactTime;
    }

    public function setAddExternalContactTime(?\DateTimeInterface $addExternalContactTime): static
    {
        $this->addExternalContactTime = $addExternalContactTime;

        return $this;
    }

    public function getAddHalfExternalContactTime(): ?\DateTimeInterface
    {
        return $this->addHalfExternalContactTime;
    }

    public function setAddHalfExternalContactTime(?\DateTimeInterface $addHalfExternalContactTime): static
    {
        $this->addHalfExternalContactTime = $addHalfExternalContactTime;

        return $this;
    }

    public function getDelExternalContactTime(): ?\DateTimeInterface
    {
        return $this->delExternalContactTime;
    }

    public function setDelExternalContactTime(?\DateTimeInterface $delExternalContactTime): static
    {
        $this->delExternalContactTime = $delExternalContactTime;

        return $this;
    }

    public function getDelFollowUserTime(): ?\DateTimeInterface
    {
        return $this->delFollowUserTime;
    }

    public function setDelFollowUserTime(?\DateTimeInterface $delFollowUserTime): static
    {
        $this->delFollowUserTime = $delFollowUserTime;

        return $this;
    }}

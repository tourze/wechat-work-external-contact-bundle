<?php

namespace WechatWorkExternalContactBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\WechatWorkContracts\CorpInterface;
use Tourze\WechatWorkContracts\UserInterface;
use WechatWorkExternalContactBundle\Repository\ExternalServiceRelationRepository;

/**
 * @see https://developer.work.weixin.qq.com/document/path/92277
 */
#[ORM\Entity(repositoryClass: ExternalServiceRelationRepository::class)]
#[ORM\Table(name: 'wechat_work_external_service_relation', options: ['comment' => '外部联系人服务关系'])]
#[ORM\UniqueConstraint(name: 'wechat_work_external_service_relation_idx_uniq', columns: ['user_id', 'external_user_id'])]
class ExternalServiceRelation implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[ORM\ManyToOne(targetEntity: CorpInterface::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?CorpInterface $corp = null;

    #[ORM\ManyToOne]
    private ?UserInterface $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?ExternalUser $externalUser = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '成员添加外部联系人时间'])]
    #[Assert\Type(type: \DateTimeInterface::class)]
    private ?\DateTimeInterface $addExternalContactTime = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '外部联系人主动添加时间'])]
    #[Assert\Type(type: \DateTimeInterface::class)]
    private ?\DateTimeInterface $addHalfExternalContactTime = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '成员删除外部联系人时间'])]
    #[Assert\Type(type: \DateTimeInterface::class)]
    private ?\DateTimeInterface $delExternalContactTime = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '成员被外部联系人删除时间'])]
    #[Assert\Type(type: \DateTimeInterface::class)]
    private ?\DateTimeInterface $delFollowUserTime = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getCorp(): ?CorpInterface
    {
        return $this->corp;
    }

    public function setCorp(?CorpInterface $corp): void
    {
        $this->corp = $corp;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): void
    {
        $this->user = $user;
    }

    public function getExternalUser(): ?ExternalUser
    {
        return $this->externalUser;
    }

    public function setExternalUser(?ExternalUser $externalUser): void
    {
        $this->externalUser = $externalUser;
    }

    public function getAddExternalContactTime(): ?\DateTimeInterface
    {
        return $this->addExternalContactTime;
    }

    public function setAddExternalContactTime(?\DateTimeInterface $addExternalContactTime): void
    {
        $this->addExternalContactTime = $addExternalContactTime;
    }

    public function getAddHalfExternalContactTime(): ?\DateTimeInterface
    {
        return $this->addHalfExternalContactTime;
    }

    public function setAddHalfExternalContactTime(?\DateTimeInterface $addHalfExternalContactTime): void
    {
        $this->addHalfExternalContactTime = $addHalfExternalContactTime;
    }

    public function getDelExternalContactTime(): ?\DateTimeInterface
    {
        return $this->delExternalContactTime;
    }

    public function setDelExternalContactTime(?\DateTimeInterface $delExternalContactTime): void
    {
        $this->delExternalContactTime = $delExternalContactTime;
    }

    public function getDelFollowUserTime(): ?\DateTimeInterface
    {
        return $this->delFollowUserTime;
    }

    public function setDelFollowUserTime(?\DateTimeInterface $delFollowUserTime): void
    {
        $this->delFollowUserTime = $delFollowUserTime;
    }

    public function __toString(): string
    {
        return (string) $this->getId();
    }
}

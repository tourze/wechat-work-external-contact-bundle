<?php

namespace WechatWorkExternalContactBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\EasyAdmin\Attribute\Action\Listable;
use Tourze\EasyAdmin\Attribute\Column\PictureColumn;
use Tourze\WechatWorkContracts\CorpInterface;
use Tourze\WechatWorkExternalContactModel\ExternalContactInterface;
use WechatWorkExternalContactBundle\Repository\ExternalUserRepository;

/**
 * @see https://developer.work.weixin.qq.com/document/path/95149
 */
#[Listable]
#[ORM\Entity(repositoryClass: ExternalUserRepository::class)]
#[ORM\Table(name: 'wechat_work_external_user', options: ['comment' => '外部联系人'])]
class ExternalUser implements \Stringable, PlainArrayInterface, ApiArrayInterface, ExternalContactInterface
{
    use TimestampableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\ManyToOne(targetEntity: CorpInterface::class)]
    private ?CorpInterface $corp = null;

    #[Groups(['admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 120, nullable: true, options: ['comment' => '昵称'])]
    private ?string $nickname = null;

    #[Groups(['admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 120, unique: true, options: ['comment' => '外部UserID'])]
    private ?string $externalUserId = null;

    #[Groups(['admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 120, nullable: true, options: ['comment' => 'UnionID'])]
    private ?string $unionId = null;

    #[PictureColumn]
    #[Groups(['admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '头像'])]
    private ?string $avatar = null;

    #[Groups(['admin_curd'])]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '性别'])]
    private ?int $gender = null;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '会话上下文信息'])]
    private ?array $enterSessionContext = [];

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '备注'])]
    private ?string $remark = null;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '标签'])]
    private ?array $tags = [];

    #[ORM\Column(nullable: true, options: ['comment' => '是否被成员标记为客户'])]
    private ?bool $customer = null;

    #[ORM\Column(length: 120, nullable: true, options: ['comment' => '外部联系人临时ID'])]
    private ?string $tmpOpenId = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '首次添加/进群的时间'])]
    private ?\DateTimeInterface $addTime = null;

    #[ORM\Column(nullable: true)]
    private ?array $rawData = null;

    public function __toString(): string
    {
        if (!$this->getId()) {
            return '';
        }

        return "{$this->getNickname()}[{$this->getExternalUserId()}]";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getExternalUserId(): ?string
    {
        return $this->externalUserId;
    }

    public function setExternalUserId(string $externalUserId): self
    {
        $this->externalUserId = $externalUserId;

        return $this;
    }

    public function getUnionId(): ?string
    {
        return $this->unionId;
    }

    public function setUnionId(?string $unionId): self
    {
        $this->unionId = $unionId;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getGender(): ?int
    {
        return $this->gender;
    }

    public function setGender(?int $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getEnterSessionContext(): ?array
    {
        return $this->enterSessionContext;
    }

    public function setEnterSessionContext(?array $enterSessionContext): self
    {
        $this->enterSessionContext = $enterSessionContext;

        return $this;
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

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): self
    {
        $this->remark = $remark;

        return $this;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(?array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function isCustomer(): ?bool
    {
        return $this->customer;
    }

    public function setCustomer(?bool $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    public function getTmpOpenId(): ?string
    {
        return $this->tmpOpenId;
    }

    public function setTmpOpenId(?string $tmpOpenId): static
    {
        $this->tmpOpenId = $tmpOpenId;

        return $this;
    }

    public function getAddTime(): ?\DateTimeInterface
    {
        return $this->addTime;
    }

    public function setAddTime(?\DateTimeInterface $addTime): static
    {
        $this->addTime = $addTime;

        return $this;
    }

    public function getRawData(): ?array
    {
        return $this->rawData;
    }

    public function setRawData(?array $rawData): static
    {
        $this->rawData = $rawData;

        return $this;
    }public function retrievePlainArray(): array
    {
        return [
            'id' => $this->getId(),
            'externalUserId' => $this->getExternalUserId(),
        ];
    }

    public function retrieveApiArray(): array
    {
        return [
            'id' => $this->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'nickname' => $this->getNickname(),
            'externalUserId' => $this->getExternalUserId(),
            'unionId' => $this->getUnionId(),
            'avatar' => $this->getAvatar(),
            'gender' => $this->getGender(),
        ];
    }
}

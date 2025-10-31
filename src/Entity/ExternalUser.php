<?php

namespace WechatWorkExternalContactBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\WechatWorkContracts\CorpInterface;
use Tourze\WechatWorkExternalContactModel\ExternalContactInterface;
use WechatWorkExternalContactBundle\Repository\ExternalUserRepository;

/**
 * @see https://developer.work.weixin.qq.com/document/path/95149
 * @implements PlainArrayInterface<string, mixed>
 * @implements ApiArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: ExternalUserRepository::class)]
#[ORM\Table(name: 'wechat_work_external_user', options: ['comment' => '外部联系人'])]
class ExternalUser implements \Stringable, PlainArrayInterface, ApiArrayInterface, ExternalContactInterface
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[ORM\ManyToOne(targetEntity: CorpInterface::class)]
    private ?CorpInterface $corp = null;

    #[Groups(groups: ['admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 120, nullable: true, options: ['comment' => '昵称'])]
    #[Assert\Length(max: 120, maxMessage: '昵称不能超过 {{ limit }} 个字符')]
    private ?string $nickname = null;

    #[Groups(groups: ['admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 120, unique: true, options: ['comment' => '外部UserID'])]
    #[Assert\NotBlank(message: '外部UserID不能为空')]
    #[Assert\Length(max: 120, maxMessage: '外部UserID不能超过 {{ limit }} 个字符')]
    private ?string $externalUserId = null;

    #[Groups(groups: ['admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 120, nullable: true, options: ['comment' => 'UnionID'])]
    #[Assert\Length(max: 120, maxMessage: 'UnionID不能超过 {{ limit }} 个字符')]
    private ?string $unionId = null;

    #[Groups(groups: ['admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '头像'])]
    #[Assert\Length(max: 255, maxMessage: '头像URL不能超过 {{ limit }} 个字符')]
    #[Assert\Url(message: '头像必须是有效的URL')]
    private ?string $avatar = null;

    #[Groups(groups: ['admin_curd'])]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '性别'])]
    #[Assert\Choice(choices: [0, 1, 2], message: '性别值无效，只能是 0(未知)、1(男)、2(女)')]
    private ?int $gender = null;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '会话上下文信息'])]
    #[Assert\Type(type: 'array', message: '会话上下文信息必须是数组类型')]
    private ?array $enterSessionContext = [];

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '备注'])]
    #[Assert\Length(max: 65535, maxMessage: '备注不能超过 {{ limit }} 个字符')]
    private ?string $remark = null;

    /**
     * @var array<mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '标签'])]
    #[Assert\Type(type: 'array', message: '标签必须是数组类型')]
    private ?array $tags = [];

    #[ORM\Column(nullable: true, options: ['comment' => '是否被成员标记为客户'])]
    #[Assert\Type(type: 'bool', message: '客户标记必须是布尔值类型')]
    private ?bool $customer = null;

    #[ORM\Column(length: 120, nullable: true, options: ['comment' => '外部联系人临时ID'])]
    #[Assert\Length(max: 120, maxMessage: '临时OpenID不能超过 {{ limit }} 个字符')]
    private ?string $tmpOpenId = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '首次添加/进群的时间'])]
    #[Assert\Type(type: \DateTimeInterface::class, message: '添加时间必须是有效的日期时间')]
    private ?\DateTimeInterface $addTime = null;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(nullable: true, options: ['comment' => '原始数据'])]
    #[Assert\Type(type: 'array', message: '原始数据必须是数组类型')]
    private ?array $rawData = null;

    public function __toString(): string
    {
        if (0 === $this->getId()) {
            return '';
        }

        return "{$this->getNickname()}[{$this->getExternalUserId()}]";
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): void
    {
        $this->nickname = $nickname;
    }

    public function getExternalUserId(): ?string
    {
        return $this->externalUserId;
    }

    public function setExternalUserId(?string $externalUserId): void
    {
        $this->externalUserId = $externalUserId;
    }

    public function getUnionId(): ?string
    {
        return $this->unionId;
    }

    public function setUnionId(?string $unionId): void
    {
        $this->unionId = $unionId;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    public function getGender(): ?int
    {
        return $this->gender;
    }

    public function setGender(?int $gender): void
    {
        $this->gender = $gender;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getEnterSessionContext(): ?array
    {
        return $this->enterSessionContext;
    }

    /**
     * @param array<string, mixed>|null $enterSessionContext
     */
    public function setEnterSessionContext(?array $enterSessionContext): void
    {
        $this->enterSessionContext = $enterSessionContext;
    }

    public function getCorp(): ?CorpInterface
    {
        return $this->corp;
    }

    public function setCorp(?CorpInterface $corp): void
    {
        $this->corp = $corp;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): void
    {
        $this->remark = $remark;
    }

    /**
     * @return array<mixed>
     */
    public function getTags(): array
    {
        return $this->tags ?? [];
    }

    /**
     * @param array<mixed>|null $tags
     */
    public function setTags(?array $tags): void
    {
        $this->tags = $tags;
    }

    public function isCustomer(): ?bool
    {
        return $this->customer;
    }

    public function setCustomer(?bool $customer): void
    {
        $this->customer = $customer;
    }

    public function getTmpOpenId(): ?string
    {
        return $this->tmpOpenId;
    }

    public function setTmpOpenId(?string $tmpOpenId): void
    {
        $this->tmpOpenId = $tmpOpenId;
    }

    public function getAddTime(): ?\DateTimeInterface
    {
        return $this->addTime;
    }

    public function setAddTime(?\DateTimeInterface $addTime): void
    {
        $this->addTime = $addTime;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getRawData(): ?array
    {
        return $this->rawData;
    }

    /**
     * @param array<string, mixed>|null $rawData
     */
    public function setRawData(?array $rawData): void
    {
        $this->rawData = $rawData;
    }

    /**
     * @return array<string, mixed>
     */
    public function retrievePlainArray(): array
    {
        return [
            'id' => $this->getId(),
            'externalUserId' => $this->getExternalUserId(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
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

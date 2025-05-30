<?php

namespace WechatWorkExternalContactBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\EasyAdmin\Attribute\Action\Creatable;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Action\Editable;
use Tourze\EasyAdmin\Attribute\Action\Listable;
use Tourze\EasyAdmin\Attribute\Column\BoolColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Field\SelectField;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;
use Tourze\WechatWorkStaffModel\UserInterface;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;
use WechatWorkExternalContactBundle\Enum\InterceptType;
use WechatWorkExternalContactBundle\Repository\InterceptRuleRepository;
use WechatWorkJssdkBundle\Semantics\SemanticsList;
use WechatWorkStaffBundle\Entity\Department;

/**
 * 敏感词规则
 *
 * @see https://developer.work.weixin.qq.com/document/path/96346
 */
#[AsPermission(title: '敏感词管理')]
#[Deletable]
#[Editable]
#[Creatable]
#[Listable]
#[ORM\Entity(repositoryClass: InterceptRuleRepository::class)]
#[ORM\Table(name: 'wechat_work_intercept_rule', options: ['comment' => '敏感词管理'])]
class InterceptRule
{
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ListColumn(title: '企业')]
    #[FormField(title: '企业')]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Corp $corp = null;

    #[ListColumn(title: '应用')]
    #[FormField(title: '应用')]
    #[ORM\ManyToOne(targetEntity: Agent::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Agent $agent = null;

    #[ORM\Column(length: 60, nullable: true, options: ['comment' => '规则ID'])]
    private ?string $ruleId = null;

    #[Filterable]
    #[Groups(['admin_curd'])]
    #[ListColumn]
    #[FormField]
    #[ORM\Column(type: Types::STRING, length: 20, options: ['comment' => '规则名称'])]
    private ?string $name = null;

    #[Filterable]
    #[Groups(['admin_curd'])]
    #[ListColumn]
    #[FormField]
    #[ORM\Column(type: Types::JSON, options: ['comment' => '敏感词列表'])]
    private array $wordList = [];

    #[Groups(['admin_curd'])]
    #[ListColumn]
    #[FormField]
    #[SelectField(targetEntity: SemanticsList::class, mode: 'multiple')]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '额外的拦截语义规则'])]
    private ?array $semanticsList = [];

    #[Groups(['admin_curd'])]
    #[ListColumn]
    #[FormField]
    #[ORM\Column(length: 10, enumType: InterceptType::class, options: ['comment' => '拦截方式'])]
    private ?InterceptType $interceptType = null;

    #[Groups(['admin_curd'])]
    #[ListColumn]
    #[FormField]
    #[SelectField(targetEntity: UserInterface::class, mode: 'multiple', idColumn: 'user_id')]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '可使用的userid列表'])]
    private array $applicableUserList = [];

    #[Groups(['admin_curd'])]
    #[ListColumn]
    #[FormField]
    #[SelectField(targetEntity: Department::class, mode: 'multiple', idColumn: 'id')]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '可使用的部门列表'])]
    private array $applicableDepartmentList = [];

    #[BoolColumn]
    #[IndexColumn]
    #[TrackColumn]
    #[Groups(['admin_curd'])]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '已同步', 'default' => 0])]
    #[ListColumn(order: 97)]
    private ?bool $sync = null;

    #[CreatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;

    #[UpdatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;

    #[Filterable]
    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[Groups(['admin_curd'])]
    #[ExportColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCorp(): ?Corp
    {
        return $this->corp;
    }

    public function setCorp(?Corp $corp): self
    {
        $this->corp = $corp;

        return $this;
    }

    public function getAgent(): ?Agent
    {
        return $this->agent;
    }

    public function setAgent(?Agent $agent): self
    {
        $this->agent = $agent;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getWordList(): array
    {
        return $this->wordList;
    }

    public function setWordList(array $wordList): self
    {
        $this->wordList = $wordList;

        return $this;
    }

    public function getSemanticsList(): ?array
    {
        return $this->semanticsList;
    }

    public function setSemanticsList(?array $semanticsList): self
    {
        if (!empty($semanticsList)) {
            sort($semanticsList);
        }

        $this->semanticsList = $semanticsList;

        return $this;
    }

    public function getInterceptType(): ?InterceptType
    {
        return $this->interceptType;
    }

    public function setInterceptType(InterceptType $interceptType): self
    {
        $this->interceptType = $interceptType;

        return $this;
    }

    public function getApplicableUserList(): array
    {
        return $this->applicableUserList;
    }

    public function setApplicableUserList(?array $applicableUserList): self
    {
        $this->applicableUserList = $applicableUserList;

        return $this;
    }

    public function getApplicableDepartmentList(): array
    {
        return $this->applicableDepartmentList;
    }

    public function setApplicableDepartmentList(?array $applicableDepartmentList): self
    {
        $this->applicableDepartmentList = $applicableDepartmentList;

        return $this;
    }

    public function getRuleId(): ?string
    {
        return $this->ruleId;
    }

    public function setRuleId(?string $ruleId): self
    {
        $this->ruleId = $ruleId;

        return $this;
    }

    public function isSync(): ?bool
    {
        return $this->sync;
    }

    public function setSync(?bool $sync): self
    {
        $this->sync = $sync;

        return $this;
    }

    public function setCreatedBy(?string $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setUpdatedBy(?string $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }

    public function setCreateTime(?\DateTimeInterface $createdAt): void
    {
        $this->createTime = $createdAt;
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function setUpdateTime(?\DateTimeInterface $updateTime): void
    {
        $this->updateTime = $updateTime;
    }

    public function getUpdateTime(): ?\DateTimeInterface
    {
        return $this->updateTime;
    }
}

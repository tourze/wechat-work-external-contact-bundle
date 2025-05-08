<?php

namespace WechatWorkExternalContactBundle\Entity;

use Carbon\Carbon;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\EasyAdmin\Attribute\Action\Creatable;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Action\Editable;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Event\AfterCreate;
use Tourze\EasyAdmin\Attribute\Event\AfterEdit;
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;
use WechatWorkBundle\Service\WorkService;
use WechatWorkExternalContactBundle\Repository\CorpTagGroupRepository;
use WechatWorkExternalContactBundle\Repository\CorpTagItemRepository;
use WechatWorkExternalContactBundle\Request\Tag\AddCorpTagRequest;
use WechatWorkExternalContactBundle\Request\Tag\EditCorpTagRequest;

/**
 * @see https://developer.work.weixin.qq.com/document/path/92117
 */
#[AsPermission(title: '企业标签项目')]
#[Deletable]
#[Editable]
#[Creatable]
#[ORM\Entity(repositoryClass: CorpTagItemRepository::class)]
#[ORM\Table(name: 'wechat_work_corp_tag_item', options: ['comment' => '企业标签项目'])]
#[ORM\UniqueConstraint(name: 'wechat_work_corp_tag_item_uniq_idx', columns: ['tag_group_id', 'name'])]
class CorpTagItem implements \Stringable
{
    #[ExportColumn]
    #[ListColumn(order: -1, sorter: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Corp::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Corp $corp = null;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: CorpTagGroup::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CorpTagGroup $tagGroup = null;

    #[FormField]
    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 120, options: ['comment' => '标签名'])]
    private string $name;

    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '远程ID'])]
    private ?string $remoteId = null;

    #[FormField]
    #[ListColumn]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '排序', 'default' => 0])]
    private ?int $sortNumber = null;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Agent::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Agent $agent = null;

    #[CreateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '创建时IP'])]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '更新时IP'])]
    private ?string $updatedFromIp = null;

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
    #[ListColumn(order: 99, sorter: true)]
    #[Filterable]
    #[ExportColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    public function __toString(): string
    {
        if (!$this->getId()) {
            return '';
        }

        return "{$this->getTagGroup()->getName()}-{$this->getName()}";
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getRemoteId(): ?string
    {
        return $this->remoteId;
    }

    public function setRemoteId(?string $remoteId): self
    {
        $this->remoteId = $remoteId;

        return $this;
    }

    public function getTagGroup(): ?CorpTagGroup
    {
        return $this->tagGroup;
    }

    public function setTagGroup(?CorpTagGroup $tagGroup): self
    {
        $this->tagGroup = $tagGroup;

        return $this;
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

    public function getSortNumber(): ?int
    {
        return $this->sortNumber;
    }

    public function setSortNumber(int $sortNumber): self
    {
        $this->sortNumber = $sortNumber;

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

    public function setCreatedFromIp(?string $createdFromIp): self
    {
        $this->createdFromIp = $createdFromIp;

        return $this;
    }

    public function getCreatedFromIp(): ?string
    {
        return $this->createdFromIp;
    }

    public function setUpdatedFromIp(?string $updatedFromIp): self
    {
        $this->updatedFromIp = $updatedFromIp;

        return $this;
    }

    public function getUpdatedFromIp(): ?string
    {
        return $this->updatedFromIp;
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

    /**
     * 编辑后，同步到远程.
     */
    #[AfterCreate]
    #[AfterEdit]
    public function syncToRemote(
        CorpTagGroupRepository $tagGroupRepository,
        CorpTagItemRepository $itemRepository,
        WorkService $mediaService,
        LoggerInterface $logger,
        EntityManagerInterface $entityManager,
    ): void {
        if ($this->getRemoteId()) {
            // 更新
            $request = new EditCorpTagRequest();
            $request->setAgent($this->getAgent());
            $request->setId($this->getRemoteId());
            $request->setName($this->getName());
            $request->setOrder($this->getSortNumber());
            $res = $mediaService->request($request);
            $logger->debug('更新企业标签', [
                'item' => $this,
                'res' => $res,
            ]);
        } else {
            $request = new AddCorpTagRequest();
            $request->setAgent($this->getAgent());
            // 创建
            // 有可能分组还没创建的喔
            if (!$this->getTagGroup()->getRemoteId()) {
                $request->setGroupId($this->getTagGroup()->getRemoteId());
            }
            $request->setGroupName($this->getTagGroup()->getName());
            $request->setOrder($this->getTagGroup()->getSortNumber());
            $request->setTagList([
                [
                    'name' => $this->getName(),
                    'order' => $this->getSortNumber(),
                ],
            ]);
            $res = $mediaService->request($request);
            $logger->debug('新增企业标签', [
                'item' => $this,
                'res' => $res,
            ]);
            if (isset($res['tag_group'])) {
                // 补充分组信息
                if (!$this->getTagGroup()->getRemoteId()) {
                    $this->getTagGroup()->setRemoteId($res['tag_group']['group_id']);
                    $entityManager->persist($this->getTagGroup());
                    $entityManager->flush();
                }

                foreach ($res['tag_group']['tag'] as $tag) {
                    if ($tag['name'] === $this->getName()) {
                        $this->setRemoteId($tag['id']);
                        $this->setSortNumber($tag['order']);
                        $this->setCreateTime(Carbon::createFromTimestamp($tag['create_time'], date_default_timezone_get()));
                        $entityManager->persist($this);
                        $entityManager->flush();
                    }
                }
            }
        }
    }
}

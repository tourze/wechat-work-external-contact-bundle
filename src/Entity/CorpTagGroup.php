<?php

namespace WechatWorkExternalContactBundle\Entity;

use AntdCpBundle\Builder\Action\ModalFormAction;
use AntdCpBundle\Service\FormFieldBuilder;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use DoctrineEnhanceBundle\Traits\PrimaryKeyAware;
use DoctrineEnhanceBundle\Traits\TimestampableAware;
use Symfony\Component\Serializer\Attribute\Groups;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\EasyAdmin\Attribute\Action\Creatable;
use Tourze\EasyAdmin\Attribute\Action\CurdAction;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Action\Editable;
use Tourze\EasyAdmin\Attribute\Action\HeaderAction;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Service\WorkService;
use WechatWorkExternalContactBundle\Repository\CorpTagGroupRepository;
use WechatWorkExternalContactBundle\Repository\CorpTagItemRepository;
use WechatWorkExternalContactBundle\Request\Tag\GetCorpTagListRequest;

/**
 * @see https://developer.work.weixin.qq.com/document/path/92117
 */
#[AsPermission(title: '企业标签分组')]
#[Deletable]
#[Editable]
#[Creatable]
#[ORM\Entity(repositoryClass: CorpTagGroupRepository::class)]
#[ORM\Table(name: 'wechat_work_corp_tag_group', options: ['comment' => '企业标签分组'])]
class CorpTagGroup
{
    use PrimaryKeyAware;
    use TimestampableAware;

    #[CreatedByColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;

    #[UpdatedByColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;

    #[ListColumn(title: '所属企业')]
    #[ORM\ManyToOne(targetEntity: Corp::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Corp $corp = null;

    #[ListColumn(title: '同步应用')]
    #[ORM\ManyToOne(targetEntity: Agent::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Agent $agent = null;

    #[FormField]
    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 120, options: ['comment' => '分组名'])]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '远程ID'])]
    private ?string $remoteId = null;

    /**
     * @var Collection<CorpTagItem>
     */
    #[ListColumn(title: '标签数据')]
    #[CurdAction(label: '标签管理')]
    #[ORM\OneToMany(mappedBy: 'tagGroup', targetEntity: CorpTagItem::class)]
    private Collection $items;

    #[FormField]
    #[ListColumn]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '排序', 'default' => 0])]
    private ?int $sortNumber = null;

    #[CreateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '创建时IP'])]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '更新时IP'])]
    private ?string $updatedFromIp = null;

    public function __construct()
    {
        $this->items = new ArrayCollection();
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    #[HeaderAction(title: '从企业微信服务器同步', featureKey: 'WECHAT_WORK_CORP_TAG_GROUP_SYNC_FROM_AGENT')]
    public function renderSyncFromAgentButton(FormFieldBuilder $fieldHelper): ModalFormAction
    {
        return ModalFormAction::gen()
            ->setFormTitle('从企业微信服务器同步')
            ->setLabel('从企业微信服务器同步')
            ->setFormWidth(600)
            ->setFormFields([
                $fieldHelper->createSelectFromEntityClass(Agent::class)
                    ->setSpan(12)
                    ->setId('from_agent')
                    ->setLabel('同步应用'),
            ])
            ->setCallback(function (
                array $form,
                array $record,
                CorpTagGroupRepository $groupRepository,
                CorpTagItemRepository $itemRepository,
                AgentRepository $agentRepository,
                WorkService $workService,
                EntityManagerInterface $entityManager,
            ) {
                $agent = $agentRepository->find($form['from_agent']);
                $request = new GetCorpTagListRequest();
                $request->setAgent($agent);
                $remoteTags = $workService->request($request)['tag_group'];

                // 放到一个事务内
                $entityManager->wrapInTransaction(function () use ($remoteTags, $agent, $groupRepository, $itemRepository, $entityManager) {
                    foreach ($remoteTags as $groupInfo) {
                        // 保存分组
                        $group = $groupRepository->findOneBy([
                            'corp' => $agent->getCorp(),
                            'remoteId' => $groupInfo['group_id'],
                        ]);
                        if (!$group) {
                            $group = new CorpTagGroup();
                            $group->setCorp($agent->getCorp());
                            $group->setRemoteId($groupInfo['group_id']);
                        }

                        $group->setAgent($agent);
                        $group->setName($groupInfo['group_name']);
                        $group->setCreateTime(Carbon::createFromTimestamp($groupInfo['create_time'], date_default_timezone_get()));
                        $entityManager->persist($group);
                        $entityManager->flush();

                        // 将现在数据库里带有远程ID的标签删除
                        foreach ($group->getItems() as $item) {
                            if ($item->getRemoteId()) {
                                $entityManager->remove($item);
                            }
                        }
                        $entityManager->flush();

                        if (isset($groupInfo['tag']) && is_array($groupInfo['tag'])) {
                            foreach ($groupInfo['tag'] as $tagInfo) {
                                $tag = $itemRepository->findOneBy([
                                    'corp' => $agent->getCorp(),
                                    'remoteId' => $tagInfo['id'],
                                ]);
                                if (!$tag) {
                                    $tag = new CorpTagItem();
                                    $tag->setCorp($agent->getCorp());
                                    $tag->setRemoteId($tagInfo['id']);
                                }

                                $tag->setAgent($agent);
                                $tag->setTagGroup($group);
                                $tag->setName($tagInfo['name']);
                                $tag->setCreateTime(Carbon::createFromTimestamp($tagInfo['create_time'], date_default_timezone_get()));
                                $entityManager->persist($tag);
                                $entityManager->flush();
                            }
                        }
                    }
                });

                return [
                    '__message' => '同步成功',
                    'form' => $form,
                    'record' => $record,
                    // 'list' => $list,
                ];
            });
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

    /**
     * @return Collection<int, CorpTagItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(CorpTagItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setTagGroup($this);
        }

        return $this;
    }

    public function removeItem(CorpTagItem $item): self
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getTagGroup() === $this) {
                $item->setTagGroup(null);
            }
        }

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

    public function getAgent(): ?Agent
    {
        return $this->agent;
    }

    public function setAgent(?Agent $agent): self
    {
        $this->agent = $agent;

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
}

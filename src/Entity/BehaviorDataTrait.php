<?php

namespace WechatWorkExternalContactBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @see https://developer.work.weixin.qq.com/document/path/92132
 */
trait BehaviorDataTrait
{
    #[ORM\Column(type: Types::DATE_MUTABLE, options: ['comment' => '日期'])]
    private ?\DateTimeInterface $date = null;

    /**
     * @var int|null 成员通过「搜索手机号」、「扫一扫」、「从微信好友中添加」、「从群聊中添加」、「添加共享、分配给我的客户」、「添加单向、双向删除好友关系的好友」、「从新的联系人推荐中添加」等渠道主动向客户发起的好友申请数量
     */
    #[ORM\Column(nullable: true, options: ['comment' => '发起申请数'])]
    private ?int $newApplyCount = null;

    /**
     * @var int|null 成员新添加的客户数量
     */
    #[ORM\Column(nullable: true, options: ['comment' => '新增客户数'])]
    private ?int $newContactCount = null;

    /**
     * @var int|null 成员有主动发送过消息的单聊总数
     */
    #[ORM\Column(nullable: true, options: ['comment' => '聊天总数'])]
    private ?int $chatCount = null;

    /**
     * @var int|null 成员在单聊中发送的消息总数
     */
    #[ORM\Column(nullable: true, options: ['comment' => '发送消息数'])]
    private ?int $messageCount = null;

    /**
     * @var float|null 客户主动发起聊天后，成员在一个自然日内有回复过消息的聊天数/客户主动发起的聊天数比例，不包括群聊，仅在确有聊天时返回
     */
    #[ORM\Column(nullable: true, options: ['comment' => '已回复聊天占比'])]
    private ?float $replyPercentage = null;

    /**
     * @var int|null 单位为分钟，即客户主动发起聊天后，成员在一个自然日内首次回复的时长间隔为首次回复时长，所有聊天的首次回复总时长/已回复的聊天总数即为平均首次回复时长，不包括群聊，仅在确有聊天时返回
     */
    #[ORM\Column(nullable: true, options: ['comment' => '平均首次回复时长'])]
    private ?int $avgReplyTime = null;

    /**
     * @var int|null 删除/拉黑成员的客户数，即将成员删除或加入黑名单的客户数
     */
    #[ORM\Column(nullable: true, options: ['comment' => '删除/拉黑成员的客户数'])]
    private ?int $negativeFeedbackCount = null;

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getNewApplyCount(): ?int
    {
        return $this->newApplyCount;
    }

    public function setNewApplyCount(?int $newApplyCount): static
    {
        $this->newApplyCount = $newApplyCount;

        return $this;
    }

    public function getNewContactCount(): ?int
    {
        return $this->newContactCount;
    }

    public function setNewContactCount(?int $newContactCount): static
    {
        $this->newContactCount = $newContactCount;

        return $this;
    }

    public function getChatCount(): ?int
    {
        return $this->chatCount;
    }

    public function setChatCount(?int $chatCount): static
    {
        $this->chatCount = $chatCount;

        return $this;
    }

    public function getMessageCount(): ?int
    {
        return $this->messageCount;
    }

    public function setMessageCount(?int $messageCount): static
    {
        $this->messageCount = $messageCount;

        return $this;
    }

    public function getReplyPercentage(): ?float
    {
        return $this->replyPercentage;
    }

    public function setReplyPercentage(?float $replyPercentage): static
    {
        $this->replyPercentage = $replyPercentage;

        return $this;
    }

    public function getAvgReplyTime(): ?int
    {
        return $this->avgReplyTime;
    }

    public function setAvgReplyTime(?int $avgReplyTime): static
    {
        $this->avgReplyTime = $avgReplyTime;

        return $this;
    }

    public function getNegativeFeedbackCount(): ?int
    {
        return $this->negativeFeedbackCount;
    }

    public function setNegativeFeedbackCount(?int $negativeFeedbackCount): static
    {
        $this->negativeFeedbackCount = $negativeFeedbackCount;

        return $this;
    }
}

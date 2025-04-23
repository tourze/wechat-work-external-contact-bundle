<?php

namespace WechatWorkExternalContactBundle\Request\Tag;

use HttpClientBundle\Request\ApiRequest;
use Tourze\JsonRPC\Core\Exception\ApiException;
use WechatWorkBundle\Request\AgentAware;

/**
 * 添加企业客户标签
 * 企业可通过此接口向客户标签库中添加新的标签组和标签，每个企业最多可配置10000个企业标签。
 *
 * @see https://developer.work.weixin.qq.com/document/path/92117
 */
class AddCorpTagRequest extends ApiRequest
{
    use AgentAware;

    /**
     * @var string|null 标签组id
     */
    private ?string $groupId = null;

    /**
     * @var string|null 标签组名称，最长为30个字符
     */
    private ?string $groupName = null;

    /**
     * @var array 添加的标签
     */
    private array $tagList;

    /**
     * @var int|null 标签/标签组的次序值。order值大的排序靠前。有效的值范围是[0, 2^32)
     */
    private ?int $order = null;

    /**
     * @var int|null 授权方安装的应用agentid。仅旧的第三方多应用套件需要填此参数
     */
    private ?int $agentId = null;

    public function getRequestPath(): string
    {
        return '/cgi-bin/externalcontact/add_corp_tag';
    }

    public function getRequestOptions(): ?array
    {
        $json = [];

        $tags = [];
        foreach ($this->getTagList() as $tagItem) {
            if (!is_array($tagItem)) {
                throw new ApiException('标签格式错误');
            }
            if (empty($tagItem['name'])) {
                throw new ApiException('缺少标签名');
            }
            if (mb_strlen($tagItem['name']) > 30) {
                throw new ApiException('标签名不得超过30个字符');
            }
            $tags[] = $tagItem;
        }
        $json['tag'] = $tags;
        if (null !== $this->getGroupId()) {
            $json['group_id'] = $this->getGroupId();
        }
        if (null !== $this->getGroupName()) {
            if (mb_strlen($this->getGroupName()) > 30) {
                throw new ApiException('标签组名称不得超过30个字符');
            }
            $json['group_name'] = $this->getGroupName();
        }
        if (null !== $this->getOrder()) {
            $json['order'] = $this->getOrder();
        }
        if (null !== $this->getAgentId()) {
            $json['agentid'] = $this->getAgentId();
        }

        return [
            'json' => $json,
        ];
    }

    public function getRequestMethod(): ?string
    {
        return 'POST';
    }

    public function getGroupId(): ?string
    {
        return $this->groupId;
    }

    public function setGroupId(?string $groupId): void
    {
        $this->groupId = $groupId;
    }

    public function getGroupName(): ?string
    {
        return $this->groupName;
    }

    public function setGroupName(?string $groupName): void
    {
        $this->groupName = $groupName;
    }

    public function getTagList(): array
    {
        return $this->tagList;
    }

    public function setTagList(array $tagList): void
    {
        $this->tagList = $tagList;
    }

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function setOrder(?int $order): void
    {
        $this->order = $order;
    }

    public function getAgentId(): ?int
    {
        return $this->agentId;
    }

    public function setAgentId(?int $agentId): void
    {
        $this->agentId = $agentId;
    }
}

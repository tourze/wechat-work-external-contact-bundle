<?php

namespace WechatWorkExternalContactBundle\Request\Tag;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 编辑企业客户标签
 * 企业可通过此接口编辑客户标签/标签组的名称或次序值。
 *
 * 修改后的标签组不能和已有的标签组重名，标签也不能和同一标签组下的其他标签重名。
 *
 * @see https://developer.work.weixin.qq.com/document/path/92117
 */
class EditCorpTagRequest extends ApiRequest
{
    use AgentAware;

    /**
     * @var string 标签或标签组的id
     */
    private string $id;

    /**
     * @var string|null 新的标签或标签组名称，最长为30个字符
     */
    private ?string $name = null;

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
        return '/cgi-bin/externalcontact/edit_corp_tag';
    }

    public function getRequestOptions(): ?array
    {
        $json = [
            'id' => $this->getId(),
        ];
        if (null !== $this->getName()) {
            $json['name'] = $this->getName();
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

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
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

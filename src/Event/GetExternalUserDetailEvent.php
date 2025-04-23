<?php

namespace WechatWorkExternalContactBundle\Event;

use AppBundle\Event\HaveUserAware;
use Symfony\Contracts\EventDispatcher\Event;
use WechatWorkExternalContactBundle\Entity\ExternalUser;

/**
 * 查找外部用户时触发这个
 *
 * 因为在保存外部用户时，我们有特地转化外部用户为系统用户，所以这里也会有一个 Biz\User 的对象
 */
class GetExternalUserDetailEvent extends Event
{
    private array $result = [];

    public function getResult(): array
    {
        return $this->result;
    }

    public function setResult(array $result): void
    {
        $this->result = $result;
    }

    use HaveUserAware;

    private ExternalUser $externalUser;

    public function getExternalUser(): ExternalUser
    {
        return $this->externalUser;
    }

    public function setExternalUser(ExternalUser $externalUser): void
    {
        $this->externalUser = $externalUser;
    }
}

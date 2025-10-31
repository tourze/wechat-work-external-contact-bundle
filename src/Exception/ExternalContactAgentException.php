<?php

namespace WechatWorkExternalContactBundle\Exception;

class ExternalContactAgentException extends \RuntimeException
{
    public static function invalidAgentInstance(): self
    {
        return new self('Agent must be an instance of Agent entity to get database ID');
    }

    public static function agentNotFound(): self
    {
        return new self('Agent cannot be null when creating user');
    }

    public static function agentNotFoundForDetail(): self
    {
        return new self('Agent cannot be null when fetching external user detail');
    }
}

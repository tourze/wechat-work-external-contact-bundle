<?php

namespace WechatWorkExternalContactBundle\Exception;

class ExternalContactUserException extends \RuntimeException
{
    public static function invalidUserInterface(): self
    {
        return new self('User must implement UserInterface');
    }

    public static function externalUserIdNotFound(): self
    {
        return new self('External user ID cannot be null');
    }
}

<?php

namespace WechatWorkExternalContactBundle\Exception;

class ExternalContactCorpException extends \RuntimeException
{
    public static function corpNotFound(): self
    {
        return new self('Corp cannot be null when creating user');
    }
}

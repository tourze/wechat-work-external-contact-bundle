<?php

namespace WechatWorkExternalContactBundle\Service;

use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\TextManageBundle\Service\TextFormatter;

#[AsAlias(id: TextFormatter::class)]
#[Autoconfigure(public: true)]
class DefaultTextFormatter implements TextFormatter
{
    /**
     * @param array<string, mixed> $params
     */
    public function formatText(string $text, array $params = []): string
    {
        return $text;
    }
}

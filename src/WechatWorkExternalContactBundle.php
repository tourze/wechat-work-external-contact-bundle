<?php

namespace WechatWorkExternalContactBundle;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\FileStorageBundle\FileStorageBundle;
use Tourze\JsonRPCLockBundle\JsonRPCLockBundle;
use Tourze\RoutingAutoLoaderBundle\RoutingAutoLoaderBundle;
use WechatWorkBundle\WechatWorkBundle;
use WechatWorkStaffBundle\WechatWorkStaffBundle;
use Tourze\EasyAdminMenuBundle\EasyAdminMenuBundle;

class WechatWorkExternalContactBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            DoctrineBundle::class => ['all' => true],
            WechatWorkBundle::class => ['all' => true],
            JsonRPCLockBundle::class => ['all' => true],
            RoutingAutoLoaderBundle::class => ['all' => true],
            WechatWorkStaffBundle::class => ['all' => true],
            FileStorageBundle::class => ['all' => true],
            EasyAdminMenuBundle::class => ['all' => true],
        ];
    }
}

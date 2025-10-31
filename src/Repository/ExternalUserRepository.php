<?php

namespace WechatWorkExternalContactBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use Tourze\WechatWorkContracts\CorpInterface;
use Tourze\WechatWorkExternalContactModel\ExternalContactInterface;
use Tourze\WechatWorkExternalContactModel\ExternalUserLoaderInterface;
use WechatWorkExternalContactBundle\Entity\ExternalUser;

/**
 * @extends ServiceEntityRepository<ExternalUser>
 */
#[AsAlias(id: ExternalUserLoaderInterface::class)]
#[AsRepository(entityClass: ExternalUser::class)]
class ExternalUserRepository extends ServiceEntityRepository implements ExternalUserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExternalUser::class);
    }

    public function loadByUnionIdAndCorp(string $unionId, CorpInterface $corp): ?ExternalContactInterface
    {
        return $this->findOneBy([
            'unionId' => $unionId,
            'corp' => $corp,
        ]);
    }

    public function save(ExternalUser $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ExternalUser $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}

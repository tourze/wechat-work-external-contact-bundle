<?php

namespace WechatWorkExternalContactBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use WechatWorkExternalContactBundle\Entity\ExternalServiceRelation;

/**
 * @extends ServiceEntityRepository<ExternalServiceRelation>
 */
#[AsRepository(entityClass: ExternalServiceRelation::class)]
class ExternalServiceRelationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExternalServiceRelation::class);
    }

    public function save(ExternalServiceRelation $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ExternalServiceRelation $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}

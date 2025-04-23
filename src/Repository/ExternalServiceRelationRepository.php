<?php

namespace WechatWorkExternalContactBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;
use WechatWorkExternalContactBundle\Entity\ExternalServiceRelation;

/**
 * @method ExternalServiceRelation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExternalServiceRelation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExternalServiceRelation[]    findAll()
 * @method ExternalServiceRelation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExternalServiceRelationRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExternalServiceRelation::class);
    }
}

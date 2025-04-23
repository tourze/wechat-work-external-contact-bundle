<?php

namespace WechatWorkExternalContactBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;
use WechatWorkExternalContactBundle\Entity\CorpTagGroup;

/**
 * @method CorpTagGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method CorpTagGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method CorpTagGroup[]    findAll()
 * @method CorpTagGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CorpTagGroupRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CorpTagGroup::class);
    }
}

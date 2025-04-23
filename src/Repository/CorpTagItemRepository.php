<?php

namespace WechatWorkExternalContactBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WechatWorkExternalContactBundle\Entity\CorpTagItem;

/**
 * @method CorpTagItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method CorpTagItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method CorpTagItem[]    findAll()
 * @method CorpTagItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CorpTagItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CorpTagItem::class);
    }
}

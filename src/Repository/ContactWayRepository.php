<?php

namespace WechatWorkExternalContactBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WechatWorkExternalContactBundle\Entity\ContactWay;

/**
 * @method ContactWay|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactWay|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactWay[]    findAll()
 * @method ContactWay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactWayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactWay::class);
    }
}

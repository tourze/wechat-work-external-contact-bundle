<?php

namespace WechatWorkExternalContactBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WechatWorkExternalContactBundle\Entity\ExternalUser;

/**
 * @method ExternalUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExternalUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExternalUser[]    findAll()
 * @method ExternalUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExternalUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExternalUser::class);
    }
}

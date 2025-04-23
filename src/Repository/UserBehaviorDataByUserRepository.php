<?php

namespace WechatWorkExternalContactBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WechatWorkExternalContactBundle\Entity\UserBehaviorDataByUser;

/**
 * @method UserBehaviorDataByUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserBehaviorDataByUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserBehaviorDataByUser[]    findAll()
 * @method UserBehaviorDataByUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserBehaviorDataByUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserBehaviorDataByUser::class);
    }
}

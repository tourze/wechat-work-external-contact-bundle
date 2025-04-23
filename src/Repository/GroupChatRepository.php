<?php

namespace WechatWorkExternalContactBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;
use WechatWorkExternalContactBundle\Entity\GroupChat;

/**
 * @method GroupChat|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupChat|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupChat[]    findAll()
 * @method GroupChat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupChatRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupChat::class);
    }
}

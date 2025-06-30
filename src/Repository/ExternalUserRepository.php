<?php

namespace WechatWorkExternalContactBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Tourze\WechatWorkContracts\CorpInterface;
use Tourze\WechatWorkExternalContactModel\ExternalContactInterface;
use Tourze\WechatWorkExternalContactModel\ExternalUserLoaderInterface;
use WechatWorkExternalContactBundle\Entity\ExternalUser;

/**
 * @method ExternalUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExternalUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExternalUser[]    findAll()
 * @method ExternalUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
#[AsAlias(id: ExternalUserLoaderInterface::class)]
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
}

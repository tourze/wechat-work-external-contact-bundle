<?php

namespace WechatWorkExternalContactBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatWorkBundle\Entity\Corp;
use WechatWorkExternalContactBundle\Entity\ExternalServiceRelation;
use WechatWorkExternalContactBundle\Entity\ExternalUser;
use WechatWorkExternalContactBundle\Repository\ExternalServiceRelationRepository;

/**
 * @internal
 * @template-extends AbstractRepositoryTestCase<ExternalServiceRelation>
 */
#[CoversClass(ExternalServiceRelationRepository::class)]
#[RunTestsInSeparateProcesses]
final class ExternalServiceRelationRepositoryTest extends AbstractRepositoryTestCase
{
    private ExternalServiceRelationRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(ExternalServiceRelationRepository::class);
    }

    protected function createNewEntity(): object
    {
        $entity = new ExternalServiceRelation();
        // 实体需要 Corp 才能保存，为每个测试创建独立的 Corp
        $corp = $this->createCorp('_create_new_entity_' . uniqid());
        $entity->setCorp($corp);

        // 确保 corp 数据被持久化到数据库
        self::getEntityManager()->flush();

        return $entity;
    }

    /**
     * @return ServiceEntityRepository<ExternalServiceRelation>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }

    private function createCorp(string $suffix = ''): Corp
    {
        $corp = new Corp();
        $corp->setName('测试企业_' . uniqid() . $suffix);
        $corp->setCorpId('test_corp_' . uniqid() . $suffix);
        $corp->setCorpSecret('test_secret');
        self::getEntityManager()->persist($corp);

        return $corp;
    }

    #[Test]
    public function testFindOneByWithOrderByForNullValues(): void
    {
        $corp = $this->createCorp('_null_order_test');

        // 创建有null值的记录
        $relation1 = new ExternalServiceRelation();
        $relation1->setCorp($corp);
        $relation1->setAddExternalContactTime(null);
        self::getEntityManager()->persist($relation1);

        $relation2 = new ExternalServiceRelation();
        $relation2->setCorp($corp);
        $relation2->setAddExternalContactTime(new \DateTimeImmutable('2024-01-01 10:00:00'));
        self::getEntityManager()->persist($relation2);
        self::getEntityManager()->flush();

        // 测试null值排序
        $nullFirst = $this->repository->findOneBy(['corp' => $corp], ['addExternalContactTime' => 'ASC']);
        $this->assertInstanceOf(ExternalServiceRelation::class, $nullFirst);
        $this->assertNull($nullFirst->getAddExternalContactTime());

        $nullLast = $this->repository->findOneBy(['corp' => $corp], ['addExternalContactTime' => 'DESC']);
        $this->assertInstanceOf(ExternalServiceRelation::class, $nullLast);
        $this->assertNotNull($nullLast->getAddExternalContactTime());
    }

    #[Test]
    public function testComplexAssociationQueries(): void
    {
        $corp = $this->createCorp('_complex_association_test');

        $externalUser = new ExternalUser();
        $externalUser->setExternalUserId('complex_user');
        $externalUser->setCorp($corp);
        self::getEntityManager()->persist($externalUser);

        $relation = new ExternalServiceRelation();
        $relation->setCorp($corp);
        $relation->setExternalUser($externalUser);
        $relation->setAddExternalContactTime(new \DateTimeImmutable('2024-01-01 15:30:00'));
        self::getEntityManager()->persist($relation);
        self::getEntityManager()->flush();

        // 测试通过关联对象的字段查询
        $resultsByCorpId = $this->repository->createQueryBuilder('r')
            ->join('r.corp', 'c')
            ->where('c.corpId = :corpId')
            ->setParameter('corpId', $corp->getCorpId())
            ->getQuery()
            ->getResult()
        ;
        $this->assertIsArray($resultsByCorpId);
        $this->assertGreaterThanOrEqual(1, count($resultsByCorpId));

        // 测试通过外部用户的字段查询
        $resultsByExternalUserId = $this->repository->createQueryBuilder('r')
            ->join('r.externalUser', 'eu')
            ->where('eu.externalUserId = :externalUserId')
            ->setParameter('externalUserId', 'complex_user')
            ->getQuery()
            ->getResult()
        ;
        $this->assertIsArray($resultsByExternalUserId);
        $this->assertGreaterThanOrEqual(1, count($resultsByExternalUserId));
    }

    #[Test]
    public function testAdvancedNullChecks(): void
    {
        $corp = $this->createCorp('_advanced_null_test');

        // 创建多种null状态的记录
        $relationAllNull = new ExternalServiceRelation();
        $relationAllNull->setCorp($corp);
        $relationAllNull->setUser(null);
        $relationAllNull->setExternalUser(null);
        $relationAllNull->setAddExternalContactTime(null);
        $relationAllNull->setDelExternalContactTime(null);
        self::getEntityManager()->persist($relationAllNull);

        $relationPartialNull = new ExternalServiceRelation();
        $relationPartialNull->setCorp($corp);
        $relationPartialNull->setUser(null);
        $relationPartialNull->setAddExternalContactTime(new \DateTimeImmutable('2024-01-01 10:00:00'));
        $relationPartialNull->setDelExternalContactTime(null);

        // 创建一个 ExternalUser 以确保这个关系的 externalUser 不为 null
        $externalUser = new ExternalUser();
        $externalUser->setExternalUserId('partial_null_test_user');
        $externalUser->setCorp($corp);
        self::getEntityManager()->persist($externalUser);
        $relationPartialNull->setExternalUser($externalUser);

        self::getEntityManager()->persist($relationPartialNull);
        self::getEntityManager()->flush();

        // 测试 IS NULL 查询
        $qb = $this->repository->createQueryBuilder('r');
        $nullUserRelations = $qb->where('r.user IS NULL')
            ->andWhere('r.corp = :corp')
            ->setParameter('corp', $corp)
            ->getQuery()
            ->getResult()
        ;
        $this->assertIsArray($nullUserRelations);
        $this->assertCount(2, $nullUserRelations);

        // 测试 IS NOT NULL 查询
        $qb = $this->repository->createQueryBuilder('r');
        $notNullTimeRelations = $qb->where('r.addExternalContactTime IS NOT NULL')
            ->andWhere('r.corp = :corp')
            ->setParameter('corp', $corp)
            ->getQuery()
            ->getResult()
        ;
        $this->assertIsArray($notNullTimeRelations);
        $this->assertCount(1, $notNullTimeRelations);

        // 测试复合NULL条件
        $qb = $this->repository->createQueryBuilder('r');
        $complexNullQuery = $qb->where('r.user IS NULL')
            ->andWhere('r.externalUser IS NULL')
            ->andWhere('r.corp = :corp')
            ->setParameter('corp', $corp)
            ->getQuery()
            ->getResult()
        ;
        $this->assertIsArray($complexNullQuery);
        $this->assertCount(1, $complexNullQuery);
    }

    #[Test]
    public function testSaveMethodPersistsEntity(): void
    {
        $corp = $this->createCorp('_test_save');

        $externalUser = new ExternalUser();
        $externalUser->setExternalUserId('ext_user_123');
        $externalUser->setCorp($corp);
        self::getEntityManager()->persist($externalUser);

        $relation = new ExternalServiceRelation();
        $relation->setCorp($corp);
        $relation->setExternalUser($externalUser);
        $relation->setAddExternalContactTime(new \DateTimeImmutable('2024-01-01 10:00:00'));

        $this->repository->save($relation, true);

        $this->assertNotNull($relation->getId());
        $savedRelation = $this->repository->find($relation->getId());
        $this->assertInstanceOf(ExternalServiceRelation::class, $savedRelation);
        $savedCorp = $savedRelation->getCorp();
        $this->assertInstanceOf(Corp::class, $savedCorp);
        $this->assertEquals($corp->getId(), $savedCorp->getId());
        $this->assertEquals($externalUser->getId(), $savedRelation->getExternalUser()?->getId());
    }

    #[Test]
    public function testSaveMethodWithoutFlush(): void
    {
        $corp = $this->createCorp('_test_no_flush');

        $relation = new ExternalServiceRelation();
        $relation->setCorp($corp);

        $this->repository->save($relation, false);
        self::getEntityManager()->flush();

        $this->assertNotNull($relation->getId());
        $savedRelation = $this->repository->find($relation->getId());
        $this->assertInstanceOf(ExternalServiceRelation::class, $savedRelation);
    }

    #[Test]
    public function testRemoveMethodDeletesEntity(): void
    {
        $corp = $this->createCorp('_remove_test');

        $relation = new ExternalServiceRelation();
        $relation->setCorp($corp);
        self::getEntityManager()->persist($relation);
        self::getEntityManager()->flush();

        $relationId = $relation->getId();
        $this->repository->remove($relation, true);

        $deletedRelation = $this->repository->find($relationId);
        $this->assertNull($deletedRelation);
    }

    #[Test]
    public function testFindByNullableFields(): void
    {
        $corp = $this->createCorp('_nullable_test');

        $relation = new ExternalServiceRelation();
        $relation->setCorp($corp);
        $relation->setUser(null);
        $relation->setExternalUser(null);
        $relation->setAddExternalContactTime(null);
        $relation->setAddHalfExternalContactTime(null);
        $relation->setDelExternalContactTime(null);
        $relation->setDelFollowUserTime(null);
        self::getEntityManager()->persist($relation);
        self::getEntityManager()->flush();

        $relationsWithNullUser = $this->repository->findBy(['user' => null]);
        $this->assertGreaterThanOrEqual(1, count($relationsWithNullUser));

        $relationsWithNullExternalUser = $this->repository->findBy(['externalUser' => null]);
        $this->assertGreaterThanOrEqual(1, count($relationsWithNullExternalUser));

        $relationsWithNullAddTime = $this->repository->findBy(['addExternalContactTime' => null]);
        $this->assertGreaterThanOrEqual(1, count($relationsWithNullAddTime));

        $relationsWithNullDelTime = $this->repository->findBy(['delExternalContactTime' => null]);
        $this->assertGreaterThanOrEqual(1, count($relationsWithNullDelTime));
    }

    #[Test]
    public function testCountByNullableFields(): void
    {
        $corp = $this->createCorp('_count_null_test');

        $relation = new ExternalServiceRelation();
        $relation->setCorp($corp);
        $relation->setUser(null);
        $relation->setExternalUser(null);
        self::getEntityManager()->persist($relation);
        self::getEntityManager()->flush();

        $countWithNullUser = $this->repository->count(['user' => null]);
        $this->assertGreaterThanOrEqual(1, $countWithNullUser);

        $countWithNullExternalUser = $this->repository->count(['externalUser' => null]);
        $this->assertGreaterThanOrEqual(1, $countWithNullExternalUser);
    }

    #[Test]
    public function testAssociationQueries(): void
    {
        $corp = $this->createCorp('_association_test');

        $externalUser = new ExternalUser();
        $externalUser->setExternalUserId('association_ext_user');
        $externalUser->setCorp($corp);
        self::getEntityManager()->persist($externalUser);

        $relation = new ExternalServiceRelation();
        $relation->setCorp($corp);
        $relation->setExternalUser($externalUser);
        self::getEntityManager()->persist($relation);
        self::getEntityManager()->flush();

        $relationsByCorp = $this->repository->findBy(['corp' => $corp]);
        $this->assertGreaterThanOrEqual(1, count($relationsByCorp));

        $relationsByExternalUser = $this->repository->findBy(['externalUser' => $externalUser]);
        $this->assertGreaterThanOrEqual(1, count($relationsByExternalUser));

        $countByCorp = $this->repository->count(['corp' => $corp]);
        $this->assertGreaterThanOrEqual(1, $countByCorp);
    }

    #[Test]
    public function testFindOneByWithOrderBy(): void
    {
        $corp = $this->createCorp('_findone_order_test');

        $relation1 = new ExternalServiceRelation();
        $relation1->setCorp($corp);
        $relation1->setAddExternalContactTime(new \DateTimeImmutable('2024-01-01 10:00:00'));
        self::getEntityManager()->persist($relation1);

        $relation2 = new ExternalServiceRelation();
        $relation2->setCorp($corp);
        $relation2->setAddExternalContactTime(new \DateTimeImmutable('2024-01-02 10:00:00'));
        self::getEntityManager()->persist($relation2);
        self::getEntityManager()->flush();

        $earliestRelation = $this->repository->findOneBy(['corp' => $corp], ['addExternalContactTime' => 'ASC']);
        $this->assertInstanceOf(ExternalServiceRelation::class, $earliestRelation);
        $this->assertEquals('2024-01-01 10:00:00', $earliestRelation->getAddExternalContactTime()?->format('Y-m-d H:i:s'));

        $latestRelation = $this->repository->findOneBy(['corp' => $corp], ['addExternalContactTime' => 'DESC']);
        $this->assertInstanceOf(ExternalServiceRelation::class, $latestRelation);
        $this->assertEquals('2024-01-02 10:00:00', $latestRelation->getAddExternalContactTime()?->format('Y-m-d H:i:s'));
    }

    #[Test]
    public function testBasicCrudOperations(): void
    {
        $corp = $this->createCorp('_crud_test');

        $relation = new ExternalServiceRelation();
        $relation->setCorp($corp);
        self::getEntityManager()->persist($relation);
        self::getEntityManager()->flush();

        $id = $relation->getId();
        $this->assertNotNull($id);

        $foundRelation = $this->repository->find($id);
        $this->assertInstanceOf(ExternalServiceRelation::class, $foundRelation);
        $this->assertEquals($id, $foundRelation->getId());

        $allRelations = $this->repository->findAll();
        $this->assertIsArray($allRelations);
        $this->assertGreaterThanOrEqual(1, count($allRelations));

        $relationsByCorp = $this->repository->findBy(['corp' => $corp]);
        $this->assertIsArray($relationsByCorp);
        $this->assertGreaterThanOrEqual(1, count($relationsByCorp));

        $totalCount = $this->repository->count([]);
        $this->assertGreaterThanOrEqual(1, $totalCount);
    }

    #[Test]
    public function testLimitAndOffsetParameters(): void
    {
        $corp = $this->createCorp('_pagination_test');

        for ($i = 1; $i <= 5; ++$i) {
            $relation = new ExternalServiceRelation();
            $relation->setCorp($corp);
            self::getEntityManager()->persist($relation);
        }
        self::getEntityManager()->flush();

        $results = $this->repository->findBy(['corp' => $corp], null, 2);
        $this->assertCount(2, $results);

        $offsetResults = $this->repository->findBy(['corp' => $corp], null, 2, 1);
        $this->assertCount(2, $offsetResults);
    }

    #[Test]
    public function testDateTimeFieldsQueries(): void
    {
        $corp = $this->createCorp('_datetime_test');

        $addTime = new \DateTimeImmutable('2024-01-01 12:00:00');
        $delTime = new \DateTimeImmutable('2024-01-02 12:00:00');

        $relation = new ExternalServiceRelation();
        $relation->setCorp($corp);
        $relation->setAddExternalContactTime($addTime);
        $relation->setDelExternalContactTime($delTime);
        self::getEntityManager()->persist($relation);
        self::getEntityManager()->flush();

        $relationsWithAddTime = $this->repository->findBy(['addExternalContactTime' => $addTime]);
        $this->assertGreaterThanOrEqual(1, count($relationsWithAddTime));

        $relationsWithDelTime = $this->repository->findBy(['delExternalContactTime' => $delTime]);
        $this->assertGreaterThanOrEqual(1, count($relationsWithDelTime));

        $countWithAddTime = $this->repository->count(['addExternalContactTime' => $addTime]);
        $this->assertGreaterThanOrEqual(1, $countWithAddTime);
    }

    #[Test]
    public function testFindOneByWithOrderByShouldRespectSortingLogic(): void
    {
        $corp = $this->createCorp('_order_logic_test');

        $relation1 = new ExternalServiceRelation();
        $relation1->setCorp($corp);
        $relation1->setAddExternalContactTime(new \DateTimeImmutable('2024-01-01 10:00:00'));
        self::getEntityManager()->persist($relation1);

        $relation2 = new ExternalServiceRelation();
        $relation2->setCorp($corp);
        $relation2->setAddExternalContactTime(new \DateTimeImmutable('2024-01-02 10:00:00'));
        self::getEntityManager()->persist($relation2);
        self::getEntityManager()->flush();

        $earliestRelation = $this->repository->findOneBy(['corp' => $corp], ['addExternalContactTime' => 'ASC']);
        $this->assertInstanceOf(ExternalServiceRelation::class, $earliestRelation);
        $this->assertEquals('2024-01-01 10:00:00', $earliestRelation->getAddExternalContactTime()?->format('Y-m-d H:i:s'));

        $latestRelation = $this->repository->findOneBy(['corp' => $corp], ['addExternalContactTime' => 'DESC']);
        $this->assertInstanceOf(ExternalServiceRelation::class, $latestRelation);
        $this->assertEquals('2024-01-02 10:00:00', $latestRelation->getAddExternalContactTime()?->format('Y-m-d H:i:s'));
    }

    #[Test]
    public function testCountByAssociationFieldsShouldReturnCorrectNumber(): void
    {
        $corp = $this->createCorp('_count_assoc_test');

        $externalUser = new ExternalUser();
        $externalUser->setExternalUserId('count_user');
        $externalUser->setCorp($corp);
        self::getEntityManager()->persist($externalUser);

        $relation = new ExternalServiceRelation();
        $relation->setCorp($corp);
        $relation->setExternalUser($externalUser);
        self::getEntityManager()->persist($relation);
        self::getEntityManager()->flush();

        $countByCorp = $this->repository->count(['corp' => $corp]);
        $this->assertGreaterThanOrEqual(1, $countByCorp);

        $countByExternalUser = $this->repository->count(['externalUser' => $externalUser]);
        $this->assertGreaterThanOrEqual(1, $countByExternalUser);
    }

    #[Test]
    public function testFindByAssociationFieldsShouldReturnCorrectResults(): void
    {
        $corp = $this->createCorp('_find_assoc_test');

        $externalUser = new ExternalUser();
        $externalUser->setExternalUserId('find_user');
        $externalUser->setCorp($corp);
        self::getEntityManager()->persist($externalUser);

        $relation = new ExternalServiceRelation();
        $relation->setCorp($corp);
        $relation->setExternalUser($externalUser);
        self::getEntityManager()->persist($relation);
        self::getEntityManager()->flush();

        $relationsByCorp = $this->repository->findBy(['corp' => $corp]);
        $this->assertGreaterThanOrEqual(1, count($relationsByCorp));
        $this->assertInstanceOf(ExternalServiceRelation::class, $relationsByCorp[0]);

        $relationsByExternalUser = $this->repository->findBy(['externalUser' => $externalUser]);
        $this->assertGreaterThanOrEqual(1, count($relationsByExternalUser));
        $this->assertInstanceOf(ExternalServiceRelation::class, $relationsByExternalUser[0]);
    }

    #[Test]
    public function testFindByNullFieldsShouldReturnCorrectResults(): void
    {
        $corp = $this->createCorp('_null_fields_test');

        $relation = new ExternalServiceRelation();
        $relation->setCorp($corp);
        $relation->setUser(null);
        $relation->setExternalUser(null);
        $relation->setAddExternalContactTime(null);
        self::getEntityManager()->persist($relation);
        self::getEntityManager()->flush();

        $relationsWithNullUser = $this->repository->findBy(['user' => null]);
        $this->assertGreaterThanOrEqual(1, count($relationsWithNullUser));

        $relationsWithNullExternalUser = $this->repository->findBy(['externalUser' => null]);
        $this->assertGreaterThanOrEqual(1, count($relationsWithNullExternalUser));

        $relationsWithNullTime = $this->repository->findBy(['addExternalContactTime' => null]);
        $this->assertGreaterThanOrEqual(1, count($relationsWithNullTime));
    }

    #[Test]
    public function testCountByNullFieldsShouldReturnCorrectNumber(): void
    {
        $corp = $this->createCorp('_count_null_fields_test');

        $relation = new ExternalServiceRelation();
        $relation->setCorp($corp);
        $relation->setUser(null);
        $relation->setExternalUser(null);
        $relation->setAddExternalContactTime(null);
        self::getEntityManager()->persist($relation);
        self::getEntityManager()->flush();

        $countWithNullUser = $this->repository->count(['user' => null]);
        $this->assertGreaterThanOrEqual(1, $countWithNullUser);

        $countWithNullExternalUser = $this->repository->count(['externalUser' => null]);
        $this->assertGreaterThanOrEqual(1, $countWithNullExternalUser);

        $countWithNullTime = $this->repository->count(['addExternalContactTime' => null]);
        $this->assertGreaterThanOrEqual(1, $countWithNullTime);
    }
}

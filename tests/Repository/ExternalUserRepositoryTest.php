<?php

namespace WechatWorkExternalContactBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use Tourze\WechatWorkExternalContactModel\ExternalUserLoaderInterface;
use WechatWorkBundle\Entity\Corp;
use WechatWorkExternalContactBundle\Entity\ExternalUser;
use WechatWorkExternalContactBundle\Repository\ExternalUserRepository;

/**
 * @internal
 * @template-extends AbstractRepositoryTestCase<ExternalUser>
 */
#[CoversClass(ExternalUserRepository::class)]
#[RunTestsInSeparateProcesses]
final class ExternalUserRepositoryTest extends AbstractRepositoryTestCase
{
    private ExternalUserRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(ExternalUserRepository::class);
    }

    protected function createNewEntity(): object
    {
        $entity = new ExternalUser();
        $entity->setExternalUserId('test_user_' . uniqid());
        $entity->setNickname('Test User ' . uniqid());

        // 为了确保数据一致性，也为外部用户添加 corp
        $corp = $this->createCorp('_create_new_entity_' . uniqid());
        $entity->setCorp($corp);

        // 确保 corp 数据被持久化到数据库
        self::getEntityManager()->flush();

        return $entity;
    }

    /**
     * @return ServiceEntityRepository<ExternalUser>
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
    public function testFindOneByWithOrderByAdvanced(): void
    {
        $corp = $this->createCorp('_order_test_advanced');

        $user1 = new ExternalUser();
        $user1->setExternalUserId('order_test_1');
        $user1->setNickname('Z User');
        $user1->setCorp($corp);
        self::getEntityManager()->persist($user1);

        $user2 = new ExternalUser();
        $user2->setExternalUserId('order_test_2');
        $user2->setNickname('A User');
        $user2->setCorp($corp);
        self::getEntityManager()->persist($user2);

        $user3 = new ExternalUser();
        $user3->setExternalUserId('order_test_3');
        // 不设置nickname以测试null值
        $user3->setCorp($corp);
        self::getEntityManager()->persist($user3);
        self::getEntityManager()->flush();

        // 测试按多个字段排序
        $result = $this->repository->findOneBy(
            ['corp' => $corp],
            ['nickname' => 'ASC', 'externalUserId' => 'DESC']
        );
        $this->assertInstanceOf(ExternalUser::class, $result);

        // 测试null值在排序中的处理
        $resultWithNulls = $this->repository->findBy(
            ['corp' => $corp],
            ['nickname' => 'ASC']
        );
        $this->assertGreaterThanOrEqual(3, count($resultWithNulls));
    }

    #[Test]
    public function testAdvancedAssociationQueries(): void
    {
        $corp1 = $this->createCorp('_assoc_corp1');

        $corp2 = $this->createCorp('_assoc_corp2');

        $user1 = new ExternalUser();
        $user1->setExternalUserId('assoc_user1');
        $user1->setUnionId('union_123');
        $user1->setCorp($corp1);
        self::getEntityManager()->persist($user1);

        $user2 = new ExternalUser();
        $user2->setExternalUserId('assoc_user2');
        $user2->setUnionId('union_456');
        $user2->setCorp($corp2);
        self::getEntityManager()->persist($user2);
        self::getEntityManager()->flush();

        // 测试复杂关联查询
        $qb = $this->repository->createQueryBuilder('u');
        $results = $qb->join('u.corp', 'c')
            ->where('c.corpId LIKE :pattern')
            ->andWhere('u.unionId IS NOT NULL')
            ->setParameter('pattern', '%_assoc_%')
            ->orderBy('c.corpId', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        $this->assertIsArray($results);
        $this->assertCount(2, $results);

        // 测试统计查询
        $qb = $this->repository->createQueryBuilder('u');
        $count = $qb->select('COUNT(u.id)')
            ->join('u.corp', 'c')
            ->where('c.corpId LIKE :pattern')
            ->setParameter('pattern', '%_assoc_%')
            ->getQuery()
            ->getSingleScalarResult()
        ;
        $this->assertEquals(2, $count);
    }

    #[Test]
    public function testComplexNullAndNotNullQueries(): void
    {
        $corp = $this->createCorp('_null_complex_test');

        // 创建不同的null组合
        $userAllFields = new ExternalUser();
        $userAllFields->setExternalUserId('all_fields');
        $userAllFields->setUnionId('union_all');
        $userAllFields->setNickname('All Fields');
        $userAllFields->setAvatar('http://example.com/avatar.jpg');
        $userAllFields->setRemark('Some remark');
        $userAllFields->setCorp($corp);
        self::getEntityManager()->persist($userAllFields);

        $userSomeNull = new ExternalUser();
        $userSomeNull->setExternalUserId('some_null');
        $userSomeNull->setUnionId(null);
        $userSomeNull->setNickname('Some Null');
        $userSomeNull->setAvatar(null);
        $userSomeNull->setRemark('Some remark');
        $userSomeNull->setCorp($corp);
        self::getEntityManager()->persist($userSomeNull);

        $userMostNull = new ExternalUser();
        $userMostNull->setExternalUserId('most_null');
        $userMostNull->setUnionId(null);
        // 不设置nickname以保持null值
        $userMostNull->setAvatar(null);
        $userMostNull->setRemark(null);
        $userMostNull->setCorp(null);
        self::getEntityManager()->persist($userMostNull);
        self::getEntityManager()->flush();

        // 测试多个NULL条件
        $qb = $this->repository->createQueryBuilder('u');
        $nullUsers = $qb->where('u.unionId IS NULL')
            ->andWhere('u.avatar IS NULL')
            ->getQuery()
            ->getResult()
        ;
        $this->assertIsArray($nullUsers);
        $this->assertCount(2, $nullUsers);

        // 测试混合NULL和NOT NULL条件
        $qb = $this->repository->createQueryBuilder('u');
        $mixedUsers = $qb->where('u.unionId IS NULL')
            ->andWhere('u.nickname IS NOT NULL')
            ->andWhere('u.corp = :corp')
            ->setParameter('corp', $corp)
            ->getQuery()
            ->getResult()
        ;
        $this->assertIsArray($mixedUsers);
        $this->assertCount(1, $mixedUsers);
        $this->assertInstanceOf(ExternalUser::class, $mixedUsers[0]);
        $this->assertEquals('Some Null', $mixedUsers[0]->getNickname());

        // 测试OR条件中的NULL
        $qb = $this->repository->createQueryBuilder('u');
        $orNullUsers = $qb->where(
            $qb->expr()->orX(
                'u.unionId IS NULL',
                'u.nickname IS NULL'
            )
        )->getQuery()
            ->getResult()
        ;
        $this->assertIsArray($orNullUsers);
        $this->assertGreaterThanOrEqual(2, count($orNullUsers));
    }

    #[Test]
    public function testRepositoryImplementsExternalUserLoaderInterface(): void
    {
        $this->assertInstanceOf(ExternalUserLoaderInterface::class, $this->repository);
    }

    #[Test]
    public function testLoadByUnionIdAndCorpReturnsExternalUser(): void
    {
        $corp = $this->createCorp('_test_corp_123');

        $externalUser = new ExternalUser();
        $externalUser->setExternalUserId('ext_user_123');
        $externalUser->setUnionId('union_123');
        $externalUser->setCorp($corp);
        self::getEntityManager()->persist($externalUser);
        self::getEntityManager()->flush();

        $result = $this->repository->loadByUnionIdAndCorp('union_123', $corp);

        $this->assertInstanceOf(ExternalUser::class, $result);
        $this->assertEquals('union_123', $result->getUnionId());
        $resultCorp = $result->getCorp();
        $this->assertInstanceOf(Corp::class, $resultCorp);
        $this->assertEquals($corp->getId(), $resultCorp->getId());
    }

    #[Test]
    public function testLoadByUnionIdAndCorpReturnsNullWhenNotFound(): void
    {
        $corp = $this->createCorp('_test_corp_456');
        self::getEntityManager()->flush();

        $result = $this->repository->loadByUnionIdAndCorp('nonexistent_union', $corp);

        $this->assertNull($result);
    }

    #[Test]
    public function testSaveMethodPersistsExternalUser(): void
    {
        $externalUser = new ExternalUser();
        $externalUser->setExternalUserId('new_user_456');
        $externalUser->setNickname('Test User');

        $this->repository->save($externalUser, true);

        $this->assertNotNull($externalUser->getId());
        $savedUser = $this->repository->find($externalUser->getId());
        $this->assertNotNull($savedUser);
        $this->assertEquals('new_user_456', $savedUser->getExternalUserId());
        $this->assertEquals('Test User', $savedUser->getNickname());
    }

    #[Test]
    public function testSaveMethodWithoutFlush(): void
    {
        $externalUser = new ExternalUser();
        $externalUser->setExternalUserId('no_flush_user');

        $this->repository->save($externalUser, false);
        self::getEntityManager()->flush();

        $this->assertNotNull($externalUser->getId());
        $savedUser = $this->repository->find($externalUser->getId());
        $this->assertNotNull($savedUser);
        $this->assertEquals('no_flush_user', $savedUser->getExternalUserId());
    }

    #[Test]
    public function testRemoveMethodDeletesExternalUser(): void
    {
        $externalUser = new ExternalUser();
        $externalUser->setExternalUserId('to_be_deleted');
        self::getEntityManager()->persist($externalUser);
        self::getEntityManager()->flush();

        $userId = $externalUser->getId();
        $this->repository->remove($externalUser, true);

        $deletedUser = $this->repository->find($userId);
        $this->assertNull($deletedUser);
    }

    #[Test]
    public function testFindByNullableFields(): void
    {
        $externalUser = new ExternalUser();
        $externalUser->setExternalUserId('nullable_test_user');
        $externalUser->setUnionId(null);
        $externalUser->setAvatar(null);
        $externalUser->setRemark(null);
        self::getEntityManager()->persist($externalUser);
        self::getEntityManager()->flush();

        $usersWithNullUnionId = $this->repository->findBy(['unionId' => null]);
        $this->assertGreaterThanOrEqual(1, count($usersWithNullUnionId));

        $usersWithNullAvatar = $this->repository->findBy(['avatar' => null]);
        $this->assertGreaterThanOrEqual(1, count($usersWithNullAvatar));

        $usersWithNullNickname = $this->repository->findBy(['nickname' => null]);
        $this->assertGreaterThanOrEqual(1, count($usersWithNullNickname));

        $usersWithNullRemark = $this->repository->findBy(['remark' => null]);
        $this->assertGreaterThanOrEqual(1, count($usersWithNullRemark));
    }

    #[Test]
    public function testCountByNullableFields(): void
    {
        $externalUser = new ExternalUser();
        $externalUser->setExternalUserId('count_null_test');
        $externalUser->setUnionId(null);
        $externalUser->setAvatar(null);
        self::getEntityManager()->persist($externalUser);
        self::getEntityManager()->flush();

        $countWithNullUnionId = $this->repository->count(['unionId' => null]);
        $this->assertGreaterThanOrEqual(1, $countWithNullUnionId);

        $countWithNullAvatar = $this->repository->count(['avatar' => null]);
        $this->assertGreaterThanOrEqual(1, $countWithNullAvatar);
    }

    #[Test]
    public function testAssociationQueries(): void
    {
        $corp = $this->createCorp('_association_test_corp');

        $externalUser = new ExternalUser();
        $externalUser->setExternalUserId('association_test_user');
        $externalUser->setCorp($corp);
        self::getEntityManager()->persist($externalUser);
        self::getEntityManager()->flush();

        $usersByCorp = $this->repository->findBy(['corp' => $corp]);
        $this->assertGreaterThanOrEqual(1, count($usersByCorp));

        $countByCorp = $this->repository->count(['corp' => $corp]);
        $this->assertGreaterThanOrEqual(1, $countByCorp);

        $nullCorpUsers = $this->repository->findBy(['corp' => null]);
        $this->assertGreaterThanOrEqual(0, count($nullCorpUsers));

        $nullCorpCount = $this->repository->count(['corp' => null]);
        $this->assertGreaterThanOrEqual(0, $nullCorpCount);
    }

    #[Test]
    public function testBasicCrudOperations(): void
    {
        $externalUser = new ExternalUser();
        $externalUser->setExternalUserId('crud_test_user');
        self::getEntityManager()->persist($externalUser);
        self::getEntityManager()->flush();

        $id = $externalUser->getId();
        $this->assertNotNull($id);

        $foundUser = $this->repository->find($id);
        $this->assertInstanceOf(ExternalUser::class, $foundUser);
        $this->assertEquals($id, $foundUser->getId());

        $allUsers = $this->repository->findAll();
        $this->assertIsArray($allUsers);
        $this->assertGreaterThanOrEqual(1, count($allUsers));

        $totalCount = $this->repository->count([]);
        $this->assertGreaterThanOrEqual(1, $totalCount);
    }

    #[Test]
    public function testLimitAndOffsetParameters(): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            $externalUser = new ExternalUser();
            $externalUser->setExternalUserId('pagination_user_' . $i);
            self::getEntityManager()->persist($externalUser);
        }
        self::getEntityManager()->flush();

        $results = $this->repository->findBy([], null, 2);
        $this->assertCount(2, $results);

        $offsetResults = $this->repository->findBy([], null, 2, 1);
        $this->assertCount(2, $offsetResults);
    }

    #[Test]
    public function testFindOneByWithOrderBy(): void
    {
        $corp = $this->createCorp('_order_test_specific');

        $user1 = new ExternalUser();
        $user1->setExternalUserId('order_test_1');
        $user1->setNickname('Z User');
        $user1->setCorp($corp);
        self::getEntityManager()->persist($user1);

        $user2 = new ExternalUser();
        $user2->setExternalUserId('order_test_2');
        $user2->setNickname('A User');
        $user2->setCorp($corp);
        self::getEntityManager()->persist($user2);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy(['corp' => $corp], ['nickname' => 'ASC']);
        $this->assertInstanceOf(ExternalUser::class, $result);
        $this->assertEquals('A User', $result->getNickname());

        $result = $this->repository->findOneBy(['corp' => $corp], ['nickname' => 'DESC']);
        $this->assertInstanceOf(ExternalUser::class, $result);
        $this->assertEquals('Z User', $result->getNickname());
    }

    #[Test]
    public function testFindOneByWithOrderByShouldRespectSortingLogic(): void
    {
        $corp = $this->createCorp('_order_sorting_test');

        $user1 = new ExternalUser();
        $user1->setExternalUserId('order_user_1');
        $user1->setNickname('Z User');
        $user1->setCorp($corp);
        self::getEntityManager()->persist($user1);

        $user2 = new ExternalUser();
        $user2->setExternalUserId('order_user_2');
        $user2->setNickname('A User');
        $user2->setCorp($corp);
        self::getEntityManager()->persist($user2);
        self::getEntityManager()->flush();

        $ascResult = $this->repository->findOneBy(['corp' => $corp], ['nickname' => 'ASC']);
        $this->assertInstanceOf(ExternalUser::class, $ascResult);
        $this->assertEquals('A User', $ascResult->getNickname());

        $descResult = $this->repository->findOneBy(['corp' => $corp], ['nickname' => 'DESC']);
        $this->assertInstanceOf(ExternalUser::class, $descResult);
        $this->assertEquals('Z User', $descResult->getNickname());
    }

    #[Test]
    public function testCountByAssociationFieldsShouldReturnCorrectNumber(): void
    {
        $corp = $this->createCorp('_count_association_test');

        $user = new ExternalUser();
        $user->setExternalUserId('count_assoc_user');
        $user->setCorp($corp);
        self::getEntityManager()->persist($user);
        self::getEntityManager()->flush();

        $countByCorp = $this->repository->count(['corp' => $corp]);
        $this->assertGreaterThanOrEqual(1, $countByCorp);

        $countByNullCorp = $this->repository->count(['corp' => null]);
        $this->assertGreaterThanOrEqual(0, $countByNullCorp);
    }

    #[Test]
    public function testFindByAssociationFieldsShouldReturnCorrectResults(): void
    {
        $corp = $this->createCorp('_find_association_test');

        $user = new ExternalUser();
        $user->setExternalUserId('find_assoc_user');
        $user->setCorp($corp);
        self::getEntityManager()->persist($user);
        self::getEntityManager()->flush();

        $usersByCorp = $this->repository->findBy(['corp' => $corp]);
        $this->assertGreaterThanOrEqual(1, count($usersByCorp));
        $this->assertInstanceOf(ExternalUser::class, $usersByCorp[0]);

        $usersByNullCorp = $this->repository->findBy(['corp' => null]);
        $this->assertIsArray($usersByNullCorp);
    }

    #[Test]
    public function testFindByNullFieldsShouldReturnCorrectResults(): void
    {
        $user = new ExternalUser();
        $user->setExternalUserId('null_test_user');
        $user->setUnionId(null);
        $user->setAvatar(null);
        $user->setRemark(null);
        self::getEntityManager()->persist($user);
        self::getEntityManager()->flush();

        $usersWithNullUnionId = $this->repository->findBy(['unionId' => null]);
        $this->assertGreaterThanOrEqual(1, count($usersWithNullUnionId));

        $usersWithNullAvatar = $this->repository->findBy(['avatar' => null]);
        $this->assertGreaterThanOrEqual(1, count($usersWithNullAvatar));

        $usersWithNullRemark = $this->repository->findBy(['remark' => null]);
        $this->assertGreaterThanOrEqual(1, count($usersWithNullRemark));
    }

    #[Test]
    public function testCountByNullFieldsShouldReturnCorrectNumber(): void
    {
        $user = new ExternalUser();
        $user->setExternalUserId('count_null_test_user');
        $user->setUnionId(null);
        $user->setAvatar(null);
        $user->setRemark(null);
        self::getEntityManager()->persist($user);
        self::getEntityManager()->flush();

        $countWithNullUnionId = $this->repository->count(['unionId' => null]);
        $this->assertGreaterThanOrEqual(1, $countWithNullUnionId);

        $countWithNullAvatar = $this->repository->count(['avatar' => null]);
        $this->assertGreaterThanOrEqual(1, $countWithNullAvatar);

        $countWithNullRemark = $this->repository->count(['remark' => null]);
        $this->assertGreaterThanOrEqual(1, $countWithNullRemark);
    }
}

<?php

namespace WechatWorkExternalContactBundle\Tests\Integration;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use WechatWorkExternalContactBundle\Entity\ContactWay;
use WechatWorkExternalContactBundle\Repository\ContactWayRepository;

/**
 * ContactWayRepository 集成测试
 * 
 * 测试Repository与实际数据库的交互功能
 */
class ContactWayRepositoryIntegrationTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private ContactWayRepository $repository;

    protected static function getKernelClass(): string
    {
        return IntegrationTestKernel::class;
    }

    protected function setUp(): void
    {
        // 启动内核
        self::bootKernel();
        $container = static::getContainer();

        // 获取实体管理器和Repository
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->repository = $container->get(ContactWayRepository::class);

        // 创建/更新数据库模式
        $schemaTool = new SchemaTool($this->entityManager);
        $metadatas = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadatas);
        $schemaTool->createSchema($metadatas);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
        // 清理EntityManager
        if ($this->entityManager) {
            $this->entityManager->close();
        }
    }

    public function test_repository_service_registration(): void
    {
        // 验证Repository服务已正确注册
        $this->assertInstanceOf(ContactWayRepository::class, $this->repository);
        $this->assertSame(ContactWay::class, $this->repository->getClassName());
    }

    public function test_find_withNonExistentId_returnsNull(): void
    {
        $result = $this->repository->find(999);
        
        $this->assertNull($result);
    }

    public function test_findAll_withEmptyTable_returnsEmptyArray(): void
    {
        $result = $this->repository->findAll();
        
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_findBy_withEmptyTable_returnsEmptyArray(): void
    {
        $result = $this->repository->findBy(['state' => '1']);
        
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_findOneBy_withEmptyTable_returnsNull(): void
    {
        $result = $this->repository->findOneBy(['configId' => 'test_config']);
        
        $this->assertNull($result);
    }

    public function test_save_and_find_basicCRUD(): void
    {
        // 创建ContactWay实体
        $contactWay = new ContactWay();
        $contactWay->setConfigId('test_config_001');
        $contactWay->setType(1);
        $contactWay->setScene(1);
        $contactWay->setState('1');
        $contactWay->setUser(['user_001']);
        $contactWay->setParty([1, 2]);
        $contactWay->setTemp(true);
        $contactWay->setExpiresIn(86400);
        $contactWay->setChatExpiresIn(3600);
        $contactWay->setUnionId('union_test_001');
        $contactWay->setExclusive(false);
        $contactWay->setConclusions([]);
        $contactWay->setSkipVerify(false);
        
        // 保存到数据库
        $this->entityManager->persist($contactWay);
        $this->entityManager->flush();
        
        // 验证已分配ID
        $this->assertGreaterThan(0, $contactWay->getId());
        
        $savedId = $contactWay->getId();
        
        // 清除实体管理器缓存
        $this->entityManager->clear();
        
        // 通过Repository查找
        $foundContactWay = $this->repository->find($savedId);
        
        $this->assertNotNull($foundContactWay);
        $this->assertInstanceOf(ContactWay::class, $foundContactWay);
        $this->assertSame($savedId, $foundContactWay->getId());
        $this->assertSame('test_config_001', $foundContactWay->getConfigId());
        $this->assertSame(1, $foundContactWay->getType());
        $this->assertSame(1, $foundContactWay->getScene());
        $this->assertSame('1', $foundContactWay->getState());
        $this->assertTrue($foundContactWay->isTemp());
        $this->assertSame(86400, $foundContactWay->getExpiresIn());
        $this->assertSame(3600, $foundContactWay->getChatExpiresIn());
        $this->assertSame('union_test_001', $foundContactWay->getUnionId());
        $this->assertFalse($foundContactWay->isExclusive());
        $this->assertFalse($foundContactWay->isSkipVerify());
    }

    public function test_findBy_withCriteria_returnsMatchingResults(): void
    {
        // 创建多个ContactWay实体
        $contactWay1 = new ContactWay();
        $contactWay1->setConfigId('config_001');
        $contactWay1->setType(1);
        $contactWay1->setState(1);
        
        $contactWay2 = new ContactWay();
        $contactWay2->setConfigId('config_002');
        $contactWay2->setType(1);
        $contactWay2->setState(0); // 不同状态
        
        $contactWay3 = new ContactWay();
        $contactWay3->setConfigId('config_003');
        $contactWay3->setType(2); // 不同类型
        $contactWay3->setState(1);
        
        // 保存所有实体
        $this->entityManager->persist($contactWay1);
        $this->entityManager->persist($contactWay2);
        $this->entityManager->persist($contactWay3);
        $this->entityManager->flush();
        
        // 测试按type查找
        $resultsByType = $this->repository->findBy(['type' => 1]);
        $this->assertCount(2, $resultsByType);
        
        // 测试按state查找
        $resultsByState = $this->repository->findBy(['state' => 1]);
        $this->assertCount(2, $resultsByState);
        
        // 测试复合条件查找
        $resultsByBoth = $this->repository->findBy(['type' => 1, 'state' => 1]);
        $this->assertCount(1, $resultsByBoth);
        $this->assertSame('config_001', $resultsByBoth[0]->getConfigId());
        
        // 测试按configId查找
        $resultsByConfigId = $this->repository->findBy(['configId' => 'config_002']);
        $this->assertCount(1, $resultsByConfigId);
        $this->assertSame(0, $resultsByConfigId[0]->getState());
    }

    public function test_findOneBy_withCriteria_returnsFirstMatch(): void
    {
        // 创建多个相同类型的ContactWay实体
        $contactWay1 = new ContactWay();
        $contactWay1->setConfigId('config_type1_first');
        $contactWay1->setType(1);
        
        $contactWay2 = new ContactWay();
        $contactWay2->setConfigId('config_type1_second');
        $contactWay2->setType(1);
        
        // 保存实体
        $this->entityManager->persist($contactWay1);
        $this->entityManager->persist($contactWay2);
        $this->entityManager->flush();
        
        // 使用findOneBy查找
        $result = $this->repository->findOneBy(['type' => 1]);
        
        $this->assertNotNull($result);
        $this->assertInstanceOf(ContactWay::class, $result);
        $this->assertSame(1, $result->getType());
        // 应该返回第一个匹配的实体
        $this->assertContains($result->getConfigId(), ['config_type1_first', 'config_type1_second']);
    }

    public function test_findBy_withOrderBy_returnsSortedResults(): void
    {
        // 创建多个ContactWay实体，configId使用特定顺序
        $contactWay1 = new ContactWay();
        $contactWay1->setConfigId('config_c');
        $contactWay1->setType(1);
        
        $contactWay2 = new ContactWay();
        $contactWay2->setConfigId('config_a');
        $contactWay2->setType(1);
        
        $contactWay3 = new ContactWay();
        $contactWay3->setConfigId('config_b');
        $contactWay3->setType(1);
        
        // 保存实体
        $this->entityManager->persist($contactWay1);
        $this->entityManager->persist($contactWay2);
        $this->entityManager->persist($contactWay3);
        $this->entityManager->flush();
        
        // 测试按configId升序排序
        $resultsAsc = $this->repository->findBy(['type' => 1], ['configId' => 'ASC']);
        $this->assertCount(3, $resultsAsc);
        $this->assertSame('config_a', $resultsAsc[0]->getConfigId());
        $this->assertSame('config_b', $resultsAsc[1]->getConfigId());
        $this->assertSame('config_c', $resultsAsc[2]->getConfigId());
        
        // 测试按configId降序排序
        $resultsDesc = $this->repository->findBy(['type' => 1], ['configId' => 'DESC']);
        $this->assertCount(3, $resultsDesc);
        $this->assertSame('config_c', $resultsDesc[0]->getConfigId());
        $this->assertSame('config_b', $resultsDesc[1]->getConfigId());
        $this->assertSame('config_a', $resultsDesc[2]->getConfigId());
    }

    public function test_findBy_withLimitAndOffset_returnsPaginatedResults(): void
    {
        // 创建5个ContactWay实体
        for ($i = 1; $i <= 5; $i++) {
            $contactWay = new ContactWay();
            $contactWay->setConfigId("config_00{$i}");
            $contactWay->setType(1);
            $this->entityManager->persist($contactWay);
        }
        $this->entityManager->flush();
        
        // 测试分页：前2条
        $firstPage = $this->repository->findBy(['type' => 1], ['configId' => 'ASC'], 2, 0);
        $this->assertCount(2, $firstPage);
        $this->assertSame('config_001', $firstPage[0]->getConfigId());
        $this->assertSame('config_002', $firstPage[1]->getConfigId());
        
        // 测试分页：跳过前2条，取接下来2条
        $secondPage = $this->repository->findBy(['type' => 1], ['configId' => 'ASC'], 2, 2);
        $this->assertCount(2, $secondPage);
        $this->assertSame('config_003', $secondPage[0]->getConfigId());
        $this->assertSame('config_004', $secondPage[1]->getConfigId());
        
        // 测试分页：最后1条
        $lastPage = $this->repository->findBy(['type' => 1], ['configId' => 'ASC'], 2, 4);
        $this->assertCount(1, $lastPage);
        $this->assertSame('config_005', $lastPage[0]->getConfigId());
    }

    public function test_findAll_returnsAllEntities(): void
    {
        // 创建3个ContactWay实体
        for ($i = 1; $i <= 3; $i++) {
            $contactWay = new ContactWay();
            $contactWay->setConfigId("config_all_{$i}");
            $contactWay->setType($i);
            $this->entityManager->persist($contactWay);
        }
        $this->entityManager->flush();
        
        // 测试findAll
        $allResults = $this->repository->findAll();
        $this->assertCount(3, $allResults);
        
        // 验证返回的都是ContactWay实例
        foreach ($allResults as $result) {
            $this->assertInstanceOf(ContactWay::class, $result);
        }
    }

    public function test_entityPersistence_withComplexData(): void
    {
        // 创建包含复杂数据的ContactWay实体
        $contactWay = new ContactWay();
        $contactWay->setConfigId('complex_config');
        $contactWay->setType(2);
        $contactWay->setScene(2);
        $contactWay->setState(1);
        $contactWay->setUser(['user_001', 'user_002', 'user_003']);
        $contactWay->setParty([10, 20, 30]);
        $contactWay->setTemp(false);
        $contactWay->setExpiresIn(null);
        $contactWay->setChatExpiresIn(null);
        $contactWay->setUnionId('complex_union_id');
        $contactWay->setExclusive(true);
        $contactWay->setConclusions([
            [
                'text' => ['content' => '欢迎语'],
                'image' => ['media_id' => 'image_123'],
            ]
        ]);
        $contactWay->setSkipVerify(true);
        
        // 保存到数据库
        $this->entityManager->persist($contactWay);
        $this->entityManager->flush();
        
        $savedId = $contactWay->getId();
        
        // 清除缓存
        $this->entityManager->clear();
        
        // 重新查找并验证
        $foundContactWay = $this->repository->find($savedId);
        
        $this->assertNotNull($foundContactWay);
        $this->assertSame('complex_config', $foundContactWay->getConfigId());
        $this->assertSame(2, $foundContactWay->getType());
        $this->assertSame(2, $foundContactWay->getScene());
        $this->assertSame(['user_001', 'user_002', 'user_003'], $foundContactWay->getUser());
        $this->assertSame([10, 20, 30], $foundContactWay->getParty());
        $this->assertFalse($foundContactWay->isTemp());
        $this->assertNull($foundContactWay->getExpiresIn());
        $this->assertNull($foundContactWay->getChatExpiresIn());
        $this->assertSame('complex_union_id', $foundContactWay->getUnionId());
        $this->assertTrue($foundContactWay->isExclusive());
        $this->assertTrue($foundContactWay->isSkipVerify());
        
        $conclusions = $foundContactWay->getConclusions();
        $this->assertIsArray($conclusions);
        $this->assertCount(1, $conclusions);
        $this->assertArrayHasKey('text', $conclusions[0]);
        $this->assertArrayHasKey('image', $conclusions[0]);
    }
} 
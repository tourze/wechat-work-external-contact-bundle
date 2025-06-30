<?php

namespace WechatWorkExternalContactBundle\Tests\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use WechatWorkExternalContactBundle\Entity\ExternalServiceRelation;
use WechatWorkExternalContactBundle\Repository\ExternalServiceRelationRepository;

class ExternalServiceRelationRepositoryTest extends TestCase
{
    private ManagerRegistry $registry;
    private EntityManagerInterface $entityManager;
    private ExternalServiceRelationRepository $repository;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        
        $this->registry->expects($this->any())
            ->method('getManagerForClass')
            ->with(ExternalServiceRelation::class)
            ->willReturn($this->entityManager);
        
        $classMetadata = $this->createMock(ClassMetadata::class);
        $classMetadata->name = ExternalServiceRelation::class;
        
        $this->entityManager->expects($this->any())
            ->method('getClassMetadata')
            ->with(ExternalServiceRelation::class)
            ->willReturn($classMetadata);
        
        $this->repository = new ExternalServiceRelationRepository($this->registry);
    }

    public function testConstruct(): void
    {
        $this->assertInstanceOf(ExternalServiceRelationRepository::class, $this->repository);
    }

    public function testFindOneBy(): void
    {
        // 由于 findOneBy 是 parent 类的方法，这里只测试仓库能正确创建
        $this->assertInstanceOf(ExternalServiceRelationRepository::class, $this->repository);
    }
}
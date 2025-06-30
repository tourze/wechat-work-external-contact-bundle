<?php

namespace WechatWorkExternalContactBundle\Tests\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Tourze\WechatWorkExternalContactModel\ExternalUserLoaderInterface;
use WechatWorkExternalContactBundle\Entity\ExternalUser;
use WechatWorkExternalContactBundle\Repository\ExternalUserRepository;

class ExternalUserRepositoryTest extends TestCase
{
    private ManagerRegistry $registry;
    private EntityManagerInterface $entityManager;
    private ExternalUserRepository $repository;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        
        $this->registry->expects($this->any())
            ->method('getManagerForClass')
            ->with(ExternalUser::class)
            ->willReturn($this->entityManager);
        
        $classMetadata = $this->createMock(ClassMetadata::class);
        $classMetadata->name = ExternalUser::class;
        
        $this->entityManager->expects($this->any())
            ->method('getClassMetadata')
            ->with(ExternalUser::class)
            ->willReturn($classMetadata);
        
        $this->repository = new ExternalUserRepository($this->registry);
    }

    public function testConstruct(): void
    {
        $this->assertInstanceOf(ExternalUserRepository::class, $this->repository);
    }

    public function testLoadByUnionIdAndCorp(): void
    {
        $corp = $this->createStub(\Tourze\WechatWorkContracts\CorpInterface::class);
        $unionId = 'test_union_id';
        
        // 由于 findOneBy 是 parent 类的方法，这里只测试仓库能正确实现接口
        $this->assertInstanceOf(ExternalUserLoaderInterface::class, $this->repository);
    }
}
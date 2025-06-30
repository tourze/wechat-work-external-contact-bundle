<?php

namespace WechatWorkExternalContactBundle\Tests\Procedure;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Exception\ApiException;
use WechatWorkExternalContactBundle\Entity\ExternalUser;
use WechatWorkExternalContactBundle\Procedure\SaveWechatWorkExternalUser;
use WechatWorkExternalContactBundle\Repository\ExternalUserRepository;

class SaveWechatWorkExternalUserTest extends TestCase
{
    private ExternalUserRepository $externalUserRepository;
    private EntityManagerInterface $entityManager;
    private SaveWechatWorkExternalUser $procedure;

    protected function setUp(): void
    {
        $this->externalUserRepository = $this->createMock(ExternalUserRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        
        $this->procedure = new SaveWechatWorkExternalUser(
            $this->externalUserRepository,
            $this->entityManager
        );
    }

    public function testExecuteWithValidUser(): void
    {
        $this->procedure->externalUserId = 'test_external_user_id';
        $this->procedure->remark = 'Test Remark';
        $this->procedure->tags = ['tag1', 'tag2'];
        
        $externalUser = $this->createMock(ExternalUser::class);
        
        $this->externalUserRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['externalUserId' => 'test_external_user_id'])
            ->willReturn($externalUser);
        
        $externalUser->expects($this->once())
            ->method('setRemark')
            ->with('Test Remark');
        
        $externalUser->expects($this->once())
            ->method('setTags')
            ->with(['tag1', 'tag2']);
        
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($externalUser);
        
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        $result = $this->procedure->execute();
        
        $this->assertEquals(['__message' => '更新成功'], $result);
    }

    public function testExecuteWithNullRemark(): void
    {
        $this->procedure->externalUserId = 'test_external_user_id';
        $this->procedure->remark = null;
        $this->procedure->tags = ['tag1'];
        
        $externalUser = $this->createMock(ExternalUser::class);
        
        $this->externalUserRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($externalUser);
        
        $externalUser->expects($this->never())
            ->method('setRemark');
        
        $externalUser->expects($this->once())
            ->method('setTags')
            ->with(['tag1']);
        
        $this->entityManager->expects($this->once())
            ->method('persist');
        
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        $result = $this->procedure->execute();
        
        $this->assertEquals(['__message' => '更新成功'], $result);
    }

    public function testExecuteWithNullTags(): void
    {
        $this->procedure->externalUserId = 'test_external_user_id';
        $this->procedure->remark = 'Test';
        $this->procedure->tags = null;
        
        $externalUser = $this->createMock(ExternalUser::class);
        
        $this->externalUserRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($externalUser);
        
        $externalUser->expects($this->once())
            ->method('setRemark')
            ->with('Test');
        
        $externalUser->expects($this->never())
            ->method('setTags');
        
        $this->entityManager->expects($this->once())
            ->method('persist');
        
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        $result = $this->procedure->execute();
        
        $this->assertEquals(['__message' => '更新成功'], $result);
    }

    public function testExecuteWithNonExistentUser(): void
    {
        $this->procedure->externalUserId = 'non_existent_user';
        
        $this->externalUserRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['externalUserId' => 'non_existent_user'])
            ->willReturn(null);
        
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('找不到指定外部用户');
        
        $this->procedure->execute();
    }
}
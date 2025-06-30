<?php

namespace WechatWorkExternalContactBundle\Tests\Procedure;

use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\JsonRPC\Core\Exception\ApiException;
use WechatWorkExternalContactBundle\Entity\ExternalUser;
use WechatWorkExternalContactBundle\Event\GetExternalUserDetailEvent;
use WechatWorkExternalContactBundle\Procedure\GetWechatWorkExternalUserDetail;
use WechatWorkExternalContactBundle\Repository\ExternalUserRepository;

class GetWechatWorkExternalUserDetailTest extends TestCase
{
    private ExternalUserRepository $externalUserRepository;
    private EventDispatcherInterface $eventDispatcher;
    private GetWechatWorkExternalUserDetail $procedure;

    protected function setUp(): void
    {
        $this->externalUserRepository = $this->createMock(ExternalUserRepository::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        
        $this->procedure = new GetWechatWorkExternalUserDetail(
            $this->externalUserRepository,
            $this->eventDispatcher
        );
    }

    public function testExecuteWithValidExternalUser(): void
    {
        $this->procedure->entry = 'single_kf_tools';
        $this->procedure->shareTicket = 'test_ticket';
        $this->procedure->externalUserId = 'test_external_user_id';
        
        $externalUser = $this->createMock(ExternalUser::class);
        $externalUser->expects($this->once())
            ->method('retrieveApiArray')
            ->willReturn(['id' => 'test_external_user_id', 'name' => 'Test User']);
        
        $this->externalUserRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['externalUserId' => 'test_external_user_id'])
            ->willReturn($externalUser);
        
        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function ($event) {
                return $event instanceof GetExternalUserDetailEvent
                    && $event->getResult() === ['id' => 'test_external_user_id', 'name' => 'Test User'];
            }));
        
        $result = $this->procedure->execute();
        
        $this->assertEquals(['id' => 'test_external_user_id', 'name' => 'Test User'], $result);
    }

    public function testExecuteWithNullExternalUser(): void
    {
        $this->procedure->entry = 'single_kf_tools';
        $this->procedure->externalUserId = 'non_existent_user';
        
        $this->externalUserRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['externalUserId' => 'non_existent_user'])
            ->willReturn(null);
        
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('找不到指定外部用户');
        
        $this->procedure->execute();
    }

    public function testExecuteWithEventModifyingResult(): void
    {
        $this->procedure->entry = 'single_kf_tools';
        $this->procedure->externalUserId = 'test_external_user_id';
        
        $externalUser = $this->createMock(ExternalUser::class);
        $externalUser->expects($this->once())
            ->method('retrieveApiArray')
            ->willReturn(['id' => 'test_external_user_id']);
        
        $this->externalUserRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($externalUser);
        
        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->willReturnCallback(function (GetExternalUserDetailEvent $event) {
                $event->setResult(['id' => 'test_external_user_id', 'modified' => true]);
                return $event;
            });
        
        $result = $this->procedure->execute();
        
        $this->assertEquals(['id' => 'test_external_user_id', 'modified' => true], $result);
    }

    public function testExecuteWithNullEventResult(): void
    {
        $this->procedure->entry = 'single_kf_tools';
        $this->procedure->externalUserId = 'test_external_user_id';
        
        $externalUser = $this->createMock(ExternalUser::class);
        $externalUser->expects($this->once())
            ->method('retrieveApiArray')
            ->willReturn(['id' => 'test_external_user_id']);
        
        $this->externalUserRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($externalUser);
        
        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->willReturnCallback(function (GetExternalUserDetailEvent $event) {
                $event->setResult([]);
                return $event;
            });
        
        $result = $this->procedure->execute();
        
        $this->assertEquals([], $result);
    }
}
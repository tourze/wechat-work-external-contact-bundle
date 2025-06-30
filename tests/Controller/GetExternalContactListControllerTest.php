<?php

namespace WechatWorkExternalContactBundle\Tests\Controller;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Repository\CorpRepository;
use WechatWorkBundle\Service\WorkService;
use WechatWorkExternalContactBundle\Controller\GetExternalContactListController;
use WechatWorkExternalContactBundle\Request\GetExternalContactListRequest;

class GetExternalContactListControllerTest extends TestCase
{
    private CorpRepository $corpRepository;
    private AgentRepository $agentRepository;
    private WorkService $workService;
    private GetExternalContactListController $controller;

    protected function setUp(): void
    {
        $this->corpRepository = $this->createMock(CorpRepository::class);
        $this->agentRepository = $this->createMock(AgentRepository::class);
        $this->workService = $this->createMock(WorkService::class);
        
        $this->controller = new GetExternalContactListController(
            $this->corpRepository,
            $this->agentRepository,
            $this->workService
        );
        
        // 设置 container 以支持 json() 方法
        $container = new Container();
        $this->controller->setContainer($container);
    }

    public function testInvokeWithValidData(): void
    {
        $request = new Request();
        $request->query->set('corpId', 'test_corp_id');
        $request->query->set('userId', 'test_user_id');
        
        $agent = $this->createMock(AgentInterface::class);
        
        $this->corpRepository->expects($this->once())
            ->method('find')
            ->with('test_corp_id')
            ->willReturn(null);
            
        $this->corpRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['corpId' => 'test_corp_id'])
            ->willReturn((object)['id' => 1]);
            
        $this->agentRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($agent);
            
        $expectedResponse = ['external_userid' => ['user1', 'user2']];
        
        $this->workService->expects($this->once())
            ->method('request')
            ->with($this->isInstanceOf(GetExternalContactListRequest::class))
            ->willReturn($expectedResponse);
            
        $response = $this->controller->__invoke($request);
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($expectedResponse, json_decode($response->getContent(), true));
    }

    public function testInvokeWithAgentId(): void
    {
        $request = new Request();
        $request->query->set('corpId', 'test_corp_id');
        $request->query->set('agentId', 'test_agent_id');
        $request->query->set('userId', 'test_user_id');
        
        $corp = (object)['id' => 1];
        $agent = $this->createMock(AgentInterface::class);
        
        $this->corpRepository->expects($this->once())
            ->method('find')
            ->with('test_corp_id')
            ->willReturn($corp);
            
        $this->agentRepository->expects($this->once())
            ->method('findOneBy')
            ->with([
                'corp' => $corp,
                'agentId' => 'test_agent_id'
            ])
            ->willReturn($agent);
            
        $expectedResponse = ['external_userid' => []];
        
        $this->workService->expects($this->once())
            ->method('request')
            ->willReturn($expectedResponse);
            
        $response = $this->controller->__invoke($request);
        
        $this->assertInstanceOf(JsonResponse::class, $response);
    }
}
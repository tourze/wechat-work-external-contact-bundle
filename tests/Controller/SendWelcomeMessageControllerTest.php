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
use WechatWorkExternalContactBundle\Controller\SendWelcomeMessageController;
use WechatWorkExternalContactBundle\Request\SendWelcomeMessageRequest;

class SendWelcomeMessageControllerTest extends TestCase
{
    private CorpRepository $corpRepository;
    private AgentRepository $agentRepository;
    private WorkService $workService;
    private SendWelcomeMessageController $controller;

    protected function setUp(): void
    {
        $this->corpRepository = $this->createMock(CorpRepository::class);
        $this->agentRepository = $this->createMock(AgentRepository::class);
        $this->workService = $this->createMock(WorkService::class);
        
        $this->controller = new SendWelcomeMessageController(
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
        $request->query->set('welcomeCode', 'test_welcome_code');
        
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
            
        $expectedResponse = ['errcode' => 0, 'errmsg' => 'ok'];
        
        $this->workService->expects($this->once())
            ->method('request')
            ->with($this->callback(function ($request) {
                return $request instanceof SendWelcomeMessageRequest
                    && $request->getWelcomeCode() === 'test_welcome_code'
                    && str_starts_with($request->getTextContent(), '哈哈');
            }))
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
        $request->query->set('welcomeCode', 'test_welcome_code');
        
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
            
        $expectedResponse = ['errcode' => 0];
        
        $this->workService->expects($this->once())
            ->method('request')
            ->willReturn($expectedResponse);
            
        $response = $this->controller->__invoke($request);
        
        $this->assertInstanceOf(JsonResponse::class, $response);
    }
}
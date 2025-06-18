<?php

namespace WechatWorkExternalContactBundle\Tests\Controller;

use Doctrine\Common\Collections\Criteria;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Repository\CorpRepository;
use WechatWorkBundle\Service\WorkService;
use WechatWorkExternalContactBundle\Controller\TestController;

/**
 * TestController测试
 * 
 * 测试关注点：
 * - 请求参数处理
 * - 服务调用逻辑
 * - 代理获取逻辑
 * - 响应格式
 */
class TestControllerTest extends TestCase
{
    private CorpRepository&MockObject $corpRepository;
    private AgentRepository&MockObject $agentRepository;
    private WorkService&MockObject $workService;
    private ContainerInterface&MockObject $container;
    private SerializerInterface&MockObject $serializer;
    private TestController $controller;

    protected function setUp(): void
    {
        $this->corpRepository = $this->createMock(CorpRepository::class);
        $this->agentRepository = $this->createMock(AgentRepository::class);
        $this->workService = $this->createMock(WorkService::class);
        $this->container = $this->createMock(ContainerInterface::class);
        $this->serializer = $this->createMock(SerializerInterface::class);

        $this->controller = new TestController(
            $this->corpRepository,
            $this->agentRepository,
            $this->workService
        );

        // 设置容器，解决AbstractController对容器的依赖
        $this->container->method('has')
            ->willReturnCallback(function ($id) {
                return $id === 'serializer';
            });
        
        $this->container->method('get')
            ->willReturnCallback(function ($id) {
                if ($id === 'serializer') {
                    return $this->serializer;
                }
                return null;
            });

        // Mock serializer to return JSON string
        $this->serializer->method('serialize')
            ->willReturnCallback(function ($data, $format) {
                if ($format === 'json') {
                    return json_encode($data);
                }
                return '';
            });
        
        $this->controller->setContainer($this->container);
    }

    public function testContactListWithValidParameters(): void
    {
        // 准备数据
        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);
        $agent->setAgentId('12345');

        $request = new Request(['corpId' => '1', 'agentId' => '12345', 'userId' => 'user123']);

        $expectedResponse = [
            'errcode' => 0,
            'info_list' => [
                [
                    'userid' => 'user123',
                    'name' => '测试用户',
                    'avatar' => 'https://example.com/avatar.png'
                ]
            ]
        ];

        // 设置期望
        $this->corpRepository->expects($this->once())
            ->method('find')
            ->with('1')
            ->willReturn($corp);

        $this->agentRepository->expects($this->once())
            ->method('findOneBy')
            ->with([
                'corp' => $corp,
                'agentId' => '12345'
            ])
            ->willReturn($agent);

        $this->workService->expects($this->once())
            ->method('request')
            ->willReturn($expectedResponse);

        // 执行测试
        $response = $this->controller->contactList($request);

        // 验证结果
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertEquals($expectedResponse, json_decode($response->getContent(), true));
    }

    public function testContactListWithCorpIdLookup(): void
    {
        // 测试通过corpId查找企业的情况
        $corp = new Corp();
        $corp->setCorpId('wx123456');
        
        $agent = new Agent();
        $agent->setCorp($corp);

        $request = new Request(['corpId' => 'wx123456', 'userId' => 'user123']);

        // 第一次find返回null，第二次findOneBy返回corp
        $this->corpRepository->expects($this->once())
            ->method('find')
            ->with('wx123456')
            ->willReturn(null);

        $this->corpRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['corpId' => 'wx123456'])
            ->willReturn($corp);

        // 没有agentId，应该获取第一个代理
        $this->agentRepository->expects($this->once())
            ->method('findOneBy')
            ->with(
                ['corp' => $corp],
                ['id' => Criteria::ASC]
            )
            ->willReturn($agent);

        $this->workService->expects($this->once())
            ->method('request')
            ->willReturn(['errcode' => 0]);

        // 执行测试
        $response = $this->controller->contactList($request);

        // 验证结果
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testSendWelcomeMsgWithUniqueContent(): void
    {
        // 准备数据
        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);

        $request = new Request([
            'corpId' => '1',
            'agentId' => '12345',
            'welcomeCode' => 'welcome123'
        ]);

        $expectedResponse = [
            'errcode' => 0,
            'errmsg' => 'ok'
        ];

        // 设置期望
        $this->corpRepository->expects($this->once())
            ->method('find')
            ->willReturn($corp);

        $this->agentRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($agent);

        $this->workService->expects($this->once())
            ->method('request')
            ->willReturn($expectedResponse);

        // 执行测试
        $response = $this->controller->sendWelcomeMsg($request);

        // 验证结果
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expectedResponse, json_decode($response->getContent(), true));
    }

    public function testGetAgentWithSpecificAgentId(): void
    {
        // 测试getAgent方法通过特定agentId获取代理
        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);
        $agent->setAgentId('specific_agent');

        $request = new Request([
            'corpId' => '1',
            'agentId' => 'specific_agent',
            'userId' => 'test_user'
        ]);

        $this->corpRepository->expects($this->once())
            ->method('find')
            ->willReturn($corp);

        $this->agentRepository->expects($this->once())
            ->method('findOneBy')
            ->with([
                'corp' => $corp,
                'agentId' => 'specific_agent'
            ])
            ->willReturn($agent);

        $this->workService->expects($this->once())
            ->method('request')
            ->willReturn(['errcode' => 0]);

        // 通过contactList方法间接测试getAgent
        $response = $this->controller->contactList($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetAgentWithoutAgentId(): void
    {
        // 测试没有agentId时获取第一个代理
        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);

        $request = new Request(['corpId' => '1', 'userId' => 'test_user']);

        $this->corpRepository->expects($this->once())
            ->method('find')
            ->willReturn($corp);

        $this->agentRepository->expects($this->once())
            ->method('findOneBy')
            ->with(
                ['corp' => $corp],
                ['id' => Criteria::ASC]
            )
            ->willReturn($agent);

        $this->workService->expects($this->once())
            ->method('request')
            ->willReturn(['errcode' => 0]);

        // 通过contactList方法间接测试getAgent
        $response = $this->controller->contactList($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testControllerConstruction(): void
    {
        // 测试控制器构造函数
        $reflection = new \ReflectionClass($this->controller);
        $constructor = $reflection->getConstructor();
        
        $this->assertNotNull($constructor);
        $parameters = $constructor->getParameters();
        $this->assertCount(3, $parameters);
        
        $this->assertEquals('corpRepository', $parameters[0]->getName());
        $this->assertEquals('agentRepository', $parameters[1]->getName());
        $this->assertEquals('workService', $parameters[2]->getName());
    }

    public function testRouteAttributes(): void
    {
        // 测试路由属性
        $reflection = new \ReflectionClass($this->controller);
        
        // 检查类级别的路由属性
        $classAttributes = $reflection->getAttributes();
        $this->assertNotEmpty($classAttributes);
        
        // 检查方法级别的路由属性
        $methods = ['contactList', 'sendWelcomeMsg'];
        foreach ($methods as $methodName) {
            $method = $reflection->getMethod($methodName);
            $attributes = $method->getAttributes();
            $this->assertNotEmpty($attributes, "Method {$methodName} should have route attributes");
        }
    }

    public function testResponseFormat(): void
    {
        // 测试响应格式一致性
        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);

        $request = new Request(['corpId' => '1', 'userId' => 'test_user']);

        $this->corpRepository->method('find')->willReturn($corp);
        $this->agentRepository->method('findOneBy')->willReturn($agent);
        $this->workService->method('request')->willReturn(['test' => 'data']);

        $response = $this->controller->contactList($request);

        // 验证响应格式
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertJson($response->getContent());
        
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(['test' => 'data'], $data);
    }
} 
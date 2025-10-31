<?php

namespace WechatWorkExternalContactBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Repository\CorpRepository;
use WechatWorkBundle\Service\WorkServiceInterface;
use WechatWorkExternalContactBundle\Request\GetExternalContactListRequest;

final class GetExternalContactListController extends AbstractController
{
    public function __construct(
        private readonly CorpRepository $corpRepository,
        private readonly AgentRepository $agentRepository,
        private readonly WorkServiceInterface $workService,
    ) {
    }

    #[Route(path: '/wechat/work/test/get_external_contact_list', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        $agent = $this->getAgent($request);

        $userId = $request->query->get('userId');
        if (!is_string($userId)) {
            throw $this->createNotFoundException('userId parameter is required and must be string');
        }

        $apiRequest = new GetExternalContactListRequest();
        $apiRequest->setAgent($agent);
        $apiRequest->setUserId($userId);
        $response = $this->workService->request($apiRequest);

        return $this->json($response);
    }

    private function getAgent(Request $request): ?AgentInterface
    {
        $corpId = $request->query->get('corpId');
        $corp = null;

        if (is_numeric($corpId)) {
            $corp = $this->corpRepository->findOneBy(['id' => $corpId]);
        }

        if (null === $corp) {
            $corp = $this->corpRepository->findOneBy([
                'corpId' => $corpId,
            ]);
        }

        if ($request->query->has('agentId')) {
            return $this->agentRepository->findOneBy([
                'corp' => $corp,
                'agentId' => $request->query->get('agentId'),
            ]);
        }

        // 默认拿第一个
        return $this->agentRepository->findOneBy([
            'corp' => $corp,
        ], ['id' => 'ASC']);
    }
}

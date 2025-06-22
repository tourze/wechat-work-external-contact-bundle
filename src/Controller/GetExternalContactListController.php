<?php

namespace WechatWorkExternalContactBundle\Controller;

use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Repository\CorpRepository;
use WechatWorkBundle\Service\WorkService;
use WechatWorkExternalContactBundle\Request\GetExternalContactListRequest;

class GetExternalContactListController extends AbstractController
{
    public function __construct(
        private readonly CorpRepository $corpRepository,
        private readonly AgentRepository $agentRepository,
        private readonly WorkService $workService,
    ) {}

    #[Route(path: '/wechat/work/test/get_external_contact_list')]
    public function __invoke(Request $request): Response
    {
        $agent = $this->getAgent($request);

        $userId = $request->query->get('userId');

        $apiRequest = new GetExternalContactListRequest();
        $apiRequest->setAgent($agent);
        $apiRequest->setUserId($userId);
        $response = $this->workService->request($apiRequest);

        return $this->json($response);
    }

    private function getAgent(Request $request): ?AgentInterface
    {
        $corp = $this->corpRepository->find($request->query->get('corpId'));
        if ($corp === null) {
            $corp = $this->corpRepository->findOneBy([
                'corpId' => $request->query->get('corpId'),
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
        ], ['id' => Criteria::ASC]);
    }
}

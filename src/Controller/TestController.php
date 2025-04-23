<?php

namespace WechatWorkExternalContactBundle\Controller;

use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use WechatWorkBundle\Entity\AccessTokenAware;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Repository\CorpRepository;
use WechatWorkBundle\Service\WorkService;
use WechatWorkExternalContactBundle\Request\ContactWay\AddContactWayRequest;
use WechatWorkExternalContactBundle\Request\GetExternalContactListRequest;
use WechatWorkExternalContactBundle\Request\SendWelcomeMessageRequest;

#[Route(path: '/wechat/work/test')]
class TestController extends AbstractController
{
    public function __construct(
        private readonly CorpRepository $corpRepository,
        private readonly AgentRepository $agentRepository,
        private readonly WorkService $workService,
    ) {
    }

    #[Route('/get_external_contact_list')]
    public function contactList(Request $request): Response
    {
        $agent = $this->getAgent($request);

        $userId = $request->query->get('userId');

        $request = new GetExternalContactListRequest();
        $request->setAgent($agent);
        $request->setUserId($userId);
        $response = $this->workService->request($request);

        return $this->json($response);
    }

    #[Route('/add_contact_way')]
    public function addContactWay(Request $request): Response
    {
        $agent = $this->getAgent($request);

        $user = $request->query->get('user');
        $user = explode(',', $user);

        $request = new AddContactWayRequest();
        $request->setAgent($agent);
        $request->setType(1);
        $request->setScene(2);
        $request->setUser($user);
        $response = $this->workService->request($request);

        return $this->json($response);
    }

    #[Route('/send_welcome_msg')]
    public function sendWelcomeMsg(Request $request): Response
    {
        $agent = $this->getAgent($request);

        $welcomeCode = $request->query->get('welcomeCode');

        $request = new SendWelcomeMessageRequest();
        $request->setAgent($agent);
        $request->setWelcomeCode($welcomeCode);
        $request->setTextContent('哈哈' . uniqid());
        $response = $this->workService->request($request);

        return $this->json($response);
    }

    protected function getAgent(Request $request): AccessTokenAware
    {
        $corp = $this->corpRepository->find($request->query->get('corpId'));
        if (!$corp) {
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

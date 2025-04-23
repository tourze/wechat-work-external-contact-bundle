<?php

namespace WechatWorkExternalContactBundle\MessageHandler;

use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkExternalContactBundle\Entity\ExternalServiceRelation;
use WechatWorkExternalContactBundle\Entity\ExternalUser;
use WechatWorkExternalContactBundle\Message\SaveExternalContactListItemMessage;
use WechatWorkExternalContactBundle\Repository\ExternalServiceRelationRepository;
use WechatWorkExternalContactBundle\Repository\ExternalUserRepository;
use WechatWorkStaffBundle\Entity\User;
use WechatWorkStaffBundle\Repository\UserRepository;

#[AsMessageHandler]
class SaveExternalContactListItemHandler
{
    public function __construct(
        private readonly ExternalUserRepository $externalUserRepository,
        private readonly UserRepository $userRepository,
        private readonly ExternalServiceRelationRepository $externalServiceRelationRepository,
        private readonly AgentRepository $agentRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(SaveExternalContactListItemMessage $message): void
    {
        $item = $message->getItem();
        $agent = $this->agentRepository->find($message->getAgentId());
        if (!$agent) {
            return;
        }

        $externalUser = null;
        if (isset($item['external_userid']) && !$externalUser) {
            $externalUser = $this->externalUserRepository->findOneBy([
                'externalUserId' => $item['external_userid'],
                'corp' => $agent->getCorp(),
            ]);
        }
        if (isset($item['tmp_openid']) && !$externalUser) {
            $externalUser = $this->externalUserRepository->findOneBy([
                'tmpOpenId' => $item['external_userid'],
                'corp' => $agent->getCorp(),
            ]);
        }

        // 外部联系人信息同步
        if (!$externalUser) {
            $externalUser = new ExternalUser();
            $externalUser->setCorp($agent->getCorp());
        }
        if (!$externalUser->getRawData()) {
            $externalUser->setRawData($item);
        }
        if (isset($item['is_customer'])) {
            $externalUser->setCustomer((bool) $item['is_customer']);
        }
        if (isset($item['tmp_openid'])) {
            $externalUser->setTmpOpenId($item['tmp_openid']);
        }
        if (isset($item['external_userid'])) {
            $externalUser->setExternalUserId($item['external_userid']);
        }
        if (isset($item['name'])) {
            $externalUser->setNickname($item['name']);
        }
        if (isset($item['add_time'])) {
            $externalUser->setAddTime(Carbon::parse($item['add_time']));
        }
        $this->entityManager->persist($externalUser);
        $this->entityManager->flush();

        if (isset($item['follow_userid'])) {
            $user = $this->userRepository->findOneBy([
                'corp' => $agent->getCorp(),
                'userId' => $item['follow_userid'],
            ]);
            if (!$user) {
                $user = new User();
                $user->setCorp($agent->getCorp());
                $user->setAgent($agent);
                $user->setUserId($item['follow_userid']);
                $user->setName($item['follow_userid']);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }
            // 保存关系
            $relation = $this->externalServiceRelationRepository->findOneBy([
                'user' => $user,
                'externalUser' => $externalUser,
            ]);
            if (!$relation) {
                $relation = new ExternalServiceRelation();
                $relation->setUser($user);
                $relation->setExternalUser($externalUser);
            }
            $relation->setCorp($agent->getCorp());
            if (isset($item['add_time'])) {
                $relation->setAddExternalContactTime(Carbon::parse($item['add_time']));
            }
            $this->entityManager->persist($relation);
            $this->entityManager->flush();
        }
    }
}

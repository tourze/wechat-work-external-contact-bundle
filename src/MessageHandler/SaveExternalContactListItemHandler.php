<?php

namespace WechatWorkExternalContactBundle\MessageHandler;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Tourze\WechatWorkContracts\UserLoaderInterface;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkExternalContactBundle\Entity\ExternalServiceRelation;
use WechatWorkExternalContactBundle\Entity\ExternalUser;
use WechatWorkExternalContactBundle\Message\SaveExternalContactListItemMessage;
use WechatWorkExternalContactBundle\Repository\ExternalServiceRelationRepository;
use WechatWorkExternalContactBundle\Repository\ExternalUserRepository;

#[AsMessageHandler]
class SaveExternalContactListItemHandler
{
    public function __construct(
        private readonly ExternalUserRepository $externalUserRepository,
        private readonly UserLoaderInterface $userLoader,
        private readonly ExternalServiceRelationRepository $externalServiceRelationRepository,
        private readonly AgentRepository $agentRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public function __invoke(SaveExternalContactListItemMessage $message): void
    {
        $item = $message->getItem();
        $agent = $this->agentRepository->find($message->getAgentId());
        if ($agent === null) {
            return;
        }

        $externalUser = null;
        if (isset($item['external_userid'])) {
            $externalUser = $this->externalUserRepository->findOneBy([
                'externalUserId' => $item['external_userid'],
                'corp' => $agent->getCorp(),
            ]);
        }
        if (isset($item['tmp_openid']) && $externalUser === null) {
            $externalUser = $this->externalUserRepository->findOneBy([
                'tmpOpenId' => $item['external_userid'],
                'corp' => $agent->getCorp(),
            ]);
        }

        // 外部联系人信息同步
        if ($externalUser === null) {
            $externalUser = new ExternalUser();
            $externalUser->setCorp($agent->getCorp());
        }
        if ($externalUser->getRawData() === null) {
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
            $externalUser->setAddTime(CarbonImmutable::parse($item['add_time']));
        }
        $this->entityManager->persist($externalUser);
        $this->entityManager->flush();

        if (isset($item['follow_userid'])) {
            $user = $this->userLoader->loadUserByUserIdAndCorp($item['follow_userid'], $agent->getCorp());
            if ($user === null) {
                $user = $this->userLoader->createUser(
                    $agent->getCorp(),
                    $agent,
                    $item['follow_userid'],
                    $item['follow_userid'],
                );
            }
            // 保存关系
            $relation = $this->externalServiceRelationRepository->findOneBy([
                'user' => $user,
                'externalUser' => $externalUser,
            ]);
            if ($relation === null) {
                $relation = new ExternalServiceRelation();
                $relation->setUser($user);
                $relation->setExternalUser($externalUser);
            }
            $relation->setCorp($agent->getCorp());
            if (isset($item['add_time'])) {
                $relation->setAddExternalContactTime(CarbonImmutable::parse($item['add_time']));
            }
            $this->entityManager->persist($relation);
            $this->entityManager->flush();
        }
    }
}

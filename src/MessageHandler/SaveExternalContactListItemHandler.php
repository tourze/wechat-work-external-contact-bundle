<?php

namespace WechatWorkExternalContactBundle\MessageHandler;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Tourze\WechatWorkContracts\AgentInterface;
use Tourze\WechatWorkContracts\UserInterface;
use Tourze\WechatWorkContracts\UserLoaderInterface;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkExternalContactBundle\Entity\ExternalServiceRelation;
use WechatWorkExternalContactBundle\Entity\ExternalUser;
use WechatWorkExternalContactBundle\Exception\ExternalContactCorpException;
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
    ) {
    }

    public function __invoke(SaveExternalContactListItemMessage $message): void
    {
        $item = $message->getItem();
        $agent = $this->agentRepository->find($message->getAgentId());

        if (null === $agent) {
            return;
        }

        $externalUser = $this->getOrCreateExternalUser($item, $agent);
        $this->updateExternalUserData($externalUser, $item);
        $this->saveExternalUser($externalUser);

        $this->handleServiceRelation($item, $agent, $externalUser);
    }

    /**
     * @param array<string, mixed> $item
     */
    private function getOrCreateExternalUser(array $item, AgentInterface $agent): ExternalUser
    {
        $externalUser = $this->findExistingExternalUser($item, $agent);

        if (null === $externalUser) {
            $externalUser = new ExternalUser();
            $externalUser->setCorp($agent->getCorp());
        }

        return $externalUser;
    }

    /**
     * @param array<string, mixed> $item
     */
    private function findExistingExternalUser(array $item, AgentInterface $agent): ?ExternalUser
    {
        if (isset($item['external_userid'])) {
            $externalUser = $this->externalUserRepository->findOneBy([
                'externalUserId' => $item['external_userid'],
                'corp' => $agent->getCorp(),
            ]);

            if (null !== $externalUser) {
                return $externalUser;
            }
        }

        if (isset($item['tmp_openid'])) {
            return $this->externalUserRepository->findOneBy([
                'tmpOpenId' => $item['external_userid'],
                'corp' => $agent->getCorp(),
            ]);
        }

        return null;
    }

    /**
     * @param array<string, mixed> $item
     */
    private function updateExternalUserData(ExternalUser $externalUser, array $item): void
    {
        if (null === $externalUser->getRawData()) {
            $externalUser->setRawData($item);
        }

        $this->updateExternalUserFields($externalUser, $item);
    }

    /**
     * @param array<string, mixed> $item
     */
    private function updateExternalUserFields(ExternalUser $externalUser, array $item): void
    {
        $fieldMapping = [
            'is_customer' => fn ($value) => $externalUser->setCustomer((bool) $value),
            'tmp_openid' => fn ($value) => $externalUser->setTmpOpenId($value),
            'external_userid' => fn ($value) => $externalUser->setExternalUserId($value),
            'name' => fn ($value) => $externalUser->setNickname($value),
            'add_time' => function ($value) use ($externalUser) {
                assert(is_string($value));
                $externalUser->setAddTime(CarbonImmutable::parse($value));
            },
        ];

        foreach ($fieldMapping as $field => $setter) {
            if (isset($item[$field])) {
                $setter($item[$field]);
            }
        }
    }

    private function saveExternalUser(ExternalUser $externalUser): void
    {
        $this->entityManager->persist($externalUser);
        $this->entityManager->flush();
    }

    /**
     * @param array<string, mixed> $item
     */
    private function handleServiceRelation(array $item, AgentInterface $agent, ExternalUser $externalUser): void
    {
        if (!isset($item['follow_userid'])) {
            return;
        }

        assert(is_string($item['follow_userid']));
        $user = $this->getOrCreateUser($item['follow_userid'], $agent);
        $relation = $this->getOrCreateServiceRelation($user, $externalUser, $agent);

        $this->updateServiceRelationTime($relation, $item);
        $this->saveServiceRelation($relation);
    }

    private function getOrCreateUser(string $followUserId, AgentInterface $agent): UserInterface
    {
        $corp = $agent->getCorp();
        if (null === $corp) {
            throw new ExternalContactCorpException('Agent corp cannot be null');
        }

        $user = $this->userLoader->loadUserByUserIdAndCorp($followUserId, $corp);

        if (null === $user) {
            $user = $this->userLoader->createUser(
                $corp,
                $agent,
                $followUserId,
                $followUserId,
            );
        }

        return $user;
    }

    private function getOrCreateServiceRelation(UserInterface $user, ExternalUser $externalUser, AgentInterface $agent): ExternalServiceRelation
    {
        $relation = $this->externalServiceRelationRepository->findOneBy([
            'user' => $user,
            'externalUser' => $externalUser,
        ]);

        if (null === $relation) {
            $relation = new ExternalServiceRelation();
            $relation->setUser($user);
            $relation->setExternalUser($externalUser);
        }

        $relation->setCorp($agent->getCorp());

        return $relation;
    }

    /**
     * @param array<string, mixed> $item
     */
    private function updateServiceRelationTime(ExternalServiceRelation $relation, array $item): void
    {
        if (isset($item['add_time'])) {
            assert(is_string($item['add_time']));
            $relation->setAddExternalContactTime(CarbonImmutable::parse($item['add_time']));
        }
    }

    private function saveServiceRelation(ExternalServiceRelation $relation): void
    {
        $this->entityManager->persist($relation);
        $this->entityManager->flush();
    }
}

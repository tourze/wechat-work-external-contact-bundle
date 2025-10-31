<?php

namespace WechatWorkExternalContactBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Service\WorkServiceInterface;
use WechatWorkExternalContactBundle\Exception\ExternalContactAgentException;
use WechatWorkExternalContactBundle\Message\SaveExternalContactListItemMessage;
use WechatWorkExternalContactBundle\Request\GetContactListRequest;

#[AsCronTask(expression: '30 4 * * *')]
#[AsCommand(name: self::NAME, description: '同步获取已服务的外部联系人')]
class SyncExternalContactListCommand extends Command
{
    public const NAME = 'wechat-work:sync-external-contact-list';

    public function __construct(
        private readonly AgentRepository $agentRepository,
        private readonly WorkServiceInterface $workService,
        private readonly MessageBusInterface $messageBus,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->agentRepository->findAll() as $agent) {
            $this->syncContactsForAgent($agent);
        }

        return Command::SUCCESS;
    }

    private function syncContactsForAgent(AgentInterface $agent): void
    {
        $cursor = null;
        do {
            $cursor = $this->processContactBatch($agent, $cursor);
        } while (null !== $cursor);
    }

    private function processContactBatch(AgentInterface $agent, ?string $cursor): ?string
    {
        $request = $this->createContactListRequest($agent, $cursor);
        /** @var array<string, mixed> $response */
        $response = $this->workService->request($request);

        assert(is_array($response));
        /** @var array<mixed> $infoList */
        $infoList = $response['info_list'] ?? [];
        $this->dispatchContactMessages($agent, $infoList);

        /** @var string|null $nextCursor */
        $nextCursor = $response['next_cursor'] ?? null;

        return $nextCursor;
    }

    private function createContactListRequest(AgentInterface $agent, ?string $cursor): GetContactListRequest
    {
        $request = new GetContactListRequest();
        $request->setAgent($agent);

        if (null !== $cursor) {
            $request->setCursor($cursor);
        }

        return $request;
    }

    /**
     * @param array<mixed> $infoList
     */
    private function dispatchContactMessages(AgentInterface $agent, array $infoList): void
    {
        foreach ($infoList as $item) {
            assert(is_array($item));
            /** @var array<string, mixed> $typedItem */
            $typedItem = $item;

            $message = new SaveExternalContactListItemMessage();
            // 需要数据库 ID，将 AgentInterface 转换为 Agent 实体
            if ($agent instanceof Agent) {
                $message->setAgentId((string) $agent->getId());
            } else {
                throw new ExternalContactAgentException('Agent must be an instance of Agent entity to get database ID');
            }
            $message->setItem($typedItem);
            $this->messageBus->dispatch($message);
        }
    }
}

<?php

namespace WechatWorkExternalContactBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Service\WorkService;
use WechatWorkExternalContactBundle\Message\SaveExternalContactListItemMessage;
use WechatWorkExternalContactBundle\Request\GetContactListRequest;

#[AsCronTask('30 4 * * *')]
#[AsCommand(name: 'wechat-work:sync-external-contact-list', description: '同步获取已服务的外部联系人')]
class SyncExternalContactListCommand extends Command
{
    public const NAME = 'sync-external-contact-list';

    public function __construct(
        private readonly AgentRepository $agentRepository,
        private readonly WorkService $workService,
        private readonly MessageBusInterface $messageBus,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->agentRepository->findAll() as $agent) {
            $cursor = null;
            do {
                $request = new GetContactListRequest();
                $request->setAgent($agent);
                if (null !== $cursor) {
                    $request->setCursor($cursor);
                }

                $response = $this->workService->request($request);
                $cursor = $response['next_cursor'] ?? null;

                if (isset($response['info_list'])) {
                    foreach ($response['info_list'] as $item) {
                        $message = new SaveExternalContactListItemMessage();
                        $message->setAgentId($agent->getId());
                        $message->setItem($item);
                        $this->messageBus->dispatch($message);
                    }
                }
            } while (null !== $cursor);
        }

        return Command::SUCCESS;
    }
}

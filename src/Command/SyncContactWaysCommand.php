<?php

namespace WechatWorkExternalContactBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Service\WorkService;
use WechatWorkExternalContactBundle\Entity\ContactWay;
use WechatWorkExternalContactBundle\Repository\ContactWayRepository;
use WechatWorkExternalContactBundle\Request\ContactWay\GetContactWayRequest;
use WechatWorkExternalContactBundle\Request\ContactWay\ListContactWayRequest;

#[AsCronTask('1 6 * * *')]
#[AsCommand(name: 'wechat-work:sync-contact-way', description: '同步获取联系我的方式')]
class SyncContactWaysCommand extends Command
{
    public function __construct(
        private readonly AgentRepository $agentRepository,
        private readonly ContactWayRepository $contactWayRepository,
        private readonly WorkService $workService,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->agentRepository->findAll() as $agent) {
            $cursor = null;

            do {
                $listRequest = new ListContactWayRequest();
                $listRequest->setAgent($agent);
                $listRequest->setCursor($cursor);

                $response = $this->workService->request($listRequest);
                $cursor = $response['next_cursor'] ?? null;

                foreach ($response['contact_way'] as $item) {
                    $way = $this->contactWayRepository->findOneBy(['configId' => $item['config_id']]);
                    if ($way) {
                        continue;
                    }

                    $way = new ContactWay();
                    $way->setAgent($agent);
                    $way->setCorp($agent->getCorp());
                    $way->setConfigId($item['config_id']);

                    $detailRequest = new GetContactWayRequest();
                    $detailRequest->setAgent($agent);
                    $detailRequest->setConfigId($way->getConfigId());
                    $detailResponse = $this->workService->request($detailRequest);

                    $way->setType($detailResponse['contact_way']['type']);
                    $way->setScene($detailResponse['contact_way']['scene']);
                    $way->setStyle($detailResponse['contact_way']['style']);
                    $way->setRemark($detailResponse['contact_way']['remark']);
                    $way->setSkipVerify($detailResponse['contact_way']['skip_verify']);
                    $way->setState($detailResponse['contact_way']['state']);
                    $way->setQrCode($detailResponse['contact_way']['qr_code']);
                    $way->setUser($detailResponse['contact_way']['user']);
                    $way->setParty($detailResponse['contact_way']['party']);
                    $way->setTemp($detailResponse['contact_way']['is_temp'] ?? null);
                    $way->setExpiresIn($detailResponse['contact_way']['expires_in'] ?? null);
                    $way->setChatExpiresIn($detailResponse['contact_way']['chat_expires_in'] ?? null);
                    $way->setUnionId($detailResponse['contact_way']['unionid'] ?? null);
                    $way->setConclusions($detailResponse['contact_way']['conclusions'] ?? null);
                    $this->entityManager->persist($way);
                    $this->entityManager->flush();
                }
            } while (null !== $cursor);
        }

        return Command::SUCCESS;
    }
}

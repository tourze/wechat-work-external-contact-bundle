<?php

namespace WechatWorkExternalContactBundle\Command;

use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use Tourze\WechatWorkContracts\UserLoaderInterface;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Service\WorkService;
use WechatWorkExternalContactBundle\Entity\UserBehaviorDataByUser;
use WechatWorkExternalContactBundle\Repository\UserBehaviorDataByUserRepository;
use WechatWorkExternalContactBundle\Request\GetFollowUserListRequest;
use WechatWorkExternalContactBundle\Request\GetUserBehaviorDataRequest;

#[AsCronTask('14 6 * * *')]
#[AsCommand(name: 'wechat-work:SyncUserBehaviorByUserCommand', description: '获取「联系客户统计」数据-单用户的数据')]
class SyncUserBehaviorByUserCommand extends Command
{
    public function __construct(
        private readonly AgentRepository $agentRepository,
        private readonly WorkService $workService,
        private readonly UserLoaderInterface $userLoader,
        private readonly UserBehaviorDataByUserRepository $dataByUserRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $endTime = Carbon::today();
        $startTime = $endTime->clone()->subWeek();

        foreach ($this->agentRepository->findAll() as $agent) {
            $userListRequest = new GetFollowUserListRequest();
            $userListRequest->setAgent($agent);
            $userListResponse = $this->workService->request($userListRequest);
            if (!isset($userListResponse['follow_user'])) {
                continue;
            }

            foreach ($userListResponse['follow_user'] as $userId) {
                $user = $this->userLoader->loadUserByUserIdAndCorp($userId, $agent->getCorp());
                if (!$user) {
                    continue;
                }

                $request = new GetUserBehaviorDataRequest();
                $request->setAgent($agent);
                $request->setUserIds([$userId]);
                $request->setStartTime($startTime);
                $request->setEndTime($endTime);
                $response = $this->workService->request($request);
                foreach ($response['behavior_data'] as $datum) {
                    $date = Carbon::createFromTimestamp($datum['stat_time'], date_default_timezone_get())->startOfDay();
                    $data = $this->dataByUserRepository->findOneBy([
                        'date' => $date,
                        'user' => $user,
                    ]);
                    if (!$data) {
                        $data = new UserBehaviorDataByUser();
                        $data->setDate($date);
                        $data->setUser($user);
                    }
                    $data->setNewApplyCount($datum['new_apply_cnt']);
                    $data->setNewContactCount($datum['new_contact_cnt']);
                    $data->setChatCount($datum['chat_cnt']);
                    $data->setMessageCount($datum['message_cnt']);
                    $data->setReplyPercentage($datum['reply_percentage']);
                    $data->setAvgReplyTime($datum['avg_reply_time']);
                    $data->setNegativeFeedbackCount($datum['negative_feedback_cnt']);
                    $this->entityManager->persist($data);
                    $this->entityManager->flush();
                }
            }
        }

        return Command::SUCCESS;
    }
}

<?php

namespace WechatWorkExternalContactBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemOperator;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use WechatWorkExternalContactBundle\Entity\ExternalUser;
use WechatWorkExternalContactBundle\Repository\ExternalUserRepository;

#[AsCronTask('25 */8 * * *')]
#[AsCommand(name: 'wechat-work:external-contact:check-user-avatar', description: '检查用户头像并保存')]
class CheckUserAvatarCommand extends Command
{
    public function __construct(
        private readonly ExternalUserRepository $externalUserRepository,
        private readonly HttpClientInterface $httpClient,
        private readonly FilesystemOperator $mountManager,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $limit = 100;

        $qb = $this->externalUserRepository->createQueryBuilder('u')
            ->setMaxResults($limit);
        $like1 = $qb->expr()->like('u.avatar', $qb->expr()->literal('https://thirdwx.qlogo.cn/%'));
        $like2 = $qb->expr()->like('u.avatar', $qb->expr()->literal('https://wx.qlogo.cn/mmopen%'));
        $users = $qb->where("u.avatar != '' and u.avatar is not null")
            ->andWhere($like1)
            ->orWhere($like2)
            ->getQuery()
            ->toIterable();
        foreach ($users as $user) {
            /** @var ExternalUser $user */
            if (empty($user->getAvatar())) {
                continue;
            }

            try {
                $response = $this->httpClient->request('GET', $user->getAvatar());
                $header = $response->getHeaders();
                if (!isset($header['x-errno']) && 'notexist:-6101' !== $header['x-info'][0]) {
                    $content = $response->getContent();
                    $key = $this->mountManager->saveContent($content, 'png', 'wechat-work-user');
                    $url = $this->mountManager->getImageUrl($key);
                } else {
                    $url = $_ENV['DEFAULT_USER_AVATAR_URL'];
                }

                $this->logger->info('保存企业微信外部用户头像', [
                    'user' => $user,
                    'new' => $url,
                ]);
                $user->setAvatar($url);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            } catch (\Throwable $exception) {
                $output->writeln($exception->getMessage());
                continue;
            }
        }

        return Command::SUCCESS;
    }
}

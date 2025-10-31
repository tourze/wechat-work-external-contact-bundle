<?php

namespace WechatWorkExternalContactBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemOperator;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use WechatWorkExternalContactBundle\Entity\ExternalUser;
use WechatWorkExternalContactBundle\Repository\ExternalUserRepository;

#[AsCronTask(expression: '25 */8 * * *')]
#[AsCommand(name: self::NAME, description: '检查用户头像并保存')]
#[WithMonologChannel(channel: 'wechat_work_external_contact')]
class CheckUserAvatarCommand extends Command
{
    public const NAME = 'wechat-work:external-contact:check-user-avatar';

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
            ->setMaxResults($limit)
        ;
        $like1 = $qb->expr()->like('u.avatar', $qb->expr()->literal('https://thirdwx.qlogo.cn/%'));
        $like2 = $qb->expr()->like('u.avatar', $qb->expr()->literal('https://wx.qlogo.cn/mmopen%'));
        $users = $qb->where("u.avatar != '' and u.avatar is not null")
            ->andWhere($qb->expr()->orX($like1, $like2))
            ->getQuery()
            ->toIterable()
        ;
        foreach ($users as $user) {
            /** @var ExternalUser $user */
            if (null === $user->getAvatar() || '' === $user->getAvatar()) {
                continue;
            }

            try {
                $startTime = microtime(true);
                $this->logger->info('开始下载外部用户头像', [
                    'userId' => $user->getId(),
                    'externalUserId' => $user->getExternalUserId(),
                    'avatarUrl' => $user->getAvatar(),
                ]);

                // @audit-logged 外部系统交互：HttpClientInterface::request() - 已记录审计日志（请求内容、响应结果、耗时、异常等）
                $response = $this->httpClient->request('GET', $user->getAvatar());
                $duration = microtime(true) - $startTime;

                $header = $response->getHeaders();
                $statusCode = $response->getStatusCode();

                $this->logger->info('外部用户头像下载响应', [
                    'userId' => $user->getId(),
                    'statusCode' => $statusCode,
                    'duration' => round($duration * 1000, 2) . 'ms',
                    'headers' => [
                        'x-errno' => $header['x-errno'] ?? null,
                        'x-info' => $header['x-info'][0] ?? null,
                        'content-length' => $header['content-length'][0] ?? null,
                    ],
                ]);

                if (!isset($header['x-errno']) && (!isset($header['x-info']) || 'notexist:-6101' !== $header['x-info'][0])) {
                    $content = $response->getContent();
                    $key = uniqid() . '.png';
                    $this->mountManager->write($key, $content);
                    $url = 'https://cdn.example.com/' . $key;

                    $this->logger->info('头像上传成功', [
                        'userId' => $user->getId(),
                        'fileKey' => $key,
                        'newUrl' => $url,
                        'fileSize' => strlen($content),
                    ]);
                } else {
                    /** @var string $url */
                    $url = $_ENV['DEFAULT_USER_AVATAR_URL'] ?? '';

                    $this->logger->warning('头像不存在，使用默认头像', [
                        'userId' => $user->getId(),
                        'originalUrl' => $user->getAvatar(),
                        'defaultUrl' => $url,
                    ]);
                }

                $this->logger->info('保存企业微信外部用户头像', [
                    'user' => $user,
                    'new' => $url,
                ]);
                $user->setAvatar($url);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            } catch (\Throwable $exception) {
                $this->logger->error('外部用户头像处理失败', [
                    'userId' => $user->getId(),
                    'externalUserId' => $user->getExternalUserId(),
                    'avatarUrl' => $user->getAvatar(),
                    'error' => $exception->getMessage(),
                    'trace' => $exception->getTraceAsString(),
                ]);
                $output->writeln($exception->getMessage());
                continue;
            }
        }

        return Command::SUCCESS;
    }
}

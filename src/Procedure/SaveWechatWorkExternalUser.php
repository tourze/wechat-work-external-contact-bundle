<?php

namespace WechatWorkExternalContactBundle\Procedure;

use Doctrine\ORM\EntityManagerInterface;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;
use WechatWorkExternalContactBundle\Repository\ExternalUserRepository;

#[MethodTag('企业微信')]
#[MethodDoc('更新企微外部联系人')]
#[MethodExpose('SaveWechatWorkExternalUser')]
#[Log]
class SaveWechatWorkExternalUser extends LockableProcedure
{
    #[MethodParam('外部联系人ID')]
    public string $externalUserId;

    public ?string $remark = null;

    public ?array $tags = null;

    public function __construct(
        private readonly ExternalUserRepository $externalUserRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public function execute(): array
    {
        $externalUser = $this->externalUserRepository->findOneBy([
            'externalUserId' => $this->externalUserId,
        ]);
        if ($externalUser === null) {
            throw new ApiException('找不到指定外部用户');
        }

        if (null !== $this->remark) {
            $externalUser->setRemark($this->remark);
        }

        if (null !== $this->tags) {
            $externalUser->setTags($this->tags);
        }

        $this->entityManager->persist($externalUser);
        $this->entityManager->flush();

        return [
            '__message' => '更新成功',
        ];
    }
}

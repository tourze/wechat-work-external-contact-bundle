<?php

namespace WechatWorkExternalContactBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;
use WechatWorkBundle\Service\WorkService;
use WechatWorkExternalContactBundle\Entity\GroupWelcomeTemplate;
use WechatWorkExternalContactBundle\Request\GroupWelcomeTemplate\AddGroupWelcomeTemplateRequest;
use WechatWorkExternalContactBundle\Request\GroupWelcomeTemplate\DeleteGroupWelcomeTemplateRequest;
use WechatWorkExternalContactBundle\Request\GroupWelcomeTemplate\EditGroupWelcomeTemplateRequest;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: GroupWelcomeTemplate::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: GroupWelcomeTemplate::class)]
#[AsEntityListener(event: Events::postRemove, method: 'postRemove', entity: GroupWelcomeTemplate::class)]
class GroupWelcomeTemplateListener
{
    public function __construct(
        private readonly WorkService $workService,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * 创建远程记录
     */
    public function prePersist(GroupWelcomeTemplate $template): void
    {
        if (!$template->isSync()) {
            return;
        }

        $request = AddGroupWelcomeTemplateRequest::createFromEntity($template);
        $res = $this->workService->request($request);
        $template->setTemplateId($res['template_id']);
    }

    public function preUpdate(GroupWelcomeTemplate $template): void
    {
        if (!$template->isSync()) {
            $this->postRemove($template);

            return;
        }

        // 没模板ID的话，我们创建一次
        if (!$template->getTemplateId()) {
            $this->prePersist($template);

            return;
        }

        $request = EditGroupWelcomeTemplateRequest::createFromEntity($template);
        $request->setTemplateId($template->getTemplateId());
        $this->workService->request($request);
    }

    /**
     * 从远程删除
     */
    public function postRemove(GroupWelcomeTemplate $template): void
    {
        if (!$template->getTemplateId()) {
            return;
        }

        try {
            $request = new DeleteGroupWelcomeTemplateRequest();
            $request->setTemplateId($template->getTemplateId());
            $request->setAgent($template->getAgent());
            $this->workService->asyncRequest($request);
        } catch (\Throwable $exception) {
            $this->logger->error('从远程删除入群欢迎语素材时发生异常', [
                'exception' => $exception,
            ]);
        }
    }
}

<?php

namespace WechatWorkExternalContactBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use WechatWorkBundle\Entity\Corp;
use WechatWorkExternalContactBundle\Entity\ExternalUser;

class ExternalUserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 创建一个测试用的 Corp
        $corp = new Corp();
        $corp->setName('测试企业_external_user_fixtures');
        $corp->setCorpId('test_corp_external_user_fixtures');
        $corp->setCorpSecret('test_secret_external_user');
        $manager->persist($corp);

        for ($i = 1; $i <= 10; ++$i) {
            $externalUser = new ExternalUser();
            $externalUser->setCorp($corp); // 设置 Corp
            $externalUser->setNickname("测试外部用户{$i}");
            $externalUser->setExternalUserId("external_user_{$i}");
            $externalUser->setUnionId("union_id_{$i}");
            $externalUser->setAvatar("https://example.com/avatar{$i}.jpg");
            $externalUser->setGender($i % 3);
            $externalUser->setEnterSessionContext(['source' => 'test']);
            $externalUser->setRemark("测试备注{$i}");
            $externalUser->setTags(['tag1', 'tag2']);
            $externalUser->setCustomer(0 === $i % 2);
            $externalUser->setTmpOpenId("tmp_open_id_{$i}");
            $externalUser->setAddTime(new \DateTimeImmutable("-{$i} days"));
            $externalUser->setRawData(['id' => $i, 'source' => 'fixture']);

            $manager->persist($externalUser);
            $this->addReference("external-user-{$i}", $externalUser);
        }

        $manager->flush();
    }
}

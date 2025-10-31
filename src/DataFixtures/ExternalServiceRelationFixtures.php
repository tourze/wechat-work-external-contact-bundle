<?php

namespace WechatWorkExternalContactBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use WechatWorkBundle\Entity\Corp;
use WechatWorkExternalContactBundle\Entity\ExternalServiceRelation;

class ExternalServiceRelationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 创建一个测试用的 Corp
        $corp = new Corp();
        $corp->setName('测试企业_fixtures');
        $corp->setCorpId('test_corp_fixtures');
        $corp->setCorpSecret('test_secret_fixtures');
        $manager->persist($corp);

        for ($i = 1; $i <= 5; ++$i) {
            $externalServiceRelation = new ExternalServiceRelation();
            $externalServiceRelation->setCorp($corp); // 设置必需的 Corp
            $externalServiceRelation->setAddExternalContactTime(new \DateTimeImmutable("-{$i} days"));
            $externalServiceRelation->setAddHalfExternalContactTime(new \DateTimeImmutable("-{$i} days"));

            $manager->persist($externalServiceRelation);
            $this->addReference("external-service-relation-{$i}", $externalServiceRelation);
        }

        $manager->flush();
    }
}

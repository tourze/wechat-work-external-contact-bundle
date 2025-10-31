<?php

declare(strict_types=1);

namespace WechatWorkExternalContactBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use WechatWorkExternalContactBundle\Entity\ExternalServiceRelation;

/**
 * 微信企业号外部联系人服务关系管理控制器
 *
 * @template TEntity of ExternalServiceRelation
 * @extends AbstractCrudController<TEntity>
 */
#[AdminCrud(routeName: 'wechat_work_external_service_relation', routePath: '/wechat-work/external-service-relation')]
final class ExternalServiceRelationCrudController extends AbstractCrudController
{
    /**
     * @return class-string<ExternalServiceRelation>
     */
    public static function getEntityFqcn(): string
    {
        return ExternalServiceRelation::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('外部联系人服务关系')
            ->setEntityLabelInPlural('外部联系人服务关系管理')
            ->setSearchFields(['corp.name', 'user.name', 'externalUser.nickname', 'externalUser.externalUserId'])
            ->setDefaultSort(['createTime' => 'DESC'])
            ->setPaginatorPageSize(30)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID')
                ->hideOnForm(),

            AssociationField::new('corp', '企业')
                ->setRequired(true)
                ->setHelp('所属企业微信'),

            AssociationField::new('user', '成员')
                ->setRequired(false)
                ->setHelp('企业微信成员'),

            AssociationField::new('externalUser', '外部联系人')
                ->setRequired(false)
                ->setHelp('关联的外部联系人'),

            DateTimeField::new('addExternalContactTime', '成员添加外部联系人时间')
                ->setRequired(false)
                ->setFormat('yyyy-MM-dd HH:mm:ss')
                ->setHelp('成员主动添加外部联系人的时间'),

            DateTimeField::new('addHalfExternalContactTime', '外部联系人主动添加时间')
                ->setRequired(false)
                ->setFormat('yyyy-MM-dd HH:mm:ss')
                ->setHelp('外部联系人主动添加成员的时间'),

            DateTimeField::new('delExternalContactTime', '成员删除外部联系人时间')
                ->setRequired(false)
                ->setFormat('yyyy-MM-dd HH:mm:ss')
                ->setHelp('成员删除外部联系人的时间'),

            DateTimeField::new('delFollowUserTime', '成员被外部联系人删除时间')
                ->setRequired(false)
                ->setFormat('yyyy-MM-dd HH:mm:ss')
                ->setHelp('成员被外部联系人删除的时间'),

            DateTimeField::new('createTime', '创建时间')
                ->hideOnForm()
                ->setFormat('yyyy-MM-dd HH:mm:ss'),

            DateTimeField::new('updateTime', '更新时间')
                ->hideOnForm()
                ->setFormat('yyyy-MM-dd HH:mm:ss'),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('corp', '企业'))
            ->add(EntityFilter::new('user', '成员'))
            ->add(EntityFilter::new('externalUser', '外部联系人'))
            ->add(DateTimeFilter::new('addExternalContactTime', '成员添加外部联系人时间'))
            ->add(DateTimeFilter::new('addHalfExternalContactTime', '外部联系人主动添加时间'))
            ->add(DateTimeFilter::new('delExternalContactTime', '成员删除外部联系人时间'))
            ->add(DateTimeFilter::new('delFollowUserTime', '成员被外部联系人删除时间'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }
}

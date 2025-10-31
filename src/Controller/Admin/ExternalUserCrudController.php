<?php

declare(strict_types=1);

namespace WechatWorkExternalContactBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use WechatWorkExternalContactBundle\Entity\ExternalUser;

/**
 * 微信企业号外部联系人管理控制器
 *
 * @template TEntity of ExternalUser
 * @extends AbstractCrudController<TEntity>
 */
#[AdminCrud(routeName: 'wechat_work_external_user', routePath: '/wechat-work/external-user')]
final class ExternalUserCrudController extends AbstractCrudController
{
    /**
     * @return class-string<ExternalUser>
     */
    public static function getEntityFqcn(): string
    {
        return ExternalUser::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('外部联系人')
            ->setEntityLabelInPlural('外部联系人管理')
            ->setSearchFields(['nickname', 'externalUserId', 'unionId', 'tmpOpenId'])
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

            TextField::new('nickname', '昵称')
                ->setRequired(false)
                ->setHelp('外部联系人的昵称')
                ->setMaxLength(120),

            TextField::new('externalUserId', '外部用户ID')
                ->setRequired(true)
                ->setHelp('微信企业号分配的外部用户唯一标识')
                ->setMaxLength(120),

            TextField::new('unionId', 'UnionID')
                ->setRequired(false)
                ->setHelp('微信开放平台统一用户标识')
                ->setMaxLength(120),

            UrlField::new('avatar', '头像')
                ->setRequired(false)
                ->setHelp('外部联系人头像URL'),

            ChoiceField::new('gender', '性别')
                ->setRequired(false)
                ->setChoices([
                    '未知' => 0,
                    '男' => 1,
                    '女' => 2,
                ])
                ->setHelp('外部联系人性别'),

            ArrayField::new('enterSessionContext', '会话上下文')
                ->setRequired(false)
                ->setHelp('进入会话的上下文信息'),

            TextareaField::new('remark', '备注')
                ->setRequired(false)
                ->setHelp('对该外部联系人的备注信息')
                ->setNumOfRows(3),

            ArrayField::new('tags', '标签')
                ->setRequired(false)
                ->setHelp('外部联系人的标签列表'),

            BooleanField::new('customer', '是否客户')
                ->setRequired(false)
                ->setHelp('是否被成员标记为客户'),

            TextField::new('tmpOpenId', '临时OpenID')
                ->setRequired(false)
                ->setHelp('外部联系人临时OpenID')
                ->setMaxLength(120),

            DateTimeField::new('addTime', '添加时间')
                ->setRequired(false)
                ->setFormat('yyyy-MM-dd HH:mm:ss')
                ->setHelp('首次添加/进群的时间'),

            ArrayField::new('rawData', '原始数据')
                ->setRequired(false)
                ->setHelp('来自微信企业号的原始数据')
                ->hideOnIndex(),

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
            ->add(TextFilter::new('nickname', '昵称'))
            ->add(TextFilter::new('externalUserId', '外部用户ID'))
            ->add(TextFilter::new('unionId', 'UnionID'))
            ->add(ChoiceFilter::new('gender', '性别')->setChoices([
                '未知' => 0,
                '男' => 1,
                '女' => 2,
            ]))
            ->add(BooleanFilter::new('customer', '是否客户'))
            ->add(TextFilter::new('tmpOpenId', '临时OpenID'))
            ->add(DateTimeFilter::new('addTime', '添加时间'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }
}

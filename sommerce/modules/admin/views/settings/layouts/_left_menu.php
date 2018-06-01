<?php
    /* @var $this \yii\web\View */
    /* @var $active string */

    use sommerce\modules\admin\components\Url;
?>
<!-- BEGIN: Aside Menu -->
<div
    id="m_ver_menu"
    class="m-aside-menu  m-aside-menu--skin-light m-aside-menu--submenu-skin-light "
    data-menu-vertical="true"
    data-menu-scrollable="false" data-menu-dropdown-timeout="500"
    style="margin-top: 40px;" >
    <ul class="m-nav">
        <li class="m-nav__item <?=('general' == $active ? 'm-nav__item--active' : '') ?>">
            <a href="<?= Url::toRoute('/settings')?>" class="m-nav__link">
                <i class="m-nav__link-icon icon-settings"></i>
                <span class="m-nav__link-text">
                    <?= Yii::t('admin', 'settings.left_menu_general') ?>
                </span>
            </a>
        </li>
        <li class="m-nav__item <?=('payments' == $active ? 'm-nav__item--active' : '') ?>">
            <a href="<?= Url::toRoute('/settings/payments')?>" class="m-nav__link">
                <i class="m-nav__link-icon icon-wallet"></i>
                <span class="m-nav__link-text">
                    <?= Yii::t('admin', 'settings.left_menu_payments') ?>
                </span>
            </a>
        </li>
        <li class="m-nav__item <?=('providers' == $active ? 'm-nav__item--active' : '') ?>">
            <a href="<?= Url::toRoute('/settings/providers')?>" class="m-nav__link">
                <i class="m-nav__link-icon icon-share"></i>
                <span class="m-nav__link-text">
                    <?= Yii::t('admin', 'settings.left_menu_providers') ?>
                </span>
            </a>
        </li>
        <li class="m-nav__item <?=('navigation' == $active ? 'm-nav__item--active' : '') ?>">
            <a href="<?= Url::toRoute('/settings/navigation')?>" class="m-nav__link">
                <i class="m-nav__link-icon flaticon-list-1"></i>
                <span class="m-nav__link-text">
                    <?= Yii::t('admin', 'settings.left_menu_navigation') ?>
                </span>
            </a>
        </li>
        <li class="m-nav__item <?=('pages' == $active ? 'm-nav__item--active' : '') ?>">
            <a href="<?= Url::toRoute('/settings/pages')?>" class="m-nav__link">
                <i class="m-nav__link-icon icon-docs"></i>
                <span class="m-nav__link-text">
                    <?= Yii::t('admin', 'settings.left_menu_pages') ?>
                </span>
            </a>
        </li>
        <li class="m-nav__item <?=('themes' == $active ? 'm-nav__item--active' : '') ?>">
            <a href="<?= Url::toRoute('/settings/themes')?>" class="m-nav__link">
                <i class="m-nav__link-icon icon-puzzle"></i>
                <span class="m-nav__link-text">
                    <?= Yii::t('admin', 'settings.left_menu_themes') ?>
                </span>
            </a>
        </li>
        <li class="m-nav__item <?=('blocks' == $active ? 'm-nav__item--active' : '') ?>">
            <a href="<?= Url::toRoute('/settings/blocks')?>" class="m-nav__link">
                <i class="m-nav__link-icon icon-layers"></i>
                <span class="m-nav__link-text">
                    <?= Yii::t('admin', 'settings.left_menu_blocks') ?>
                </span>
            </a>
        </li>
        <li class="m-nav__item <?=('notifications' == $active ? 'm-nav__item--active' : '') ?>">
            <a href="<?= Url::toRoute('/settings/notifications')?>" class="m-nav__link">
                <i class="flaticon-alert-2 m-nav__link-icon"></i>
                <span class="m-nav__link-text">
                    <?= Yii::t('admin', 'settings.left_menu_notifications') ?>
                </span>
            </a>
        </li>
        <li class="m-nav__item <?=('languages' == $active ? 'm-nav__item--active' : '') ?>">
            <a href="<?= Url::toRoute('/settings/languages')?>" class="m-nav__link">
                <i class="m-nav__link-icon fa fa-language"></i>
                <span class="m-nav__link-text">
                    <?= Yii::t('admin', 'settings.left_menu_languages') ?>
                </span>
            </a>
        </li>
    </ul>
</div>
<!-- END: Aside Menu -->
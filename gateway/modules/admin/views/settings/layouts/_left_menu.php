<?php
    /* @var $this \yii\web\View */
    /* @var $active string */

    use admin\components\Url;
?>
<!-- BEGIN: Aside Menu -->
<div
    id="m_ver_menu"
    class="m-aside-menu  m-aside-menu--skin-light m-aside-menu--submenu-skin-light "
    data-menu-vertical="true"
    data-menu-scrollable="false" data-menu-dropdown-timeout="500"
    style="margin-top: 40px;" >
    <ul class="m-nav">
        <li class="m-nav__item <?=('payments' == $active ? 'm-nav__item--active' : '') ?>">
            <a href="<?= Url::toRoute('/settings/payments')?>" class="m-nav__link">
                <i class="m-nav__link-icon icon-wallet"></i>
                <span class="m-nav__link-text">
                    <?= Yii::t('admin', 'settings.left_menu_payments') ?>
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
    </ul>
</div>
<!-- END: Aside Menu -->
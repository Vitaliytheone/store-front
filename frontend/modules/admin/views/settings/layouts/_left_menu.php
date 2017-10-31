<?php
    /* @var $this \yii\web\View */
    /* @var $active string */

    use frontend\modules\admin\components\Url;
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
                <span class="m-nav__link-text">General</span>
            </a>
        </li>
        <li class="m-nav__item <?=('payments' == $active ? 'm-nav__item--active' : '') ?>">
            <a href="<?= Url::toRoute('/settings/payments')?>" class="m-nav__link">
                <i class="m-nav__link-icon icon-wallet"></i>
                <span class="m-nav__link-text">Payments</span>
            </a>
        </li>
        <li class="m-nav__item <?=('providers' == $active ? 'm-nav__item--active' : '') ?>">
            <a href="<?= Url::toRoute('/settings/providers')?>" class="m-nav__link">
                <i class="m-nav__link-icon icon-share"></i>
                <span class="m-nav__link-text">Providers</span>
            </a>
        </li>
        <li class="m-nav__item <?=('navigations' == $active ? 'm-nav__item--active' : '') ?>">
            <a href="<?= Url::toRoute('/settings/navigations')?>" class="m-nav__link">
                <i class="m-nav__link-icon flaticon-list-1"></i>
                <span class="m-nav__link-text">Navigation</span>
            </a>
        </li>
        <li class="m-nav__item <?=('pages' == $active ? 'm-nav__item--active' : '') ?>">
            <a href="<?= Url::toRoute('/settings/pages')?>" class="m-nav__link">
                <i class="m-nav__link-icon icon-docs"></i>
                <span class="m-nav__link-text">Pages</span>
            </a>
        </li>
        <li class="m-nav__item <?=('themes' == $active ? 'm-nav__item--active' : '') ?>">
            <a href="<?= Url::toRoute('/settings/themes')?>" class="m-nav__link">
                <i class="m-nav__link-icon icon-puzzle"></i>
                <span class="m-nav__link-text">Themes</span>
            </a>
        </li>
        <li class="m-nav__item <?=('blocks' == $active ? 'm-nav__item--active' : '') ?>">
            <a href="<?= Url::toRoute('/settings/blocks')?>" class="m-nav__link">
                <i class="m-nav__link-icon icon-layers"></i>
                <span class="m-nav__link-text">Blocks</span>
            </a>
        </li>
    </ul>
</div>
<!-- END: Aside Menu -->
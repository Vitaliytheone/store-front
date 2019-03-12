<?php
    /* @var $this \yii\web\View */

    use store\modules\admin\components\Url;
    use yii\helpers\ArrayHelper;
    use store\modules\admin\helpers\NavbarHelper;

$navbarItems = NavbarHelper::getNavbarItems($this->context->route);

?>
<!-- begin::Header -->
<header class="m-grid__item	m-header"  data-minimize="minimize" data-minimize-offset="200" data-minimize-mobile-offset="200" >

    <div class="m-header__top">
        <div class="m-container m-container--responsive m-container--xxl m-container--full-height m-page__container">
            <div class="m-stack m-stack--ver m-stack--desktop">
                <!-- begin::Brand -->
                <div class="m-stack__item m-brand">
                    <div class="m-stack m-stack--ver m-stack--general m-stack--inline">
                        <div class="m-stack__item m-stack__item--middle m-brand__logo">
                            <a href="<?= Url::toRoute('/orders')?>" class="m-brand__logo-wrapper">
                                Name panel
                            </a>
                        </div>
                        <div class="m-stack__item m-stack__item--middle m-brand__tools">
                            <!-- begin::Responsive Header Menu Toggler-->
                            <a id="m_aside_header_menu_mobile_toggle" href="javascript:;" class="m-brand__icon m-brand__toggler m--visible-tablet-and-mobile-inline-block">
                                <span></span>
                            </a>
                            <!-- end::Responsive Header Menu Toggler-->
                        </div>
                    </div>
                </div>
                <!-- end::Brand -->
            </div>
        </div>
    </div>

    <div class="m-header__bottom">
        <div class="m-container m-container--responsive m-container--fuild m-container--full-height m-page__container">
            <div class="m-stack m-stack--ver m-stack--desktop">
                <!-- begin::Horizontal Menu -->
                <div class="m-stack__item m-stack__item--middle m-stack__item--fluid">
                    <button class="m-aside-header-menu-mobile-close  m-aside-header-menu-mobile-close--skin-light " id="m_aside_header_menu_mobile_close_btn">
                        <i class="la la-close"></i>
                    </button>
                    <div id="m_header_menu" class="m-header-menu m-aside-header-menu-mobile m-aside-header-menu-mobile--offcanvas  m-header-menu--skin-dark m-header-menu--submenu-skin-light m-aside-header-menu-mobile--skin-light m-aside-header-menu-mobile--submenu-skin-light "  >
                        <ul class="m-menu__nav  m-menu__nav--submenu-arrow ">
                            <!-- Menu items  -->
                            <?php foreach ($navbarItems as $menuKey => $menuItem): ?>
                                <li class="m-menu__item <?php if ($menuItem['active']): ?> m-menu__item--active <?php endif; ?> <?= ArrayHelper::getValue($menuItem, 'class' ,'') ?>"  aria-haspopup="true">
                                    <a  href="<?= $menuItem['url'] ?>" class="m-menu__link ">
                                        <span class="m-menu__link-text">
                                            <?= $menuItem['label'] ?>
                                        </span>
                                    </a>
                                </li>
                                <!-- Submenu items  -->
                                <?php if(isset($menuItem['submenuItems'])): ?>
                                <li class="m-menu__item m-menu__item--submenu m-menu__item--rel m-menu__item--more m-menu__item--icon-only mobile-show" data-menu-submenu-toggle="hover" data-redirect="true" aria-haspopup="true">
                                    <a href="<?= Url::toRoute('/settings')?>" class="m-menu__link m-menu__toggle">
                                        <span class="m-menu__item-here"></span>
                                        <span class="m-menu__link-text"> Settings</span>
                                        <i class="m-menu__ver-arrow la la-angle-right"></i>
                                    </a>
                                    <div class="m-menu__submenu m-menu__submenu--classic m-menu__submenu--left m-menu__submenu--pull">
                                        <span class="m-menu__arrow m-menu__arrow--adjust"></span>
                                        <ul class="m-menu__subnav">
                                        <?php foreach ($menuItem['submenuItems'] as $subKey => $subItem): ?>
                                            <li class="m-menu__item <?php if ($menuItem['active']): ?> m-menu__item--active <?php endif; ?> <?= ArrayHelper::getValue($subItem, 'class' ,'') ?> " data-redirect="true" aria-haspopup="true">
                                                <a href="<?= $subItem['url'] ?>" class="m-menu__link ">
                                                    <?php if (isset($subItem['icon'])): ?>
                                                        <i class="m-menu__link-icon <?= $subItem['icon'] ?>"></i>
                                                    <?php endif; ?>
                                                    <span class="m-menu__link-text">
                                                        <?= $subItem['label'] ?>
                                                    </span>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </li>
                                <?php  endif;?>
                                <!--/ Submenu items  -->
                            <?php endforeach; ?>
                            <!--/ Menu items  -->
                        </ul>
                    </div>
                </div>
                <!-- end::Horizontal Menu -->

                <div class="m-stack__item m-stack__item--middle m-dropdown m-dropdown--arrow m-dropdown--large m-dropdown--mobile-full-width m-dropdown--align-right m-dropdown--skin-light m-header-search m-header-search--expandable m-header-search--skin-" id="m_quicksearch" data-search-type="default">
                    <!--begin::Search Form -->
                    <form class="m-header-search__form">
                        <ul class="m-menu--right">
                            <li>
                                <a href="<?= Url::toRoute('/account')?>">Account</a>
                            </li>
                            <li>
                                <a href="<?= Url::toRoute('/logout') ?>">Logout</a>
                            </li>
                        </ul>
                    </form>
                    <!--end::Search Form -->
                </div>


            </div>
        </div>
    </div>
</header>
<!-- end::Header -->
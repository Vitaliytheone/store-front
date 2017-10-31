<?php
    /* @var $this \yii\web\View */

    use frontend\modules\admin\components\Url;
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
                            <li class="m-menu__item m-menu__logo"  aria-haspopup="true">
                                <a  href="<?= Url::toRoute('/orders')?>" class="m-menu__link ">
                                    <span class="m-menu__link-text ">
                                        Panel theme
                                    </span>
                                </a>
                            </li>
                            <li class="m-menu__item"  aria-haspopup="true">
                                <a  href="<?= Url::toRoute('/orders')?>" class="m-menu__link ">
                                    <span class="m-menu__item-here"></span>
                                    <span class="m-menu__link-text">
                                                Orders
                                            </span>
                                </a>
                            </li>
                            <li class="m-menu__item"  aria-haspopup="true">
                                <a  href="<?= Url::toRoute('/payments')?>" class="m-menu__link ">
                                    <span class="m-menu__item-here"></span>
                                    <span class="m-menu__link-text">
                                        Payments
                                    </span>
                                </a>
                            </li>
                            <li class="m-menu__item"  aria-haspopup="true">
                                <a  href="<?= Url::toRoute('/products')?>" class="m-menu__link ">
                                    <span class="m-menu__item-here"></span>
                                    <span class="m-menu__link-text">
    													Products
    												</span>
                                </a>
                            </li>
                            <li class="m-menu__item mobile-hidden"  aria-haspopup="true">
                                <a  href="<?= Url::toRoute('/settings')?>" class="m-menu__link ">
                                    <span class="m-menu__item-here"></span>
                                    <span class="m-menu__link-text">
    													Settings
    												</span>
                                </a>
                            </li>
                            <li class="m-menu__item m-menu__item--submenu m-menu__item--rel m-menu__item--more m-menu__item--icon-only mobile-show" data-menu-submenu-toggle="hover" data-redirect="true" aria-haspopup="true">
                                <a href="<?= Url::toRoute('/settings')?>" class="m-menu__link m-menu__toggle">
                                    <span class="m-menu__item-here"></span>
                                    <span class="m-menu__link-text"> Settings</span>
                                    <i class="m-menu__ver-arrow la la-angle-right"></i>
                                </a>
                                <div class="m-menu__submenu m-menu__submenu--classic m-menu__submenu--left m-menu__submenu--pull">
                                    <span class="m-menu__arrow m-menu__arrow--adjust"></span>
                                    <ul class="m-menu__subnav">
                                        <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                            <a href="<?= Url::toRoute('/orders')?>" class="m-menu__link ">
                                                <i class="m-menu__link-icon icon-settings"></i>
                                                <span class="m-menu__link-text">
    																General
    															</span>
                                            </a>
                                        </li>
                                        <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                            <a href="<?= Url::toRoute('/payments')?>" class="m-menu__link ">
                                                <i class="m-menu__link-icon icon-wallet"></i>
                                                <span class="m-menu__link-text">
    																Payments
    															</span>
                                            </a>
                                        </li>
                                        <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                            <a href="<?= Url::toRoute('/providers')?>" class="m-menu__link ">
                                                <i class="m-menu__link-icon icon-share"></i>
                                                <span class="m-menu__link-text">
    																Providers
    															</span>
                                            </a>
                                        </li>
                                        <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                            <a href="<?= Url::toRoute('/navigations')?>" class="m-menu__link ">
                                                <i class="m-menu__link-icon flaticon-list-1"></i>
                                                <span class="m-menu__link-text">
    																Navigation
    															</span>
                                            </a>
                                        </li>
                                        <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                            <a href="<?= Url::toRoute('/pages')?>" class="m-menu__link ">
                                                <i class="m-menu__link-icon icon-docs"></i>
                                                <span class="m-menu__link-text">
    																Pages
    															</span>
                                            </a>
                                        </li>
                                        <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                            <a href="<?= Url::toRoute('/themes')?>" class="m-menu__link ">
                                                <i class="m-menu__link-icon icon-puzzle"></i>
                                                <span class="m-menu__link-text">
    																Themes
    															</span>
                                            </a>
                                        </li>
                                        <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                            <a href="<?= Url::toRoute('/blocks')?>" class="m-menu__link ">
                                                <i class="m-menu__link-icon icon-layers"></i>
                                                <span class="m-menu__link-text">
    																Blocks
    															</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- end::Horizontal Menu -->

                <div class="m-stack__item m-stack__item--middle m-dropdown m-dropdown--arrow m-dropdown--large m-dropdown--mobile-full-width m-dropdown--align-right m-dropdown--skin-light m-header-search m-header-search--expandable m-header-search--skin-" id="m_quicksearch" data-search-type="default">
                    <!--begin::Search Form -->
                    <form class="m-header-search__form">
                        <ul class="m-menu--right">
                            <li>
                                <a href="<?= Url::toRoute('/account')?>">Username</a>
                            </li>
                            <li>
                                <a href="#">Exit</a>
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
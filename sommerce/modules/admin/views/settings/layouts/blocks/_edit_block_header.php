<?php
    /* @var $this \yii\web\View */

    use sommerce\modules\admin\components\Url;
?>
<!-- begin::Header -->
<header class="m-grid__item m-header "  data-minimize="minimize" data-minimize-offset="200" data-minimize-mobile-offset="200" >
    <div class="m-header__bottom">
        <div class="m-container m-container--responsive container m-container--full-height m-page__container" style="padding: 0px;">
            <div class="m-stack m-stack--ver m-stack--desktop">
                <!-- begin::Horizontal Menu -->
                <div class="m-stack__item m-stack__item--middle m-stack__item--fluid">
                    <button class="m-aside-header-menu-mobile-close  m-aside-header-menu-mobile-close--skin-light " id="m_aside_header_menu_mobile_close_btn">
                        <i class="la la-close"></i>
                    </button>
                    <div id="m_header_menu" class="m-header-menu m-aside-header-menu-mobile m-aside-header-menu-mobile--offcanvas  m-header-menu--skin-dark m-header-menu--submenu-skin-light m-aside-header-menu-mobile--skin-light m-aside-header-menu-mobile--submenu-skin-light "  >
                        <ul class="m-menu__nav  m-menu__nav--submenu-arrow ">
                            <li class="m-menu__item"  aria-haspopup="true">
                                <a  href="<?= Url::toRoute(['/settings/blocks'])?>" class="m-menu__link ">
                                    <span class="m-menu__item-here"></span>
                                    <span class="m-menu__link-text">
                                        <span class="fa fa-arrow-left"></span> Back
                                    </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- end::Horizontal Menu -->

                <div class="text-sm-right m-stack__item m-stack__item--middle m-dropdown m-dropdown--arrow m-dropdown--large m-dropdown--mobile-full-width m-dropdown--align-right m-dropdown--skin-light m-header-search m-header-search--expandable">
                    <button class="btn btn-success" id="save-changes">Save changes</button>
                    <form action="/" id="save-changes-form">
                        <textarea name="block_json" class="hide" id="save-changes-input"></textarea>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- end::Header -->

<!-- Preloader start-->
<div id="preload" class="d-flex justify-content-center align-items-center">
    <div class="preloader-content">
        <div class="preloader-image">
            <img src="https://cdn.dribbble.com/users/69182/screenshots/2179253/animated_loading__by__amiri.gif" alt="" class="img-fluid">
        </div>
        <div class="prealoder-text"></div>
    </div>
</div>
<!-- Preloader end-->
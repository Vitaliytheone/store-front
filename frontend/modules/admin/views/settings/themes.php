<?php
    /* @var $this \yii\web\View */

    use frontend\modules\admin\components\Url;
?>

<!-- begin::Body -->
<div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor-desktop m-grid--desktop m-body">
    <div class="m-grid__item m-grid__item--fluid  m-grid m-grid--ver	m-container m-container--responsive m-container--xxl m-page__container">
        <!-- BEGIN: Left Aside -->
        <button class="m-aside-left-close m-aside-left-close--skin-light" id="m_aside_left_close_btn">
            <i class="la la-close"></i>
        </button>
        <div id="m_aside_left" class="m-grid__item m-aside-left ">
            <?= $this->render('layouts/_left_menu', [
                'active' => 'themes'
            ])?>
        </div>
        <!-- END: Left Aside -->
        <div class="m-grid__item m-grid__item--fluid m-wrapper">
            <!-- BEGIN: Subheader -->
            <div class="m-subheader ">
                <div class="d-flex align-items-center">
                    <div class="mr-auto">
                        <h3 class="m-subheader__title">
                            Themes
                        </h3>
                    </div>
                    <div>
                        <div class="m-dropdown--align-right">
                            <a href="<?= Url::toRoute('/settings/add-theme') ?>" class="btn btn-primary m-btn--air btn-brand btn-primary">Add theme</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END: Subheader -->
            <div class="m-content">
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                        <div class="sommerce-settings_theme-cards active-theme">
                            <div class="sommerce-settings_cards-preview">
                                <div class="sommerce-settings_theme-live">
                                    <div>
                                        <a href="<?= Url::toRoute('/settings/edit-theme') ?>">Edit code</a>
                                        <a href="http://front.sommerce.net/sommerce_themes/classic/dist/">Customize</a>
                                    </div>
                                </div>
                                <img src="https://bootswatch.com/cerulean/thumbnail.png" alt="" class="img-fluid">
                            </div>
                            <div class="sommerce-settings_cards-title">
                                <strong>Active:</strong> Classic
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                        <div class="sommerce-settings_theme-cards">
                            <div class="sommerce-settings_cards-preview">
                                <div class="sommerce-settings_theme-live">
                                    <a href="#">Activate</a>
                                </div>
                                <img src="https://bootswatch.com/cyborg/thumbnail.png" alt="" class="img-fluid">
                            </div>
                            <div class="sommerce-settings_cards-title">
                                Orange
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                        <div class="sommerce-settings_theme-cards">
                            <div class="sommerce-settings_cards-preview">
                                <div class="sommerce-settings_theme-live">
                                    <a href="#">Activate</a>
                                </div>
                                <img src="https://bootswatch.com/united/thumbnail.png" alt="" class="img-fluid">
                            </div>
                            <div class="sommerce-settings_cards-title">
                                Green
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                        <div class="sommerce-settings_theme-cards">
                            <div class="sommerce-settings_cards-preview">
                                <div class="sommerce-settings_theme-live">
                                    <a href="#">Activate</a>
                                </div>
                                <img src="https://bootswatch.com/united/thumbnail.png" alt="" class="img-fluid">
                            </div>
                            <div class="sommerce-settings_cards-title">
                                SMM
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
</div>
<!-- end::Body -->
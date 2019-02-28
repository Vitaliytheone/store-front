<?php
    /* @var $this \yii\web\View */
    /* @var $providers \store\modules\admin\models\search\ProvidersSearch */
?>

<!-- begin::Body -->
<div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor-desktop m-grid--desktop m-body">
    <div class="m-grid__item m-grid__item--fluid  m-grid m-grid--ver m-container m-container--responsive m-container--xxl m-page__container">
        <!-- BEGIN: Left Aside -->
        <button class="m-aside-left-close m-aside-left-close--skin-light" id="m_aside_left_close_btn">
            <i class="la la-close"></i>
        </button>
        <div id="m_aside_left" class="m-grid__item m-aside-left ">
            <?= $this->render('layouts/_left_menu', [
                'active' => 'providers'
            ])?>
        </div>
        <!-- END: Left Aside -->
        <div class="m-grid__item m-grid__item--fluid m-wrapper">
            <!-- BEGIN: Subheader -->
            <div class="m-subheader ">
                <div class="d-flex align-items-center">
                    <div class="mr-auto">
                        <h3 class="m-subheader__title">
                            <?= Yii::t('admin', 'settings.providers_page_title') ?>
                        </h3>
                    </div>
                    <div>
                        <div class="m-dropdown--align-right">
                            <a class="btn btn-primary  m-btn--air btn-brand cursor-pointer" id="showCreateProviderModal" data-backdrop="static" href="#">
                                <?= Yii::t('admin', 'settings.providers_add') ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END: Subheader -->
            <div class="m-content">
                <?= $this->render('layouts/providers/_providers_list', [
                    'providers' => $providers
                ]); ?>
            </div>

        </div>
    </div>
</div>

<?= $this->render('layouts/providers/_add_provider_modal'); ?>
<?php

use yii\helpers\Html;
use frontend\modules\admin\components\Url;
use common\components\ActiveForm;

/* @var $this \yii\web\View */
/* @var $timezones array */
/* @var $store \frontend\modules\admin\models\forms\EditStoreSettingsForm */

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
                'active' => 'general'
            ]) ?>
        </div>
        <!-- END: Left Aside -->
        <div class="m-grid__item m-grid__item--fluid m-wrapper">
            <!-- BEGIN: Subheader -->
            <div class="m-subheader ">
                <div class="d-flex align-items-center">
                    <div class="mr-auto">
                        <h3 class="m-subheader__title">
                            <?= Yii::t('admin', 'settings.general_title') ?>
                        </h3>
                    </div>
                </div>
            </div>
            <!-- END: Subheader -->
            <div class="m-content">

                <?php if($store->hasErrors()): ?>
                    <div class="error-summary alert alert-danger"><?= ActiveForm::firstError($store) ?></div>
                <?php endif; ?>

                <form id="settings-general-form" action="<?= Url::toRoute('/settings') ?>" method="post" name="SettingsGeneralForm" role="form" enctype="multipart/form-data">
                    <?= Html::beginForm(); ?>

                    <div class="row">
                        <div class="col-lg-7 order-2 order-lg-1">
                            <div class="form-group">
                                <div>
                                    <?= Yii::t('admin', 'settings.general_logo') ?>
                                </div>
                                <label for="setting-logo">
                                    <a class="btn btn-primary btn-sm m-btn--air btn-file__white">
                                        <?= Yii::t('admin', 'settings.general_logo_upload') ?>
                                    </a>

                                    <input id="setting-logo" type="file" class="settings-file" name="SettingsGeneralForm[logoFile]">
                                </label>
                                <small class="form-text text-muted">
                                    <?= Yii::t('admin', 'settings.general_logo_limits') ?>
                                </small>
                            </div>
                        </div>
                        <div class="col-lg-5 d-flex justify-content-lg-end align-items-lg-center order-1 order-lg-2">
                            <div class="sommerce-settings__theme-imagepreview">
                                <a href="#" class="sommerce-settings__delete-image" data-toggle="modal" data-target="#delete-modal"><span class="flaticon-cancel"></span></a>
                                <img src="http://fastinsta.sommerce.net/upload/logo/14954621475922f103a72fe3.74873262.png" alt="...">
                            </div>
                        </div>

                        <div class="col-lg-7 order-4 order-lg-4">
                            <div class="form-group">
                                <div>
                                    <?= Yii::t('admin', 'settings.general_favicon') ?>
                                </div>
                                <label for="setting-favicon">
                                    <a class="btn btn-primary btn-sm m-btn--air btn-file__white">
                                        <?= Yii::t('admin', 'settings.general_favicon_upload') ?>
                                    </a>

                                    <input id="setting-favicon" type="file" class="settings-file" name="SettingsGeneralForm[faviconFile]">

                                </label>
                                <small class="form-text text-muted">
                                    <?= Yii::t('admin', 'settings.general_favicon_limits') ?>
                                </small>
                            </div>
                        </div>
                        <div class="col-lg-5 d-flex justify-content-lg-end align-items-lg-center order-3 order-lg-4">
                            <div class="sommerce-settings__theme-imagepreview">
                                <a href="#" class="sommerce-settings__delete-image" data-toggle="modal" data-target="#delete-modal"><span class="flaticon-cancel"></span></a>
                                <img src="http://d30fl32nd2baj9.cloudfront.net/media/2017/04/15/1492274418_google-plus.png/BINARY/1492274418_google-plus.png" alt="...">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label" for="settingsgeneralform-name">
                            <?= Yii::t('admin', 'settings.general_store_name') ?>
                        </label>
                        <input type="text" id="settingsgeneralform-name" class="form-control" name="SettingsGeneralForm[name]" value="<?= $store->name ?>" autofocus="" aria-required="true"
                               placeholder="<?= Yii::t('admin', 'settings.general_store_name_placeholder') ?>">

                    </div>
                    <div class="form-group field-settingsgeneralform-timezone required">
                        <label class="control-label" for="settingsgeneralform-timezone">
                            <?= Yii::t('admin', 'settings.general_timezone') ?>
                        </label>
                        <select id="settingsgeneralform-timezone" class="form-control" name="SettingsGeneralForm[timezone]" aria-required="true">
                            <?php  foreach ($timezones as $offset => $timezone): ?>
                                <option value="<?= $offset ?>"  <?= $store->timezone ? ($store->timezone == $offset ? 'selected' : '') : ($offset == 0 ? 'selected' : 0) ?> > <?= $timezone ?> </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="card card-white ">
                        <div class="card-body">

                            <div class="row seo-header align-items-center">
                                <div class="col-sm-8">
                                    <?= Yii::t('admin', 'settings.general_seo') ?>
                                </div>
                                <div class="col-sm-4 text-sm-right">
                                    <url class="btn btn-sm btn-link" data-toggle="collapse" href="#seo-block">
                                        <?= Yii::t('admin', 'settings.general_seo_edit') ?>
                                    </url>
                                </div>
                            </div>

                            <div class="seo-preview">
                                <div class="seo-preview__title edit-seo__title">
                                    <?= Yii::t('admin', 'settings.general_seo_index') ?>
                                </div>
                                <div class="seo-preview__url">http://<?= $store->domain ?></div>
                                <div class="seo-preview__description edit-seo__meta">
                                    <?= Yii::t('admin', 'settings.general_seo_meta_default') ?>
                                </div>
                            </div>

                            <div class="collapse" id="seo-block">
                                <div class="form-group">
                                    <label for="edit-seo__title">
                                        <?= Yii::t('admin', 'settings.general_seo_index') ?>
                                    </label>
                                    <input class="form-control" id="edit-seo__title" name="SettingsGeneralForm[seo_title]"
                                           value="<?= $store->seo_title ? $store->seo_title : Yii::t('admin', 'settings.general_seo_index_default') ?>">
                                    <small class="form-text text-muted"><span class="edit-seo__title-muted"></span>
                                        <?= Yii::t('admin', 'settings.general_seo_index_limits') ?>
                                         </small>
                                </div>
                                <div class="form-group">
                                    <label for="edit-seo__meta">
                                        <?= Yii::t('admin', 'settings.general_seo_meta') ?>
                                    </label>
                                    <textarea class="form-control" id="edit-seo__meta" rows="3" name="SettingsGeneralForm[seo_description]">
                                        <?= $store->seo_description ? $store->seo_description : Yii::t('admin', 'settings.general_seo_meta_default') ?>
                                    </textarea>
                                    <small class="form-text text-muted"><span class="edit-seo__meta-muted"></span>
                                        <?= Yii::t('admin', 'settings.general_seo_meta_limits') ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <button type="submit" class="btn btn-success m-btn--air" name="save-button">
                            <?= Yii::t('admin', 'settings.general_save') ?>
                        </button>
                    </div>

                    <?= Html::endForm(); ?>
                </form>

            </div>
        </div>
    </div>
</div>
<!-- end::Body -->
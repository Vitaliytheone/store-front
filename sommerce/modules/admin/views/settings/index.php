<?php

use common\components\ActiveForm;
use common\models\sommerce\Files;
use sommerce\modules\admin\components\Url;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $timezones array */
/** @var $currencies array */
/* @var $store \sommerce\modules\admin\models\forms\EditStoreSettingsForm */
/* @var $iconFileSizeLimit string */
/* @var $logoFileSizeLimit string */

$storeUrl = $store->getBaseSite();

?>
<div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor-desktop m-grid--desktop m-body">
    <div class="m-grid__item m-grid__item--fluid  m-grid m-grid--ver	m-container m-container--responsive m-container--xxl m-page__container">
        <button class="m-aside-left-close m-aside-left-close--skin-light" id="m_aside_left_close_btn">
            <i class="la la-close"></i>
        </button>
        <div id="m_aside_left" class="m-grid__item m-aside-left ">
            <?= $this->render('layouts/_left_menu', [
                'active' => 'general'
            ]) ?>
        </div>
        <div class="m-grid__item m-grid__item--fluid m-wrapper">
            <div class="m-subheader ">
                <div class="d-flex align-items-center">
                    <div class="mr-auto">
                        <h3 class="m-subheader__title">
                            <?= Yii::t('admin', 'settings.general_title') ?>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="m-content">

                <?php if($store->hasErrors()): ?>
                    <div class="error-summary alert alert-danger"><?= ActiveForm::firstError($store) ?></div>
                <?php endif; ?>

                <form id="settings-general-form" action="<?= Url::toRoute('/settings') ?>" method="post" name="SettingsGeneralForm" role="form" enctype="multipart/form-data">
                    <?= Html::beginForm(); ?>

                    <div class="row">

                        <?php if (false) : ?>
                        <div class="col-lg-7 order-2 order-lg-1">
                            <div class="form-group">
                                <div>
                                    <?= Yii::t('admin', 'settings.general_logo') ?>
                                </div>
                                <label for="setting-logo">
                                    <a class="btn btn-primary btn-sm m-btn--air btn-file__white">
                                        <?= Yii::t('admin', 'settings.general_logo_upload') ?>
                                    </a>

                                    <input id="setting-logo" type="file" class="settings-file" name="SettingsGeneralForm[logoFile]" data-target="#setting-logo__preview">

                                </label>
                                <small class="form-text text-muted">
                                    <?= Yii::t('admin', 'settings.general_logo_limits', ['fileSize' => $logoFileSizeLimit]) ?>
                                </small>
                            </div>
                        </div>

                        <div class="col-lg-5 d-flex justify-content-lg-end align-items-lg-center order-1 order-lg-2 uploaded-image" id="setting-logo__preview">
                        <?php if ($store->logo): ?>
                            <div class="sommerce-settings__theme-imagepreview">
                                <a href="<?= Url::toRoute(['/settings/delete-image', 'type' => Files::FILE_TYPE_LOGO]) ?>" class="sommerce-settings__delete-image delete-uploaded-images" data-toggle="modal" data-target="#delete-modal" data-field="settings-logo-field"><span class="flaticon-cancel"></span></a>
                                <img src="<?= $store->logo ?>" alt="...">
                            </div>
                        <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <div class="col-lg-7 order-4 order-lg-4">
                            <div class="form-group">
                                <div>
                                    <?= Yii::t('admin', 'settings.general_favicon') ?>
                                </div>
                                <label for="setting-favicon">
                                    <a class="btn btn-primary btn-sm m-btn--air btn-file__white">
                                        <?= Yii::t('admin', 'settings.general_favicon_upload') ?>
                                    </a>

                                    <input id="setting-favicon" type="file" class="settings-file" name="SettingsGeneralForm[faviconFile]" data-target="#setting-favicon__preview">

                                </label>
                                <small class="form-text text-muted">
                                    <?= Yii::t('admin', 'settings.general_favicon_limits', ['fileSize' => $iconFileSizeLimit]) ?>
                                </small>
                            </div>
                        </div>

                        <div class="col-lg-5 d-flex justify-content-lg-end align-items-lg-center order-3 order-lg-4 uploaded-image" id="setting-favicon__preview">
                        <?php if ($store->favicon): ?>
                            <div class="sommerce-settings__theme-imagepreview">
                                <a href="<?= Url::toRoute(['/settings/delete-image', 'type' => Files::FILE_TYPE_FAVICON]) ?>" class="sommerce-settings__delete-image delete-uploaded-images" data-toggle="modal" data-target="#delete-modal" data-field="settings-favicon-field"><span class="flaticon-cancel"></span></a>
                                <img src="<?= $store->favicon ?>" alt="...">
                            </div>
                        <?php endif; ?>
                        </div>

                    </div>

                    <div class="form-group">
                        <label class="control-label" for="store-name">
                            <?= Yii::t('admin', 'settings.general_store_name') ?>
                        </label>
                        <input type="text" id="store-name" class="form-control" name="SettingsGeneralForm[name]" value="<?= $store->name ?>" autofocus="" aria-required="true"
                               placeholder="<?= Yii::t('admin', 'settings.general_store_name_placeholder') ?>">
                    </div>

                    <div class="form-group field-settingsgeneralform-currency required">
                        <label class="control-label" for="settingsgeneralform-currency">
                            <?= Yii::t('admin', 'settings.general_currency') ?>
                        </label>
                        <select id="settingsgeneralform-currency" class="form-control" name="SettingsGeneralForm[currency]" aria-required="true">
                            <?php  foreach ($currencies as $currencyCode => $currencyName): ?>
                                <option value="<?= $currencyCode ?>"  <?= $store->currency == $currencyCode ? 'selected' : '' ?> > <?= $currencyName ?> </option>
                            <?php endforeach; ?>
                        </select>
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

                    <hr>
                    <div class="form-group">
                        <button type="submit" class="btn btn-success m-btn--air 3333222" id="generalSettingsSubmit" name="save-button"
                                data-title="<?= Yii::t('admin', 'settings.general_currency_change_approving') ?>"
                                data-action-url="<?= Url::toRoute(['/settings/check-currency']) ?>"
                                data-message="<?= Yii::t('admin', 'settings.general_delete_payments_agree') ?>"
                                data-confirm_button="<?= Yii::t('admin', 'settings.general_delete_submit') ?>"
                                data-cancel_button="<?= Yii::t('admin', 'settings.general_delete_cancel') ?>">
                            <?= Yii::t('admin', 'settings.general_save') ?>
                        </button>
                    </div>

                    <?= Html::endForm(); ?>
                </form>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-loader hidden"></div>
            <div class="container-fluid">
                <div class="row">
                    <div class="col modal-delete-block text-center">
                        <span class="fa fa-trash-o"></span>
                        <p><?= Yii::t('admin', 'settings.general_delete_agree') ?></p>
                        <button class="btn btn-secondary cursor-pointer m-btn--air" data-dismiss="modal"><?= Yii::t('admin', 'settings.general_delete_cancel') ?></button>
                        <a href="#" class="btn btn-danger m-btn--air" id="delete-image"><?= Yii::t('admin', 'settings.general_delete_submit') ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
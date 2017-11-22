<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $formatter yii\i18n\Formatter */
/* @var common\models\stores\StoreProviders[] $storeProviders  */

$formatter = Yii::$app->formatter;

$linkTypes = Yii::$app->params['packageLinkTypes'];
?>


<div class="modal fade add_package" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-loader hidden"></div>
            <div class="modal-header"
                 data-title_create="<?= Yii::t('admin', 'products.window_package_title_create') ?>"
                 data-title_edit="<?= Yii::t('admin', 'products.window_package_title_edit') ?>"
            >
                <h5 class="modal-title">Add package</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="packageForm" action="/" method="post" role="form" data-success_redirect="<?= Url::to(['/admin/products'])?>">

                <!--  Field for store package`s product_id value  -->
                <input type="hidden" class="form_field__product_id" name="PackageForm[product_id]" value="">
                <!--  Field for store package`s product_id value  -->

                <div class="modal-body">
                    <div id="package-form-error"></div>
                    <div class="form-group">
                        <label for="package-name">
                            <?= Yii::t('admin', 'products.window_package_p_name') ?>
                        </label>
                        <input type="text" class="form-control form_field__name" id="package-name"
                               name="PackageForm[name]" value="">
                    </div>
                    <div class="form-group">
                        <label for="package-price">
                            <?= Yii::t('admin', 'products.window_package_p_price') ?>
                        </label>
                        <input type="number" min="0.01" step="0.01"
                               class="form-control form_field__price" id="package-price"
                               name="PackageForm[price]" value="">
                    </div>
                    <div class="form-group">
                        <label for="package-quantity">
                            <?= Yii::t('admin', 'products.window_package_p_quantity') ?>
                        </label>
                        <input type="number" min="1" step="1"
                               class="form-control form_field__quantity" id="package-quantity"
                               name="PackageForm[quantity]" value="">
                    </div>
                    <div class="form-group">
                        <label for="package-best">
                            <?= Yii::t('admin', 'products.window_package_p_best') ?>
                        </label>
                        <select id="package-best" class="form-control form_field__best" name="PackageForm[best]">
                            <option value="1">
                                <?= Yii::t('admin', 'products.window_package_p_best_option_enabled') ?>
                            </option>
                            <option value="0">
                                <?= Yii::t('admin', 'products.window_package_p_best_option_disabled') ?>
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="package-link-type">
                            <?= Yii::t('admin', 'products.window_package_p_link') ?>
                        </label>
                        <select id="package-link-type" class="form-control form_field__link_type"
                                name="PackageForm[link_type]">
                            <option value="">
                                <?= Yii::t('admin', 'products.window_package_p_link_option_default') ?>
                            </option>
                            <?php foreach ($linkTypes as $linkType => $linkTypeCaption): ?>
                                <option value="<?= $linkType ?>"><?= $linkTypeCaption ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="package-availability">
                            <?= Yii::t('admin', 'products.window_package_p_availability') ?>
                        </label>
                        <select id="package-availability" class="form-control form_field__visibility"
                                name="PackageForm[visibility]">
                            <option value="1">
                                <?= Yii::t('admin', 'products.window_package_p_availability_option_enabled') ?>
                            </option>
                            <option value="0">
                                <?= Yii::t('admin', 'products.window_package_p_availability_option_disabled') ?>
                            </option>
                        </select>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="package-mode">
                            <?= Yii::t('admin', 'products.window_package_p_mode') ?>
                        </label>
                        <select id="package-mode" class="form-control form_field__mode" name="PackageForm[mode]">
                            <option value="0">
                                <?= Yii::t('admin', 'products.window_package_p_mode_option_manual') ?>
                            </option>
                            <option value="1">
                                <?= Yii::t('admin', 'products.window_package_p_mode_option_auto') ?>
                            </option>
                        </select>
                    </div>
                    <div class="form-group d-none">
                        <hr>
                        <label for="package-provider_id">
                            <?= Yii::t('admin', 'products.window_package_p_provider') ?>
                        </label>
                        <select id="package-provider_id" class="form-control form_field__provider_id" name="PackageForm[provider_id]">
                            <option value="" selected>
                                <?= Yii::t('admin', 'products.window_package_p_provider_option_default') ?>
                            </option>
                            <?php foreach ($storeProviders as $storeProvider): ?>
                            <option value="<?= $storeProvider->provider->id ?>" data-action-url="<?= Url::to(['products/get-provider-services', 'provider_id' => $storeProvider->provider->id ]) ?>">
                                <?= Html::encode($storeProvider->provider->site) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="provider-service-group form-group d-none">
                        <label for="package-provider_service">
                            <?= Yii::t('admin', 'products.window_package_p_service') ?>
                        </label>
                        <select id="package-provider_service" class="form-control form_field__provider_service" name="PackageForm[provider_service]"
                                data-ajax_timeout_message="<?= Yii::t('admin', 'products.window_package_ajax_timeout_message') ?>"
                        >
                            <option value="" selected>
                                <?= Yii::t('admin', 'products.window_package_p_service_option_default') ?>
                            </option>
                        </select>
                    </div>
                    <span class="api-error m--font-danger d-none"></span>
                </div>
                <div class="modal-footer justify-content-start">
                    <button type="submit" id="submitPackageForm" class="btn btn-primary"
                            data-title_create="<?= Yii::t('admin', 'products.window_package_button_save_title_create') ?>"
                            data-title_save="<?= Yii::t('admin', 'products.window_package_button_save_title_save') ?>"
                    >
                        Add package
                    </button>
                    <button type="button" id="cancelPackageForm" class="btn btn-secondary" data-dismiss="modal">
                        <?= Yii::t('admin', 'products.window_package_button_cancel_title') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
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
            <div class="modal-header">
                <h5 class="modal-title">Add package</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="packageForm" action="/" method="post" role="form">

                <!--  Field for store package`s product_id value  -->
                <input type="hidden" class="form_field__product_id" name="PackageForm[product_id]" value="">
                <!--  Field for store package`s product_id value  -->

                <div class="modal-body">
                    <div id="package-form-error"></div>
                    <div class="form-group">
                        <label for="package-name">Package name *</label>
                        <input type="text" class="form-control form_field__name" id="package-name"
                               name="PackageForm[name]" value="">
                    </div>
                    <div class="form-group">
                        <label for="package-price">Price *</label>
                        <input type="number" class="form-control form_field__price" id="package-price"
                               name="PackageForm[price]" value="">
                    </div>
                    <div class="form-group">
                        <label for="package-quantity">Quantity *</label>
                        <input type="number" class="form-control form_field__quantity" id="package-quantity"
                               name="PackageForm[quantity]" value="">
                    </div>
                    <div class="form-group">
                        <label for="package-best">Best package</label>
                        <select id="package-best" class="form-control form_field__best" name="PackageForm[best]">
                            <option value="1">Enabled</option>
                            <option value="0">Disabled</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="package-link-type">Link Type</label>
                        <select id="package-link-type" class="form-control form_field__link_type"
                                name="PackageForm[link_type]">
                            <option value="">None</option>
                            <?php foreach ($linkTypes as $linkType => $linkTypeCaption): ?>
                                <option value="<?= $linkType ?>"><?= $linkTypeCaption ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="package-availability">Availability</label>
                        <select id="package-availability" class="form-control form_field__visibility"
                                name="PackageForm[visibility]">
                            <option value="1">Enabled</option>
                            <option value="0">Disabled</option>
                        </select>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="package-mode">Mode</label>
                        <select id="package-mode" class="form-control form_field__mode" name="PackageForm[mode]">
                            <option value="0">Manual</option>
                            <option value="1">Auto</option>
                        </select>
                    </div>
                    <hr>
                    <div class="form-group d-none">
                        <label for="package-provider_id">Provider</label>
                        <select id="package-provider_id" class="form-control form_field__provider_id" name="PackageForm[provider_id]">
                            <?php foreach ($storeProviders as $storeProvider): ?>
                            <option value="<?= $storeProvider->provider->id ?>" data-action-url="<?= Url::to(['products/get-provider-services', 'provider_id' => $storeProvider->provider->id ]) ?>">
                                <?= Html::encode($storeProvider->provider->site) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group d-none">
                        <label for="package-provider_service">Provider</label>
                        <select id="package-provider_service" class="form-control form_field__provider_service" name="PackageForm[provider_service]">
                        </select>
                    </div>
                </div>
                <div class="modal-footer justify-content-start">
                    <button type="submit" id="submitPackageForm" class="btn btn-primary">Add package</button>
                    <button type="button" id="cancelPackageForm" class="btn btn-secondary" data-dismiss="modal">Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
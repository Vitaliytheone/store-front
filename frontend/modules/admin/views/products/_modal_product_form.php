<?php

use yii\helpers\Url;

/* @var $this yii\web\View */

$currentStore = yii::$app->store->getInstance();
$storeUrl = $currentStore->domain;

?>


<div class="modal fade add_product" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-loader hidden"></div>
            <div class="modal-header" data-title_create="<?= Yii::t('admin', 'products.product_title_create') ?>" data-title_edit="<?= Yii::t('admin', 'products.product_title_edit') ?>">
                <h5 class="modal-title">
                    Add product
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="productForm" class="form-horizontal" action="/" method="post" role="form" data-success_redirect="<?= Url::to(['/admin/products'])?>">

                <div class="modal-body">
                    <div id="product-form-error"></div>

                    <div class="form-group">
                        <label for="edit-page-title">
                            <?= Yii::t('admin', 'products.product_name') ?>
                        </label>
                        <input type="text" class="form_field__name form-control" id="edit-page-title" name="ProductForm[name]" value="">
                    </div>

                    <div class="form-group m-form__group">

                        <label for="edit-page-visibility">
                            <?= Yii::t('admin', 'products.product_visibility') ?>
                        </label>

                        <select class="form_field__visibility form-control m-input" id="edit-page-visibility" name="ProductForm[visibility]">
                            <option name="ProductForm[visibility]" value="1">
                                <?= Yii::t('admin', 'products.product_visibility_enabled') ?>
                            </option>
                            <option name="ProductForm[visibility]" value="0">
                                <?= Yii::t('admin', 'products.product_visibility_disabled') ?>
                            </option>
                        </select>

                    </div>

                    <div class="form-group">
                        <textarea class="form_field__description summernote" id="description" title="Description" name="ProductForm[description]"></textarea>
                    </div>

                    <div class="form-group">

                        <label for="package-product">
                            <?= Yii::t('admin', 'products.product_properties_title') ?>
                        </label>

                        <div class="input-group">
                            <input type="text" class="form-control input-properties"
                                   placeholder="<?= Yii::t('admin', 'products.product_properties_placeholder') ?>">
                            <span class="input-group-btn">
                                <button class="btn btn-primary add-properies" type="button">
                                    <?= Yii::t('admin', 'products.product_properties_add') ?>
                                </button>
                              </span>
                        </div>

                        <span class="empty-property-error m--font-danger d-none">
                            <?= Yii::t('admin', 'products.product_properties_message') ?>
                        </span>

                        <ul class="form_field__properties list-group list-properties"></ul>

                    </div>

                    <div class="card card-white">
                        <div class="card-body">

                            <div class="row seo-header align-items-center">
                                <div class="col-sm-8">
                                    <?= Yii::t('admin', 'products.product_seo_preview') ?>
                                </div>
                                <div class="col-sm-4 text-sm-right">
                                    <a class="btn btn-sm btn-link" data-toggle="collapse" href="#seo-block">
                                        <?= Yii::t('admin', 'products.product_seo_edit') ?>
                                    </a>
                                </div>
                            </div>

                            <div class="seo-preview">
                                <div class="seo-preview__title edit-seo__title"></div>
                                <div class="seo-preview__url">http://<?= $storeUrl; ?>/<span class="edit-seo__url"></span></div>
                                <div class="seo-preview__description edit-seo__meta"></div>
                            </div>

                            <div class="collapse" id="seo-block">

                                <div class="form-group">
                                    <label for="edit-seo__title">
                                        <?= Yii::t('admin', 'products.product_seo_page') ?>
                                    </label>
                                    <input class="form_field__seo_title form-control" id="edit-seo__title" name="ProductForm[seo_title]"
                                           value="<?= Yii::t('admin', 'products.product_seo_page_default') ?>">
                                    <small class="form-text text-muted"><span class="edit-seo__title-muted"></span>
                                        <?= Yii::t('admin', 'products.product_seo_page_chars_used') ?>
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="edit-seo__meta">
                                        <?= Yii::t('admin', 'products.product_seo_meta') ?>
                                    </label>
                                    <textarea class="form_field__seo_description form-control" id="edit-seo__meta" rows="3" name="ProductForm[seo_description]">
                                        <?= Yii::t('admin', 'products.product_seo_meta_default') ?>
                                    </textarea>
                                    <small class="form-text text-muted"><span class="edit-seo__meta-muted"></span>
                                        <?= Yii::t('admin', 'products.product_seo_meta_chars_used') ?>
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="edit-seo__url">
                                        <?= Yii::t('admin', 'products.product_seo_url') ?>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-addon"
                                              id="basic-addon3">http://<?= $storeUrl; ?>/</span>
                                        <input type="text" class="form_field__url form-control" id="edit-seo__url" name="ProductForm[url]" value="<?= Yii::t('admin', 'products.product_seo_url_default') ?>">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer justify-content-start">
                    <button type="submit" id="submitProductForm" class="btn btn-primary" data-title_create="<?= Yii::t('admin', 'products.product_save_title_create') ?>" data-title_save="<?= Yii::t('admin', 'products.product_save_title_save') ?>">
                        Add product
                    </button>

                    <button type="button" id="cancelProductForm" class="btn btn-secondary" data-dismiss="modal">
                        <?= Yii::t('admin', 'products.product_cancel') ?>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>








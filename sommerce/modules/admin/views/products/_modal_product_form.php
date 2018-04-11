<?php

use sommerce\modules\admin\components\Url;

/* @var $this yii\web\View */
/* @var $store \common\models\stores\Stores */

$storeUrl = 'http://' . $store->domain;

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

            <form id="productForm" class="form-horizontal" action="/" method="post" role="form" data-success_redirect="<?= Url::toRoute(['/products'])?>" data-get_urls_url="<?= Url::toRoute('/api/get-url-list') ?>">

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
                        <label><?= Yii::t('admin', 'products.product_color') ?></label>
                        <div class="product-color__wrap">
                            <input type="text" class="form_field__color product-color" id="edit-page-color" value="#ffffff" name="ProductForm[color]">
                        </div>
                    </div>

                    <div class="form-group">
                        <textarea class="form_field__description summernote" id="description" title="Description" name="ProductForm[description]"></textarea>
                    </div>

                    <div class="card card-white mb-3">
                        <div class="card-body">
                            <div class="row seo-header align-items-center">
                                <div class="col-sm-8">
                                    <span>
                                        <?= Yii::t('admin', 'products.product_properties_title') ?>
                                    </span>
                                </div>

                                <div class="col-sm-4 text-sm-right">
                                    <div class="m-dropdown m-dropdown--inline m-dropdown--large m-dropdown--arrow m-dropdown--align-left" data-dropdown-toggle="hover" aria-expanded="true">
                                        <a class="btn btn-sm btn-link m-dropdown__toggle" href="#"><span class="la 	la-clone"></span><?= Yii::t('admin', 'products.product_properties_copy') ?></a>
                                        <div class="m-dropdown__wrapper">
                                            <span class="m-dropdown__arrow m-dropdown__arrow--left"></span>
                                            <div class="m-dropdown__inner">
                                                <div class="m-dropdown__body">
                                                    <div class="m-dropdown__content dd-properties__max-height">
                                                        <div class="m--font-primary dd-properties__alert" role="alert">
                                                            <?= Yii::t('admin', 'products.product_properties_copy_text') ?>
                                                        </div>
                                                        <ul class="m-nav list__products_properties"></ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control input-properties">
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary add-properies" type="button"><?= Yii::t('admin', 'products.product_properties_add') ?></button>
                                    </span>
                                </div>
                                <span class="empty-property-error m--font-danger d-none">
                                        <?= Yii::t('admin', 'products.product_properties_message') ?>
                                </span>
                            </div>

                            <div class="alert m-alert--default d-none info__create_new_prop" role="alert">
                                <?= Yii::t('admin', 'products.product_properties_create_new_1') ?> <b><span class="la la-clone" style="font-size: 12px;"></span> <?= Yii::t('admin', 'products.product_properties_create_new_2') ?></b> <?= Yii::t('admin', 'products.product_properties_create_new_3') ?>
                            </div>

                        </div>

                        <div class="dd-properties">
                            <div class="dd" id="nestableProperties">
                                <ol class="dd-list form_field__properties"></ol>
                            </div>
                        </div>

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
                                <div class="seo-preview__url"><?= $storeUrl; ?>/<span class="edit-seo__url"></span></div>
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
                                    <textarea class="form_field__seo_description form-control" id="edit-seo__meta" rows="3" name="ProductForm[seo_description]"><?/*= Yii::t('admin', 'products.product_seo_meta_default') */?></textarea>
                                    <small class="form-text text-muted"><span class="edit-seo__meta-muted"></span>
                                        <?= Yii::t('admin', 'products.product_seo_meta_chars_used') ?>
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="edit-seo__meta-keyword"><?= Yii::t('admin', 'settings.product_seo_meta_keywords') ?></label>
                                    <textarea class="form_field__seo_keywords form-control" id="edit-seo__meta-keyword" rows="3" name="ProductForm[seo_keywords]"></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="edit-seo__url">
                                        <?= Yii::t('admin', 'products.product_seo_url') ?>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-addon"
                                              id="basic-addon3"><?= $storeUrl; ?>/</span>
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

<!--Copy properties modal-->
<div class="modal fade" id="copyPropertiesModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col modal-delete-block text-center">
                        <span class="la la-warning" style="font-size: 60px;"></span>
                        <p>All current properties will be deleted</p>
                        <button class="btn btn-secondary cursor-pointer m-btn--air" data-dismiss="modal">No</button>
                        <button class="btn btn-primary btn__submit_copy" id="m-btn--air" data-dismiss="modal">Ok</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>





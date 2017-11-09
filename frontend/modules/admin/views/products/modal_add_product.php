<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $formatter yii\i18n\Formatter */

$currentStore = yii::$app->store->getInstance();
$storeUrl = $currentStore->domain;
$formatter = Yii::$app->formatter;
?>

<div class="modal fade add_product" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-loader hidden"></div>
            <div class="modal-header">
                <h5 class="modal-title">Add product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="productForm" class="form-horizontal" action="<?= Url::to(['products/create-product']) ?>" method="post" role="form">


                <div class="modal-body">
                    <div id="product-form-error"></div>

                    <div class="form-group">
                        <label for="edit-page-title">Product name</label>
                        <input type="text" class="form_field__name form-control" id="edit-page-title" name="ProductForm[name]" value="">
                    </div>

                    <div class="form-group m-form__group">
                        <label for="edit-page-visibility">Visibility</label>
                        <select class="form_field__visibility form-control m-input" id="edit-page-visibility" name="ProductForm[visibility]">
                            <option name="ProductForm[visibility]" value="1">Enabled</option>
                            <option name="ProductForm[visibility]" value="0">Disabled</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <textarea class="form_field__description summernote" id="description" title="Description" name="ProductForm[description]"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="package-product">Properties</label>
                        <div class="input-group">
                            <input type="text" class="form-control input-properties" placeholder="Add properties">
                            <span class="input-group-btn">
                                <button class="btn btn-primary add-properies" type="button">Add</button>
                              </span>
                        </div>

                        <ul class="form_field__properties list-group list-properties">
                        </ul>
                    </div>

                    <div class="card card-white">
                        <div class="card-body">

                            <div class="row seo-header align-items-center">
                                <div class="col-sm-8">
                                    Search engine listing preview
                                </div>
                                <div class="col-sm-4 text-sm-right">
                                    <button class="btn btn-sm btn-link" data-toggle="collapse" href="#seo-block">Edit
                                        website SEO
                                    </button>
                                </div>
                            </div>

                            <div class="seo-preview">
                                <div class="seo-preview__title edit-seo__title"></div>
                                <div class="seo-preview__url">http://<?= $storeUrl; ?>/<span class="edit-seo__url"></span></div>
                                <div class="seo-preview__description edit-seo__meta"></div>
                            </div>

                            <div class="collapse" id="seo-block">
                                <div class="form-group">
                                    <label for="edit-seo__title">Page title</label>
                                    <input class="form_field__seo_title form-control" id="edit-seo__title" name="ProductForm[seo_title]" value="Product">
                                    <small class="form-text text-muted"><span class="edit-seo__title-muted"></span> of
                                        70 characters used
                                    </small>
                                </div>
                                <div class="form-group">
                                    <label for="edit-seo__meta">Meta description</label>
                                    <textarea class="form_field__seo_description form-control" id="edit-seo__meta" rows="3" name="ProductForm[seo_description]">A great About Us page helps builds trust between you and your customers. The more content you provide about you and your business, the more confident people will text</textarea>
                                    <small class="form-text text-muted"><span class="edit-seo__meta-muted"></span> of
                                        160 characters used
                                    </small>
                                </div>
                                <div class="form-group">
                                    <label for="edit-seo__url">URL</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"
                                              id="basic-addon3">http://<?= $storeUrl; ?>/</span>
                                        <input type="text" class="form_field__url form-control" id="edit-seo__url" name="ProductForm[url]" value="about-us">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer justify-content-start">
                    <button type="submit" id="submitProductForm" class="btn btn-primary">Add product</button>
                    <button type="button" id="cancelProductForm" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>

            </form>
        </div>
    </div>
</div>








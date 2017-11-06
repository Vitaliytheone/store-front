<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $formatter yii\i18n\Formatter */

$formatter = Yii::$app->formatter;
?>

<div class="modal fade add_product" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <div class="form-group">
                    <label for="edit-page-title">Product name</label>
                    <input type="text" class="form-control" id="edit-page-title">
                </div>

                <div class="form-group">
                    <div id="summernote"></div>
                </div>

                <div class="form-group">
                    <label for="package-product">Properties</label>
                    <div class="input-group">
                        <input type="text" class="form-control input-properties" placeholder="Add properties">
                        <span class="input-group-btn">
                                <button class="btn btn-primary add-properies" type="button">Add</button>
                              </span>
                    </div>

                    <ul class="list-group list-preperties">
                        <li class="list-group-item">Properties #1 <span class="fa fa-times delete-properies"></span></li>
                        <li class="list-group-item">Properties #2 <span class="fa fa-times delete-properies"></span></li>
                        <li class="list-group-item">Properties #3 <span class="fa fa-times delete-properies"></span></li>
                        <li class="list-group-item">Properties #4 <span class="fa fa-times delete-properies"></span></li>
                        <li class="list-group-item">Properties #5 <span class="fa fa-times delete-properies"></span></li>
                    </ul>
                </div>

                <div class="card card-white">
                    <div class="card-body">

                        <div class="row seo-header align-items-center">
                            <div class="col-sm-8">
                                Search engine listing preview
                            </div>
                            <div class="col-sm-4 text-sm-right">
                                <button class="btn btn-sm btn-link" data-toggle="collapse" href="#seo-block">Edit website SEO</button>
                            </div>
                        </div>

                        <div class="seo-preview">
                            <div class="seo-preview__title edit-seo__title">Product</div>
                            <div class="seo-preview__url">http://fastinsta.sommerce.net/<span class="edit-seo__url">product</span></div>
                            <div class="seo-preview__description edit-seo__meta">
                                A great About Us page helps builds trust between you and your customers. The more content you provide about you and your business, the more confident people wil...
                            </div>
                        </div>

                        <div class="collapse" id="seo-block">
                            <div class="form-group">
                                <label for="edit-seo__title">Page title</label>
                                <input class="form-control" id="edit-seo__title" value="Product">
                                <small class="form-text text-muted"><span class="edit-seo__title-muted"></span> of 70 characters used</small>
                            </div>
                            <div class="form-group">
                                <label for="edit-seo__meta">Meta description</label>
                                <textarea class="form-control" id="edit-seo__meta" rows="3">A great About Us page helps builds trust between you and your customers. The more content you provide about you and your business, the more confident people will text</textarea>
                                <small class="form-text text-muted"><span class="edit-seo__meta-muted"></span> of 160 characters used</small>
                            </div>
                            <div class="form-group">
                                <label for="edit-seo__url">URL</label>
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon3">http://fastinsta.sommerce.net/</span>
                                    <input type="text" class="form-control" id="edit-seo__url" value="about-us">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>


            <div class="modal-footer justify-content-start">
                <button type="button" class="btn btn-primary">Add product</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>







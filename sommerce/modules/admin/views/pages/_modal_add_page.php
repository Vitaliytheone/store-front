<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
?>


<div class="modal fade" id="modal-create-page" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-middle" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">New page</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group m-form__group">
                    <label>Page name</label>
                    <input type="text" class="form-control m-input">
                </div>

                <div class="card card-white mb-4">
                    <div class="card-body">

                        <div class="row seo-header align-items-center">
                            <div class="col-sm-8">
                                Search engine listing preview
                            </div>
                            <div class="col-sm-4 text-sm-right">
                                <a class="btn btn-sm btn-link" data-toggle="collapse" href="#seo-block">Edit website SEO</a>
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
                                <label for="edit-seo__meta-keyword">Meta keywords</label>
                                <textarea class="form-control" id="edit-seo__meta-keyword" rows="3"></textarea>
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

                <div class="form-group m-form__group">
                    <div class="m-switch-group">
                        <span class="m-switch m-switch--sm">
                            <label>
                                <input type="checkbox" checked="checked" name="">
                                   <span></span>
                            </label>
                        </span>
                        <span class="m-switch-label">Visibility</span>
                    </div>
                </div>

            </div>
            <div class="modal-footer text-right d-flex justify-content-between">
                <div>
                    <div class="btn btn-modal-delete">
                        <div class="sommerce-dropdown__delete">
                            <div class="sommerce-dropdown__delete-description">
                                Are you sure you want to <br>
                                <b>delete</b> this page?
                            </div>
                            <a href="#" class="btn btn-danger btn-sm mr-2 sommerce-dropdown__delete-cancel">Cancel</a>
                            <a href="#" class="btn btn-secondary btn-sm">Delete</a>
                        </div>
                        Delete
                    </div>
                </div>
                <div>
                    <button type="button" class="btn btn-secondary mr-3" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Add page</button>
                </div>
            </div>
        </div>
    </div>
</div>
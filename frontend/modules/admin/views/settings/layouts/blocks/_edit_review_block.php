<?php
    /* @var $this \yii\web\View */
?>

<div class="container mt-2">
    <!--Feature setting panel START-->
    <div class="row editor-block border border-grey align-items-center m-portlet m-portlet--mobile">
        <div class="col-md-4">
            <button class="btn btn-primary cursor-pointer" id="new-review">New review</button>
        </div>
        <div class="col-md-8 text-md-right">
            <div class="editor-block__setting-block flex-wrap">
                <div class="editor-block__setting-title">Column</div>
                <div class="editor-block__setting-action d-flex flex-wrap align-items-center">
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-secondary">
                                <input type="radio" name="review-column" value="1" class="review-column" autocomplete="off"> 1
                            </label>
                            <label class="btn btn-secondary">
                                <input type="radio" name="review-column" value="2" class="review-column" autocomplete="off"> 2
                            </label>
                            <label class="btn btn-secondary">
                                <input type="radio" name="review-column" value="3" class="review-column" autocomplete="off"> 3
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Feature edit Title block & Description START -->
    <div class="row editor-block border border-grey m-portlet m-portlet--mobile">
        <div class="col-12">
            <div class="editor-block__title-h1">
                <div class="editor-textarea__text-edit-off">
                    <textarea class="editor-textarea__h editor-textarea__h2 js-auto-size review-title" data-textarea-title="header-title" rows="1" spellcheck="false" placeholder="Add title..."></textarea>
                    <div class="editor-textarea__text-edit-action">
                        <button class="btn btn-sm btn-success cursor-pointer editor-textarea__text-edit-save">Save</button>
                        <button class="btn btn-sm btn-secondary cursor-pointer editor-textarea__text-edit-close">Close</button>
                    </div>
                </div>
            </div>
            <div class="editor-block__description">
                <div class="editor_textarea-block">
                    <div class="editor-textarea__text-edit-off">
                        <textarea class="editor_textarea__text js-auto-size text-center review-description" rows="1" data-textarea-title="header-description" spellcheck="false" placeholder="Add text..."></textarea>
                        <div class="editor-textarea__text-edit-action">
                            <button class="btn btn-sm btn-success cursor-pointer editor-textarea__text-edit-save">Save</button>
                            <button class="btn btn-sm btn-secondary cursor-pointer editor-textarea__text-edit-close">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row editor-block border border-grey m-portlet m-portlet--mobile">
        <!-- Swiper -->
        <div class="swiper-container block-slider">
            <div class="swiper-wrapper"></div>
            <!-- Add Pagination -->
            <div class="swiper-pagination"></div>
        </div>
    </div>
</div>
<!--Are your sure-->
<div class="modal fade" id="delete-feature-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col modal-delete-block text-center">
                        <span class="fa fa-trash-o"></span>
                        <p>Are your sure that your want to delete this feature?</p>
                        <button class="btn btn-secondary cursor-pointer" data-dismiss="modal">Cancel</button>
                        <button class="btn btn-danger" id="feature-delete" data-dismiss="modal">Yes, delete it!</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


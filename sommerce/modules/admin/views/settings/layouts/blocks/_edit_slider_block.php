<?php
    /* @var $this \yii\web\View */
?>
<div class="container mt-2">

    <!--Feature setting panel START-->
    <div class="row editor-block border border-grey align-items-center m-portlet m-portlet--mobile">
        <div class="col-md-4">
            <button class="btn btn-primary cursor-pointer new-preview">New slide</button>
        </div>
        <div class="col-md-8 text-md-right">

            <div class="editor-block__setting-block flex-wrap ">
                <div class="editor-block__setting-title">Effect</div>
                <div class="editor-block__setting-action d-flex flex-wrap align-items-center">
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-secondary">
                                <input type="radio" class="slider-effects" name="feature-column" value="fade" autocomplete="off"> Fade
                            </label>
                            <label class="btn btn-secondary">
                                <input type="radio" class="slider-effects" name="feature-column" value="slide" autocomplete="off"> Slide
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="editor-block__setting-block flex-wrap">
                <div class="editor-block__setting-title">Rotation interval</div>
                <div class="editor-block__setting-action d-flex flex-wrap align-items-center">
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-secondary">
                                <input type="radio" name="feature-align-card" class="slider-interval" value="5000" autocomplete="off">  5
                            </label>
                            <label class="btn btn-secondary">
                                <input type="radio" name="feature-align-card" class="slider-interval" value="7000" autocomplete="off"> 7
                            </label>
                            <label class="btn btn-secondary">
                                <input type="radio" name="feature-align-card" class="slider-interval" value="10000" autocomplete="off"> 10
                            </label>
                            <label class="btn btn-secondary">
                                <input type="radio" name="feature-align-card" class="slider-interval" value="15000" autocomplete="off"> 15
                            </label>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="row editor-block border border-grey m-portlet m-portlet--mobile">
        <!-- Swiper -->
        <div class="swiper-container block-slider">
            <div class="swiper-wrapper">

            </div>
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

<?= $this->render('_slide_link_modal') ?>
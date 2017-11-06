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
            <form id="productForm" class="form-horizontal" action="<?= Url::to(['products/create-product']) ?>" method="post" role="form">


                <div class="modal-body">
                    <div id="package-form-error"></div>

                    <div class="form-group">
                        <label for="edit-page-title">Product name</label>
                        <input type="text" class="form-control" id="edit-page-title" name="name" value="">
                    </div>

                    <div class="form-group">
                        <textarea id="summernote" title="Description" name="description"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="package-product">Properties</label>
                        <div class="input-group">
                            <input type="text" class="form-control input-properties" placeholder="Add properties">
                            <span class="input-group-btn">
                                <button class="btn btn-primary add-properies" type="button">Add</button>
                              </span>
                        </div>

                        <ul class="list-group list-properties">
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
                                <div class="seo-preview__title edit-seo__title">Product</div>
                                <div class="seo-preview__url">http://fastinsta.sommerce.net/<span class="edit-seo__url">product</span>
                                </div>
                                <div class="seo-preview__description edit-seo__meta">
                                    A great About Us page helps builds trust between you and your customers. The more
                                    content you provide about you and your business, the more confident people wil...
                                </div>
                            </div>

                            <div class="collapse" id="seo-block">
                                <div class="form-group">
                                    <label for="edit-seo__title">Page title</label>
                                    <input class="form-control" id="edit-seo__title" name="seo_title" value="Product">
                                    <small class="form-text text-muted"><span class="edit-seo__title-muted"></span> of
                                        70 characters used
                                    </small>
                                </div>
                                <div class="form-group">
                                    <label for="edit-seo__meta">Meta description</label>
                                    <textarea class="form-control" id="edit-seo__meta" rows="3" name="seo_description">A great About Us page helps builds trust between you and your customers. The more content you provide about you and your business, the more confident people will text</textarea>
                                    <small class="form-text text-muted"><span class="edit-seo__meta-muted"></span> of
                                        160 characters used
                                    </small>
                                </div>
                                <div class="form-group">
                                    <label for="edit-seo__url">URL</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"
                                              id="basic-addon3">http://fastinsta.sommerce.net/</span>
                                        <input type="text" class="form-control" id="edit-seo__url" name="url" value="about-us">
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


<!-- TODO:: Delete scripts after the main script developing is finished  -->
<script src="/js/libs/jquery.js"></script>
<script src="/js/libs/popper.js"></script>
<script src="/js/libs/bootstrap.js"></script>

<script>
    /**
     * Create/Update Service form script
     */
    (function (window, alert) {
        var $modal = $('.add_product'),

            $productForm = $('#productForm'),
            $productFormInputs = $productForm.find('input'),
            $submitProductForm = $('#submitProductForm'),
            $cancelProductForm = $('#cancelProductForm'),

            $propertiesList = $productForm.find('.list-properties'),

            $summerNote,

            $modalTitle = $modal.find('.modal-title'),
            $errorContainer = $('#product-form-error'),
            $modalLoader = $modal.find('.modal-loader'),

            currentProductId,
            actionCreateUrl = '/admin/products/create-product',     // POST /admin/products/create-product
            actionUpdateUrl = '/admin/products/update-product',     // POST /admin/products/update-product/{:id}
            actionGetUrl = '/admin/products/get-product';           // GET  /admin/products/get-product/{:id}

        $(document).ready(function () {
            initSummernote();
            initPropertiesList();
        });

        /*******************************************************************************************
         * Save Product form data
         *******************************************************************************************/
        $productForm.submit(function (e) {
            // Define action Url depends on Create new product or Save exiting
            var saveActionUrl = currentProductId ? actionUpdateUrl + '/' + currentProductId : actionCreateUrl;

            e.preventDefault();
            $.ajax({
                url: saveActionUrl,
                type: "POST",
                data: $(this).serialize(),
                success: function (data, textStatus, jqXHR) {
                    if (data.error) {
                        $errorContainer.append(data.error.html);
                        return;
                    }
                    $modal.modal('hide');
                    //Redirect on success
                    _.delay(function () {
                        $(location).attr('href', '/admin/products');
                    }, 500);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $modal.modal('hide');
                    console.log('Error on service save', jqXHR, textStatus, errorThrown);
                }
            });

            $errorContainer.empty();
        });

        /*******************************************************************************************
         * Common functions
         *******************************************************************************************/
        /**
         * Reset form fields to init values
         */
        function resetForm(){
            //Reset inputs
            $productForm.find('input').val('');
            //Reset note-editor
            $summerNote.summernote('reset');
            //Reset properties list
            $propertiesList.empty();
        }

        /**
         * Init Summernote editor
         */
        function initSummernote(){
            $summerNote = $('#summernote').summernote({
                 minHeight: 300,
                 focus: true,
                 toolbar: [['style', ['style', 'bold', 'italic']], ['lists', ['ul', 'ol']], ['para', ['paragraph']], ['color', ['color']], ['insert', ['link', 'picture', 'video']], ['codeview', ['codeview']]],
                 disableDragAndDrop: true,
                 styleTags: ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
                 popover: {
                     image: [['float', ['floatLeft', 'floatRight', 'floatNone']], ['remove', ['removeMedia']]]
                 },
                 dialogsFade: true
             });
        }

        /**
         * Init properties list
         */
        function initPropertiesList() {
            $(document).on('click', '.delete-properies', function () {
                $(this).parent().remove();
            });
            $(document).on('click', '.add-properies', function () {
                var inputProperties = $('.input-properties').val();
                if (inputProperties.length) {
                    $propertiesList.append('<li class="list-group-item">' + inputProperties + ' <span class="fa fa-times delete-properies"></span><input type="hidden" name="properties[]" value="' + inputProperties + '"></li>');
                    $('.input-properties').val('');
                }
            });
        }

        /*******************************************************************************************
         * Create new product routine
         *******************************************************************************************/
        function createProduct(){

        }

        /*******************************************************************************************
         * Update exiting product routine
         *******************************************************************************************/
        function updateProduct(){

            $modalLoader.removeClass('hidden');
            $.ajax({
                url: actionGetUrl + '/' + currentProductId,
                type: "GET",
                success: function (data, textStatus, jqXHR) {
                    if (data.service) {
                    }
                    $modalLoader.addClass('hidden');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log('Something was wrong...', textStatus, errorThrown, jqXHR);
                    $modalLoader.addClass('hidden');
                }
            });
        }

        /*******************************************************************************************
         * Modal Events
         *******************************************************************************************/
        /**
         * Modal Hide Event
         */
        $modal.on('hidden.bs.modal', function () {
            resetForm();
            $errorContainer.empty();
        });

        /**
         * Modal Show Event
         */
        $modal.on('show.bs.modal', function (event) {
            // Define if pressed "Add Service" or "Edit" exiting
            var button = $(event.relatedTarget);

            currentProductId = button.data('id') || undefined; // id or undefined

            // Define UI elements captions depends on mode save|update
            var modalTitle = currentProductId ? 'Update product' : 'Add product',
                submitTitle = currentProductId ? 'Save product' : 'Add product';

            $modalTitle.html(modalTitle);
            $submitProductForm.html(submitTitle);

            if (currentProductId === undefined) {
                createProduct();
            } else {
                updateProduct();
            }
        });

    })({}, function () {})
</script>









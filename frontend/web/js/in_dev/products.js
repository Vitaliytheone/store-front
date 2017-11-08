// TODO:: Convert scripts to Custom module after developing is finished

/**
 * Create/Update Service form script
 */
(function (window, alert) {
    'use strict';
    var formName = 'ProductForm';

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
        $modalLoader.removeClass('hidden');
        $.ajax({
            url: saveActionUrl,
            type: "POST",
            data: $(this).serialize(),
            success: function (data, textStatus, jqXHR) {
                $modalLoader.addClass('hidden');
                if (data.error) {
                    $errorContainer.append(data.error.html);
                    return;
                }
                //Success
                $modal.modal('hide');
                _.delay(function () {
                    $(location).attr('href', '/admin/products');
                }, 500);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $modalLoader.addClass('hidden');
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
        //Reset inputs & textarea
        $productForm.find('input').val('');
        $productForm.find('textarea').val('');
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

    function fillFormFields(data){
        console.log('fill form fields');
        var defaultData = {
            name : '',
            description : '',
            properties : [],
            url : '',
            seo_title : '',
            seo_description : ''
        },
        formData = _.defaults(data, defaultData);

        var $formFields = {
            name            : $productForm.find('[name = "ProductForm[name]"]'),
            description     : $productForm.find('[name = "ProductForm[description]"]'),
            properties      : $productForm.find('[name = "ProductForm[properties]"]'),
            url             : $productForm.find('[name = "ProductForm[url]"]'),
            seo_title       : $productForm.find('[name = "ProductForm[seo_title]"]'),
            seo_description : $productForm.find('[name = "ProductForm[seo_description]"]')
        };

        // Fill form data
        $formFields.name.val(formData.name);
        $formFields.description.val(formData.description);
        $formFields.url.val(formData.url);
        $formFields.seo_title.val(formData.seo_title);
        $formFields.seo_description.val(formData.seo_description);

        $formFields.description.val(formData.description);



        _.each($formFields, function($field, key, list){
            $field.val(formData[key]);
        });

        var $name = $productForm.find('[name = "ProductForm[name]"]'),
            $filedDescription = $productForm.find('[name = "ProductForm[description]"]');


    }

    /**
     * Init properties list
     */
    function initPropertiesList(){
        $(document).on('click', '.delete-properies', function () {
            $(this).parent().remove();
        });
        $(document).on('click', '.add-properies', function () {
            var inputProperties = $('.input-properties').val();
            if (inputProperties.length) {
                $propertiesList.append('<li class="list-group-item">' + inputProperties + '<span class="fa fa-times delete-properies"></span><input type="hidden" name="' + formName + '[properties][]" value="' + inputProperties + '"></li>');
                $('.input-properties').val('');
            }
        });
    }

    /*******************************************************************************************
     * Create new product routine
     *******************************************************************************************/
    function createProduct(){
        // fillFormFields({
        //     name : 'Default',
        //     description : '',
        //     properties : ['4321312','4321', '4321', '312312'],
        //     url : '',
        //     seo_title : '',
        //     seo_description : ''
        // });
        // $('#edit-seo__url').val($('#edit-page-title').val()).trigger('input')
    }

    /*******************************************************************************************
     * Update exiting product routine
     *******************************************************************************************/
    function updateProduct(){

        $modalLoader.removeClass('hidden');
        // Get exiting product
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
        resetForm();

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

})({}, function () {});

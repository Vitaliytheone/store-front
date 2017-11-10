// TODO:: Convert scripts to Custom module after developing is finished

/**
 * Create/Update Service form script
 */
(function (window, alert){
    'use strict';
    var formName = 'ProductForm';

    var $modal = $('.add_product'),

        $productForm = $('#productForm'),
        $submitProductForm = $('#submitProductForm'),
        $cancelProductForm = $('#cancelProductForm'),

        $modalTitle = $modal.find('.modal-title'),
        $errorContainer = $('#product-form-error'),
        $modalLoader = $modal.find('.modal-loader'),

        defaultFormData,

        currentProductId,
        actionCreateUrl = '/admin/products/create-product',     // POST /admin/products/create-product
        actionUpdateUrl = '/admin/products/update-product',     // POST /admin/products/update-product/{:id}
        actionGetUrl = '/admin/products/get-product';           // GET  /admin/products/get-product/{:id}

    var $formFields = {
        name            : $productForm.find('.form_field__name'),
        description     : $productForm.find('.form_field__description'),
        properties      : $productForm.find('.form_field__properties'),
        url             : $productForm.find('.form_field__url'),
        visibility      : $productForm.find('.form_field__visibility'),
        seo_title       : $productForm.find('.form_field__seo_title'),
        seo_description : $productForm.find('.form_field__seo_description')
    };

    defaultFormData = {
        name            : $formFields.name.val(),
        description     : $formFields.description.val(),
        properties      : [],
        url             : $formFields.url.val(),
        visibility      : $formFields.visibility.val(),
        seo_title       : $formFields.seo_title.val(),
        seo_description : $formFields.seo_description.val()
    };

    initPropertiesList();
    initSeoParts();
    initSummernote($formFields.description);

    /*******************************************************************************************
     * Save Product form data
     *******************************************************************************************/
    $productForm.submit(function (e){
        // Define action Url depends on Create new product or Save exiting
        var saveActionUrl = currentProductId ? actionUpdateUrl + '?id=' + currentProductId : actionCreateUrl;

        e.preventDefault();
        $modalLoader.removeClass('hidden');
        $.ajax({
            url: saveActionUrl,
            type: "POST",
            data: $(this).serialize(),
            success: function (data, textStatus, jqXHR){
                $modalLoader.addClass('hidden');
                if (data.error){
                    $errorContainer.append(data.error.html);
                    $modal.animate({ scrollTop: 0 }, 'slow');
                    return;
                }
                //Success
                $modal.modal('hide');
                _.delay(function (){
                    $(location).attr('href', '/admin/products');
                }, 1000);
            },
            error: function (jqXHR, textStatus, errorThrown){
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
     *  Fill form fields by data
     * @param data
     */
    function fillFormFields(data){
        var defaultData, formData;
        if (data !== undefined && _.isObject(data)){
            defaultData = {
                name : '',
                description : '',
                properties : [],
                visibility : 1,
                url : '',
                seo_title : '',
                seo_description : ''
            };
            formData = _.defaults(data, defaultData);

            // Fill form data
            $formFields.name.val(formData.name).trigger('input');
            $formFields.visibility.val(formData.visibility).trigger('change');
            $formFields.url.val(formData.url).trigger('input');
            $formFields.seo_title.val(formData.seo_title).trigger('input');
            $formFields.seo_description.val(formData.seo_description).trigger('input');

            // Fill summernote
            $formFields.description.summernote('code', formData.description);

            // Fill properties array
            _.each(formData.properties, function (value, key, list){
                $formFields.properties.append(getPropertyField(value, 'properties', formName));
            });
        }
    }

    /**
     * Reset form fields to init values
     */
    function resetForm(){
        //Reset inputs & textarea
        $productForm.find('input').val('');
        $productForm.find('textarea').val('');
        //Reset note-editor
        $formFields.description.summernote('reset');
        //Reset properties list
        $formFields.properties.empty();
    }

    /**
     * Init Summernote editor
     */
    function initSummernote($element){
        $element.summernote({
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
    function initPropertiesList(){
        $(document).on('click', '.delete-properies', function (){
            $(this).parent().remove();
        });
        $(document).on('click', '.add-properies', function (){
            var inputProperties = $('.input-properties').val();
            if (inputProperties.length){
                $formFields.properties.append(getPropertyField(inputProperties, 'properties', formName));
                $('.input-properties').val('');
            }
        });
    }

    /**
     * Return vallid address path by passed string
     * a-z, -_ ,0-9
     * @param string
     */
    function getValidAddressByString(string){
        return string.replace(/[^a-z0-9_\-\s]/gmi, "").replace(/\s+/g, '-');
    }

    /**
     * Init auto-fill SEO-edit part
     */
    function initSeoParts(){
        if ($('.edit-seo__title').length > 0){
            (function (){

                var seoEdit = ['edit-seo__title', 'edit-seo__meta', 'edit-seo__url'];

                var _loop = function _loop(i){
                    $("." + seoEdit[i] + '-muted').text($("#" + seoEdit[i]).val().length);
                    $("#" + seoEdit[i]).on('input', function (e){
                        if (i == 2){
                            $('.' + seoEdit[i]).text(getValidAddressByString($(e.target).val()));
                        } else {
                            $("." + seoEdit[i] + '-muted').text($(e.target).val().length);
                            $('.' + seoEdit[i]).text($(e.target).val());
                        }
                    });
                };

                for (var i = 0; i < seoEdit.length; i++){
                    _loop(i);
                }
            })();
        }
    }

    /**
     * Return Properties Item field
     * @param propertyText
     * @param propertyFieldName
     * @param formName
     * @returns {string}
     */
    function getPropertyField(propertyText, propertyFieldName, formName){
        var propertyName = formName ? formName + '[' + propertyFieldName + '][]' : propertyFieldName + '[]';
        return '<li class="list-group-item">' + propertyText + '<span class="fa fa-times delete-properies"></span><input type="hidden" name="'+ propertyName +'" value="'+ propertyText +'"></li>';
    }

    /*******************************************************************************************
     * Create new product routine
     *******************************************************************************************/
    function createProduct(){
        fillFormFields(defaultFormData);
        /* Events subscriptions */
        bindCreateProductEvents();

        $formFields.name.focus();
    }

    function bindCreateProductEvents(){
        // Start autofilling URL
        $formFields.name.on('input.create_product', autofillUrl);
        // Stop autofill on first user's touch
        $formFields.url.on('focus.create_product', autofillUrlOff);
        // Start cleanup url
        $formFields.url.on('input.create_product', cleanupUrl);
    }

    function unbindCrereateProductEvents(){
        // Stop autofilling URL
        $formFields.name.off('input.create_product');
        // Stop autofill on first user's touch
        $formFields.url.off('focus.create_product');
        // Stop cleanup url
        $formFields.url.off('input.create_product');
    }
    
    /**
     * Autofilling `url` by `product name`
     */
    function autofillUrl(e){
        var inputName = $(e.target).val();
        $formFields.url.val(inputName).trigger('input');
    }

    /**
     * Stop autofilling `url` by `product name`
     */
    function autofillUrlOff(){
        $formFields.name.off('input', autofillUrl);
    }

    /**
     * Cleanup url
     */
    function cleanupUrl(e){
        var urlMaxLenght = 200,
            urlByName = '',
            inputedName = $(e.target).val();

        urlByName = getValidAddressByString(inputedName);
        if (urlByName.length >= urlMaxLenght){
            urlByName = urlByName.substring(0, (urlMaxLenght-1));
        }
        $formFields.url.val(urlByName.toLowerCase());
    }

    /*******************************************************************************************
     * Update exiting product routine
     *******************************************************************************************/
    function updateProduct(){
        bindEditProductEvents();

        $modalLoader.removeClass('hidden');
        // Get exiting product
        $.ajax({
            url: actionGetUrl + '?id=' + currentProductId,
            type: "GET",
            success: function (data, textStatus, jqXHR){
                if (data.product){
                    fillFormFields(data.product);
                }
                $modalLoader.addClass('hidden');
            },
            error: function (jqXHR, textStatus, errorThrown){
                console.log('Something was wrong...', textStatus, errorThrown, jqXHR);
                $modalLoader.addClass('hidden');
            }
        });
    }

    function bindEditProductEvents(){
        // Start cleanup url
        $formFields.url.on('input.update_product', cleanupUrl);
    }

    function unbindEditProductEvents(){
        // Stop cleanup url
        $formFields.url.off('input.update_product');
    }


    /*******************************************************************************************
     * Modal Events
     *******************************************************************************************/
    /**
     * Modal Hide Event
     */
    $modal.on('hidden.bs.modal', function (){
        /* Unbind events */
        unbindCrereateProductEvents();
        unbindEditProductEvents();

        resetForm();
        $errorContainer.empty();
    });

    /**
     * Modal Show Event
     */
    $modal.on('shown.bs.modal', function (event){
        resetForm();
        // Define if pressed "Add Service" or "Edit" exiting
        var button = $(event.relatedTarget);

        currentProductId = button.data('id') || undefined; // id or undefined


        // Define UI elements captions depends on mode save|update
        var modalTitle = currentProductId ? 'Update product' : 'Add product',
            submitTitle = currentProductId ? 'Save product' : 'Add product';

        $modalTitle.html(modalTitle);
        $submitProductForm.html(submitTitle);

        if (currentProductId === undefined){
            createProduct();
        } else {
            updateProduct();
        }
    });

})({}, function (){});

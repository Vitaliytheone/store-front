// TODO:: Convert scripts to Custom module after developing is finished

/*****************************************************************************************************
 *                      Create/Update Products form script
 *****************************************************************************************************/
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

        $addPropertyInput = $modal.find('.input-properties'),
        defaultFormData,

        currentProductId,
        currentActionUrl;

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
        e.preventDefault();
        $modalLoader.removeClass('hidden');
        $.ajax({
            url: currentActionUrl,
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
            addProperty();
        });
        $addPropertyInput.on('keyup', function (e) {
            if (e.keyCode !== 13) {
                return;
            }
            addProperty();
        });

        function addProperty(){
            var inputProperties = $addPropertyInput.val();
            if (inputProperties.length){
                $formFields.properties.append(getPropertyField(inputProperties, 'properties', formName));
                $addPropertyInput.val('').focus();
            }
        }
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
    function updateProduct(productUrl){
        bindEditProductEvents();

        $modalLoader.removeClass('hidden');
        // Get exiting product
        $.ajax({
            url: productUrl,
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
     * Modal Hide Events
     */
    $modal.on('hidden.bs.modal', function (){
        /* Unbind events */
        unbindCrereateProductEvents();
        unbindEditProductEvents();

        resetForm();
        $errorContainer.empty();
    });

    /**
     * Modal Show Events
     */
    $modal.on('show.bs.modal', function (event){
        $modalLoader.removeClass('hidden');
        resetForm();
    });

    $modal.on('shown.bs.modal', function (event){
        $modalLoader.addClass('hidden');

        // Define if pressed "Add Service" or "Edit" exiting
        var button = $(event.relatedTarget);
        var productUrl;

        currentProductId = button.data('id') || undefined;
        currentActionUrl = button.data('action-url');

        // Define UI elements captions depends on mode save|update
        var modalTitle = currentProductId ? 'Update product' : 'Add product',
            submitTitle = currentProductId ? 'Save product' : 'Add product';

        $modalTitle.html(modalTitle);
        $submitProductForm.html(submitTitle);

        if (currentProductId === undefined){
            createProduct();
        } else {
            productUrl = button.data('get-url');
            updateProduct(productUrl);
        }
    });

})({}, function (){});



/*****************************************************************************************************
 *                      Create/Update Package form script
 *****************************************************************************************************/
(function (window, alert){
    'use strict';

    var $modal = $('.add_package'),

        $packageForm = $('#packageForm'),
        $submitPackageForm = $('#submitPackageForm'),
        $cancelPackageForm = $('#cancelPackageForm'),

        $modalTitle = $modal.find('.modal-title'),
        $errorContainer = $('#package-form-error'),
        $modalLoader = $modal.find('.modal-loader'),

        currentPackageId,
        currentActionUrl;

    var $formFields = {
        name            : $packageForm.find('.form_field__name'),
        price           : $packageForm.find('.form_field__price'),
        quantity        : $packageForm.find('.form_field__quantity'),
        link_type       : $packageForm.find('.form_field__link_type'),
        visibility      : $packageForm.find('.form_field__visibility'),
        best            : $packageForm.find('.form_field__best'),
        mode            : $packageForm.find('.form_field__mode'),
        product_id      : $packageForm.find('.form_field__product_id')
    };

    var defaultFormData = {
        name            : $formFields.name.val(),
        price           : $formFields.price.val(),
        quantity        : $formFields.quantity.val(),
        link_type       : $formFields.link_type.val(),
        visibility      : $formFields.visibility.val(),
        best            : $formFields.best.val(),
        mode            : $formFields.mode.val(),
        product_id      : $formFields.product_id.val()
    };

    /*******************************************************************************************
     * Save Package form data
     *******************************************************************************************/
    $packageForm.submit(function (e){
        e.preventDefault();
        $modalLoader.removeClass('hidden');
        $.ajax({
            url: currentActionUrl,
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
     * @param formData
     */
    function fillFormFields(formData){
        if (formData === undefined || !_.isObject(formData)){
            return;
        }
        _.each(formData, function(fieldValue, formField, list){
            if (!_.has($formFields, formField)) {
                return;
            }
            $formFields[formField].val(fieldValue);
        });
    }

    /**
     * Reset form fields to init values
     */
    function resetForm(){
        $errorContainer.empty();
        fillFormFields(defaultFormData);
    }

    /*******************************************************************************************
     * Create new package for product routine
     *******************************************************************************************/
    function createPackage(productId){
        bindCreatePackageEvents();
        $formFields.product_id.val(productId);
        $formFields.name.focus();
    }

    function bindCreatePackageEvents(){
    }

    function unbindCreatePackageEvents(){
    }

    /*******************************************************************************************
     * Update exiting package routine
     *******************************************************************************************/
    function updatePackage(packageUrl){
        bindEditPackageEvents();
        $modalLoader.removeClass('hidden');
        // Get exiting package
        $.ajax({
            url: packageUrl,
            type: "GET",
            success: function (data, textStatus, jqXHR){
                if (data.package){
                    fillFormFields(data.package);
                }
                $modalLoader.addClass('hidden');
            },
            error: function (jqXHR, textStatus, errorThrown){
                console.log('Something was wrong...', textStatus, errorThrown, jqXHR);
                $modalLoader.addClass('hidden');
            }
        });
    }

    function bindEditPackageEvents(){
    }

    function unbindEditProductEvents(){
    }


    /*******************************************************************************************
     * Modal Events
     *******************************************************************************************/
    /**
     * Modal Hide Events
     */
    $modal.on('hidden.bs.modal', function (){
        /* Unbind events */
        unbindCreatePackageEvents();
        unbindEditProductEvents();

        resetForm();
    });

    /**
     * Modal Show Events
     */
    $modal.on('show.bs.modal', function (event){
        $modalLoader.removeClass('hidden');
    });

    $modal.on('shown.bs.modal', function (event){
        $modalLoader.addClass('hidden');

        // Define if pressed "Add Service" or "Edit" exiting
        var button = $(event.relatedTarget);
        var packageUrl, productId;

        currentPackageId = button.data('id') || undefined;
        currentActionUrl = button.data('action-url');

        // Define UI elements captions depends on mode save|update
        var modalTitle = currentPackageId ? 'Edit package' : 'Add package',
            submitTitle = currentPackageId ? 'Save package' : 'Add package';

        $modalTitle.html(modalTitle);
        $submitPackageForm.html(submitTitle);

        if (currentPackageId === undefined){
            productId = button.data('product_id');
            createPackage(productId);
        } else {
            packageUrl = button.data('get-url');
            updatePackage(packageUrl);
        }
    });

})({}, function (){});


/*****************************************************************************************************
 *                      Delete (mark as deleted) Package
 *****************************************************************************************************/
(function (window, alert){
    'use strict';
    var $modal = $('#delete-modal'),
        $modalLoader = $modal.find('.modal-loader'),
        buttonDelete = $modal.find('#feature-delete'),
        actionUrl;

    buttonDelete.on('click', function(){
        $modalLoader.removeClass('hidden');
        $.ajax({
            url: actionUrl,
            type: "DELETE",
            success: function (data, textStatus, jqXHR){
                $modalLoader.addClass('hidden');
                if (data.error){
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
    });

    $modal.on('show.bs.modal', function (event){
        var button = $(event.relatedTarget);
        actionUrl = button.data('action-url');
    });

    $modal.on('hidden.bs.modal', function (){
        actionUrl = null;
    });

})({}, function (){});

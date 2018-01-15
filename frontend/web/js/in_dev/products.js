// TODO:: Convert scripts to Custom module after developing is finished


/*****************************************************************************************************
 *                     Sortable Products-Packages
 *****************************************************************************************************/
(function (window, alert){
    var $productsSortable = $('.sortable'),
        $packagesSortable = $(".group-items");

    // Init sortable
    if ($productsSortable.length > 0) {
        // Sort the parents
        $productsSortable.sortable({
            containment: "document",
            items: "> div.product-item",
            handle: ".move",
            tolerance: "pointer",
            cursor: "move",
            opacity: 0.7,
            revert: false,
            delay: false,
            placeholder: "movable-placeholder"
        });

        // Sort the children
        $packagesSortable.sortable({
            items: "> div.package-item",
            handle: ".move",
            tolerance: "pointer",
            containment: "parent"
        });
    }

    $productsSortable.sortable({
        update: function(event, ui) {
            var currentItem = ui.item,
                newPosition = currentItem.index(),
                actionUrl = currentItem.data('action-url') + newPosition;

            $.ajax({
                url: actionUrl,
                type: "POST",
                success: function (data, textStatus, jqXHR){
                    if (data.error){
                        return;
                    }
                    //Success
                },
                error: function (jqXHR, textStatus, errorThrown){
                    console.log('Error on save', jqXHR, textStatus, errorThrown);
                }
            });
        }
    });

    $packagesSortable.sortable({
        update: function (event, ui) {
            var currentItem = ui.item,
                newPosition = currentItem.index(),
                actionUrl = currentItem.data('action-url') + newPosition;

            $.ajax({
                url: actionUrl,
                type: "POST",
                success: function (data, textStatus, jqXHR) {
                    if (data.error) {
                        return;
                    }
                    //Success
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log('Error on save', jqXHR, textStatus, errorThrown);
                }
            });
        }
    });

})({}, function (){});

/*****************************************************************************************************
 *                      Create/Update Products form script
 *****************************************************************************************************/
(function (window, alert){
    'use strict';

    var _self = this;

    var formName = 'ProductForm';

    var $modal = $('.add_product'),

        $productForm = $('#productForm'),
        $submitProductForm = $('#submitProductForm'),
        $cancelProductForm = $('#cancelProductForm'),

        $modalTitle = $modal.find('.modal-title'),
        $errorContainer = $('#product-form-error'),
        $modalLoader = $modal.find('.modal-loader'),
        $seoCollapse = $modal.find('.collapse'),

        $addPropertyInput = $modal.find('.input-properties'),
        $inputPropertyError = $modal.find('.empty-property-error'),

        currentProductId,
        currentActionUrl,
        successRedirectUrl,
        getExitingUrlsUrl;

    var exitingUrls;

    var $formFields = {
        name            : $productForm.find('.form_field__name'),
        description     : $productForm.find('.form_field__description'),
        properties      : $productForm.find('.form_field__properties'),
        url             : $productForm.find('.form_field__url'),
        visibility      : $productForm.find('.form_field__visibility'),
        seo_title       : $productForm.find('.form_field__seo_title'),
        seo_description : $productForm.find('.form_field__seo_description')
    };

    var defaultFormData = {
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
                if (data.error){
                    $modalLoader.addClass('hidden');
                    $errorContainer.append(data.error.html);
                    $modal.animate({ scrollTop: 0 }, 'slow');
                    $seoCollapse.collapse("show");
                    return;
                }
                //Success
                _.delay(function(){
                    $(location).attr('href', successRedirectUrl);
                    // $modalLoader.addClass('hidden');
                    // $modal.modal('hide');
                }, 500);
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

        /* fix summernote submit form on Enter bug */
        $productForm.keypress(function (e) {
            if (e.which === 13) {
                $productForm.submit();
                return false;
            }
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
            checkInput();
        });
        $addPropertyInput.keypress(function (e) {
            if (e.which !== 13) {
                return;
            }
            e.stopImmediatePropagation();
            e.preventDefault();
            checkInput();
        });
        $addPropertyInput.focusout(function (e) {
            $inputPropertyError.addClass('d-none');
        });

        function checkInput(){
            var inputProperty = $addPropertyInput.val(),
                length = inputProperty.length;

            if (!!length) {
                addProperty(inputProperty);
            }
            $inputPropertyError.toggleClass('d-none', !!length);
        }

        function addProperty(property){
            $formFields.properties.append(getPropertyField(property, 'properties', formName));
            $addPropertyInput.val('').focus();
        }
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
                            $('.' + seoEdit[i]).text($(e.target).val().toLowerCase());
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

        fetchExitingUrls();

        $(document).on('urls-fetched', function(e, urls){

            exitingUrls = urls;

            fillFormFields(defaultFormData);

            /* Events subscriptions */
            bindCreateProductEvents();

            $formFields.name.focus();
        });
    }

    function bindCreateProductEvents(){
        // Start autofilling URL
        $formFields.name.on('input.create_product', autoFillFields);

        // Stop autofill on first user's touch
        $formFields.url.on('focus.create_product', autoFillFieldsOff);
        $formFields.seo_title.on('focus.create_product', autoFillFieldsOff);

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
     * Fetch exiting url
     */
    function fetchExitingUrls(){
        $.ajax({
            url: getExitingUrlsUrl,
            type: "GET",
            success: function ($urls, textStatus, jqXHR){
                $(document).trigger('urls-fetched', [$urls]);
            },
            error: function (jqXHR, textStatus, errorThrown){
                $modalLoader.addClass('hidden');
                $modal.modal('hide');
                console.log('Error on service save', jqXHR, textStatus, errorThrown);
            }
        });
    }

    /**
     * Autofilling `url` by `product name`
     */
    function autoFillFields(e){
        var inputName = $(e.target).val(),
            generatedUrl;

        generatedUrl = custom.generateUrlFromString(inputName);
        generatedUrl = custom.generateUniqueUrl(generatedUrl, exitingUrls);

        // Autofill Url
        $formFields.url.val(generatedUrl).trigger('input');
        // Autofill Title
        $formFields.seo_title.val(inputName).trigger('input');
    }

    /**
     * Stop autofilling `url` by `product name`
     */
    function autoFillFieldsOff(){
        $formFields.name.off('input', autoFillFields);
        $formFields.seo_title.off('input', autoFillFields);
    }

    /**
     * Cleanup url
     */
    function cleanupUrl(e){
        var urlMaxLenght = 200,
            urlByName,
            target = e.currentTarget,
            $target = $(target),
            inputedName = $target.val();

        var position = target.selectionStart;
        
        urlByName = custom.generateUrlFromString(inputedName);

        if (urlByName.length >= urlMaxLenght){
            urlByName = urlByName.substring(0, (urlMaxLenght-1));
        }

        $target.val(urlByName);

        target.selectionEnd = position;
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
        $inputPropertyError.addClass('d-none');
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
        successRedirectUrl = $productForm.data('success_redirect');
        getExitingUrlsUrl = $productForm.data('get_urls_url');

        // Define UI elements captions depends on mode create|edit
        var $dataTitle = $modal.find('.modal-header'),
            modalTitle = currentProductId ? $dataTitle.data('title_edit') : $dataTitle.data('title_create'),
            submitTitle = currentProductId ? $submitProductForm.data('title_save') : $submitProductForm.data('title_create');

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
        $apiError = $modal.find('.api-error'),

        packageModel,
        currentPackageId,
        currentActionUrl,
        successRedirectUrl,
        ajaxTimeoutMessage;

    var $formFields = {
        name                : $packageForm.find('.form_field__name'),
        price               : $packageForm.find('.form_field__price'),
        quantity            : $packageForm.find('.form_field__quantity'),
        link_type           : $packageForm.find('.form_field__link_type'),
        visibility          : $packageForm.find('.form_field__visibility'),
        best                : $packageForm.find('.form_field__best'),
        mode                : $packageForm.find('.form_field__mode'),
        provider_id         : $packageForm.find('.form_field__provider_id'),
        provider_service    : $packageForm.find('.form_field__provider_service'),
        product_id          : $packageForm.find('.form_field__product_id')
    };

    var defaultFormData = {
        name                : $formFields.name.val(),
        price               : $formFields.price.val(),
        quantity            : $formFields.quantity.val(),
        link_type           : $formFields.link_type.val(),
        visibility          : $formFields.visibility.val(),
        best                : $formFields.best.val(),
        mode                : $formFields.mode.val(),
        provider_id         : $formFields.provider_id.val(),
        provider_service    : $formFields.provider_service.val(),
        product_id          : $formFields.product_id.val()
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
                if (data.error){
                    $modalLoader.addClass('hidden');
                    $errorContainer.append(data.error.html);
                    $modal.animate({ scrollTop: 0 }, 'slow');
                    return;
                }
                //Success
                _.delay(function(){
                    $(location).attr('href', successRedirectUrl);
                    // $modalLoader.addClass('hidden');
                    // $modal.modal('hide');
                }, 500);
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

    function bindCommonPackageEvents(){

        $formFields.mode.on('change', function(e) {
            var mode = parseInt($(this).val());
            $formFields.provider_id.closest('.form-group').toggleClass('d-none', !mode);
            $formFields.provider_service.closest('.form-group').toggleClass('d-none', !mode);
            $apiError.addClass('d-none');
            $errorContainer.empty();
        });

        // Change `provider_id` => fetch provider`s services
        $formFields.provider_id.on('change', function(e, selectedServiceId){
            var $optionSelected = $("option:selected", this),
                actionUrl = $optionSelected.data('action-url'),
                ajaxTimeoutMessage = $formFields.provider_service.data('ajax_timeout_message');

            $errorContainer.empty();

            clearProviderServisesList();
            if (actionUrl === undefined) {
                hideApiError();
                return;
            }
            $modalLoader.removeClass('hidden');
            $.ajax({
                url: actionUrl,
                type: "GET",
                timeout: 15000,
                success: function(data, textStatus, jqXHR) {

                    if (data.hasOwnProperty('error')) {
                        showApiError(data.message);
                    } else {
                        hideApiError();
                        renderProviderServices(data, selectedServiceId);
                    }

                    $modalLoader.addClass('hidden');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    var errorMessage = '';
                    // Timeout error
                    if (textStatus === "timeout") {
                        errorMessage = ajaxTimeoutMessage;
                    }  else {
                        errorMessage = jqXHR.responseJSON.message;
                    }

                    console.log('Something was wrong...', textStatus, errorThrown, jqXHR);
                    $modalLoader.addClass('hidden');
                    showApiError(errorMessage);
                }
            });
        });
    }

    function unbindCommonPackageEvents(){
        $formFields.mode.off('change');
        $formFields.provider_id.off('change');
    }

    /**
     * Show provider Api error message
     * @param errorMessage
     */
    function showApiError(errorMessage){
        $apiError.removeClass('d-none').html(errorMessage);
        $formFields.provider_service.closest('.provider-service-group').addClass('d-none');
    }

    /**
     * Hide provider Api error message
     */
    function hideApiError(){
        $apiError.addClass('d-none').html('');
        $formFields.provider_service.closest('.provider-service-group').removeClass('d-none');
    }

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
        hideApiError();
        fillFormFields(defaultFormData);
    }

    /** Render array of Provider Services
     * @param services
     * @param selectedServiceId service_id | undefined if new package
     */
    function renderProviderServices(services, selectedServiceId){
        var selected,
            $container = $('<div></div>');
        _.each(services, function (s) {
            if (selectedServiceId) {
                selected = s.service.toString() === selectedServiceId.toString() ? 'selected' : '';
            }
            $container.append('<option value="' + s.service + '"'+ selected + '>' + s.service + ' - ' + s.name + '</option>');
        });
        clearProviderServisesList();
        $formFields.provider_service.append($container.html());
    }

    function clearProviderServisesList() {
        $formFields.provider_service.find("option:not(:eq(0))").remove();
        $formFields.provider_service.find('option:eq(0)').prop('selected', true);
    }

    /*******************************************************************************************
     * Create new package for product routine
     *******************************************************************************************/
    function createPackage(productId){
        bindCommonPackageEvents();
        bindCreatePackageEvents();
        $formFields.product_id.val(productId);
        $formFields.name.focus();
        $formFields.mode.trigger('change');
    }

    function bindCreatePackageEvents(){
        // // Change `mode`
        // $formFields.mode.on('change', function(e){
        //     var mode = parseInt($(this).val());
        //     // Activate default provider & default in list
        //     if (mode === 1) {
        //         $formFields.provider_id.find('option:eq(0)').prop('selected', true);
        //         $formFields.provider_service.find('option:eq(0)').prop('selected', true);
        //     }
        // });
    }

    function unbindCreatePackageEvents(){
        $formFields.mode.off('change');
    }

    /*******************************************************************************************
     * Update exiting package routine
     *******************************************************************************************/
    function updatePackage(packageUrl){
        bindCommonPackageEvents();
        bindEditPackageEvents();
        $modalLoader.removeClass('hidden');
        // Get exiting package
        packageModel = null;
        $.ajax({
            url: packageUrl,
            type: "GET",
            success: function (data, textStatus, jqXHR){
                packageModel = data.package;
                if (packageModel){
                    fillFormFields(packageModel);
                    $modalLoader.addClass('hidden');
                    $formFields.mode.trigger('change');
                }
            },
            error: function (jqXHR, textStatus, errorThrown){
                console.log('Something was wrong...', textStatus, errorThrown, jqXHR);
                $modalLoader.addClass('hidden');
            }
        });
    }

    function bindEditPackageEvents(){
        // Change `mode`
        $formFields.mode.on('change', function(e){
            var mode = parseInt($(this).val()),
            providerId;
            if (mode === 1) {
                providerId = packageModel.provider_id;
                // Activate first provider in list
                if (providerId === undefined || providerId === null) {
                    $formFields.provider_id.find('option:eq(0)').prop('selected', true).trigger('change');
                    return;
                }
                $formFields.provider_id.trigger('change', [packageModel.provider_service]);
            }
        });
    }

    function unbindEditPackageEvents(){
        $formFields.mode.off('change');
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
        unbindEditPackageEvents();
        unbindCommonPackageEvents();
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
        successRedirectUrl = $packageForm.data('success_redirect');

        // Define UI elements captions depends on mode save|update
        // var modalTitle = currentPackageId ? 'Edit package' : 'Add package',
        //     submitTitle = currentPackageId ? 'Save package' : 'Add package';

        var $dataTitle = $modal.find('.modal-header'),
            modalTitle = currentPackageId ? $dataTitle.data('title_edit') : $dataTitle.data('title_create'),
            submitTitle = currentPackageId ? $submitPackageForm.data('title_save') : $submitPackageForm.data('title_create');


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

})(window);


/*****************************************************************************************************
 *                      Delete (mark as deleted) Package
 *****************************************************************************************************/
(function (window, alert){
    'use strict';
    var $modal = $('#delete-modal'),
        $modalLoader = $modal.find('.modal-loader'),
        buttonDelete = $modal.find('#feature-delete'),
        actionUrl,
        successRedirectUrl;

    buttonDelete.on('click', function(){
        $modalLoader.removeClass('hidden');
        $.ajax({
            url: actionUrl,
            type: "DELETE",
            success: function (data, textStatus, jqXHR){
                if (data.error){
                    $modalLoader.addClass('hidden');
                    return;
                }
                //Success
                _.delay(function(){
                    $(location).attr('href', successRedirectUrl);
                    // $modalLoader.addClass('hidden');
                    // $modal.modal('hide');
                }, 500);
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
        successRedirectUrl = $modal.data('success_redirect');
    });

    $modal.on('hidden.bs.modal', function (){
        actionUrl = null;
    });

})({}, function (){});

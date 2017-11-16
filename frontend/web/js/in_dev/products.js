// TODO:: Convert scripts to Custom module after developing is finished


/*****************************************************************************************************
 *                     Notifications init
 *****************************************************************************************************/
(function (window, alert){
    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-bottom-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "5000",
        "timeOut": "5000",
        "extendedTimeOut": "5000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

})({}, function (){});

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
    var formName = 'ProductForm';

    var $modal = $('.add_product'),

        $productForm = $('#productForm'),
        $submitProductForm = $('#submitProductForm'),
        $cancelProductForm = $('#cancelProductForm'),

        $modalTitle = $modal.find('.modal-title'),
        $errorContainer = $('#product-form-error'),
        $modalLoader = $modal.find('.modal-loader'),

        $addPropertyInput = $modal.find('.input-properties'),
        $inputPropertyError = $modal.find('.empty-property-error'),
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

        packageModel,
        currentPackageId,
        currentActionUrl;

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
        provider_id         : 0,
        provider_service    : 0,
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

    function bindCommonPackageEvents(){

        $formFields.mode.on('change', function(e) {
            var mode = parseInt($(this).val());
            $formFields.provider_id.closest('.form-group').toggleClass('d-none', !mode);
            $formFields.provider_service.closest('.form-group').toggleClass('d-none', !mode);
        });

        // Change `provider_id` => fetch provider`s services
        $formFields.provider_id.on('change', function(e, selectedServiceId){
            $formFields.provider_service.empty();
            var $optionSelected = $("option:selected", this),
                actionUrl = $optionSelected.data('action-url');
            if (actionUrl === undefined) {
                return;
            }
            $modalLoader.removeClass('hidden');
            $.ajax({
                url: actionUrl,
                type: "GET",
                success: function(data, textStatus, jqXHR) {
                    if (data.services) {
                        renderProviderServices(data.services, selectedServiceId);
                    }
                    $modalLoader.addClass('hidden');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log('Something was wrong...', textStatus, errorThrown, jqXHR);
                    $modalLoader.addClass('hidden');
                    toastr.error(jqXHR.responseJSON.message);
                }
            });
        });
    }

    function unbindCommonPackageEvents(){
        $formFields.mode.off('change');
        $formFields.provider_id.off('change');
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
        fillFormFields(defaultFormData);
    }

    /* Render array of Provider Services */
    /**
     *
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
            $container.append('<option value="' + s.service + '"'+ selected + '>' + s.name + '</option>');
        });
        $formFields.provider_service.empty().html($container.html());
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
        // Change `mode`
        $formFields.mode.on('change', function(e){
            var mode = parseInt($(this).val());
            // Activate first provider in list
            if (mode === 1) {
                $formFields.provider_id.find('option:eq(0)').prop('selected', true).trigger("change");
            }
        });
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

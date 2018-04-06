
customModule.adminProductEdit = {
    run: function(params) {
        /*****************************************************************************************************
         *                      Create/Update Products form script
         *****************************************************************************************************/
        (function (window, alert){
            'use strict';

            var _self = this;

            var productsProperties = _.isArray(params.products) ? params.products : [];

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

                $productsPropertiesList = $modal.find('.list__products_properties'),
                $modalPropertiesCopy = $('#copyPropertiesModal');

            var currentProductId,
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
                color           : $productForm.find('.form_field__color'),
                seo_title       : $productForm.find('.form_field__seo_title'),
                seo_description : $productForm.find('.form_field__seo_description'),
                seo_keywords    : $productForm.find('.form_field__seo_keywords')
            };

            var defaultFormData = {
                name            : $formFields.name.val(),
                description     : $formFields.description.val(),
                properties      : [],
                url             : $formFields.url.val(),
                visibility      : $formFields.visibility.val(),
                color           : $formFields.color.val(),
                seo_title       : $formFields.seo_title.val(),
                seo_description : $formFields.seo_description.val(),
                seo_keywords    : $formFields.seo_keywords.val()
            };

            initPropertiesList();
            initSeoParts();
            initSummernote($formFields.description);
            initColorSpectrum();
            initProductsPropertiesList();

            // Fix modal on modal bug
            $(document).on('hidden.bs.modal', function (event) {
                if ($('.modal:visible').length) {
                    $('body').addClass('modal-open');
                }
            });

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

            /** Init spectrum color plugin */
            function initColorSpectrum() {
                $formFields.color.spectrum({
                    allowEmpty:true,
                    // color: "#ffffff",
                    showInput: true,
                    containerClassName: "full-spectrum",
                    showInitial: true,
                    showPalette: true,
                    showSelectionPalette: true,
                    showAlpha: true,
                    maxPaletteSize: 19,
                    preferredFormat: "hex",
                    localStorageKey: "spectrum.color",
                    change: function(){

                    },
                    palette: [
                        ["#f44336", "#E91E63", "#9C27B0", "#673AB7", "#3F51B5", "#2196F3", "#03A9F4", "#00BCD4", "#009688", "#4CAF50", "#8BC34A", "#CDDC39", "#FFEB3B", "#FFC107", "#FF9800", "#FF5722", "#795548", "#9E9E9E", "#607D8B"],
                        ["#ffebee", "#FCE4EC", "#F3E5F5", "#EDE7F6", "#E8EAF6", "#E3F2FD", "#E1F5FE", "#E0F7FA", "#E0F2F1", "#E8F5E9", "#F1F8E9", "#F9FBE7", "#FFFDE7", "#FFF8E1", "#FFF3E0", "#FBE9E7", "#EFEBE9", "#FAFAFA", "#ECEFF1"],
                        ["#ffcdd2", "#F8BBD0", "#E1BEE7", "#D1C4E9", "#C5CAE9", "#BBDEFB", "#B3E5FC", "#B2EBF2", "#B2DFDB", "#C8E6C9", "#DCEDC8", "#F0F4C3", "#FFF9C4", "#FFECB3", "#FFE0B2", "#FFCCBC", "#D7CCC8", "#F5F5F5", "#CFD8DC"],
                        ["#ef9a9a", "#F48FB1", "#CE93D8", "#B39DDB", "#9FA8DA", "#90CAF9", "#81D4FA", "#80DEEA", "#80CBC4", "#A5D6A7", "#C5E1A5", "#E6EE9C", "#FFF59D", "#FFE082", "#FFCC80", "#FFAB91", "#BCAAA4", "#EEEEEE", "#B0BEC5"],
                        ["#e57373", "#F06292", "#BA68C8", "#9575CD", "#7986CB", "#64B5F6", "#4FC3F7", "#4DD0E1", "#4DB6AC", "#81C784", "#AED581", "#DCE775", "#FFF176", "#FFD54F", "#FFB74D", "#FF8A65", "#A1887F", "#E0E0E0", "#90A4AE"],
                        ["#ef5350", "#EC407A", "#AB47BC", "#7E57C2", "#5C6BC0", "#42A5F5", "#29B6F6", "#26C6DA", "#26A69A", "#66BB6A", "#9CCC65", "#D4E157", "#FFEE58", "#FFCA28", "#FFA726", "#FF7043", "#8D6E63", "#BDBDBD", "#78909C"],
                        ["#f44336", "#E91E63", "#9C27B0", "#673AB7", "#3F51B5", "#2196F3", "#03A9F4", "#00BCD4", "#009688", "#4CAF50", "#8BC34A", "#CDDC39", "#FFEB3B", "#FFC107", "#FF9800", "#FF5722", "#795548", "#9E9E9E", "#607D8B"],
                        ["#e53935", "#D81B60", "#8E24AA", "#5E35B1", "#3949AB", "#1E88E5", "#039BE5", "#00ACC1", "#00897B", "#43A047", "#7CB342", "#C0CA33", "#FDD835", "#FFB300", "#FB8C00", "#F4511E", "#6D4C41", "#757575", "#546E7A"],
                        ["#d32f2f", "#C2185B", "#7B1FA2", "#512DA8", "#303F9F", "#1976D2", "#0288D1", "#0097A7", "#00796B", "#388E3C", "#689F38", "#AFB42B", "#FBC02D", "#FFA000", "#F57C00", "#E64A19", "#5D4037", "#616161", "#455A64"],
                        ["#c62828", "#AD1457", "#6A1B9A", "#4527A0", "#283593", "#1565C0", "#0277BD", "#00838F", "#00695C", "#2E7D32", "#558B2F", "#9E9D24", "#F9A825", "#FF8F00", "#EF6C00", "#D84315", "#4E342E", "#424242", "#37474F"],
                        ["#b71c1c", "#880E4F", "#4A148C", "#311B92", "#1A237E", "#0D47A1", "#01579B", "#006064", "#004D40", "#1B5E20", "#33691E", "#827717", "#F57F17", "#FF6F00", "#E65100", "#BF360C", "#3E2723", "#212121", "#263238"],
                    ]
                });
            }

            /**
             * Init products-properties list
             */
            function initProductsPropertiesList(){

                var itemTemplate = _.template(
                    '<li class="m-nav__item" data-id="<%- product_id %>">' +
                    '<a href="" class="m-nav__link" data-toggle="modal" data-target="#copyPropertiesModal">' +
                    '<span class="m-nav__link-text"><%- product_title %></span>' +
                    '</a>' +
                    '</li>'
                );

                var $btnSubmitCopy = $modalPropertiesCopy.find('.btn__submit_copy');

                $productsPropertiesList.empty();

                _.each(productsProperties, function (product) {
                    if (!product.properties || !_.isArray(product.properties)) {

                        return;
                    }
                    $productsPropertiesList.append(itemTemplate({
                        product_title : product.name,
                        product_id : product.id
                    }));
                });

                $modalPropertiesCopy.on('shown.bs.modal', function (event){
                    var selectedItem = $(event.relatedTarget),
                        productId =  selectedItem.closest('li').data('id');

                    $btnSubmitCopy.data('id', productId);
                });

                // Copy properties
                $btnSubmitCopy.click(function(){
                    var productId = $(this).data('id'),
                        product;

                    if (productId === undefined) {
                        return;
                    }

                    product = _.find(productsProperties, function(product_item){
                        return parseInt(product_item.id) === parseInt(productId);
                    });

                    if (product === undefined || !_.isArray(product.properties)) {
                        return;
                    }

                    // Render copied properties
                    $formFields.properties.empty();

                    _.each(product.properties, function (property){
                        $formFields.properties.append(getPropertyField(property, 'properties', formName));
                    });

                    toggleCreateNewInfoBox();
                });
            }

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
                        color : '#FFFFFF',
                        url : '',
                        seo_title : '',
                        seo_description : '',
                        seo_keywords : ''
                    };
                    formData = _.defaults(data, defaultData);

                    // Fill form data
                    $formFields.name.val(formData.name).trigger('input');
                    $formFields.visibility.val(formData.visibility).trigger('change');
                    $formFields.color.val(formData.color);
                    $formFields.url.val(formData.url).trigger('input');
                    $formFields.seo_title.val(formData.seo_title).trigger('input');
                    $formFields.seo_description.val(formData.seo_description).trigger('input');
                    $formFields.seo_keywords.val(formData.seo_keywords).trigger('input');

                    // Fill summernote
                    $formFields.description.summernote('code', formData.description);
                    // Fill colorspectrum
                    $formFields.color.spectrum('set', formData.color);

                    // Fill properties array
                    _.each(formData.properties, function (value, key, list){
                        $formFields.properties.append(getPropertyField(value, 'properties', formName));
                    });

                    toggleCreateNewInfoBox();
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

                $formFields.properties.sortable({
                    opacity: 1,
                    tolerance: "pointer",
                    revert: false,
                    delay: false,
                    // placeholder: "movable-placeholder"
                });

                toggleCreateNewInfoBox();

                $(document).on('click', '.action-delete_property', function (){
                    $(this).closest('li').remove();
                    toggleCreateNewInfoBox();
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
                    toggleCreateNewInfoBox();
                }
            }

            function toggleCreateNewInfoBox()
            {
                var toggle = !!$formFields.properties.find('li').length;
                $('.info__create_new_prop').toggleClass('d-none', toggle);
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

                var itemTemplate = _.template(
                    '<li class="dd-item" data-id="3">' +
                    '<div class="dd-handle">' +
                    '<div class="dd-handle__icon">' +
                    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">' +
                    '<title>Drag-handle</title>' +
                    '<path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z" fill="#c6cad4"></path>' +
                    '</svg>' +
                    '</div>' +
                    '<%- title %>' +
                    '</div>' +
                    '<div class="dd-edit-button">' +
                    '<a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill action-delete_property" title="Delete">' +
                    '<i class="la la-trash"></i>' +
                    '</a>' +
                    '</div>' +
                    '<input type="hidden" name="<%- property_name %>" value="<%- property_value %>"' +
                    '</li>'
                );

                return itemTemplate({
                    title : propertyText,
                    property_name : propertyName,
                    property_value : propertyText
                });
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

    }
};

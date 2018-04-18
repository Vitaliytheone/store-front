var custom = new function() {
    var self = this;

    self.request = null;

    self.confirm = function (title, text, options, callback, cancelCallback) {
        var confirmPopupHtml;
        var compiled = templates['modal/confirm'];
        confirmPopupHtml = compiled($.extend({}, true, {
            confirm_button : 'OK',
            cancel_button : 'Cancel',
            width: '600px'
        }, options, {
            'title': title,
            'confirm_message': text
        }));

        $(window.document.body).append(confirmPopupHtml);
        $('#confirmModal').modal({});

        $('#confirmModal').on('hidden.bs.modal', function (e) {
            $('#confirmModal').remove();

            if ('function' == typeof cancelCallback) {
                return cancelCallback.call();
            }
        });

        return $('#confirm_yes').on('click', function (e) {
            $("#confirm_yes").unbind("click");
            $('#confirmModal').modal('hide');
            return callback.call();
        });
    };

    self.ajax = function(options) {
        var settings = $.extend({}, true, options);
        if ("object" === typeof options) {
            options.beforeSend = function() {
                if ('function' === typeof settings.beforeSend) {
                    settings.beforeSend();
                }
            };
            options.success = function(response) {
                if ('function' === typeof settings.success) {
                    settings.success(response);
                }
            };
            null         != self.request ? self.request.abort() : '';
            self.request = $.ajax(options);
        }
    }

    self.notify = function(notifyData) {
        var notifyContainer = $('body'),
            key, value;
        notifyContainer.addClass('bottom-right');

        if ('object' != typeof notifyData) {
            return false;
        }
        for (key in notifyData) {

            value = $.extend({}, true, {
                type	: 'success',
                delay	: 8000,
                text	: '',
            }, notifyData[key]);

            if ('undefined' == typeof value.text || null == value.text) {
                continue;
            }

            $.notify({
                message	: value.text.toString(),
            }, {
                type: value.type,
                placement: {
                    from: "bottom",
                    align: "right"
                },
                z_index : 2000,
                delay: value.delay,
                animate: {
                    enter: 'animated fadeInDown',
                    exit: 'animated fadeOutUp'
                }
            });
        }
    }

    self.sendBtn = function(btn, settings)
    {
        if ('object' != typeof settings) {
            settings = {};
        }

        if (btn.hasClass('active')) {
            return;
        }

        btn.addClass('has-spinner');

        var options = $.extend({}, true, settings);

        options.url = btn.attr('href');

        $('.spinner', btn).remove();

        btn.prepend('<span class="spinner"><i class="fa fa-spinner fa-spin"></i></span>');

        options.beforeSend = function() {
            btn.addClass('active');
        };

        options.success = function(response) {
            btn.removeClass('active');
            $('.spinner', btn).remove();

            if ('success' == response.status) {
                if ('function' === typeof settings.callback) {
                    settings.callback(response);
                }
            } else if ('error' == response.status) {
                self.notify({0: {
                    type : 'danger',
                    text : response.message
                }});
            }
        };

        self.ajax(options);
    }

    self.sendFrom = function(btn, form, settings)
    {
        if ('object' != typeof settings) {
            settings = {};
        }

        if (btn.hasClass('active')) {
            return;
        }

        btn.addClass('has-spinner');

        var options = $.extend({}, true, settings);
        var errorSummary = $('.error-summary', form);

        options.url = form.attr('action');
        options.type = 'POST';

        $('.spinner', btn).remove();

        btn.prepend('<span class="spinner"><i class="fa fa-spinner fa-spin"></i></span>');

        options.beforeSend = function() {
            btn.addClass('active');

            if (errorSummary.length) {
                errorSummary.addClass('hidden');
                errorSummary.html('');
            }
        };

        options.success = function(response) {
            btn.removeClass('active');
            $('.spinner', btn).remove();

            if ('success' == response.status) {
                if ('function' === typeof settings.callback) {
                    settings.callback(response);
                }
            } else if ('error' == response.status) {
                if (response.message) {

                    if (errorSummary.length) {
                        errorSummary.html(response.message);
                        errorSummary.removeClass('hidden');
                    } else {
                        self.notify({0: {
                            type : 'danger',
                            text : response.message
                        }});
                    }
                }

                if (response.errors) {
                    $.each(response.errors, function(key, val) {
                        alert(val);
                        form.yiiActiveForm('updateAttribute', key, val);
                    });
                }

                if ('function' === typeof settings.errorCallback) {
                    settings.errorCallback(response);
                }
            }
        };

        self.ajax(options);
    };

    /**
     * Generate Url path from string
     * a-z, -_ ,0-9
     * @param string
     */
    self.generateUrlFromString = function(string)
    {
        var url = string.replace(/[^a-z0-9_\-\s]/gmi, "").replace(/\s+/g, '-').toLowerCase();

        if (url === '-' || url === '_') {
            url = '';
        }

        return url;
    };

    /**
     * Generate unique url
     * @param url
     * @param exitingUrls
     * @returns {*}
     */
    self.generateUniqueUrl = function(url, exitingUrls)
    {
        var generatedUrl = url,
            exiting,
            prefixCounter;

        prefixCounter = 1;

        do {
            exiting = _.find(exitingUrls, function(exitingUrl){
                return exitingUrl === generatedUrl;
            });

            if (exiting) {
                generatedUrl = url + '-' + prefixCounter;
                prefixCounter ++;
            }
        }
        while (exiting);

        return generatedUrl;
    };

};
var customModule = {};
window.modules = {};

$(function() {
    if ('object' == typeof window.modules) {
        $.each(window.modules, function(name, options) {
            if ('undefined' != typeof customModule[name]) {
                customModule[name].run(options);
            }
        });
    }
});
                var templates = {};
                

templates['modal/confirm'] = _.template("<div class=\"modal fade confirm-modal\" id=\"confirmModal\" tabindex=\"-1\" data-backdrop=\"static\">\n    <div class=\"modal-dialog modal-md\" role=\"document\">\n        <div class=\"modal-content\">\n            <% if (typeof(confirm_message) !== \"undefined\" && confirm_message != \'\') { %>\n            <div class=\"modal-header\">\n                <h3 id=\"conrirm_label\"><%= title %><\/h3>\n                <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\"><span aria-hidden=\"true\">&times;<\/span><\/button>\n            <\/div>\n\n            <div class=\"modal-body\">\n                <p><%= confirm_message %><\/p>\n            <\/div>\n\n\n            <div class=\"modal-footer justify-content-start\">\n                <button class=\"btn btn-primary m-btn--air\" id=\"confirm_yes\"><%= confirm_button %><\/button>\n                <button class=\"btn btn-secondary m-btn--air\" data-dismiss=\"modal\" aria-hidden=\"true\"><%= cancel_button %><\/button>\n            <\/div>\n            <% } else { %>\n            <div class=\"modal-body\">\n                <div class=\"text-center\">\n                    <h3 id=\"conrirm_label\"><%= title %><\/h3>\n                <\/div>\n\n                <div class=\"text-center\">\n                    <button class=\"btn btn-primary m-btn--air\" id=\"confirm_yes\"><%= confirm_button %><\/button>\n                    <button class=\"btn btn-secondary m-btn--air\" data-dismiss=\"modal\" aria-hidden=\"true\"><%= cancel_button %><\/button>\n                <\/div>\n            <\/div>\n            <% } %>\n        <\/div>\n    <\/div>\n<\/div>");
/**
 * /admin/settings custom js module
 * @type {{run: customModule.settings.run}}
 */
customModule.adminGeneral = {
    run: function (params) {
        /******************************************************************
         *            General settings SEO part interaction
         *******************************************************************/
        if ($('.edit-seo__title').length > 0) {
            (function () {

                var $storeName = $('#store-name'),
                    $seoTitle = $('#edit-seo__title');

                var seoTitleOnInit = $seoTitle.val(),
                    seoTitleTouched = false;

                var seoEdit = ['edit-seo__title', 'edit-seo__meta'];
                var _loop = function _loop(i) {
                    $("." + seoEdit[i] + '-muted').text($("#" + seoEdit[i]).val().length);

                    $("#" + seoEdit[i]).on('input', function (e) {
                        $("." + seoEdit[i] + '-muted').text($(e.target).val().length);
                        $('.' + seoEdit[i]).text($(e.target).val());
                    }).trigger('input');

                };
                for (var i = 0; i < seoEdit.length; i++) {
                    _loop(i);
                }

                $seoTitle.on('focus', function (e){
                   seoTitleTouched = true;
                });

                $storeName.on('input', function(e){
                    if (seoTitleOnInit !== '' || seoTitleTouched) {
                        return;
                    }
                    $seoTitle.val($(this).val()).trigger('input');
                });

            })();
        }

        /******************************************************************
         *            General settings delete logo & favicon
         ******************************************************************/
        var $modal = $('#delete-modal'),
        $deleteBtn = $modal.find('#delete-image');

        $modal.on('show.bs.modal', function (event){
            var button = $(event.relatedTarget),
            actionUrl = button.attr('href');
            $deleteBtn.attr('href', actionUrl);
        });

        $modal.on('hidden.bs.modal', function (){
            $deleteBtn.attr('href', null);
        });


        /******************************************************************
         *            General settings favicon & logo
         ******************************************************************/
        $(document).ready(function () {
            $('.settings-file').on('change', function () {

                var dataTarget = $(this).attr('data-target'),
                    that = this,
                    template = '';

                if (that.files && that.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        template = '<div class="sommerce-settings__theme-imagepreview"><img src="'+e.target.result+'" alt="'+that.files[0].name+'" id="setting-logo__preview"></div>'
                        $(dataTarget).html(template);
                    };

                    reader.readAsDataURL(that.files[0]);
                }
            });
        });

    }
};
customModule.adminNavigationEdit = {
    run: function(params) {

        /*****************************************************************************************************
         *                          Create/Edit menu item
         *****************************************************************************************************/
        (function (window, alert){

            var getLinksUrl         = params.get_links_url,
                successRedirectUrl  = params.success_redirect_url;

            var titles = {
                modal_title : [
                    params.modalCreate || 'Add menu item',
                    params.modalEdit || 'Edit menu item'
                ],
                submit_title : [
                    params.submitCreate || 'Add menu item',
                    params.submitEdit || 'Save menu item'
                ]
            };

            var submitModelUrl, getModelUrl;

            var mode; // 0 - Add, 1 - Edit

            var $modal = $('.edit_navigation'),
                $navForm = $('#navForm'),
                $submit = $navForm.find('button:submit'),
                $modalTitle = $modal.find('.modal-title'),
                $errorContainer = $navForm.find('.form-error'),
                $modalLoader = $modal.find('.modal-loader');

            var $formFields = {
                name         : $navForm.find('.form_field__name'),
                link         : $navForm.find('.form_field__link'),
                link_id      : $navForm.find('.form_field__link_id'),
                url          : $navForm.find('.form_field__url')
            };

            var defaultFormData = {
                name         : $formFields.name.val(),
                link         : $formFields.link.val(),
                link_id      : $formFields.link_id.val(),
                url          : $formFields.url.val()
            };

            /*******************************************************************************************
             * Save form data
             *******************************************************************************************/
            $navForm.submit(function (e){
                e.preventDefault();
                $modalLoader.removeClass('hidden');
                $.ajax({
                    url: submitModelUrl,
                    type: "POST",
                    data: $(this).serialize(),

                    success: function (data, textStatus, jqXHR){
                        if (data.error){
                            $modalLoader.addClass('hidden');
                            showError(data.error);
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

                hideError();
            });

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
             * Reset form data to default values
             */
            function resetForm() {
                hideError();
                fillFormFields(defaultFormData);

                // Select Link type to default
                $formFields.link.find('option').prop('selected',false);
                $formFields.link.find('option:eq(0)').prop('selected', true).trigger('change');
            }

            function showError(error) {
                $errorContainer.append(error);
                $errorContainer.removeClass('d-none');
            }

            function hideError() {
                $errorContainer.empty();
                $errorContainer.addClass('d-none');
            }

            /**
             * Fetch links by link type from server
             * @param linkType 2|3
             * @param selectedLinkId
             */
            function fetchLinks(linkType, selectedLinkId)
            {
                $modalLoader.removeClass('hidden');
                $.ajax({
                    url: getLinksUrl,
                    type: "GET",
                    data: {
                        link_type: linkType
                    },
                    success: function(data, textStatus, jqXHR) {
                        if (data.links) {
                            renderLinks(data.links, selectedLinkId);
                        }
                        $modalLoader.addClass('hidden');
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log('Something was wrong...', textStatus, errorThrown, jqXHR);
                        $modalLoader.addClass('hidden');
                    }
                });
            }

            /**
             * Fetch exiting Nav by id
             */
            function fetchModel() {
                $modalLoader.removeClass('hidden');
                $.ajax({
                    url: getModelUrl,
                    type: "GET",
                    success: function(data, textStatus, jqXHR) {
                        if (data.model) {
                            fillFormFields(data.model);
                            $formFields.link.trigger('change', [data.model.link_id]);
                        }
                        $modalLoader.addClass('hidden');
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log('Something was wrong...', textStatus, errorThrown, jqXHR);
                        $modalLoader.addClass('hidden');
                    }
                });
            }

            /**
             * Render fetched links and set selected
             * @param links
             * @param selectedId
             */
            function renderLinks(links, selectedId) {
                var selected,
                    $container = $('<div></div>');
                _.each(links, function (l) {
                    if (selectedId !== undefined) {
                        selected = l.id.toString() === selectedId.toString() ? 'selected' : '';
                    }
                    $container.append('<option value="' + l.id + '"'+ selected + '>' + l.name + '</option>');
                });

                $formFields.link_id.empty().append($container.html());
            }

            /**
             * Set captions & titles depends on mode
             * mode = 0 : Created
             * mode = 1 : Updated
             * @param mode
             */
            function setTitles(mode)
            {
                $submit.html(titles.submit_title[mode]);
                $modalTitle.html(titles.modal_title[mode]);
            }

            /*******************************************************************************************
             *                              Create
             *******************************************************************************************/

            function createNav() {
                fillFormFields(defaultFormData);
                $formFields.name.focus();
            }

            /*******************************************************************************************
             *                              Update
             *******************************************************************************************/

            function updateNav() {
                fetchModel();
            }

            /*******************************************************************************************
             *                              Events
             *******************************************************************************************/

            /**
             * Link type selection changed
             */
            $formFields.link.on('change', function (e, selectedLinkId) {
                $('.hide-link').hide();

                var $link = $(this).find('option:selected'),
                    linkType = $link.val(),
                    fetched = $link.data('fetched') || false,
                    selectId = $link.data('select_id') || false,
                    labelText = $link.text().trim();

                if (selectId) {

                    $('.link-' + selectId).fadeIn().find('label').text(labelText);
                }
                if (fetched) {
                    fetchLinks(linkType, selectedLinkId);
                }
            });

            $modal.on('hidden.bs.modal', function (){
                resetForm();
            });

            $modal.on('show.bs.modal', function (event){
                $modalLoader.removeClass('hidden');

            });

            $modal.on('shown.bs.modal', function (event){

                var $button = $(event.relatedTarget),
                    modelId =  $button.closest('li').data('id') || undefined;

                submitModelUrl = $button.data('submit_url');

                $modalLoader.addClass('hidden');

                if (modelId === undefined) {
                    mode = 0;
                    createNav();
                } else {
                    mode = 1;
                    getModelUrl = $button.data('get_url');
                    updateNav();
                }

                setTitles(mode);
            });

        })({}, function (){});

    }
};




customModule.adminNavigationList = {
    run: function(params) {

        /*****************************************************************************************************
         *                     Nestable menu items
         *****************************************************************************************************/
        (function (window, alert){

            var updatePositionUrl = params.action_update_url;

            var $neatable = $('#nestable'),
                updateOutput = function updateOutput(e) {
                    var list = e.length ? e : $(e.target),
                        output = list.data('output');
                    if (window.JSON) {
                        console.log('Ok');
                    } else {
                        output.html('JSON browser support required for this demo.');
                    }
                };
            if ($neatable.length > 0) {

                $neatable.nestable({
                    group: 0,
                    maxDepth: 3
                }).on('change', updater);

                // updateOutput($('#nestable').data('output', $('#nestable-output')));
            }

            function updater(e) {

                var positions = $neatable.nestable('serialize');

                $.ajax({
                    url: updatePositionUrl,
                    type: "POST",
                    data: {
                        positions: positions
                    },
                    success: function(data, textStatus, jqXHR) {
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log('Something was wrong...', textStatus, errorThrown, jqXHR);
                    }
                });

            }

        })({}, function (){});


        /*****************************************************************************************************
         *                      Delete (mark as deleted) Nav
         *****************************************************************************************************/
        (function (window, alert){
            'use strict';

            var params = {}; // TODO:: DELETE IT! Prepare for custom modules
            var successRedirectUrl  = params.successRedirectUrl || '/admin/settings/navigation';

            var modelId;

            var deleteModelUrl;

            var $modal = $('#delete-modal'),
                $modalLoader = $modal.find('.modal-loader'),
                $buttonDelete = $modal.find('#feature-delete');

            $buttonDelete.on('click', function(){

                $modalLoader.removeClass('hidden');
                $.ajax({
                    url: deleteModelUrl,
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
                var $button = $(event.relatedTarget);
                modelId =  $button.closest('li').data('id');
                deleteModelUrl = $button.data('delete_url');
            });

            $modal.on('hidden.bs.modal', function (){
                modelId = null;
                deleteModelUrl = null;
            });

        })({}, function (){});

    }
};




customModule.adminPackageEdit = {
    run: function(params) {

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
                    modalTitle = currentPackageId ? $dataTitle.data('title_edit') + ' (ID: ' + currentPackageId + ')' : $dataTitle.data('title_create'),
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
    }
};

/**
 * /admin/settings/pages custom js module
 * @type {{run: customModule.settings.run}}
 */
customModule.adminPages = {
    run: function (params) {
        /*****************************************************************************************************
         *                      Delete (mark as deleted) Page
         *****************************************************************************************************/
        var $modal = $('#delete-modal'),
            $modalLoader = $modal.find('.modal-loader'),
            $buttonDelete = $modal.find('#feature-delete'),
            actionUrl,
            successRedirectUrl;

        $buttonDelete.on('click', function(){
            $modalLoader.removeClass('hidden');
            $.ajax({
                url: actionUrl,
                type: "DELETE",
                success: function (data, textStatus, jqXHR){
                    //Success
                    _.delay(function(){
                        $(location).attr('href', successRedirectUrl);
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
            actionUrl = button.data('action_url');
            successRedirectUrl = $modal.data('success_redirect');
        });

        $modal.on('hidden.bs.modal', function (){
            actionUrl = null;
        });
    }
};
/**
 * /admin/settings/edit-page custom js module
 * @type {{run: customModule.settings.run}}
 */
customModule.adminPageEdit = {
    run: function (params) {

        /*****************************************************************************************************
         *              Create/Edit Page autofill SEO & URL routine
         *****************************************************************************************************/
        var $pageForm = $('#pageForm'),
            $seoCollapse = $pageForm.find('.collapse');

        var isNewPage = $pageForm.data('new_page');
        var $formFields = {
            name            : $pageForm.find('.form_field__name'),
            content         : $pageForm.find('.form_field__content'),
            url             : $pageForm.find('.form_field__url'),
            visibility      : $pageForm.find('.form_field__visibility'),
            seo_title       : $pageForm.find('.form_field__seo_title'),
            seo_description : $pageForm.find('.form_field__seo_description')
        };

        var exitingUrls = params.urls || [];
        var isValidationUrlError = params.url_error || false;

        initSeoParts();
        initSummernote($formFields.content);

        // Expand collapse if error
        if (isValidationUrlError) {
            $seoCollapse.collapse("show");
        }

        if (isNewPage) {
            $formFields.name.focus();
            // Start autofilling URL
            $formFields.name.on('input', autoFillFields);

            // Stop autofill on first user's touch
            $formFields.url.on('focus', autoFillFieldsOff);
            $formFields.seo_title.on('focus', autoFillFieldsOff);
        }

        // Start cleanup url
        $formFields.url.on('input', cleanupUrl);

        /**
         * Init summernote
         * @param $element
         */
        function initSummernote($element){
            $formFields.content.summernote({
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
            $pageForm.keypress(function (e) {
                if (e.which === 13) {
                    $pageForm.submit();
                    return false;
                }
            });
        }

        /**
         * Init autofill SEO part of the page
         */
        function initSeoParts(){
            if ($('.edit-seo__title').length > 0){
                (function (){
                    var seoEdit = ['edit-seo__title', 'edit-seo__meta', 'edit-seo__url'];

                    var _loop = function _loop(i){
                        $("." + seoEdit[i] + '-muted').text($("#" + seoEdit[i]).val().length);
                        $("#" + seoEdit[i]).on('input', function (e){
                            if (i === 2){
                                $('.' + seoEdit[i]).text($(e.target).val().toLowerCase());
                            } else {
                                $("." + seoEdit[i] + '-muted').text($(e.target).val().length);
                                $('.' + seoEdit[i]).text($(e.target).val());
                            }
                        }).trigger('input');
                    };

                    for (var i = 0; i < seoEdit.length; i++){
                        _loop(i);
                    }
                })();
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
    }
};
/**
 * /admin/settings/payments custom js module
 * @type {{run: customModule.settings.run}}
 */
customModule.adminPayments = {
    run: function (params) {
        /******************************************************************
         *            Toggle `payment method` active status
         ******************************************************************/
        $(document).on('change', '.toggle-active', function (e) {
            var $checkbox = $(e.currentTarget),
                actionUrl = $checkbox.data('action_url'),
                method = $checkbox.data('payment_method'),
                active = $checkbox.prop('checked') | 0;

            $.ajax({
                url: actionUrl,
                type: "POST",
                data: {
                    active: active
                },
                success: function (data, textStatus, jqXHR) {
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log('Error on update', jqXHR, textStatus, errorThrown);
                }
            });
        });
    }
};

customModule.adminProductEdit = {
    run: function(params) {
        var self = this;
        var confirmMenuOptions = params.confirmMenu;

        /*****************************************************************************************************
         *                      Create/Update Products form script
         *****************************************************************************************************/
        (function (window, alert) {
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
                name: $productForm.find('.form_field__name'),
                description: $productForm.find('.form_field__description'),
                properties: $productForm.find('.form_field__properties'),
                url: $productForm.find('.form_field__url'),
                visibility: $productForm.find('.form_field__visibility'),
                color: $productForm.find('.form_field__color'),
                seo_title: $productForm.find('.form_field__seo_title'),
                seo_description: $productForm.find('.form_field__seo_description'),
                seo_keywords: $productForm.find('.form_field__seo_keywords')
            };

            $productForm.formType = undefined;

            var defaultFormData = {
                name: $formFields.name.val(),
                description: $formFields.description.val(),
                properties: [],
                url: $formFields.url.val(),
                visibility: $formFields.visibility.val(),
                color: $formFields.color.val(),
                seo_title: $formFields.seo_title.val(),
                seo_description: $formFields.seo_description.val(),
                seo_keywords: $formFields.seo_keywords.val()
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
            $productForm.submit(function (e) {
                e.preventDefault();
                $modalLoader.removeClass('hidden');
                $.ajax({
                    url: currentActionUrl,
                    type: "POST",
                    data: $(this).serialize(),

                    success: function (data, textStatus, jqXHR) {
                        if (data.error) {
                            $modalLoader.addClass('hidden');
                            $errorContainer.append(data.error.html);
                            $modal.animate({scrollTop: 0}, 'slow');
                            $seoCollapse.collapse("show");
                            return;
                        }
                        //Success
                        _.delay(function () {
                            if ('update' == $productForm.formType){
                                location.href = successRedirectUrl;
                                return;
                            }

                            $modal.modal('hide');
                            var message = confirmMenuOptions.labels.message.replace('{name}', data.product.name);

                            custom.confirm(confirmMenuOptions.labels.title, message, {
                                confirm_button : confirmMenuOptions.labels.confirm_button,
                                cancel_button : confirmMenuOptions.labels.cancel_button
                            }, function() {
                                $.ajax({
                                    url: confirmMenuOptions.url,
                                    data: {
                                        id: data.product.id
                                    },
                                    async: false
                                });
                            }, function() {
                                location.href = successRedirectUrl;
                            });
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

            /** Init spectrum color plugin */
            function initColorSpectrum() {
                $formFields.color.spectrum({
                    allowEmpty: true,
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
                    change: function () {

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
            function initProductsPropertiesList() {

                var itemTemplate = _.template(
                    '<li class="m-nav__item" data-id="<%- product_id %>">' +
                    '<a href="" class="m-nav__link">' +
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
                        product_title: product.name,
                        product_id: product.id
                    }));
                });

                $productsPropertiesList.find('li a').on('click', function (event) {
                    event.preventDefault();

                    var selectedItem = $(event.currentTarget),
                        productId =  selectedItem.closest('li').data('id');

                    $btnSubmitCopy.data('id', productId);

                    // Show or not modal if present product properties
                    if ($formFields.properties.find('li').length === 0) {
                        $btnSubmitCopy.click();
                    } else {
                        $modalPropertiesCopy.modal('show');
                    }
                });

                // Copy properties
                $btnSubmitCopy.click(function () {
                    var productId = $(this).data('id'),
                        product;

                    if (productId === undefined) {
                        return;
                    }

                    product = _.find(productsProperties, function (product_item) {
                        return parseInt(product_item.id) === parseInt(productId);
                    });

                    if (product === undefined || !_.isArray(product.properties)) {
                        return;
                    }

                    // Render copied properties
                    $formFields.properties.empty();

                    _.each(product.properties, function (property) {
                        $formFields.properties.append(getPropertyField(property, 'properties', formName));
                    });

                    toggleCreateNewInfoBox();
                });
            }

            /**
             *  Fill form fields by data
             * @param data
             */
            function fillFormFields(data) {
                var defaultData, formData;
                if (data !== undefined && _.isObject(data)) {
                    defaultData = {
                        name : '',
                        description : '',
                        properties : [],
                        visibility : 1,
                        color : null,
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
                    _.each(formData.properties, function (value, key, list) {
                        $formFields.properties.append(getPropertyField(value, 'properties', formName));
                    });

                    toggleCreateNewInfoBox();
                }
            }

            /**
             * Reset form fields to init values
             */
            function resetForm() {
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
            function initSummernote($element) {
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
            function initPropertiesList() {

                $formFields.properties.sortable({
                    opacity: 1,
                    tolerance: "pointer",
                    revert: false,
                    delay: false,
                    // placeholder: "movable-placeholder"
                });

                toggleCreateNewInfoBox();

                $(document).on('click', '.action-delete_property', function () {
                    $(this).closest('li').remove();
                    toggleCreateNewInfoBox();
                });
                $(document).on('click', '.add-properies', function () {
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

                function checkInput() {
                    var inputProperty = $addPropertyInput.val(),
                        length = inputProperty.length;

                    if (!!length) {
                        addProperty(inputProperty);
                    }
                    $inputPropertyError.toggleClass('d-none', !!length);
                }

                function addProperty(property) {
                    $formFields.properties.append(getPropertyField(property, 'properties', formName));
                    $addPropertyInput.val('').focus();
                    toggleCreateNewInfoBox();
                }
            }

            function toggleCreateNewInfoBox() {
                var toggle = !!$formFields.properties.find('li').length;
                $('.info__create_new_prop').toggleClass('d-none', toggle);
            }

            /**
             * Init auto-fill SEO-edit part
             */
            function initSeoParts() {
                if ($('.edit-seo__title').length > 0) {
                    (function () {

                        var seoEdit = ['edit-seo__title', 'edit-seo__meta', 'edit-seo__url'];

                        var _loop = function _loop(i) {
                            $("." + seoEdit[i] + '-muted').text($("#" + seoEdit[i]).val().length);
                            $("#" + seoEdit[i]).on('input', function (e) {
                                if (i == 2) {
                                    $('.' + seoEdit[i]).text($(e.target).val().toLowerCase());
                                } else {
                                    $("." + seoEdit[i] + '-muted').text($(e.target).val().length);
                                    $('.' + seoEdit[i]).text($(e.target).val());
                                }
                            });
                        };

                        for (var i = 0; i < seoEdit.length; i++) {
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
            function getPropertyField(propertyText, propertyFieldName, formName) {
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
                    title: propertyText,
                    property_name: propertyName,
                    property_value: propertyText
                });
            }

            /*******************************************************************************************
             * Create new product routine
             *******************************************************************************************/
            function createProduct() {

                fetchExitingUrls();

                $(document).on('urls-fetched', function (e, urls) {

                    exitingUrls = urls;

                    fillFormFields(defaultFormData);

                    /* Events subscriptions */
                    bindCreateProductEvents();

                    $formFields.name.focus();
                    $productForm.formType = 'create';
                });
            }

            function bindCreateProductEvents() {
                // Start autofilling URL
                $formFields.name.on('input.create_product', autoFillFields);

                // Stop autofill on first user's touch
                $formFields.url.on('focus.create_product', autoFillFieldsOff);
                $formFields.seo_title.on('focus.create_product', autoFillFieldsOff);

                // Start cleanup url
                $formFields.url.on('input.create_product', cleanupUrl);
            }

            function unbindCrereateProductEvents() {
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
            function fetchExitingUrls() {
                $.ajax({
                    url: getExitingUrlsUrl,
                    type: "GET",
                    success: function ($urls, textStatus, jqXHR) {
                        $(document).trigger('urls-fetched', [$urls]);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $modalLoader.addClass('hidden');
                        $modal.modal('hide');
                        console.log('Error on service save', jqXHR, textStatus, errorThrown);
                    }
                });
            }

            /**
             * Autofilling `url` by `product name`
             */
            function autoFillFields(e) {
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
            function autoFillFieldsOff() {
                $formFields.name.off('input', autoFillFields);
                $formFields.seo_title.off('input', autoFillFields);
            }

            /**
             * Cleanup url
             */
            function cleanupUrl(e) {
                var urlMaxLenght = 200,
                    urlByName,
                    target = e.currentTarget,
                    $target = $(target),
                    inputedName = $target.val();

                var position = target.selectionStart;

                urlByName = custom.generateUrlFromString(inputedName);

                if (urlByName.length >= urlMaxLenght) {
                    urlByName = urlByName.substring(0, (urlMaxLenght - 1));
                }

                $target.val(urlByName);

                target.selectionEnd = position;
            }

            /*******************************************************************************************
             * Update exiting product routine
             *******************************************************************************************/
            function updateProduct(productUrl) {
                bindEditProductEvents();

                $productForm.formType = 'update';

                $modalLoader.removeClass('hidden');
                // Get exiting product
                $.ajax({
                    url: productUrl,
                    type: "GET",
                    success: function (data, textStatus, jqXHR) {
                        if (data.product) {
                            fillFormFields(data.product);
                        }
                        $modalLoader.addClass('hidden');
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log('Something was wrong...', textStatus, errorThrown, jqXHR);
                        $modalLoader.addClass('hidden');
                    }
                });
            }

            function bindEditProductEvents() {
                // Start cleanup url
                $formFields.url.on('input.update_product', cleanupUrl);
            }

            function unbindEditProductEvents() {
                // Stop cleanup url
                $formFields.url.off('input.update_product');
            }


            /*******************************************************************************************
             * Modal Events
             *******************************************************************************************/
            /**
             * Modal Hide Events
             */
            $modal.on('hidden.bs.modal', function () {
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
            $modal.on('show.bs.modal', function (event) {
                $modalLoader.removeClass('hidden');
                resetForm();
            });

            $modal.on('shown.bs.modal', function (event) {
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

                if (currentProductId === undefined) {
                    createProduct();
                } else {
                    productUrl = button.data('get-url');
                    updateProduct(productUrl);
                }
            });

        })({}, function () {
        });

    }
};


customModule.adminProductsList = {
    run: function(params) {

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
    }
};

/**
 * /admin/settings/pages custom js module
 * @type {{run: customModule.settings.run}}
 */
customModule.adminThemes = {
    run: function (params) {

        /*****************************************************************************************************
         *                     CodeMirror activation
         *****************************************************************************************************/

        var fileType = params.extention || null;

        var $codeMirror = $('#code'),
            codeMirror,
            contentOnInit;

        var codeMirroSetting = {},
            codeType = 'js';

        switch (fileType){
            case 'twig':
                codeMirroSetting = {
                    mode : "text/html",
                    lineNumbers : true,
                    profile: 'xhtml',
                    lineWrapping: true,
                    extraKeys: {"Ctrl-Q": function(cm){ cm.foldCode(cm.getCursor()); }},
                    foldGutter: true,
                    gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"]
                };
                break;
            case 'css':
                codeMirroSetting = {
                    mode : "text/css",
                    lineNumbers : true,
                    lineWrapping: true,
                    extraKeys: {"Ctrl-Q": function(cm){ cm.foldCode(cm.getCursor()); }},
                    foldGutter: true,
                    gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"]
                };
                break;
            case 'js':
                codeMirroSetting = {
                    mode : "text/javascript",
                    lineNumbers : true,
                    lineWrapping: true,
                    extraKeys: {"Ctrl-Q": function(cm){ cm.foldCode(cm.getCursor()); }},
                    foldGutter: true,
                    gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"]
                };
                break;
            default:
                codeMirroSetting = {
                    lineNumbers : true,
                    lineWrapping: true,
                    foldGutter: true,
                    gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"]
                };
                break;
        }

        if ($codeMirror.length > 0) {
            codeMirror = CodeMirror.fromTextArea($codeMirror[0], codeMirroSetting);
            contentOnInit = codeMirror.getValue();
        }


        // var $codeMirror = $('#codemirror'),
        //     codeMirror,
        //     contentOnInit;
        //
        // var $modalSubmitClose = $('#modal_submit_close');
        // var $modalSubmitReset = $('#modal_submit_reset');
        //
        // if ($codeMirror.length > 0) {
        //     codeMirror = CodeMirror.fromTextArea($codeMirror[0], {
        //         lineNumbers: true
        //     });
        //
        //     contentOnInit = codeMirror.getValue();
        // }

        /*****************************************************************************************************
         *                     JS Tree Files init
         *****************************************************************************************************/
        var $filesTree = $('#m_tree_1');
        $filesTree.jstree({
            "core" : {
                "themes" : {
                    "responsive": false
                }
            },
            "types" : {
                "default" : {
                    "icon" : "fa fa-folder"
                },
                "file" : {
                    "icon" : "fa fa-file"
                }
            },
            "plugins": ["types"]
        });

        $filesTree.on('select_node.jstree', function(e, node) {
            var _node = node.node;
            if (_node && _node.hasOwnProperty('a_attr') && (_node.a_attr.href !== '#')) {

                // if (codeMirror && (codeMirror.getValue() !== contentOnInit)) {
                //     $modal.modal('show');
                //     return;
                // }

                window.location = _node.a_attr.href;
            }
        });

        /*****************************************************************************************************
         *               Modal submit close
         *****************************************************************************************************/
        var $modalSubmitClose = $('#modal_submit_close');

        $modalSubmitClose.on('show.bs.modal', function(event){
            var $href = $(event.relatedTarget),
                href = $href.attr('href');

            // Prevent show process if
            if (codeMirror === undefined || codeMirror.getValue() === contentOnInit) {
                event.stopPropagation();
                window.location.href = href;
                return false;
            }
            // Else — show
            $(this).find('.submit_button').attr('href', href);
        });

        /*****************************************************************************************************
         *               Modal submit reset
         *****************************************************************************************************/
        var $modalSubmitReset = $('#modal_submit_reset');

        $modalSubmitReset.on('show.bs.modal', function(event){
            var $href = $(event.relatedTarget),
                href = $href.attr('href');

            $(this).find('.submit_button').attr('href', href);
        });

    }
};
customModule.adminBlocks = {
    run : function(params) {
        var self = this;

        $(document).on('change', '.change-status', function(e) {
            e.preventDefault();

            var checkbox = $(this);
            var enableUrl = checkbox.data('enable');
            var disableUrl = checkbox.data('disable');
            var url = undefined;

            if (checkbox.prop('checked')) {
                url = enableUrl;
            } else {
                url = disableUrl;
            }

            custom.ajax({
                url: url
            });

            return false;
        });
    }
};
customModule.adminEditBlock = {
    state: {
        steps: false,
        review: false,
        slider: false,
        feature: false,
        actions: {
            delete: {
                turn: false
            },
            editorText: {
                node: false,
                nodeText: false,
                nodeHeight: false,
                save: false
            },
            slider: {
                link: false,
                type: false
            },
            feature: {
                activeIconId: false,
                activeIcon: false
            },
            steps: {
                activeIconId: false,
                activeIcon: false
            },
            dropdown: {
                id: false
            }
        }
    },
    run : function(params) {
        var self = this;
        var code = 'undefined' !== typeof params.code ? params.code : undefined;

        switch (code) {
            case 'slider':
                self.slider(params);
                break;

            case 'features':
                self.features(params);
                break;

            case 'reviews':
                self.review(params);
                break;

            case 'process':
                self.process(params);
                break;
        }

        self.initTextareaAutosizer();
    },
    slider: function(params) {
        var self = this;
        var state = self.state;

        state.slider = params.block;

        var blockLinks = {
            render: 'http://www.mocky.io/v2/5a98092630000075005c2018',
            save: params.saveUrl,
            upload: params.uploadUrl
        };

        var textAreaResizer = function textAreaResizer() {
            $('textarea.js-auto-size').textareaAutoSize();
        };
        var swiperSlider;

        var generateSlide = function generateSlide(action, id, title, description, button, image) {
            var template = '<div class="swiper-slide">\n                    <div class="slider__block-wrap slider__block-' + id + ' d-flex flex-wrap">\n\n                        <div class="editor-tooltip bg-danger editor-tooltip__right-top editor-action__delete-review editor-action__delete"  data-id="' + id + '" data-type="review" data-toggle="modal" data-target="#delete-feature-modal">\n                            <span class="fa fa-times"></span>\n                        </div>\n                        <div class="col-md-4">\n                            <label for="slider-image-' + id + '" class="slider__image slider__image_' + id + ' slider-image-' + id + '" style="background-image: url(' + image + ');">\n                                <input id="slider-image-' + id + '" type="file" name="slider-image-' + id + '" class="editor-slider-image-input" data-id="' + id + '">\n                            </label>\n                        </div>\n                        <div class="col">\n                            <div class="editor-block__reviev_name">\n                                <div class="editor-textarea__text-edit-off">\n                                    <textarea class="editor-textarea__h text-left editor-textarea__h3 js-auto-size" data-id="' + id + '" data-textarea-title="title" rows="1" spellcheck="false" placeholder="Add title...">' + title + '</textarea>\n                                    <div class="editor-textarea__text-edit-action">\n                                        <button class="btn btn-sm btn-success cursor-pointer editor-textarea__text-edit-save">Save</button>\n                                        <button class="btn btn-sm btn-secondary cursor-pointer editor-textarea__text-edit-close">Close</button>\n                                    </div>\n                                </div>\n                            </div>\n                            <div class="editor-block__description">\n                                <div class="editor-textarea__text-edit-off">\n                                    <textarea class="editor_textarea__text js-auto-size" data-id="' + id + '" data-textarea-title="description" rows="1" spellcheck="false" placeholder="Add text...">' + description + '</textarea>\n                                    <div class="editor-textarea__text-edit-action">\n                                        <button class="btn btn-sm btn-success cursor-pointer editor-textarea__text-edit-save">Save</button>\n                                        <button class="btn btn-sm btn-secondary cursor-pointer editor-textarea__text-edit-close">Close</button>\n                                    </div>\n                                </div>\n                            </div>\n                            <div class="editor-block__description">\n                                <button class="learn-more learn-more-' + id + '" data-toggle="modal" data-target="#learn-more" data-id="' + id + '">' + button + '</button>\n                            </div>\n                        </div>\n\n                    </div>\n\n                </div>';


            switch (action) {
                case 'render':
                    swiperSlider.appendSlide(template);
                    swiperSlider.slideTo(0);
                    break;
                case 'add':
                    swiperSlider.appendSlide(template);
                    swiperSlider.slideTo(0);
                    state.slider.data.push({
                        "id": id.toString(),
                        "title": title,
                        "description": description,
                        "button": {
                            "title": button,
                            "link": 0,
                            "type": 0
                        },
                        "image": 0
                    });
                    break;
            }

            //textAreaResizer();
        };

        var initData = function(result) {
            $('#preload').remove();

            if(state.slider.data !== undefined) {
                $('.no-slide').remove();
                swiperSlider = new Swiper('.block-slider', {
                    pagination: '.swiper-pagination',
                    paginationClickable: true,
                    scrollbarDraggable: false,
                    simulateTouch: false
                });

                for (var i = 0; i < result.data.length; i++) {
                    generateSlide('render', result.data[i].id, result.data[i].title, result.data[i].description, result.data[i].button.title, result.data[i].image);
                }
            }else{
                $('.swiper-wrapper').append('<div class="no-slide">No slides</div>');
            }

            var sliderEffects = $('.slider-effects'),
                sliderInterval = $('.slider-interval');

            for (var i = 0; i < sliderEffects.length; i++) {
                if (sliderEffects[i].value.toLocaleLowerCase() == result.settings.effect.toLocaleLowerCase()) {
                    sliderEffects[i].checked = true;
                    $(sliderEffects[i].parentNode).addClass('active');
                }
            }
            for (var i = 0; i < sliderInterval.length; i++) {
                if (sliderInterval[i].value.toLocaleLowerCase() == result.settings.interval.toLocaleLowerCase()) {
                    sliderInterval[i].checked = true;
                    $(sliderInterval[i].parentNode).addClass('active');
                }
            }
        };

        $('.new-preview').on('click', function (e) {
            e.preventDefault();
            var lastSlide = '';
            if (state.slider.data == undefined) {
                $('.no-slide').remove();
                swiperSlider = new Swiper('.block-slider', {
                    pagination: '.swiper-pagination',
                    paginationClickable: true,
                    scrollbarDraggable: false,
                    simulateTouch: false
                });
                state.slider.data = [];
                lastSlide = "1";
            } else {
                lastSlide = parseInt(state.slider.data[state.slider.data.length - 1].id) + 1;
            }
            generateSlide('add', lastSlide, '', '', 'Learn more', false);
        });

        $(document).on('click', '.learn-more', function () {
            var slideID = $(this).attr('data-id');
            state.actions.slider.link = slideID;
            $('.slider-link__type').addClass('hide-link');
            for (var i = 0; i < state.slider.data.length; i++) {
                if (state.slider.data[i].id.indexOf(slideID) == 0) {
                    $('.learn-more__input').val(state.slider.data[i].button.title);
                    state.actions.slider.type = state.slider.data[i].button.type;
                }
            }

            var selectedTypes = $('#select-menu__link')[0],
                dataSlide = '';

            for (var i = 0; i < state.slider.data.length; i++) {
                if (state.slider.data[i].id.indexOf(state.actions.slider.link) == 0) {
                    dataSlide = state.slider.data[i];
                }
            }

            if (dataSlide.button.type) {
                for (var i = 0; i < selectedTypes.length; i++) {
                    if (selectedTypes[i].value.toLocaleLowerCase() == dataSlide.button.type) {
                        selectedTypes[i].selected = true;
                        $('.slider-link__type-' + dataSlide.button.type).removeClass('hide-link');
                    }
                }
            } else {
                selectedTypes[0].selected = true;
            }

            var selectedNode = ".link-input__" + dataSlide.button.type;
            switch (dataSlide.button.type) {
                case "web":
                    $(selectedNode).val(dataSlide.button.link);
                    break;
                case "products":
                case "page":
                    for (var i = 0; i < selectedNode.length; i++) {
                        if (selectedNode[i].value == selectedNode.button.link) {
                            selectedTypes[i].selected = true;
                        }
                    }
                    break;
            }
        });

        $(document).on('click', '#learn-more__save', function () {
            var selectedMenu = $("#select-menu__link option:selected").val();
            for (var i = 0; i < state.slider.data.length; i++) {
                if (state.slider.data[i].id.indexOf(state.actions.slider.link) == 0) {
                    if ($('.learn-more__input').val() == '') {
                        state.slider.data[i].button.title = 'Learn more';
                        state.slider.data[i].button.type = false;
                    } else {
                        state.slider.data[i].button.title = $('.learn-more__input').val();
                        state.slider.data[i].button.type = selectedMenu;
                    }

                    switch (selectedMenu) {
                        case "web":
                            state.slider.data[i].button.link = $('.link-input__' + selectedMenu).val();
                            break;
                        case "products":
                        case "page":
                            var selectedNode = ".slider-link__type-" + selectedMenu;
                            state.slider.data[i].button.link = $(selectedNode + " option:selected").val();
                            break;
                        case "none":
                            state.slider.data[i].button.link = 0;
                            break;
                        case "home":
                            state.slider.data[i].button.link = '/';
                            break;
                        default:
                            console.log('default');
                            state.slider.data[i].button.link = 0;
                            break;
                    }

                    $('.learn-more-' + state.actions.slider.link).text(state.slider.data[i].button.title);
                }
            }
        });

        $(document).on('change', '.editor-slider-image-input', function () {
            if($(this).val().length) {
                var classId = '.' + this.id,
                    dataID = $(this).attr('data-id');

                $(classId).addClass('image-loader');
                var data = new FormData();
                data.append('file', $(this)[0].files[0]);
                data.append('type', 'slider');

                $.ajax({
                    url: blockLinks.upload,
                    data: data,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    success: function (response) {
                        $(classId).removeClass('image-loader');

                        if ('error' == response.status) {
                            toastr.error(response.error);
                        }

                        if ('success' == response.status) {
                            $(classId).css('background-image', 'url(' + response.link + ')');
                            for (var i = 0; i < state.slider.data.length; i++) {
                                if (state.slider.data[i].id.indexOf(dataID) == 0) {
                                    state.slider.data[i].image = response.link;
                                    return;
                                }
                            }
                        }
                    },
                    error: function error(_error) {
                    }
                });
            }
        });

        $(document).on('click', '.editor-action__delete', function () {
            var slideID = $(this).attr('data-id');
            state.actions.delete.turn = slideID;
        });

        $(document).on('click', '#feature-delete', function () {
            for (var i = 0; i < state.slider.data.length; i++) {
                if (state.slider.data[i].id.indexOf(state.actions.delete.turn) == 0) {
                    state.slider.data.splice(i, 1);
                    swiperSlider.removeSlide(swiperSlider.activeIndex);

                    if(!state.slider.data.length){
                        delete state.slider.data;
                        swiperSlider.destroy(true, true);
                        $('.swiper-wrapper').append('<div class="no-slide">No slides</div>');
                    }
                    return;
                }
            }
        });

        $(document).on('change', '.js-auto-size', function () {
            var slideID = $(this).attr('data-id'),
                slideType = $(this).attr('data-textarea-title'),
                content = $(this).val();

            for (var i = 0; i < state.slider.data.length; i++) {
                if (state.slider.data[i].id.indexOf(slideID) == 0) {
                    state.slider.data[i][slideType] = content;
                    return;
                }
            }
        });

        $(document).on('change', '#select-menu__link', function () {
            var linkType = $(this).find('option:selected').val();
            $('.slider-link__type').addClass('hide-link');
            $('.slider-link__type-' + linkType).removeClass('hide-link');
        });

        /* Settings */

        $(document).on('change', '.slider-effects', function () {
            state.slider.settings.effect = $(this).val();
        });

        $(document).on('change', '.slider-interval', function () {
            state.slider.settings.interval = $(this).val();
        });

        $(document).on('click', '#save-changes', function () {
            var that = this;
            $(that).addClass('m-loader m-loader--light m-loader--right');
            $(that).prop('disabled', true);
            $.ajax({
                url: blockLinks.save,
                data: {
                    content: state.slider
                },
                type: 'POST',
                success: function success(response) {
                    $(that).removeClass('m-loader m-loader--light m-loader--right');
                    $(that).prop('disabled', false);
                    self.saveCallback(response);
                },
                error: function error(_error2) {
                    $(that).removeClass('m-loader m-loader--light m-loader--right');
                    $(that).prop('disabled', false);
                    toastr.error("Error status " + _error2.status);
                }
            });
        });


        initData(state.slider);

    },
    features: function(params) {
        var self = this;
        var state = self.state;

        state.feature = params.block;

        var blockLinks = {
            render: 'http://www.mocky.io/v2/5a9909042e000003265534a8',
            save: params.saveUrl,
            upload: params.uploadUrl
        };

        var textAreaResizer = function textAreaResizer() {
            $('textarea.js-auto-size').textareaAutoSize();
        };

        var initData = function(result) {
            $('#preload').remove();

            var featureColumn = $('.feature-column'),
                featureAlign = $('.feature-align');

            for (var i = 0; i < featureColumn.length; i++) {
                if (state.feature.settings.column == featureColumn[i].value) {
                    featureColumn[i].checked = true;
                    $(featureColumn[i].parentNode).addClass('active');
                }
            }

            for (var i = 0; i < featureAlign.length; i++) {
                if (state.feature.settings.align == featureAlign[i].value) {
                    featureAlign[i].checked = true;
                    $(featureAlign[i].parentNode).addClass('active');
                }
            }

            $("#feature-fontSize").slider({
                min: 12,
                max: 240,
                step: 12,
                value: state.feature.settings.icon_size,
                slide: function slide(event, ui) {
                    $(".feature-icon-size-show").text(ui.value);
                    $("#feature-size-icon").val(ui.value);
                    $('.feature-icon').css({
                        "fontSize": ui.value + 'px'
                    });
                    state.feature.settings.icon_size = ui.value;
                }
            });
            $('.feature-icon-size-show').text(state.feature.settings.icon_size);

            for (var i = 0; i < state.feature.data.length; i++) {
                generateCards('render', state.feature.data[i].id, state.feature.data[i].title, state.feature.data[i].description, state.feature.data[i].icon);
            }

            $("#feature-list").dragsort({
                dragBetween: true,
                dragSelector: ".editor-action__drag",
                dragEnd: function() {
                    var elements = $('#feature-list li'),
                        dataClone = $.extend(true, {}, state.feature.data),
                        dataNew = [];

                    for (var i = 0; i < elements.length; i++){
                        for (var j = 0; j<state.feature.data.length; j++){
                            if ($(elements[i]).data('id') == state.feature.data[j].id) {
                                dataNew.push(state.feature.data[j])
                            }
                        }
                    }
                    state.feature.data = dataNew;
                },
                placeHolderTemplate: '<li class="col margin-top-bottom editor-placeholder-move"><div class="editor-card editor-card__left editor-tooltip__show placeholder-template d-flex align-items-lg-center justify-content-center"><span>Insert</span></div></li>'
            });

            includeContent();
            textAreaResizer();
        }

        var generateCards = function generateCards(action, id, title, description, icon) {
            var iconSize = state.feature.settings.icon_size,
                column = state.feature.settings.column,
                align = state.feature.settings.align,
                colAlignTitle = '',
                colAlignIcon = '';

            if(align == 'left'){
                colAlignTitle = 'col-7';
                colAlignIcon = 'col-5';
            }else{
                colAlignTitle = 'col-12';
                colAlignIcon = 'col-12';
            }

            var featureCardTemplate = '<li class="col-lg-' + column + ' margin-top-bottom feature-id-' + id + '" data-id="'+id+'">\n                    <div class="editor-card editor-card__left editor-tooltip__show">\n                        <div class="row">\n                            <div class="editor-tooltip bg-success editor-tooltip__right-top editor-action__drag">move</div>\n                            <div class="editor-tooltip bg-danger editor-tooltip__left-top editor-action__delete"  data-id="' + id + '" data-type="feature" data-toggle="modal" data-target="#delete-feature-modal">\n                                <span class="fa fa-times"></span>\n                            </div>\n\n                            <div class="editor-card__icon-block '+colAlignIcon+' ">\n                                <div class="editor-preview__block" data-toggle="modal" data-target="#preview-edit-modal" data-id="' + id + '">\n                                    <div class="editor-preview__tooltip">edit</div>\n                                    <span class="fa ' + icon + ' feature-icon" id="feature-icon-' + id + '" style="font-size: ' + iconSize + 'px;"></span>\n                                </div>\n                            </div>\n                            <div class="editor-card__title-block '+colAlignTitle+' ">\n                                <div class="editor-textarea__text-edit-off">\n                                    <textarea class="editor-textarea__h editor-textarea__h3 js-auto-size" data-id="' + id + '" data-textarea-title="title" rows="1" spellcheck="false" placeholder="Add title...">' + title + '</textarea>\n                                    <div class="editor-textarea__text-edit-action">\n                                        <button class="btn btn-sm btn-success cursor-pointer editor-textarea__text-edit-save">Save</button>\n                                        <button class="btn btn-sm btn-secondary cursor-pointer editor-textarea__text-edit-close">Close</button>\n                                    </div>\n                                </div>\n                            </div>\n                            <div class="editor-card__text-block col-12">\n                                <div class="editor_textarea-block">\n                                    <div class="editor-textarea__text-edit-off">\n                                    <textarea class="editor_textarea__text js-auto-size" rows="1" spellcheck="false" data-id="' + id + '" data-textarea-title="description" placeholder="Add text...">' + description + '</textarea>\n                                        <div class="editor-textarea__text-edit-action">\n                                            <button class="btn btn-sm btn-success cursor-pointer editor-textarea__text-edit-save">Save</button>\n                                            <button class="btn btn-sm btn-secondary cursor-pointer editor-textarea__text-edit-close">Close</button>\n                                        </div>\n                                    </div>\n                                </div>\n                            </div>\n\n                        </div>\n                    </div>\n                </li>';

            $("#feature-list").append(featureCardTemplate);
        };

        var includeContent = function includeContent() {
            $('.feature-title').val(state.feature.header.title);
            $('.feature-description').val(state.feature.header.description);
        };

        $(document).on('click', '.editor-action__delete', function () {
            var dataID = $(this).attr('data-id');
            state.actions.delete.turn = dataID;
        });

        $(document).on('click', '#feature-delete', function () {
            for (var i = 0; i < state.feature.data.length; i++) {
                if (state.feature.data[i].id.indexOf(state.actions.delete.turn) == 0) {
                    state.feature.data.splice(i, 1);
                }
            }
            var removeClass = '.feature-id-' + state.actions.delete.turn;
            $(removeClass).remove();
        });

        $(document).on('change', '.js-auto-size', function () {
            var featureID = $(this).attr('data-id'),
                featureType = $(this).attr('data-textarea-title'),
                content = $(this).val();

            switch (featureType) {
                case "header-title":
                    state.feature.header.title = content;
                    break;
                case "header-description":
                    state.feature.header.description = content;
                    break;
                default:
                    for (var i = 0; i < state.feature.data.length; i++) {
                        if (state.feature.data[i].id.indexOf(featureID) == 0) {
                            state.feature.data[i][featureType] = content;
                            return;
                        }
                    }
                    break;
            }
        });

        $(document).on('change', '.feature-align', function () {
            var featureAlign = $(this).val();
            state.feature.settings.align = featureAlign;
            switch (featureAlign) {
                case 'left':
                    $('.editor-card__icon-block').removeClass('col-12').addClass('col-5');
                    $('.editor-card__title-block').removeClass('col-12').addClass('col-7');
                    break;
                case 'center':
                    $('.editor-card__icon-block').removeClass('col-5').addClass('col-12');
                    $('.editor-card__title-block').removeClass('col-7').addClass('col-12');
                    break;
            }
        });

        $(document).on('change', '.feature-column', function () {
            state.feature.settings.column = $(this).val();
        });

        $(document).on('click', '#save-changes', function () {
            var that = this;
            $(that).addClass('m-loader m-loader--light m-loader--right');
            $(that).prop('disabled', true);
            $.ajax({
                url: blockLinks.save,
                data: {
                    content: state.feature
                },
                type: 'POST',
                success: function success(response) {
                    $(that).removeClass('m-loader m-loader--light m-loader--right');
                    $(that).prop('disabled', false);
                    self.saveCallback(response);
                },
                error: function error(_error2) {
                    $(that).removeClass('m-loader m-loader--light m-loader--right');
                    $(that).prop('disabled', false);
                    toastr.error("Error status " + _error2.status);
                }
            });
        });

        $(document).on('click', '.edit-modal__icons-action', function () {
            $('.edit-modal__icons-action').removeClass('active-icon');
            $(this).addClass('active-icon');
            state.actions.feature.activeIcon = $(this).attr('data-icon-name');
        });

        $(document).on('click', '.editor-preview__block', function () {
            var dataID = $(this).attr('data-id');
            state.actions.feature.activeIconId = dataID;
        });

        $(document).on('click', '#feature-saveIcon', function () {
            var currentID = state.actions.feature.activeIconId,
                iconClass = '#feature-icon-' + currentID,
                classStroke = 'fa ' + state.actions.feature.activeIcon + ' feature-icon';

            for (var i = 0; i < state.feature.data.length; i++) {
                if (state.feature.data[i].id.indexOf(currentID) == 0) {
                    state.feature.data[i].icon = state.actions.feature.activeIcon;
                }
            }

            $(iconClass).removeAttr('class');
            $(iconClass).addClass(classStroke);
        });

        $(document).on('change', '.feature-column', function () {
            $("#feature-list li").removeClass('col-lg-3 col-lg-4 col-lg-6');
            $('#feature-list li').addClass('col-lg-' + state.feature.settings.column);
        });

        $(document).on('click', '#feature-new', function () {

            var maxID = 0;
            for (var i = 0; i < state.feature.data.length; i++) {
                if (maxID < parseInt(state.feature.data[i].id)) {
                    maxID = parseInt(state.feature.data[i].id);
                }
            }
            maxID++;
            var featureID = maxID.toString();
            state.feature.data.push({
                "id": featureID,
                "title": "",
                "description": "",
                "icon": "fa-picture-o"
            });
            generateCards('add', featureID, '', '', 'fa-picture-o');
        });

        initData(state.feature);

    },
    review: function(params) {
        var self = this;
        var state = self.state;

        state.review = params.block;

        var blockLinks = {
            render: 'http://www.mocky.io/v2/5a9907fe2e00004e255534a3',
            save: params.saveUrl,
            upload: params.uploadUrl
        };

        var textAreaResizer = function textAreaResizer() {
            $('textarea.js-auto-size').textareaAutoSize();
        };

        var swiperSlider;

        var initData = function(result) {
            $('#preload').remove();


            if(state.review.data !== undefined) {
                swiperSlider = new Swiper('.swiper-container', {
                    pagination: '.swiper-pagination',
                    paginationClickable: true,
                    scrollbarDraggable: false,
                    centeredSlides: false,
                    simulateTouch: false,
                    slidesPerView: parseInt(state.review.settings.column)
                });

                for (var i = 0; i < state.review.data.length; i++) {
                    generateSlide('render', state.review.data[i].id, state.review.data[i].name, state.review.data[i].rating, state.review.data[i].description, state.review.data[i].image);
                }

            }else{
                $('.swiper-wrapper').append('<div class="no-slide">No reviews</div>');
            }
            var reviewColumn = $('.review-column');

            for (var i = 0; i < reviewColumn.length; i++) {
                if (state.review.settings.column == reviewColumn[i].value) {
                    reviewColumn[i].checked = true;
                    $(reviewColumn[i].parentNode).addClass('active');
                }
            }
            includeContent();
        };

        var generateSlide = function generateSlide(action, id, name, rating, description, image) {

            if (!image) {
                image = '/img/review_no_avatar.gif';
            }

            var templateRating = '';
            for (var i = 1; i < 6; i++) {
                if (i == parseInt(rating)) {
                    templateRating += '<input type="radio" name="rating" class="rating" value=' + i + ' checked/>';
                } else {
                    templateRating += '<input type="radio" name="rating" class="rating" value=' + i + ' />';
                }
            }

            var template = '<div class="swiper-slide">\n                        <div class="editor-review__block">\n                            <div class="editor-tooltip bg-danger editor-tooltip__left-top editor-action__delete-review editor-action__delete"  data-id="' + id + '" data-type="review" data-toggle="modal" data-target="#delete-feature-modal">\n                                <span class="fa fa-times"></span>\n                            </div>\n                            <div class="editor-block__review-avatar">\n                                <div class="editor-tooltip bg-danger editor-tooltip__left-top review-image-delete" data-id="' + id + '" data-type="avatar" data-toggle="modal" data-target="#delete-feature-modal">\n                                    <span class="fa fa-times"></span>\n                                </div>\n                                <label for="review-avatar-' + id + '">\n                                    <div class="editor-preview__block-avatar">\n                                        <div style="background-image: url(\'' + image + '\');" alt="" title="" class="editor-avatar__image rounded-circle review-avatar-' + id + '"></div>\n                                    </div>\n                                    <input id="review-avatar-' + id + '" type="file" class="editor-preview__avatar-input" data-id="' + id + '">\n                                </label>\n                            </div>\n                            <div class="editor-block__reviev_name">\n                                <div class="editor-textarea__text-edit-off">\n                                    <textarea class="editor-textarea__h editor-textarea__h3 js-auto-size" data-id="' + id + '" data-textarea-title="name" rows="1" spellcheck="false" placeholder="Add name...">' + name + '</textarea>\n                                    <div class="editor-textarea__text-edit-action">\n                                        <button class="btn btn-sm btn-success cursor-pointer editor-textarea__text-edit-save">Save</button>\n                                        <button class="btn btn-sm btn-secondary cursor-pointer editor-textarea__text-edit-close">Close</button>\n                                    </div>\n                                </div>\n                            </div>\n                            <div class="editor-rating__block">\n                                <div class="editor-rating_block-stars">\n                                    <div class="star-rating-' + id + '" data-id="' + id + '">\n                                        ' + templateRating + '\n                                    </div>\n                                </div>\n                            </div>\n                            <div class="editor-block__description">\n                                <div class="editor-textarea__text-edit-off">\n                                    <textarea class="editor_textarea__text js-auto-size" data-id="' + id + '" data-textarea-title="description" rows="1" spellcheck="false" placeholder="Add text...">' + description + '</textarea>\n                                    <div class="editor-textarea__text-edit-action">\n                                        <button class="btn btn-sm btn-success cursor-pointer editor-textarea__text-edit-save">Save</button>\n                                        <button class="btn btn-sm btn-secondary cursor-pointer editor-textarea__text-edit-close">Close</button>\n                                    </div>\n                                </div>\n                            </div>\n                        </div>\n                    </div>';

            switch (action) {
                case 'render':
                    swiperSlider.appendSlide(template);
                    swiperSlider.slideTo(0);
                    break;
                case 'add':
                    swiperSlider.appendSlide(template);
                    swiperSlider.slideTo(0);
                    state.review.data.push({
                        "id": id.toString(),
                        "name": name,
                        "description": description,
                        "image": '/img/review_no_avatar.gif',
                        "rating": false
                    });
                    break;
            }

            textAreaResizer();
            $('.star-rating-' + id).rating();
        };

        var includeContent = function includeContent() {
            $('.review-title').val(state.review.header.title);
            $('.review-description').val(state.review.header.description);
        };

        $(document).on('change', '.js-auto-size', function () {
            var reviewID = $(this).attr('data-id'),
                reviewType = $(this).attr('data-textarea-title'),
                content = $(this).val();

            switch (reviewType) {
                case "header-title":
                    state.review.header.title = content;
                    break;
                case "header-description":
                    state.review.header.description = content;
                    break;
                default:
                    for (var i = 0; i < state.review.data.length; i++) {
                        if (state.review.data[i].id.indexOf(reviewID) == 0) {
                            state.review.data[i][reviewType] = content;
                            return;
                        }
                    }
                    break;
            }
        });

        $(document).on('click', '.editor-action__delete', function () {
            var slideID = $(this).attr('data-id');
            state.actions.delete.turn = slideID;
        });

        $(document).on('click', '#feature-delete', function () {
            for (var i = 0; i < state.review.data.length; i++) {
                if (state.review.data[i].id.indexOf(state.actions.delete.turn) == 0) {
                    state.review.data.splice(i, 1);
                    swiperSlider.removeSlide(i);

                    if(!state.review.data.length){
                        swiperSlider.destroy(true, true);
                        delete state.review.data;
                        $('.swiper-wrapper').append('<div class="no-slide">No reviews</div>');
                    }
                    return;
                }
            }
        });

        $(document).on('change', '.review-column', function () {
            state.review.settings.column = parseInt($(this).val());

            swiperSlider = new Swiper('.swiper-container', {
                pagination: '.swiper-pagination',
                paginationClickable: true,
                scrollbarDraggable: false,
                centeredSlides: false,
                simulateTouch: false,
                slidesPerView: parseInt($(this).val())
            });

            swiperSlider.update(true);
            swiperSlider.updateProgress(true);

            textAreaResizer();
        });

        $(document).on('click', '#new-review', function (e) {
            e.preventDefault();
            var lastSlide = '';
            if (state.review.data == undefined) {
                $('.no-slide').remove();
                swiperSlider = new Swiper('.swiper-container', {
                    pagination: '.swiper-pagination',
                    paginationClickable: true,
                    scrollbarDraggable: false,
                    centeredSlides: false,
                    simulateTouch: false,
                    slidesPerView: parseInt(state.review.settings.column)
                });
                lastSlide = "1";
                state.review.data = [];
            } else {
                lastSlide = parseInt(state.review.data[state.review.data.length - 1].id) + 1;
            }
            generateSlide('add', lastSlide, '', '', '', false);
        });

        $(document).on('change', '.editor-preview__avatar-input', function () {

            var that = $(this);

            if($(this).val().length) {
                var dataID = $(this).data('id');
                var classId = '.review-avatar-' + dataID;

                $(classId).addClass('image-loader');
                $(that).prop('disabled', true);

                var data = new FormData();
                data.append('file', $(this)[0].files[0]);
                data.append('type', 'review');

                $.ajax({
                    url: blockLinks.upload,
                    data: data,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    success: function (response) {
                        $(classId).removeClass('image-loader');
                        $(that).prop('disabled', false);
                        if ('error' == response.status) {
                            toastr.error(response.error);
                            $(classId).css('background-image', 'url(/img/review_no_avatar.gif)');
                        }

                        if ('success' == response.status) {
                            for (var i = 0; i < state.review.data.length; i++) {
                                if (state.review.data[i].id.indexOf(dataID) == 0) {
                                    state.review.data[i].image = response.link;
                                    $(classId).css('background-image', 'url(' + response.link + ')');
                                    return;
                                }
                            }
                        }
                    },
                    error: function error(_error) {
                        $(that).prop('disabled', false);
                        $(classId).removeClass('image-loader');
                        $(classId).css('background-image', 'url(/img/review_no_avatar.gif)');
                    }
                });
            }
        });

        $(document).on('click', '.review-image-delete', function () {
            var dataID = $(this).data('id');
            var classId = '.review-avatar-' + dataID;

            for (var i = 0; i < state.review.data.length; i++) {
                if (state.review.data[i].id.indexOf(dataID) == 0) {
                    state.review.data[i].image = '0';
                    $(classId).css('background-image', 'url(/img/review_no_avatar.gif)');
                    return;
                }
            }
        });

        $(document).on('click', '.fullStar', function () {
            var ratingValue = $(this).attr('title'),
                ratingNode = $(this.parentNode.parentNode).attr('data-id');

            for (var i = 0; i < state.review.data.length; i++) {
                if (state.review.data[i].id.indexOf(ratingNode) == 0) {
                    state.review.data[i].rating = ratingValue;
                }
            }
        });


        $(document).on('click', '#save-changes', function () {
            var that = this;
            $(that).addClass('m-loader m-loader--light m-loader--right');
            $(that).prop('disabled', true);
            $.ajax({
                url: blockLinks.save,
                data: {
                    content: state.review
                },
                type: 'POST',
                success: function success(response) {
                    $(that).removeClass('m-loader m-loader--light m-loader--right');
                    $(that).prop('disabled', false);
                    self.saveCallback(response);
                },
                error: function error(_error2) {
                    $(that).removeClass('m-loader m-loader--light m-loader--right');
                    $(that).prop('disabled', false);
                    toastr.error("Error status " + _error2.status);
                }
            });
        });

        initData(state.review);

    },
    process: function(params) {
        var self = this;
        var state = self.state;

        state.steps = params.block;

        var blockLinks = {
            render: 'http://www.mocky.io/v2/5a9903472e0000d40f55348f',
            save: params.saveUrl,
            upload: params.uploadUrl
        };

        var textAreaResizer = function textAreaResizer() {
            $('textarea.js-auto-size').textareaAutoSize();
        };

        var initData = function(result) {
            $('#preload').remove();

            if(state.steps.settings.icon_size == undefined){
                state.steps.settings.icon_size = 75;
            }

            var stepsLength = state.steps.data.length,
                column = 3;

            if(stepsLength == 2){
                column = 4;
            }

            for (var i = 0; i < state.steps.data.length; i++) {
                generateCards('add', state.steps.data[i].id, state.steps.data[i].title, state.steps.data[i].description, state.steps.data[i].icon, column, state.steps.settings.description);
            }

            if (state.steps.settings.description && 'false' != state.steps.settings.description) {
                $('.steps-description').prop('checked', true);
            }

            var processCount = $('.process-count');

            for (var i = 0; i < processCount.length; i++) {
                if (column.toString() == processCount[i].value) {
                    processCount[i].checked = true;
                    $(processCount[i].parentNode).addClass('active');
                }
            }

            $("#steps-fontSize").slider({
                min: 12,
                max: 240,
                step: 12,
                value: state.steps.settings.icon_size,
                slide: function slide(event, ui) {
                    $(".steps-icon-size-show").text(ui.value);
                    $("#steps-size-icon").val(ui.value);
                    $('.steps-icon').css({
                        "fontSize": ui.value + 'px'
                    });
                    state.steps.settings.icon_size = ui.value;
                }
            });
            $('.steps-icon-size-show').text(state.steps.settings.icon_size);
            includeContent();
        }

        var generateCards = function generateCards(action, id, title, description, icon, col, cardDescription) {
            var showDescription = "";

            if(cardDescription == '0'){
                showDescription = 'hide-description';
            }

            var cardTemplate = '<li class="col-lg-' + col + ' margin-top-bottom process-column">\n               <div class="editor-card editor-tooltip__show">\n                   <div class="row">\n                       <div class="editor-card__icon-block col-12">\n                           <div class="editor-preview__block" data-toggle="modal" data-target="#preview-edit-modal" data-id="' + id + '">\n                               <div class="editor-preview__tooltip">edit</div>\n                               <span class="fa ' + icon + ' steps-icon" id="process-icon-' + id + '" style="font-size: '+state.steps.settings.icon_size+'px"></span>\n                           </div>\n                       </div>\n                       <div class="editor-card__title-block col-12">\n                           <div class="editor-textarea__text-edit-off">\n                               <textarea class="editor-textarea__h editor-textarea__h3 js-auto-size" data-id="' + id + '" data-textarea-title="title" rows="1" spellcheck="false" placeholder="Add title...">' + title + '</textarea>\n                               <div class="editor-textarea__text-edit-action">\n                                   <button class="btn btn-sm btn-success cursor-pointer editor-textarea__text-edit-save">Save</button>\n                                   <button class="btn btn-sm btn-secondary cursor-pointer editor-textarea__text-edit-close">Close</button>\n                               </div>\n                           </div>\n                       </div>\n                       <div class="editor-card__text-block col-12 ' + showDescription + '">\n                           <div class="editor_textarea-block">\n                               <div class="editor-textarea__text-edit-off">\n                                   <textarea class="editor_textarea__text js-auto-size" data-id="' + id + '" data-textarea-title="description" rows="1" spellcheck="false" placeholder="Add text...">' + description + '</textarea>\n                                   <div class="editor-textarea__text-edit-action">\n                                       <button class="btn btn-sm btn-success cursor-pointer editor-textarea__text-edit-save">Save</button>\n                                       <button class="btn btn-sm btn-secondary cursor-pointer editor-textarea__text-edit-close">Close</button>\n                                   </div>\n                               </div>\n                           </div>\n                       </div>\n                   </div>\n               </div>\n           </li>';
            $('#process-list').append(cardTemplate);
            textAreaResizer();
        };

        var includeContent = function includeContent() {
            $('.process-title').val(state.steps.header.title);
            $('.process-description').val(state.steps.header.description);
            textAreaResizer();
        };

        $(document).on('change', '.process-count', function () {
            state.steps.settings.column = $(this).val();
            $('#process-list').empty();

            if (parseInt($(this).val()) == 4) {
                state.steps.data.pop();
            }else{
                state.steps.data.push({
                    id: "4",
                    icon: "fa-picture-o",
                    title: '',
                    description: ''
                });
            }

            for (var i = 0; i < state.steps.data.length; i++) {
                generateCards('add', state.steps.data[i].id, state.steps.data[i].title, state.steps.data[i].description, state.steps.data[i].icon, state.steps.settings.column, state.steps.settings.description);
            }

        });

        $(document).on('change', '.steps-description', function () {

            if (this.checked) {
                state.steps.settings.description = '1';
            }else{
                state.steps.settings.description = '0';
            }

            $('#process-list').empty();

            for (var i = 0; i < state.steps.data.length; i++) {
                generateCards('add', state.steps.data[i].id, state.steps.data[i].title, state.steps.data[i].description, state.steps.data[i].icon, state.steps.settings.column, state.steps.settings.description);
            }


        });

        $(document).on('change', '.js-auto-size', function () {
            var stepsID = $(this).attr('data-id'),
                stepType = $(this).attr('data-textarea-title'),
                content = $(this).val();

            switch (stepType) {
                case "header-title":
                    state.steps.header.title = content;
                    break;
                case "header-description":
                    state.steps.header.description = content;
                    break;
                default:
                    for (var i = 0; i < state.steps.data.length; i++) {
                        if (state.steps.data[i].id.indexOf(stepsID) == 0) {
                            state.steps.data[i][stepType] = content;
                            return;
                        }
                    }
                    break;
            }
        });

        $(document).on('click', '.editor-preview__block', function () {
            var dataID = $(this).attr('data-id');
            state.actions.steps.activeIconId = dataID;
        });

        $(document).on('click', '.edit-modal__icons-action', function () {
            $('.edit-modal__icons-action').removeClass('active-icon');
            $(this).addClass('active-icon');
            state.actions.steps.activeIcon = $(this).attr('data-icon-name');
        });

        $(document).on('click', '.editor-preview__block', function () {
            var dataID = $(this).attr('data-id');
            state.actions.steps.activeIconId = dataID;
        });

        $(document).on('click', '#feature-saveIcon', function () {
            var currentID = state.actions.steps.activeIconId,
                iconClass = '#process-icon-' + currentID,
                classStroke = 'fa ' + state.actions.steps.activeIcon + ' steps-icon';

            for (var i = 0; i < state.steps.data.length; i++) {
                if (state.steps.data[i].id.indexOf(currentID) == 0) {
                    state.steps.data[i].icon = state.actions.steps.activeIcon;
                }
            }

            $(iconClass).removeAttr('class');
            $(iconClass).addClass(classStroke);
        });



        $(document).on('click', '#save-changes', function () {
            var that = this;
            $(that).addClass('m-loader m-loader--light m-loader--right');
            $(that).prop('disabled', true);
            $.ajax({
                url: blockLinks.save,
                data: {
                    content: state.steps
                },
                type: 'POST',
                success: function success(response) {
                    $(that).removeClass('m-loader m-loader--light m-loader--right');
                    $(that).prop('disabled', false);
                    self.saveCallback(response);
                },
                error: function error(_error2) {
                    $(that).removeClass('m-loader m-loader--light m-loader--right');
                    $(that).prop('disabled', false);
                    toastr.error("Error status " + _error2.status);
                }
            });
        });

        initData(state.steps);

    },
    initTextareaAutosizer: function() {
        var self = this;
        $(document).on('keydown', '.js-auto-size', function (e) {
            if (e.ctrlKey && e.keyCode == 13) {
                self.state.actions.editorText.save = true;
                $(self.state.actions.editorText.node).blur();
            }
        });

        $(document).on('focus', '.js-auto-size', function () {
            self.state.actions.editorText.node = this;
            self.state.actions.editorText.nodeText = this.value;
            self.state.actions.editorText.nodeHeight = this.style.height;

            var parentnode = this.parentNode,
                node = self.state.actions.editorText.node,
                nodeHeight = self.state.actions.editorText.nodeHeight,
                nodeText = self.state.actions.editorText.nodeText;

            $(parentnode).removeClass('editor-textarea__text-edit-off').addClass('editor-textarea__text-edit-on');

            $(document).on('click', '.editor-textarea__text-edit-close', function () {
                node.value = nodeText;
                node.style.height = nodeHeight;
                $('.js-auto-size').blur();
                $(parentnode).removeClass('editor-textarea__text-edit-on').addClass('editor-textarea__text-edit-off');
            });

            $('.editor-textarea__text-edit-save').on('mousedown', function () {
                self.state.actions.editorText.save = true;
                $(self.state.actions.editorText.node).blur();
            });
        });

        $(document).on('focusout', '.js-auto-size', function () {

            var node = self.state.actions.editorText.node,
                parentnode = node.parentNode;

            if (self.state.actions.editorText.save) {
                self.state.actions.editorText.save = false;
            } else {
                node.value = self.state.actions.editorText.nodeText;
                node.style.height = self.state.actions.editorText.nodeHeight;
            }

            $(parentnode).removeClass('editor-textarea__text-edit-on').addClass('editor-textarea__text-edit-off');
        });
    },
    saveCallback: function(response) {
        if ('undefined' == typeof response.status) {
            return;
        }

        if ('success' == response.status) {
            toastr.success("Success");
        }

        if ('error' == response.status) {
            toastr.error(response.error);
        }
    }
};
customModule.adminLayout = {
    run : function(params) {
        var self = this;

        $('.dropdown-collapse').on('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            if ($(this).next().hasClass('show')) {
                $($(this).attr('href')).collapse('hide');
            } else {
                $($(this).attr('href')).collapse('show');
            }
        });

        var inputs = document.querySelectorAll('.inputfile');
        Array.prototype.forEach.call(inputs, function (input) {
            var label = input.nextElementSibling,
                labelVal = label.innerHTML;

            input.addEventListener('change', function (e) {
                var fileName = '';
                if (this.files && this.files.length > 1) {
                    fileName = (this.getAttribute('data-multiple-caption') || '').replace('{count}', this.files.length);
                } else {
                    fileName = e.target.value.split('\\').pop();
                }
                if (fileName) {
                    //label.querySelector('span').innerHTML = fileName;
                    if (this.files && this.files[0]) {

                        var reader = new FileReader();

                        reader.onload = function (e) {
                            var template = '<div class="sommerce-settings__theme-imagepreview">\n                              <a href="#" class="sommerce-settings__delete-image"><span class="fa fa-times-circle-o"></span></a>\n                              <img src="' + e.target.result + '" alt="...">\n                          </div>';
                            $('#image-preview').html(template);
                        };
                        reader.readAsDataURL(this.files[0]);
                    }
                } else {
                    //label.innerHTML = labelVal;
                }
            });
            $(document).on('click', '.sommerce_v1.0-settings__delete-image', function (e) {
                $('#image-preview').html('<span></span>');
                input.value = '';
            });
        });

        // TODO:: Commented because conflicted with products page DOM objects. Must be rewrite.
        // /* Edit page */
        // $(document).ready(function () {
        //
        //     if ($('.edit-seo__title').length > 0) {
        //         (function () {
        //
        //             var seoEdit = ['edit-seo__title', 'edit-seo__meta', 'edit-seo__url'];
        //
        //             var _loop = function _loop(i) {
        //                 $("." + seoEdit[i] + '-muted').text($("#" + seoEdit[i]).val().length);
        //                 $("#" + seoEdit[i]).on('input', function (e) {
        //                     if (i == 2) {
        //                         $('.' + seoEdit[i]).text($(e.target).val().replace(/\s+/g, '-'));
        //                     } else {
        //                         $("." + seoEdit[i] + '-muted').text($(e.target).val().length);
        //                         $('.' + seoEdit[i]).text($(e.target).val());
        //                     }
        //                 });
        //             };
        //
        //             for (var i = 0; i < seoEdit.length; i++) {
        //                 _loop(i);
        //             }
        //         })();
        //     }
        // });


        // $('#select-menu-link').change(function () {
        //     $('.hide-link').hide();
        //     var val = $("#select-menu-link option:selected").val();
        //     $('.link-' + val).fadeIn();
        // });
        //

        $('[data-toggle="tooltip"]').tooltip();
    }
};
customModule.adminNotifyLayout = {
    run : function(params) {
        var self = this;

        /*****************************************************************************************************
         *                     Popup notifications init
         *****************************************************************************************************/
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


        /*****************************************************************************************************
         *                     Page notifications init
         *****************************************************************************************************/
        /* Check if page have messages */
        var messages = params.messages || null;

        if (!params.messages) {
            return;
        }

        _.forEach(params.messages, function(message){
            if (message.success) {
                toastr.success(message.success);
            }
            if (message.warning) {
                toastr.warning(message.warning);
            }
            if (message.error) {
                toastr.error(message.error);
            }
        });
    }
};
/**
 * Order details custom js module
 * @type {{run: customModule.ordersDetails.run}}
 */
customModule.ordersDetails = {
    run : function(params) {
        $(document).ready(function () {
            var ajaxEndpoint = '/admin/orders/get-order-details';
            var $detailsModal = $('#suborder-details-modal'),
                $modalTitle = $detailsModal.find('.modal-title'),
                $provider = $detailsModal.find('#order-detail-provider'),
                $providerOrderId = $detailsModal.find('#order-detail-provider-order-id'),
                $providerResponse = $detailsModal.find('#order-detail-provider-response'),
                $providerUpdate = $detailsModal.find('#order-detail-lastupdate'),
                $modalLoader = $detailsModal.find('.modal-loader');

            $detailsModal.on('show.bs.modal', function(e) {
                var $target = $(e.relatedTarget),
                    suborderId = $target.data('suborder-id'),
                    modalTitle = $target.data('modal_title');

                if (suborderId === undefined || isNaN(suborderId)) {
                    return;
                }
                $modalLoader.removeClass('hidden');
                $.ajax({
                    url: ajaxEndpoint,
                    type: "GET",
                    data: {
                        'suborder_id': suborderId
                    },
                    success: function (data) {
                        $modalLoader.addClass('hidden');
                        if (data.details === undefined) {
                            return;
                        }
                        renderLogs(data.details);
                    },
                    error: function(jqXHR, textStatus, errorThrown){
                        console.log('Something is wrong!');
                        console.log(jqXHR, textStatus, errorThrown);
                        $modalLoader.addClass('hidden');
                    }
                });

                function renderLogs(details){
                    $modalTitle.html(modalTitle);
                    $provider.val(details.provider);
                    $providerOrderId.val(details.provider_order_id);
                    $providerResponse.html(details.provider_response);
                    $providerUpdate.val(details.updated_at);
                }
            });

            $detailsModal.on('hidden.bs.modal',function(e) {
                var $currentTarget = $(e.currentTarget);
                $currentTarget.find('input').val('');
                $providerResponse.html('');
            });
        });
    }
};

/**
 * Order clipboard custom js module
 * @type {{run: customModule.ordersClipboard.run}}
 */
customModule.ordersClipboard = {
    run : function(params) {

        var messageCopied = params.messageCopied || 'Copied!';

        $(document).ready(function () {
            var ClipboardDemo = function () {
                var n = function n() {
                    new Clipboard("[data-clipboard=true]").on("success", function (n) {
                        n.clearSelection();
                        // Check toastr notification plugin
                        if (toastr === undefined) {
                            alert("Copied!");
                        }   else {
                            toastr.options = {
                                "positionClass": "toast-bottom-right"
                            };
                            toastr.success(messageCopied);
                        }
                    });
                };return { init: function init() {
                    n();
                } };
            }();jQuery(document).ready(function () {
                ClipboardDemo.init();
            });
        });
    }
};

/**
 * Order change status custom js module
 * @type {{run: customModule.ordersModalAlerts.run}}
 */
customModule.ordersModalAlerts = {
    run : function(params) {

        var $modals = $('.order_modal_alert');

        $modals.on('show.bs.modal', function(event){
            var $modal = $(this),
                $target = $(event.relatedTarget);
            var actionUrl = $target.data('action_url');

            $modal.find('.submit_action').attr('href', actionUrl);
        });
    }
};
/**
 * Payments custom js module
 */
customModule.payments = {
    run: function (params) {

        /******************************************************************
         *                    Get payment details
         ******************************************************************/

        var $modal = $('.payments_detail'),
            $modalTitle = $modal.find('.modal-title'),
            $detailsContainer = $modal.find('.details-container'),
            $modalLoader = $modal.find('.modal-loader');

        $modal.on('show.bs.modal', function (e) {
            var $target = $(e.relatedTarget),
                paymentId = $target.data('id'),
                modalTitle = $target.data('modal_title'),
                actionUrl = $target.data('action_url');

            if (paymentId === undefined || actionUrl === undefined ) {
                return;
            }

            $modalLoader.removeClass('hidden');
            $.ajax({
                url: actionUrl,
                type: "GET",
                success: function (data) {
                    $modalLoader.addClass('hidden');
                    if (data === undefined) {
                        return;
                    }
                    renderLogs(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log('Something is wrong!');
                    console.log(jqXHR, textStatus, errorThrown);
                    $modalLoader.addClass('hidden');
                    $modal.modal('hide');
                }
            });

            function renderLogs(details) {
                $modalTitle.html(modalTitle);
                _.each(details, function(detail){
                    $detailsContainer.append('<pre class="sommerce-pre details-item">' + detail.time  + '<br><br>' + detail.data + '</pre>');
                });
            }
        });

        $modal.on('hidden.bs.modal', function (e) {
            $modalTitle.html('');
            $detailsContainer.empty();
        });
    }
};


customModule.adminProviders = {
    run : function(params) {
        var self = this;

        $(document).on('click', '#showCreateProviderModal', function(e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#createProviderModal');
            var form = $('#createProviderForm', modal);
            var errorBlock = $('#createProviderError', form);

            errorBlock.addClass('hidden');
            errorBlock.html('');

            modal.modal('show');

            return false;
        });

        $(document).on('click', '#createProviderButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#createProviderForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#createProviderModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });
    }
};
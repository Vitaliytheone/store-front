
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
                overflow            : $packageForm.find('.form_field__overflow'),
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
                overflow            : $formFields.overflow.val(),
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
                    console.log(actionUrl);
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

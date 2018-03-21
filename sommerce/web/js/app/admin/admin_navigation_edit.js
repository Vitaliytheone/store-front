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




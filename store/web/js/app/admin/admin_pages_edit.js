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
            $submit = $pageForm.find('submit'),
            $seoCollapse = $pageForm.find('.collapse'),
            $modalLoader = $pageForm.find('.modal-loader'),
            $errorContainer = $pageForm.find('.error-summary');

        var isNewPage = $pageForm.data('new_page');
        var $formFields = {
            name            : $pageForm.find('.form_field__name'),
            content         : $pageForm.find('.form_field__content'),
            url             : $pageForm.find('.form_field__url'),
            visibility      : $pageForm.find('.form_field__visibility'),
            seo_title       : $pageForm.find('.form_field__seo_title'),
            seo_description : $pageForm.find('.form_field__seo_description')
        };

        var pageId = params.pageId || undefined;
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
            if (!$formFields.content.length) {
                return false;
            }

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

        /*****************************************************************************************************
         *              Create/Edit Page save/update
         *****************************************************************************************************/
        var actionUrl = $pageForm.attr('action');

        toastr.options = {
            "positionClass": "toast-bottom-right",
            "preventDuplicates": false,
        };

        $pageForm.submit(function (e) {
            e.preventDefault();

            $modalLoader.removeClass('hidden');
            $errorContainer.addClass('hidden');

            $.ajax({
                url: actionUrl,
                type: "POST",
                data: $(this).serialize(),

                success: function (data, textStatus, jqXHR) {

                    $modalLoader.addClass('hidden');

                    if (data.success === true) {
                        if (data.message !== undefined) {
                            toastr.success(data.message);
                        }

                        if (pageId === undefined && data.id !== undefined) {
                            pageId = data.id;
                            actionUrl = actionUrl + '?id=' + pageId;
                        }
                    }

                    if(data.success === false && data.message !== undefined) {
                        $errorContainer.removeClass('hidden');
                        $errorContainer.html(data.message);
                    }
                },

                error: function (jqXHR, textStatus, errorThrown) {
                    $modalLoader.addClass('hidden');
                    console.log('Error on create/update page!', jqXHR, textStatus, errorThrown);
                }
            });

            $errorContainer.empty();
        });
    }
};
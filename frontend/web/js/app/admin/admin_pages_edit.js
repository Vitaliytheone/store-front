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
            $summernote = $('.summernote');

        var isNewPage = $pageForm.data('new_page');

        var $formFields = {
            name            : $pageForm.find('.form_field__name'),
            description     : $pageForm.find('.form_field__description'),
            url             : $pageForm.find('.form_field__url'),
            visibility      : $pageForm.find('.form_field__visibility'),
            seo_title       : $pageForm.find('.form_field__seo_title'),
            seo_description : $pageForm.find('.form_field__seo_description')
        };

        initSeoParts();
        initSummernote($summernote);

        if (isNewPage) {
            $formFields.name.focus();
            // Start autofilling URL
            $formFields.name.on('input', autofillUrl);
            // Stop autofill on first user's touch
            $formFields.url.on('focus', autofillUrlOff);
        }

        // Start cleanup url
        $formFields.url.on('input', cleanupUrl);

        /**
         * Init summernote
         * @param $element
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
                                $('.' + seoEdit[i]).text(getValidAddressByString($(e.target).val()));
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
                urlByName ,
                inputedName = $(e.target).val();

            urlByName = getValidAddressByString(inputedName);
            if (urlByName.length >= urlMaxLenght){
                urlByName = urlByName.substring(0, (urlMaxLenght-1));
            }
            $formFields.url.val(urlByName.toLowerCase());
        }
    }
};
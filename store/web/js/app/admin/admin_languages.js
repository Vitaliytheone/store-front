customModule.adminStoreLanguages = {
    run: function(params) {

        /*****************************************************************************************************
         *                     Activate store language
         *****************************************************************************************************/
        (function (window, alert){
            'use strict';

            var actionUrl = params.action_activate_lang_url;

            var $langCheckboxes = $('.language-checkbox');

            $langCheckboxes.on('change', function(){

                var langCode = $(this).val();

                $.ajax({
                    url: actionUrl + langCode,
                    type: 'GET',
                    success: function (data, textStatus, jqXHR){
                        if (data.code !== langCode){
                            console.log('Error on updating store language!');
                        } else {
                            // console.log('Store language updated!');
                            // $(location).attr('href', successRedirectUrl);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown){
                        console.log('Error on updating store language!', jqXHR, textStatus, errorThrown);
                    }
                });
            });

        })({}, function (){});

        /*****************************************************************************************************
         *                     Add store language
         *****************************************************************************************************/
        (function (window, alert){
            'use strict';

            var actionUrl = params.action_add_lang_url,
                successUrl = params.success_redirect_url,
                errorUrl = successUrl;

            var $modal = $('.add-language-modal'),
                $form = $('.form-add-language'),
                $languagesList = $('.form_field__languages_list'),
                $submit = $form.find('.btn_submit'),
                $modalLoader = $('.modal-loader');

            $languagesList.on('change', function(event){
                var code = $(this).val();
                $submit.prop('disabled', !code);
            });

            $form.on('submit', function(event){

                event.preventDefault();

                var formData = $(this).serializeArray(),
                    code = $languagesList.find("option:selected").val();

                loading(true);

                $.ajax({
                    url: actionUrl + code,
                    type: 'GET',
                    success: function (data, textStatus, jqXHR){
                        if (data.result !== true){
                            console.log('Error on add store language!');
                        }
                        $(location).attr('href', errorUrl);
                    },
                    error: function (jqXHR, textStatus, errorThrown){
                        loading(false);
                        console.log('Error on updating store language!', jqXHR, textStatus, errorThrown);
                    }
                });
            });

            function loading(toggle) {
                $modalLoader.toggleClass('hidden', !toggle);
                $submit.prop('disabled', toggle);
            }

            loading(false);
            $languagesList.trigger('change');

        })({}, function (){});

    }
};



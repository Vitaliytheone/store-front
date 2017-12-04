/**
 * Settings custom js module
 * @type {{run: customModule.settings.run}}
 */
customModule.settings = {
    run: function (params) {
        
        /******************************************************************
         *            Toggle `payment method` active status
         ******************************************************************/
        (function (){
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
        })({}, function (){});

        /******************************************************************
         *            General settings SEO part interaction
         ******************************************************************/
        (function (){
            if ($('.edit-seo__title').length > 0) {
                (function () {

                    var seoEdit = ['edit-seo__title', 'edit-seo__meta'];

                    var _loop = function _loop(i) {
                        $("." + seoEdit[i] + '-muted').text($("#" + seoEdit[i]).val().length);
                        $("#" + seoEdit[i]).on('input', function (e) {
                            if (i == 2) {
                                $('.' + seoEdit[i]).text($(e.target).val().replace(/\s+/g, '-'));
                            } else {
                                $("." + seoEdit[i] + '-muted').text($(e.target).val().length);
                                $('.' + seoEdit[i]).text($(e.target).val());
                            }
                        }).trigger('input');
                    };

                    for (var i = 0; i < seoEdit.length; i++) {
                        _loop(i);
                    }
                })();
            }
        })({}, function (){});

        /******************************************************************
         *            General settings delete logo & favicon
         ******************************************************************/
        (function (){
            var $deleteImageBtns = $('.delete-uploaded-images');

            $deleteImageBtns.click('click', function(e){
                var $currentTarget = $(e.currentTarget),
                    field = $currentTarget.data('field');

                if (!field) {
                    return;
                }
                $(document).find('#' + field).attr('value', null);
                $currentTarget.closest('.uploaded-image').empty();

            });

        })({}, function (){});

        /*****************************************************************************************************
         *                      Delete (mark as deleted) Page
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
                actionUrl = button.data('action_url');
                successRedirectUrl = $modal.data('success_redirect');
            });

            $modal.on('hidden.bs.modal', function (){
                actionUrl = null;
            });

        })({}, function (){});

    }
};
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

    /*****************************************************************************************************
     *                     Add store payments
     *****************************************************************************************************/
    (function (window, alert){
        'use strict';

        var actionUrl = params.action_add_pay_url,
            successUrl = params.success_redirect_url,
            errorUrl = successUrl;

        var $modal = $('.add-method-modal'),
            $form = $('.form-add-method'),
            $methodsList = $('.form_field__method_list'),
            $submit = $form.find('.btn_submit'),
            $modalLoader = $('.modal-loader');

        $methodsList.on('change', function(event){
            var code = $(this).val();
            $submit.prop('disabled', !code);
        });

        $form.on('submit', function(event){

            event.preventDefault();

            var formData = $(this).serializeArray(),
                code = $methodsList.find("option:selected").val();

            loading(true);

            $.ajax({
                url: actionUrl + code,
                type: 'GET',
                success: function (data, textStatus, jqXHR){
                    if (data.result !== true){
                        console.log('Error on add store payment method!');
                    }
                    $(location).attr('href', errorUrl);
                },
                error: function (jqXHR, textStatus, errorThrown){
                    loading(false);
                    console.log('Error on updating store payment method!', jqXHR, textStatus, errorThrown);
                }
            });
        });

        function loading(toggle) {
            $modalLoader.toggleClass('hidden', !toggle);
            $submit.prop('disabled', toggle);
        }

        loading(false);
        $methodsList.trigger('change');

    })

};
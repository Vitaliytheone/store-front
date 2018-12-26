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

        $('.add-method').click(function(e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#addPaymentMethodModal');
            var form = $('#addPaymentMethodForm');
            var errorBlock = $('#addPaymentMethodError', form);

            errorBlock.addClass('hidden');
            errorBlock.html('');

            form.attr('action', link.attr('href'));

            modal.modal('show');

            return false;
        });

        $(document).on('click', '#addPaymentMethodButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#addPaymentMethodForm');
            var errorBlock = $('#addPaymentMethodError', form);

            errorBlock.addClass('hidden');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {

                    if ('success' == response.status) {
                        $('#editCustomerModal').modal('hide');
                        location.reload();
                    }

                    if ('error' == response.status) {
                        errorBlock.removeClass('hidden');
                        errorBlock.html(response.error);
                    }
                }
            });

            return false;
        });
    }
};
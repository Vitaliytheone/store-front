customModule.superadminPaymentGatewayController = {
    run : function(params) {
        $('.edit-payment').click(function(e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#editPaymentModal');

            $.get(link.attr('href'), function (response) {
                if (response.content) {
                    $('.modal-body', modal).html(response.content);
                    modal.modal('show');
                }
            });

            return false;
        });

        $(document).on('click', '#editPaymentButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#editPaymentForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    if ('success' == response.status) {
                        $('#editPaymentModal').modal('hide');
                        location.reload();
                    }
                }
            });

            return false;
        });
    }
};
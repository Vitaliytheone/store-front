customModule.superadminApplicationsController = {
    run: function (params) {
        $('.edit-applications').click(function (e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#editApplicationsModal');

            modal.modal('show');

            $.get(link.attr('href'), function (response) {
                if (response.content) {
                    $('.modal-body', modal).html(response.content);
                }
            });

            return false;
        });

        $(document).on('click', '#editApplicationsButton', function (e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#editApplicationsForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback: function (response) {
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
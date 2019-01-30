customModule.superadminApplicationsController = {
    run: function (params) {
        $('.edit-applications').click(function (e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#editApplicationsModal');
            var form = $('#editApplicationsForm');
            var errorBlock = $('#editApplicationsError', form);
            var details = link.data('details');

            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('#editapplicationsform-code', form).val(details.code);
            $('#editapplicationsform-options', form).val(details.options);

            form.attr('action', link.attr('href'));

            modal.modal('show');

            return false;
        });

        $(document).on('click', '#editApplicationsButton', function (e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#editApplicationsForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback: function (response) {
                    $('#editApplicationsModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });
    }
};
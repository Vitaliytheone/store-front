customModule.superadminEmailSettingsController = {
    run : function(params) {
        $('.edit-email').click(function(e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#editEmail');
            var form = $('#createEmailForm');
            var errorBlock = $('#createEmailError', form);
            var details = link.data('details');

            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('#editnotificationemailform-subject', form).val(details.subject);
            $('#editnotificationemailform-code', form).val(details.code);
            $('#editnotificationemailform-message', form).val(details.message);

            if (details.enabled == 1) {
                $('#editnotificationemailform-enabled', form).prop("checked", true);
            } else {
                $('#editnotificationemailform-enabled', form).prop("checked", false);
            }

            form.attr('action', link.attr('href'));

            modal.modal('show');

            return false;
        });

        $(document).on('click', '#createEmailButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#createEmailForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#editEmail').modal('hide');
                    location.reload();
                }
            });

            return false;
        });
    }
};
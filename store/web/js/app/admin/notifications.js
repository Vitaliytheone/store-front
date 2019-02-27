customModule.adminNotifications = {
    run : function(params) {
        var self = this;

        $(document).on('change', '.change-status', function(e) {
            e.preventDefault();

            var checkbox = $(this);
            var enableUrl = checkbox.data('enable');
            var disableUrl = checkbox.data('disable');
            var url = undefined;

            if (checkbox.prop('checked')) {
                url = enableUrl;
            } else {
                url = disableUrl;
            }

            custom.ajax({
                url: url
            });

            return false;
        });

        $(document).on('click', '.create-email, .edit-email', function(e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#createAdminEmailModal');
            var form = $('#createAdminEmailForm', modal);
            var errorBlock = $('#createAdminEmailError', form);
            var header = link.data('header');
            var email = link.data('email');
            form.attr('action', link.attr('href'));

            $('.modal-title', modal).html(header);

            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('input', form).val('');

            if (link.hasClass('edit-email')) {
                $('#editadminemailform-email', form).val(email);
            }

            modal.modal('show');

            return false;
        });

        $(document).on('click', '#createAdminEmailButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#createAdminEmailForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#createAdminEmailModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });

        $(document).on('click', '.delete-email', function(e) {
            e.preventDefault();

            var link = $(this);
            var url = link.attr('href');
            var modal = $('#deleteAdminEmailModal');
            var form = $('#deleteAdminEmailForm', modal);
            form.attr('action', link.attr('href'));

            modal.modal('show');

            return false;
        });

        $(document).on('click', '#deleteAdminEmailButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#deleteAdminEmailForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#deleteAdminEmailModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });
    }
};
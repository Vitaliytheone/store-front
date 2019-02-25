customModule.superadminStaffsController = {
    run : function(params) {
        $(document).on('click', '#createStaff', function(e) {
            e.preventDefault();

            var link = $(this);
            var form = $('#createStaffForm');
            var modal = $('#createStaffModal');
            var errorBlock = $('#createStaffError', form);

            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('input[type="text"]', form).val('');
            $('input[type="checkbox"]').prop('checked', true);
            $('select', modal).prop('selectedIndex', 0);

            modal.modal('show');

            return false;
        });

        $('.edit-account').click(function(e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#editStaffModal');
            var form = $('#editStaffForm');
            var errorBlock = $('#editStaffError', form);
            var details = link.data('details');

            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('#editstaffform-username', form).val(details.username);
            $('#editstaffform-first_name', form).val(details.first_name);
            $('#editstaffform-last_name', form).val(details.last_name);
            $('#editstaffform-status', form).val(details.status);
            $('.access', form).prop('checked', false);

            if ('undefined' !== typeof details.access && details.access.length) {
                $.each(details.access, function(key, value) {
                    $('input[name="EditStaffForm[access][' + value +']"]').prop('checked', 'checked');
                });
            }

            form.attr('action', link.attr('href'));

            modal.modal('show');

            return false;
        });

        $(document).on('click', '.change-password', function(e) {
            e.preventDefault();

            var link = $(this);
            var url = link.attr('href');

            var modal = $('#changePasswordModal');
            var form = $('#changePasswordForm');
            var errorBlock = $('#changePasswordError', form);

            $('.password', form).val('');

            form.attr('action', url);

            errorBlock.hide();
            errorBlock.html('');

            modal.modal('show');

            return false;
        });

        $('.random-password').click(function(e) {
            e.preventDefault();

            var btn = $(this);

            var form = btn.parents('form');

            $('.password', form).val(custom.generatePassword());

            return false;
        });

        $(document).on('click', '#editStaffButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#editStaffForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#editStaffModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });

        $(document).on('click', '#createStaffButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#createStaffForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#createStaffModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });

        $(document).on('click', '#changePasswordButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#changePasswordForm');
            var link = form.attr('action');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#changePassword').modal('hide');
                    location.reload();
                }
            });

            return false;
        });
    }
};
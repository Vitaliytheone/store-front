customModule.superadminCustomersController = {
    run : function(params) {

        $('.edit').click(function(e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#editCustomerModal');
            var form = $('#editCustomerForm');
            var errorBlock = $('#editCustomerError', form);
            var details = link.data('details');

            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('#editcustomerform-email', form).val(details.email);
            $('#editcustomerform-first_name', form).val(details.first_name);
            $('#editcustomerform-last_name', form).val(details.last_name);
            $('#edit-customer-referral option[value='+ details.referral_status +']').attr('selected', 'true');

            form.attr('action', link.attr('href'));

            modal.modal('show');

            return false;
        });

        $(document).on('click', '.set-password', function(e) {
            e.preventDefault();

            var link = $(this);
            var url = link.attr('href');

            var modal = $('#setPasswordModal');
            var form = $('#setPasswordForm');
            var errorBlock = $('#setPasswordError', form);

            $('#customerpasswordform-email', form).val('');

            form.attr('action', url);

            errorBlock.addClass('hidden');
            errorBlock.html('');

            modal.modal('show');

            return false;
        });

        new Clipboard('.random-password', {
            container: document.getElementById('#setPasswordModal'),
        });

        $('.random-password').click(function(e) {
            e.preventDefault();

            var btn = $(this);
            var form = btn.parents('form');
            var input = $('.password', form);
            var password = custom.generatePassword();

            input.val(password);
        });

        $(document).on('click', '#editCustomerButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#editCustomerForm');
            var errorBlock = $('#editCustomerError', form);

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

        $(document).on('click', '#setPasswordButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#setPasswordForm');
            var link = form.attr('action');
            var errorBlock = $('#setPasswordError', form);

            errorBlock.addClass('hidden');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    if ('success' == response.status) {
                        $('#setPasswordModal').modal('hide');
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
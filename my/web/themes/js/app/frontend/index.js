customModule.indexController = {
    run : function(params) {
        function generatePassword() {
            var length = 8,
                charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
                retVal = "";
            for (var i = 0, n = charset.length; i < length; ++i) {
                retVal += charset.charAt(Math.floor(Math.random() * n));
            }
            return retVal;
        }

        $('[data-toggle="tooltip"]').tooltip();

        $('.set-staff-password').click(function (e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#setStaffPasswordModal');
            var form = $('#setStaffPasswordForm');
            var errorBlock = $('#setStaffPasswordError', form);
            var details = link.data('details');

            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('#setstaffpasswordform-username', form).val(details.login);

            form.attr('action', link.attr('href'));

            modal.modal('show');

            return false;
        });

        $(document).on('click', '#setStaffPasswordButton', function (e) {
            e.preventDefault();

            var form = $('#setStaffPasswordForm');
            var errorBlock = $("#setStaffPasswordError", form);
            errorBlock.addClass('hidden');

            $.post(form.attr('action'), form.serialize(), function(response) {
                if ('success' == response.status) {
                    window.location.reload();
                }

                if ('error' == response.status) {
                    errorBlock.removeClass('hidden');
                    errorBlock.html(response.error);
                }
            });

            return false;
        });

        $(document).on('click', '.random-password', function (e) {
            e.preventDefault();

            var btn = $(this);

            var form = btn.parents('form');

            $('.password', form).val(generatePassword());

            return false;
        });

        $('#createStaff').click(function (e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#createStaffModal');
            var form = $('#createStaffForm');
            var errorBlock = $('#createStaffError', form);

            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('input[type="text"]', form).val('');
            $('input[type="checkbox"]').prop('checked', true);
            $('#hide-providers').prop('checked', false);
            $('select', modal).prop('selectedIndex', 0);

            modal.modal('show');

            return false;
        });

        $(document).on('click', '#createStaffButton', function (e) {
            e.preventDefault();

            var form = $('#createStaffForm');
            var errorBlock = $("#createStaffError", form);
            errorBlock.addClass('hidden');

            $.post(form.attr('action'), form.serialize(), function(response) {
                if ('success' == response.status) {
                    window.location.reload();
                }

                if ('error' == response.status) {
                    errorBlock.removeClass('hidden');
                    errorBlock.html(response.error);
                }
            });

            return false;
        });

        $(document).on('click', '.edit-staff', function (e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#editStaffModal');
            var form = $('#editStaffForm');
            var errorBlock = $('#editStaffError', form);
            var details = link.data('details');

            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('input[type="checkbox"]').prop('checked', false);
            $('#editstaffform-account', form).val(details.login);
            $('#editstaffform-status', form).val(details.status);

            if ('undefined' !== typeof details.accessList) {
                $.each(details.accessList, function(key, value) {
                    if (!value) {
                        return;
                    }
                    $('input[name="EditStaffForm[access][' + key +']"]').prop('checked', 'checked');
                });
            }

            form.attr('action', link.attr('href'));

            modal.modal('show');

            return false;
        });

        $('#editStaffModal, #createStaffModal').each(function() {
            var modal = $(this);
            var settingsRules = $('input[name="EditStaffForm[access][settings]"], input[name="CreateStaffForm[access][settings]"]', modal);
            var settings = $('.settings[type="checkbox"]', modal);

            var appearanceRules = $('input[name="EditStaffForm[access][appearance]"], input[name="CreateStaffForm[access][appearance]"]', modal);
            var appearance = $('.appearance[type="checkbox"]', modal);

            settingsRules.on('change', function () {
                if (this.checked) {
                    settings.prop('checked', true);
                } else {
                    settings.prop('checked', false);
                }
            });

            settings.on('change', function () {
                if ($('.settings[type="checkbox"]:checked', modal).length) {
                    settingsRules.prop('checked', true)
                } else {
                    settingsRules.prop('checked', false)
                }
            });

            appearanceRules.on('change', function () {
                if (this.checked) {
                    appearance.prop('checked', true);
                } else {
                    appearance.prop('checked', false);
                }
            });

            appearance.on('change', function () {
                if ($('.appearance[type="checkbox"]:checked', modal).length) {
                    appearanceRules.prop('checked', true)
                } else {
                    appearanceRules.prop('checked', false)
                }
            });
        });



        $(document).on('click', '#editStaffButton', function (e) {
            e.preventDefault();

            var form = $('#editStaffForm');
            var errorBlock = $("#editStaffError", form);
            errorBlock.addClass('hidden');

            $.post(form.attr('action'), form.serialize(), function(response) {
                if ('success' == response.status) {
                    window.location.reload();
                }

                if ('error' == response.status) {
                    errorBlock.removeClass('hidden');
                    errorBlock.html(response.error);
                }
            });

            return false;
        });

        $('#editAccount').on('show.bs.modal', function(e) {

            $("#editStaffBody").html('<div class="modal-body"><img src="/themes/img/ajax-loader.gif" border="0" alt="loading"></div>');
            $.get( "/staff/edit/"+$(e.relatedTarget).data('href'), function( data ) {
                $("#editStaffBody").html(data);
            });
        });

        $('#staff_edit_gen').click(function (){
            $("#staff_edit_passwd").val(generatePassword());
        });

        $('#staff_create_account').click(function (){
            $.post( "/staff/create", {
                id: $("#staff_add_id").val(),
                account: $("#staff_add_account").val(),
                password: $("#staff_add_passwd").val(),
                status: $("#staff_add_status").val(),
                rules_1: $("#staff_add_rules_1").is(':checked'),
                rules_2: $("#staff_add_rules_2").is(':checked'),
                rules_3: $("#staff_add_rules_3").is(':checked'),
                rules_4: $("#staff_add_rules_4").is(':checked'),
                rules_5: $("#staff_add_rules_5").is(':checked'),
                rules_6: $("#staff_add_rules_6").is(':checked'),
                rules_7: $("#staff_add_rules_7").is(':checked'),
            }).done(function( data ) {
                switch(data) {
                    case "5":
                        $("#staffCreate_error").html('Account cannot be blank.');
                        $("#staffCreate_error").removeClass('hidden');
                        break;
                    case "4":
                        $("#staffCreate_error").html('Password cannot be blank.');
                        $("#staffCreate_error").removeClass('hidden');
                        break;
                    case "3":
                        $("#staffCreate_error").html('Please use between 5 and 20 characters');
                        $("#staffCreate_error").removeClass('hidden');
                        break;
                    case "2":
                        $("#staffCreate_error").html('Account already exist');
                        $("#staffCreate_error").removeClass('hidden');
                        break;
                    case "1":
                        $("#staffCreate_error").addClass('hidden');
                        window.location = '/staff/' + $("#staff_add_id").val();
                        break;
                }
            });
        });

        $('#changeEmailBtn').on('click', function(e) {
            e.preventDefault();

            $('#changeEmail').modal('show');

            var form = $('#change-email-form');

            $('#changeEmail_email, #changeEmail_password', form).val('');
            $("#changeEmailError", form).addClass('hidden');

            return false;
        });

        $('#changePasswordBtn').on('click', function(e) {
            e.preventDefault();

            $('#changePassword').modal('show');

            var form = $('#change-password-form');

            $('input', form).val('');
            $("#changePasswordError", form).addClass('hidden');

            return false;
        });

        $('#changeEmailSubmit').click(function(e) {
            e.preventDefault();

            var form = $('#change-email-form');
            $("#changeEmailError", form).addClass('hidden');

            $.post( "/changeEmail", form.serialize(), function(response) {
                if ('success' == response.status) {
                    window.location = '/settings';
                }

                if ('error' == response.status) {
                    $("#changeEmailError", form).removeClass('hidden');
                    $("#changeEmailError", form).html(response.error);
                }
            });

            return false;
        });

        $('#changePasswdSubmit').click(function(e) {
            e.preventDefault();

            var form = $('#change-password-form');
            $("#changePasswordError", form).addClass('hidden');

            $.post( "/changePassword", form.serialize(), function(response) {
                if ('success' == response.status) {
                    window.location = '/settings';
                }

                if ('error' == response.status) {
                    $("#changePasswordError", form).removeClass('hidden');
                    $("#changePasswordError", form).html(response.error);
                }
            });

            return false;
        });



        $(document).on('submit', '#ticketForm', function (e) {
            e.preventDefault();
            var form = $(this);
            var container = $("#htmlText");

            container.html('<img src="/themes/img/ajax-loader.gif" border="0">');

            $("#ticketMessageError", form).addClass('hidden');

            $.post(form.attr('action'), form.serialize(), function(response) {
                if ('success' == response.status) {
                    $("#message").val("");
                }

                if ('error' == response.status) {
                    $("#ticketMessageError", form).removeClass('hidden');
                    $("#ticketMessageError", form).html(response.error);
                }

                $.get(container.data('action'), function( data ) {
                    container.html(data);
                });
            });
            return false;
        });

        $('.show-ticket').on('click', function(e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#viewTicket');
            var contentContainer = $("#modal_content", modal);
            var subject = link.data('subject');

            contentContainer.html('<img src="/themes/img/ajax-loader.gif" border="0">');
            modal.modal('show');
            $('#titleName', modal).html(subject);

            $.get(link.attr('href'), function( data ) {
                contentContainer.html(data);
            });

            return false;
        });

        $(document).on('click', '.create-order', function (e) {
            var link = $(this);
            var errorHint = $('.error-hint');
            errorHint.addClass('hidden');

            if (link.data('error')) {
                e.preventDefault();
                $('.content', errorHint).html(link.data('error'));
                errorHint.removeClass('hidden');
                return false;
            }

            return true;
        });

        $(document).on('click', '.create-ticket', function (e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#submitTicket');
            var form = $('#support-form');
            var errorBlock = $('#createTicketError', form);
            var errorHint = $('.error-hint');
            errorHint.addClass('hidden');

            if (link.data('error')) {
                $('.content', errorHint).html(link.data('error'));
                errorHint.removeClass('hidden');
                return false;
            }

            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('input, textarea', form).val('');

            modal.modal('show');
        });


        $(document).on('submit', '#support-form', function (e) {
            e.preventDefault();
            var form = $(this);
            var errorBlock = $('#createTicketError', form);

            errorBlock.addClass('hidden');

            $.post(form.attr('action'), form.serialize(), function(response) {
                if ('success' == response.status) {
                    window.location.reload();
                }

                if ('error' == response.status) {
                    errorBlock.show();
                    errorBlock.removeClass('hidden');
                    errorBlock.html(response.error);
                }
            });
            return false;
        });

        $(document).on('click', '.check-limit', function (e) {
            e.preventDefault();

            var block = $(this);
            var url = block.data('url');
            var errorBlock = block.data('errorBlock');

            var result = false;

            $(errorBlock).addClass('hidden');

            $.ajax({
                url : url,
                async : false,
                success: function (response) {
                    if ('success' == response.status) {
                        result = true;
                    }

                    if ('error' == response.status) {
                        e.stopImmediatePropagation();
                        $(errorBlock).removeClass('hidden');
                        $(errorBlock).html(response.error);
                    }
                }
            });

            return result;
        });
    }
};
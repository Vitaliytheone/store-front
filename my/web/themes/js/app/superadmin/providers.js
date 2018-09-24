customModule.superadminProvidersController = {
    run : function(params) {
        $('#providersSearch').on('submit', function(e) {
            e.preventDefault();

            var form = $('#providersSearch');
            var link = form.attr('action');

            window.location.href = link + (link.match(/\?/) ? '&' : '?') + form.serialize();
        });

        function setInputs(method, details, form) {
            $('#editproviderform-provider_id', form).val(details.provider_id);
            method == 'edit' ? $('#editproviderform-name', form).val(details.name) : $('#create-provider-name', form).val(details.name);
            method == 'edit' ? $('#editproviderform-apihelp', form).val(details.apihelp) : $('#create-provider-apihelp', form).val(details.apihelp);
            method == 'edit' ? $('#editproviderform-name_script', form).val(details.name_script) : $('#create-provider-name_script', form).val(details.name_script);
            method == 'edit' ? $('#edit-provider-sender_params', form).val(details.sender_params) : $('#create-provider-sender_params', form).val(details.sender_params);
            method == 'edit' ? $('#edit-provider-provider_service_settings', form).val(details.provider_service_settings) : $('#create-provider-provider_service_settings', form).val(details.provider_service_settings);
            method == 'edit' ? $('#edit-provider-provider_service_api_error', form).val(details.provider_service_api_error) : $('#create-provider-provider_service_api_error', form).val(details.provider_service_api_error);
            method == 'edit' ? $('#editproviderform-service_options', form).val(details.service_options) : $('#create-provider-service_options', form).val(details.service_options);
            method == 'edit' ? $('#edit-provider-getstatus_params', form).val(details.getstatus_params) : $('#create-provider-getstatus_params', form).val(details.getstatus_params);

            method == 'edit' ? $('#edit-provider-status option[value='+ details.status +']').attr('selected', 'true') : $('#create-provider-status option[value='+ details.status +']').attr('selected', 'true');
            method == 'edit' ? $('#edit-provider-type option[value='+ details.type +']').attr('selected', 'true') : $('#create-provider-type option[value='+ details.type +']').attr('selected', 'true');
            method == 'edit' ? $('#edit-provider-start_count option[value='+ details.start_count +']').attr('selected', 'true') : $('#create-provider-start_count option[value='+ details.start_count +']').attr('selected', 'true');
            method == 'edit' ? $('#edit-provider-refill option[value='+ details.refill +']').attr('selected', 'true') : $('#create-provider-refill option[value='+ details.refill +']').attr('selected', 'true');
            method == 'edit' ? $('#edit-provider-cancel option[value='+ details.cancel +']').attr('selected', 'true') : $('#create-provider-cancel option[value='+ details.cancel +']').attr('selected', 'true');
            method == 'edit' ? $('#edit-provider-send_method option[value='+ details.send_method +']').attr('selected', 'true') : $('#create-provider-send_method option[value='+ details.send_method +']').attr('selected', 'true');
            method == 'edit' ? $('#edit-provider-service_view option[value='+ details.service_view +']').attr('selected', 'true') : $('#create-provider-service_view option[value='+ details.service_view +']').attr('selected', 'true');
            method == 'edit' ? $('#edit-provider-provider_service_id_label option[value='+ details.provider_service_id_label +']').attr('selected', 'true') : $('#create-provider-provider_service_id_label option[value='+ details.provider_service_id_label +']').attr('selected', 'true');
            method == 'edit' ? $('#edit-provider-service_description option[value='+ details.service_description +']').attr('selected', 'true') : $('#create-provider-service_description option[value='+ details.service_description +']').attr('selected', 'true');
            method == 'edit' ? $('#edit-provider-service_auto_min option[value='+ details.service_auto_min +']').attr('selected', 'true') : $('#create-provider-service_auto_min option[value='+ details.service_auto_min +']').attr('selected', 'true');
            method == 'edit' ? $('#edit-provider-service_auto_max option[value='+ details.service_auto_max +']').attr('selected', 'true') : $('#create-provider-service_auto_max option[value='+ details.service_auto_max +']').attr('selected', 'true');
            method == 'edit' ? $('#edit-provider-provider_rate option[value='+ details.provider_rate +']').attr('selected', 'true') : $('#create-provider-provider_rate option[value='+ details.provider_rate +']').attr('selected', 'true');
            method == 'edit' ? $('#edit-provider-service_auto_rate option[value='+ details.service_auto_rate +']').attr('selected', 'true') : $('#create-provider-service_auto_rate option[value='+ details.service_auto_rate +']').attr('selected', 'true');
            method == 'edit' ? $('#edit-provider-import option[value='+ details.import +']').attr('selected', 'true') : $('#create-provider-import option[value='+ details.import +']').attr('selected', 'true');
        }

        $('.edit-provider').click(function(e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#editProviderModal');
            var form = $('#editProviderForm');
            var errorBlock = $('#editProviderError', form);
            var details = link.data('details');

            errorBlock.addClass('hidden');
            errorBlock.html('');

            setInputs('edit', details, form);

            form.attr('action', link.attr('href'));

            modal.modal('show');

            return false;
        });

        $('.clone-provider').click(function(e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#createProviderModal');
            var form = $('#createProviderForm');
            var errorBlock = $('#createProviderError', form);
            var details = link.data('details');

            errorBlock.addClass('hidden');
            errorBlock.html('');

            setInputs('clone', details, form);

            form.attr('action', link.attr('href'));

            modal.modal('show');

            return false;
        });

        $('#createProvider').click(function(e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#createProviderModal');
            var form = $('#createProviderForm');
            var errorBlock = $('#createProviderError', form);

            errorBlock.addClass('hidden');
            errorBlock.html('');

            $(form).children().val('');

            form.attr('action', link.attr('href'));

            modal.modal('show');

            return false;
        });

        $(document).on('click', '#editProviderButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#editProviderForm');
            var errorBlock = $('#editProviderError', form);

            errorBlock.addClass('hidden');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {

                    if ('success' == response.status) {
                        $('#editProviderModal').modal('hide');
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

        $(document).on('click', '#createProviderButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#createProviderForm');
            var errorBlock = $('#createProviderError', form);

            errorBlock.addClass('hidden');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {

                    if ('success' == response.status) {
                        $('#createProviderModal').modal('hide');
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

        $(document).on('click', '.show-panels', function(e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#providerPanelsModal');
            var container = $('.modal-body', modal);
            var header = link.data('header');

            $('.modal-title', modal).text(header);

            container.html('<img src="/themes/img/ajax-loader.gif" border="0">');
            modal.modal('show');
            var projects = link.data('projects');

            if (!projects || !projects.length) {
                container.html('');
                return false;
            }

            var href = link.data('href');
            var content = [];
            $.each(projects, function (index, project) {
                content.push('<div class="row"> <a href="' + href + '" target="_blank" class="col-md-12"> ' + project.site + ' </a> </div>');
            });

            container.html(content.join(''));

            return false;
        });
    }
};
customModule.superadminProvidersController = {
    run : function(params) {
        $('#providersSearch').on('submit', function(e) {
            e.preventDefault();

            var form = $('#providersSearch');
            var link = form.attr('action');

            window.location.href = link + (link.match(/\?/) ? '&' : '?') + form.serialize();
        });

        $('.edit').click(function(e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#editProviderModal');
            var form = $('#editProviderForm');
            var errorBlock = $('#editProviderError', form);
            var details = link.data('details');

            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('#editproviderform-provider_id', form).val(details.provider_id);
            $('#editproviderform-name', form).val(details.name);
            $('#editproviderform-apihelp', form).val(details.apihelp);
            $('#editproviderform-name_script', form).val(details.name_script);
            $('#edit-provider-sender_params', form).val(details.sender_params);
            $('#edit-provider-provider_service_settings', form).val(details.provider_service_settings);
            $('#edit-provider-provider_service_api_error', form).val(details.provider_service_api_error);
            $('#editproviderform-service_options', form).val(details.service_options);
            $('#edit-provider-getstatus_params', form).val(details.getstatus_params);

            $('#edit-provider-status option[value='+ details.status +']').attr('selected', 'true');
            $('#edit-provider-type option[value='+ details.type +']').attr('selected', 'true');
            $('#edit-provider-start_count option[value='+ details.start_count +']').attr('selected', 'true');
            $('#edit-provider-refill option[value='+ details.refill +']').attr('selected', 'true');
            $('#edit-provider-cancel option[value='+ details.cancel +']').attr('selected', 'true');
            $('#edit-provider-send_method option[value='+ details.send_method +']').attr('selected', 'true');
            $('#edit-provider-service_view option[value='+ details.service_view +']').attr('selected', 'true');
            $('#edit-provider-provider_service_id_label option[value='+ details.provider_service_id_label +']').attr('selected', 'true');
            $('#edit-provider-service_description option[value='+ details.service_description +']').attr('selected', 'true');
            $('#edit-provider-service_auto_min option[value='+ details.service_auto_min +']').attr('selected', 'true');
            $('#edit-provider-service_auto_max option[value='+ details.service_auto_max +']').attr('selected', 'true');
            $('#edit-provider-provider_rate option[value='+ details.provider_rate +']').attr('selected', 'true');
            $('#edit-provider-service_auto_rate option[value='+ details.service_auto_rate +']').attr('selected', 'true');
            $('#edit-provider-import option[value='+ details.import +']').attr('selected', 'true');

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

            var content = [];
            $.each(projects, function (index, project) {
                content.push('<div class="row"> <div class="col-md-12"> ' + project.site + ' </div> </div>');
            });

            container.html(content.join(''));

            return false;
        });

        $('.query-sort').data("sorter", false);
        $('.no_sort').data("sorter", false);

        $("#providersTable").tablesorter();
    }
};
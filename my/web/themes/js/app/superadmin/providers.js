customModule.superadminProvidersController = {
    run : function(params) {
        $('#providersSearch').on('submit', function(e) {
            e.preventDefault();

            var form = $('#providersSearch');
            var link = form.attr('action');

            window.location.href = link + (link.match(/\?/) ? '&' : '?') + form.serialize();
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

        $('.edit-provider').click(function(e) {
            e.preventDefault();

            var link = $(this);
            var action = link.attr('href');
            var form = $('#editProviderForm');
            var modal = $('#editProviderModal');
            var errorBlock = $('#editProviderError', form);

            form.attr('action', action);

            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('input[type="text"]', form).val('');

            var details = link.data('details');

            $('#editproviderform-skype').val(details.skype);
            $('#editproviderform-name').val(details.name);

            modal.modal('show');

            return false;
        });

        $(document).on('click', '#editProviderButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#editProviderForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#editProviderModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });

        $("#providersTable").tablesorter();
    }
};
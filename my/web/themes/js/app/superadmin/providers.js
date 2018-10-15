customModule.superadminProvidersController = {
    run : function(params) {
        $('#providersSearch').on('submit', function(e) {
            e.preventDefault();

            var form = $('#providersSearch');
            var link = form.attr('action');

            window.location.href = link + (link.match(/\?/) ? '&' : '?') + form.serialize();
        });

        function setInputs(method, details, form) {
            $('input', form).val('');
            $('select', form).prop('selectedIndex', 0);

            if (method == false) {
                return false;
            }

            $.each(details, function(name, value) {
                if ($('input[id$="-' + name + '"]', form).length) {
                    $('input[id$="-' + name + '"]', form).val(value);
                }
                if ($('select[id$="-' + name + '"]', form).length) {
                    $('select[id$="-' + name + '"]', form).val(value);
                }
            });
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
            setInputs(false);

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

            $('#editProviderModal').scrollTop(0);

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

            $('#createProviderModal').scrollTop(0)

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
                content.push('<div class="row"> <a href="' + project.url + '" target="_blank" class="col-md-12"> ' + project.site + ' </a> </div>');
            });

            container.html(content.join(''));

            return false;
        });
    }
};
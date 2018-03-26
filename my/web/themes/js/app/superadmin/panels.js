customModule.superadminPanelsController = {
    run : function(params) {
        $('#panelsSearch').on('submit', function(e) {
            e.preventDefault();

            var form = $('#panelsSearch');
            var link = form.attr('action');

            window.location.href = link + (link.match(/\?/) ? '&' : '?') + form.serialize();
        });

        $('.change-domain').click(function(e) {
            e.preventDefault();

            var link = $(this);
            var action = link.attr('href');
            var form = $('#changeDomainForm');
            var modal = $('#changeDomainModal');
            var errorBlock = $('#changeDomainError', form);
            var domain = link.data('domain');
            var subdomain = link.data('subdomain');

            form.attr('action', action);

            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('#changedomainform-domain', form).val(domain);
            $('#changedomainform-subdomain', form).prop('checked', subdomain);

            modal.modal('show');

            return false;
        });

        $(document).on('click', '#changeDomainButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#changeDomainForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#changeDomainModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });

        $('.edit-expiry').click(function(e) {
            e.preventDefault();

            var link = $(this);
            var action = link.attr('href');
            var form = $('#editExpiryForm');
            var modal = $('#editExpiryModal');
            var errorBlock = $('#editExpiryError', form);

            form.attr('action', action);

            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('input[type="text"]', form).val('');

            var expired = link.data('expired');

            $('#editexpiryform-expired').val(expired);

            modal.modal('show');

            return false;
        });

        $(document).on('click', '#editExpiryButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#editExpiryForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#editExpiryModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });

        $('.edit-providers').click(function(e) {
            e.preventDefault();

            var link = $(this);
            var action = link.attr('href');
            var form = $('#editProvidersForm');
            var modal = $('#editProvidersModal');
            var errorBlock = $('#editProvidersError', form);

            form.attr('action', action);

            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('input[type="checkbox"]', form).prop('checked', false);

            var providers = link.data('providers');

            if ('undefined' != typeof providers && providers.length) {
                $.each(providers, function(index, value) {
                    $('input[value="' + value + '"]', form).prop('checked', true);
                });
            }

            modal.modal('show');

            return false;
        });

        $(document).on('click', '#editProvidersButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#editProvidersForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#editProvidersModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });

        $('.downgrade').click(function(e) {
            e.preventDefault();

            var link = $(this);
            var action = link.attr('href');
            var form = $('#downgradePanelForm');
            var modal = $('#downgradePanelModal');
            var errorBlock = $('#downgradePanelError', form);
            var providersUrl = link.data('providersurl');
            var providersSelect = $('#providers', form);

            form.attr('action', action);

            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('option', providersSelect).remove();

            $.get(providersUrl, function(response) {
                if ('undefined' == typeof response.providers) {
                    return false;
                }

                $.each(response.providers,  function(key, value) {
                    providersSelect
                        .append($("<option></option>")
                            .attr("value", key)
                            .text(value));
                });

                modal.modal('show');
            });

            return false;
        });

        $(document).on('click', '#downgradePanelButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#downgradePanelForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#downgradePanelModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });

        $('.upgrade').click(function(e) {
            e.preventDefault();

            var link = $(this);
            var action = link.attr('href');
            var form = $('#upgradePanelForm');
            var modal = $('#upgradePanelModal');
            var errorBlock = $('#upgradePanelError', form);

            $('input[type="text"]', form).val('');

            var total = link.data('total');

            $('#upgradepanelform-total').val(total);

            form.attr('action', action);

            errorBlock.addClass('hidden');
            errorBlock.html('');

            modal.modal('show');

            return false;
        });

        $(document).on('click', '.upgrade-panel-button', function(e) {
            e.stopImmediatePropagation();

            var btn = $(this);
            var form = $('#upgradePanelForm');
            var mode = btn.data('mode');

            $('#upgradepanelform-mode', form).prop('checked', (mode ? true : false));

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#upgradePanelModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });
    }
};
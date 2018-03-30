customModule.superadminStoresController = {
    run : function(params) {
        $('#storesSearch').on('submit', function(e) {
            e.preventDefault();

            var form = $('#storesSearch');
            var link = form.attr('action');

            window.location.href = link + (link.match(/\?/) ? '&' : '?') + form.serialize();
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

            $('#editstoreexpiryform-expired').val(expired);

            modal.modal('show');

            return false;
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

            $('#changestoredomainform-domain', form).val(domain);
            $('#changestoredomainform-subdomain', form).prop('checked', subdomain);

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
    }
};
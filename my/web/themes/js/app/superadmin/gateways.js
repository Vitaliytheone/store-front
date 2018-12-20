customModule.superadminGatewaysController = {
    run : function(params) {
        var self = this;

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
            $('#datetimepicker').datetimepicker({format:'YYYY-MM-DD HH:mm:ss'});
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

        $('.gateway-change-status').click(function(e) {
            e.preventDefault();
            var link = $(this);
            custom.confirm(link.data('title'), '', function() {
                $.ajax({
                    url: link.attr('href'),
                    type: 'POST',
                    dataType: 'json',
                    data: link.data('params')
                });
            });
            return false;
        });
    }
};
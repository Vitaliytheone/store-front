customModule.superadminStoresController = {
    run : function(params) {
        $('#storesSearch').on('submit', function(e) {
            e.preventDefault();

            var form = $('#storesSearch');
            var link = form.attr('action');

            window.location.href = link + (link.match(/\?/) ? '&' : '?') + form.serialize();
        });

        $('.edit-store').click(function(e) {
            e.preventDefault();

            var link = $(this);
            var details = link.data('details');
            var modal = $('#editStoreModal');
            var form = $('#editStoreForm');
            var errorBlock = $('#editStoreError');
            var action = link.attr('href');

            form.attr('action', action);

            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('#editstoreform-name', form).val(details.name);
            console.log(details.currency);
            $('#editstoreform-currency_option option[value='+ details.currency +']').attr('selected', 'true');

            var $select = $('select.customers-select');
            $select.find('option').removeAttr('selected');

            var index = $('span:contains('+ details.customer_email +')').parents('li').data('original-index');

            if ($('span:contains('+ details.customer_email +')').length == 0) {
                var selectOption = $select.find('option').eq(9);

                $('li[data-original-index=9]')
                    .children()
                    .replaceWith(
                        '<a tabindex="0" class data-tokens="'+ details.customer_email +'" role="option" aria-disabled="false" aria-selected="true">' +
                        '<span class="text">'+ details.customer_email +'</span><span class="glyphicon glyphicon-ok check-mark"></span></a>'
                    );
                selectOption.val(details.customer_id);
                selectOption.data('tokens', details.customer_email);
                selectOption.html(details.customer_email);
                index = 9;
            }
            var option = $select.find('option').eq(index);
            $select.val(option.val()).change();

            modal.modal('show');

            return false
        });

        $('#editstoreform-close, #editstoreform-x_close').click(function(e) {
            e.preventDefault();

            $('#editstoreform-currency_option option').removeAttr('selected');
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

        $('.change-domain').click(function(e) {
            e.preventDefault();

            var link = $(this);
            var action = link.attr('href');
            var form = $('#changeDomainForm');
            var modal = $('#changeDomainModal');
            var errorBlock = $('#changeDomainError', form);
            var domain = link.data('domain');
            var subdomain = link.data('subdomain');
            var title = link.data('title')

            form.attr('action', action);

            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('.modal-title', modal).html(title);
            $('#changestoredomainform-domain', form).val(domain);
            $('#changestoredomainform-subdomain', form).prop('checked', subdomain);

            modal.modal('show');

            return false;
        });

        $(document).on('click', '#editStoreButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#editStoreForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#editStoreModal').modal('hide');
                    location.reload();
                }
            });

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

        $('.stores-change-status').click(function(e) {
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
        });
    }
};
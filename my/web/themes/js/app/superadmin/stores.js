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
    }
};
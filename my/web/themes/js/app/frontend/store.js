customModule.storeController = {
    run : function(params) {
        $('.edit-store-domain').click(function (e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#editStoreDomainModal');
            var form = $('#editStoreDomainForm');
            var errorBlock = $('#editStoreDomainError', form);
            var domain = link.data('domain');

            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('#editstoredomainform-domain', form).val(domain);

            form.attr('action', link.attr('href'));

            modal.modal('show');

            return false;
        });

        $(document).on('click', '#editStoreDomainButton', function (e) {
            e.preventDefault();

            var btn = $(this);
            var form = $('#editStoreDomainForm');
            var errorBlock = $("#editStoreDomainError", form);
            errorBlock.addClass('hidden');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {

                    if ('success' == response.status) {
                        window.location.reload();
                    }

                    if ('error' == response.status) {
                        errorBlock.removeClass('hidden');
                        errorBlock.html(response.error);
                    }
                }
            });

            return false;
        });
    }
};
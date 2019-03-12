customModule.adminProviders = {
    run : function(params) {
        var self = this;

        $(document).on('click', '#showCreateProviderModal', function(e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#createProviderModal');
            var form = $('#createProviderForm', modal);
            var errorBlock = $('#createProviderError', form);

            errorBlock.addClass('hidden');
            errorBlock.html('');

            modal.modal('show');

            return false;
        });

        $(document).on('click', '#createProviderButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#createProviderForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#createProviderModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });
    }
};
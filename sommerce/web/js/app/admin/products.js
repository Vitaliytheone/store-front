customModule.adminProducts = {
    run : function(params) {
        var self = this;

        $(document).on('click', '#createProduct', function(e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#createProductModal');
            var form = $('#createProductForm', modal);
            var errorBlock = $('#createProductError', form);

            form.attr('action', link.attr('href'));
            errorBlock.addClass('hidden');
            errorBlock.html('');

            modal.modal('show');

            return false;
        });

        $(document).on('click', '#createProductButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#createProductForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#createProductModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });
    }
};
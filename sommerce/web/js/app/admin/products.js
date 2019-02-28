customModule.adminProducts = {
    run : function(params) {
        var self = this;
        var exitingUrls = params.exitingUrls;

        $('#createproductform-name').keyup(function(e) {
            var name = $(this).val();
            var createPageUrl = $('#createPageUrl');
            var urlInput = $('#createproductform-url');
            var generatedUrl;
            generatedUrl = custom.generateUrlFromString(name);
            generatedUrl = custom.generateUniqueUrl(generatedUrl, []);

            createPageUrl.text(generatedUrl);
            urlInput.val(generatedUrl);
        });

        $(document).on('click', '#createProduct', function(e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#createProductModal');
            var form = $('#createProductForm', modal);
            var errorBlock = $('#createProductError', form);

            form.attr('action', link.attr('href'));
            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('input[type="text"]', form).val('');
            $('input[type="checkbox"]', form).prop('checked', false);

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

        $(document).on('click', '.edit-product', function(e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#editProductModal');
            var form = $('#editProductForm', modal);
            var errorBlock = $('#editProductError', form);
            var details = link.data('details');
            form.attr('action', link.attr('href'));
            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('#productId', modal).html(details.id);
            $('#editproductform-name', modal).val(details.name);

            modal.modal('show');

            return false;
        });

        $(document).on('click', '#editProductButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#editProductForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#editProductModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });

        $(document).on('click', '.create-package', function(e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#createPackageModal');
            var form = $('#createPackageForm', modal);
            var errorBlock = $('#createPackageError', form);

            form.attr('action', link.attr('href'));
            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('#create-package-auto', form).addClass('hidden');
            $('input[type="text"]', form).val('');
            $('input[type="checkbox"]', form).prop('checked', false);
            $('input[type="checkbox"]', form).prop('checked', false);
            $('select', form).prop('selectedIndex',0);

            modal.modal('show');

            return false;
        });

        $(document).on('click', '#createPackageButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#createPackageForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#createPackageModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });

        $(document).on('click', '.edit-package', function(e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#editPackageModal');
            var form = $('#editPackageForm', modal);
            var errorBlock = $('#editPackageError', form);
            var details = link.data('details');
            form.attr('action', link.attr('href'));
            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('#packageId', modal).html(details.id);
            $('#editpackageform-name', modal).val(details.name);
            $('#editpackageform-price', modal).val(details.price);
            $('#editpackageform-quantity', modal).val(details.quantity);
            $('#editpackageform-link_type', modal).val(details.link_type);
            $('#editpackageform-visibility', modal).val(details.visibility);
            $('#editpackageform-mode', modal).val(details.mode).trigger('change');

            modal.modal('show');

            return false;
        });

        $(document).on('click', '#editPackageButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#editPackageForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#editPackageModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });

        $('#createpackageform-mode, #editpackageform-mode').change(function () {
            var value = $(this).val() * 1;
            var form = $(this).parents('form');
            var container = $('#create-package-auto, #edit-package-auto', form);

            container.addClass('hidden');
            if (value) {
                container.removeClass('hidden');
            }
        });

        $(".sortable").sortable({
            containment: "parent",
            items: "> div",
            handle: ".move",
            tolerance: "pointer",
            cursor: "move",
            opacity: 0.7,
            revert: 300,
            delay: 150,
            dropOnEmpty: true,
            placeholder: "movable-placeholder",
            start: function(e, ui) {
                ui.placeholder.height(50);
                $(this).attr('data-previndex', ui.item.index());
            },
            update: function(e, ui) {
                var newIndex = ui.item.index();
                var oldIndex = $(this).attr('data-previndex');

                console.log(oldIndex, 'old index');
                console.log(newIndex, 'new index');

                $(this).removeAttr('data-previndex');
            }
        });

        // Sort the children
        $(".sortable-packages").sortable({
            containment: "parent",
            handle: ".sommerce-products-editor__packages-drag",
            tolerance: "pointer"
        });
    }
};
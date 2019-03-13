customModule.adminProducts = {
    run : function(params) {
        var self = this;
        var exitingUrls = params.exitingUrls;

        $(document).on('click', '.duplicate-package', function(e) {
            e.preventDefault();
            var btn = $(this);
            var confirmBtn = $('#confirm_yes');

            custom.confirm(btn.data('confirm-title'), undefined, {}, function () {
                custom.sendBtn(btn, {
                    data: self.getTokenParams(),
                    method: 'POST',
                    callback : function(response) {
                        location.reload();
                    }
                });
            });

            return false;
        });

        $('#createproductform-name').keyup(function(e) {
            var name = $(this).val();
            var createPageUrl = $('#createPageUrl');
            var urlInput = $('#createproductform-url');
            var generatedUrl;
            generatedUrl = custom.generateUrlFromString(name);
            generatedUrl = custom.generateUniqueUrl(generatedUrl, exitingUrls);

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

            if (!details) {
                return false;
            }

            $('#packageId', modal).html(details.id);
            $('#editpackageform-name', modal).val(details.name);
            $('#editpackageform-price', modal).val(details.price);
            $('#editpackageform-quantity', modal).val(details.quantity);
            $('#editpackageform-link_type', modal).val(details.link_type);
            $('#editpackageform-visibility', modal).val(details.visibility);
            if (!details.provider_id) {
                $('#editpackageform-provider_id select option[value=""]').prop('selected', true);
            }
            $('#editpackageform-provider_id', modal).val(details.provider_id);
            $('#editpackageform-provider_service', modal).val(details.provider_service);
            $('#editpackageform-id', modal).val(details.id);
            $('.delete-package', modal).attr('href', link.data('delete_link'));
            $('#editpackageform-mode', modal).val(details.mode).trigger('change');
            modal.modal('show');

            $('#editpackageform-provider_id', modal).trigger('change', [details.provider_service]);

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

        $(document).on('click', '.delete-package', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#editPackageForm');

            custom.sendBtn(btn, {
                data: self.getTokenParams(),
                method: 'POST',
                callback : function(response) {
                    $('#editPackageModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });

        self.sortable();

        self.providerServices($('#editPackageModal'), params);
        self.providerServices($('#createPackageModal'), params);
    },
    sortable: function () {
        var productsSortable = $('.sortable'),
            packagesSortable = $(".sortable-packages"),
            self = this;

        // Init sortable
        if (productsSortable.length > 0) {
            // Sort the parents
            productsSortable.sortable({
                containment: "parent",
                items: "> .product-item",
                handle: ".move",
                tolerance: "pointer",
                cursor: "move",
                opacity: 0.7,
                revert: 300,
                delay: 150,
                dropOnEmpty: true,
                placeholder: "movable-placeholder"
            });

            // Sort the children
            packagesSortable.sortable({
                items: "> .package-item",
                handle: ".sommerce-products-editor__packages-drag",
                tolerance: "pointer",
                containment: "parent"
            });

            productsSortable.sortable({
                update: function(event, ui) {
                    var currentItem = ui.item,
                        newPosition = currentItem.index(),
                        actionUrl = currentItem.data('action-url') + newPosition;

                    $.ajax({
                        url: actionUrl,
                        type: "POST",
                        data: self.getTokenParams(),
                        success: function (data, textStatus, jqXHR){
                            if (data.error){
                                return;
                            }
                            //Success
                        },
                        error: function (jqXHR, textStatus, errorThrown){
                            console.log('Error on save', jqXHR, textStatus, errorThrown);
                        }
                    });
                }
            });

            packagesSortable.sortable({
                update: function (event, ui) {
                    var currentItem = ui.item,
                        newPosition = currentItem.index(),
                        actionUrl = currentItem.data('action-url') + newPosition;

                    $.ajax({
                        url: actionUrl,
                        type: "POST",
                        data: self.getTokenParams(),
                        success: function (data, textStatus, jqXHR) {
                            if (data.error) {
                                return;
                            }
                            //Success
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.log('Error on save', jqXHR, textStatus, errorThrown);
                        }
                    });
                }
            });
        }
    },
    getTokenParams: function () {
        var csrfToken = $('meta[name="csrf-token"]').attr("content"),
            csrfParam = $('meta[name="csrf-param"]').attr("content");

        var tokenParams = {};
        tokenParams[csrfParam] = csrfToken;

        return tokenParams;
    },
    providerServices: function (modal, params) {
        var self = this;

        $(document).on('change', '#' + modal.attr('id') + ' .provider-id', function(e, selectedServiceId) {
            var apiErrorBlock = $('.api-error', modal);
            var optionSelected = $("option:selected", this),
                actionUrl = params.servicesUrl;

            clearProviderServisesList();
            if (actionUrl === undefined) {
                apiErrorBlock.addClass('hidden');
                return;
            }

            $.ajax({
                url: actionUrl,
                data: {id: optionSelected.val()},
                type: "GET",
                timeout: 15000,
                success: function(data, textStatus, jqXHR) {
                    if (data.hasOwnProperty('error')) {
                        apiErrorBlock.removeClass('hidden').text(data.message);
                    } else {
                        apiErrorBlock.addClass('hidden');
                        renderProviderServices(data, selectedServiceId);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    var errorMessage = '';
                    // Timeout error
                    if (textStatus === "timeout") {
                        errorMessage = jqXHR.responseJSON.message;
                    }

                    console.log('Something was wrong...', textStatus, errorThrown, jqXHR);
                    apiErrorBlock.removeClass('hidden').text(errorMessage);
                }
            });
        });

        function clearProviderServisesList() {
            $('.provider-service', modal).find("option:not(:eq(0))").remove();
            $('.provider-service', modal).find('option:eq(0)').prop('selected', true);
        }

        function renderProviderServices(services, selectedServiceId){
            var selected,
                $container = $('<div></div>');
            _.each(services, function (s) {
                if (selectedServiceId) {
                    selected = s.service.toString() === selectedServiceId.toString() ? 'selected' : '';
                }
                $container.append('<option value="' + s.service + '"'+ selected + '>' + s.service + ' - ' + s.name + '</option>');
            });
            clearProviderServisesList();
            $('.provider-service', modal).append($container.html());
        }
    }
};
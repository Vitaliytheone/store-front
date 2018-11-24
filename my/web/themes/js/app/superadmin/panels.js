customModule.superadminPanelsController = {
    run : function(params) {
        var self = this;

        self.editPaymentMethods(params);

        $('#search-providers').on('keyup', function(e) {
            if (e.keyCode == 13) {
                e.preventDefault();
                $('#modal-search-providers').click();
            }
        });

        $('#submitSearch').on('click', function(e) {
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

        $('.edit-providers').click(function(e) {
            e.preventDefault();
            var link = $(this);
            var action = link.attr('href');
            modal = $('#editProvidersModal');
            searchFilter = false;
            $('#show-selected-checkbox').prop('checked', false);
            $('#perfect-panel-checkbox').prop('checked', false);
            $('#search-providers').val('');

            var form = $('#editProvidersForm');
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

        $('.change-providers').click(function(e) {
            e.preventDefault();
            var link = $(this);
            var action = link.attr('href');
            var modal = $('#changePanelProviderModal');
            var url = link.data('providers');
            var modalSelect = $('.providers-list select', modal);

            var form = $('#changePanelProviderForm');
            var errorBlock = $('#changePanelProviderError', form);

            form.attr('action', action);

            errorBlock.addClass('hidden');
            errorBlock.html('');

            $.get(url, function (response) {
                $.each(response.content, function(index, name) {
                    if (response.current == index) {
                        modalSelect.append($("<option></option>", {value: index, text: name, selected: 'selected'}));
                    } else {
                        modalSelect.append($("<option></option>", {value: index, text: name}));
                    }
                });
            });

            modal.modal('show');
            return false;
        });

        $(document).on('click', '#changePanelProviderButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#changePanelProviderForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#changePanelProviderModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });

        $('.close-change-modal').click(function(e) {
            e.preventDefault();

            var modal = $('#changePanelProviderModal');
            var modalSelect = $('.providers-list select', modal);

            modalSelect.html('');
        });

        $('#changePanelProviderModal').keyup(function(e) {
            if (e.keyCode == 27) {
                var modalSelect = $('.providers-list select', $(this));
                modalSelect.html('');
            }
        });

        $('.edit-panels').click(function(e) {
            e.preventDefault();
            var link = $(this);
            var action = link.attr('href');
            var form = $('#edit-panel-form');
            var errorBlock = $('#edit-panel-error', form);
            var modal = $('#editPanelsModal');
            form.attr('action', action);

            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('input[type="checkbox"]', form).prop('checked', false);

            var panel = link.data('panels');
            setForm(panel);
            modal.modal('show');
            return false;
        });

        function setForm(panel) {
            if (!panel) {
                return;
            }
            var form = $('#edit-panel-form');
            var inputs = form.data('inputs');
            var checkboxes =  inputs['checkboxes'];

            for (var prop in checkboxes) {
                var value = 1;
                if (checkboxes[prop] == 'captcha') {
                   value = 0;
                }
                if (panel[checkboxes[prop]] == value) {
                    $('#form-' + checkboxes[prop]).prop('checked', true);
                }
            }
            var textInputs =  inputs['textInputs'];
            for (var prop in textInputs) {
                $('#editprojectform-' + textInputs[prop]).val(panel[textInputs[prop]]);
            }
            var dropdowns =  inputs['dropdowns'];

            for (var prop in dropdowns) {
                $('#editprojectform-' + dropdowns[prop]).val(panel[dropdowns[prop]]);
            }

            $('.selectpicker.customers-select').trigger('customers:add', {
                'id': panel['cid'] ,
                'email': panel['customer_email']
            });

            $('.selectpicker').selectpicker('refresh');
        }

        new Clipboard('.copy', {
            container: document.getElementById('#editPanelsModal')
        });

        $('#generate-api-key').click(function(e){
            e.preventDefault();
            var input = $('#editprojectform-apikey');
            $.ajax({
                url: input.data('action'),
                type: 'GET',
                dataType: 'json',
                async: false,
                success: function (data) {
                    input.val(data['key']);
                }
            });
        });

        $('#show-selected-checkbox, #perfect-panel-checkbox').change(function(e){
            filterProviders();
        });
        $('#modal-search-providers').click(function(e){
            searchFilter = true;
            filterProviders();
        });


        var searchFilter = false;

        function filterProviders()
        {
            var showSelected = $('#show-selected-checkbox').prop('checked');
            var showInternal = $('#perfect-panel-checkbox').prop('checked');
            var searchInput = $('#search-providers').val();
            if (!searchInput) {
                searchFilter = false;
            }
            $('.providers-filter-result .custom-checkbox').each(function (index, el) {
                var item = $(el);
                var checkbox = item.find('input[type ="checkbox"]');
                var filter = true;
                if (showSelected && !checkbox.prop('checked'))  {
                    filter = false;
                }
                if (showInternal && !item.find('.fa-check-circle-o').length)  {
                    filter = false;
                }
                if (searchFilter && item.find('.custom-control-label').text().search(searchInput) < 0) {
                    filter = false;
                }
                if (filter) {
                    item.show();
                } else {
                    item.hide();
                }

            });
        }

        $('#editprojectform-save').click(function(e){
            e.preventDefault();
            var btn = $(this);
            var form = $('#edit-panel-form');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    location.reload();
                }
            });

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
            });
            modal.modal('show');
            return false;
        });

        $(document).on('click', '#downgradePanelButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#downgradePanelForm');

            $('#downgradePanelModal').modal('hide');

            custom.confirm(btn.data('title'), '', function() {
                custom.sendFrom(btn, form, {
                    data: form.serialize(),
                    callback : function(response) {
                        $('#downgradePanelModal').modal('hide');
                        location.reload();
                    },
                    errorCallback : function() {
                        $('#downgradePanelModal').modal('show');
                    }
                });
            });
        });

        $('.panels-change-status').click(function(e) {
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
    },
    editPaymentMethods: function (params) {
        var self = this;

        $('.edit-payment-methods').click(function(e) {
            e.preventDefault();
            var link = $(this);
            var action = link.attr('href');
            var modal = $('#editPaymentMethodsModal');
            var form = $('#editPaymentMethodsForm');
            var errorBlock = $('#editPaymentMethodsError', form);
            var container = $('#editPaymentMethodsContainer', modal);

            form.attr('action', action);

            errorBlock.addClass('hidden');
            errorBlock.html('');
            container.html('');
            container.append('<span class="spinner"><i class="fa fa-spinner fa-spin"></i></span>');

            $.get(link.attr('href'), function (response) {
                if (response.content) {
                    container.html(response.content);
                }
            });

            modal.modal('show');
            return false;
        });

        $(document).on('click', '#editPaymentMethodsButton, #addPaymentMethodBtn', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#editPaymentMethodsForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#editPaymentMethodsModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });
    }
};
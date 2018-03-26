customModule.orderDomainController = {
    run : function(params) {

        var self = this;
        var orderDomainForm = 'undefined' !== typeof params.orderDomainForm ? params.orderDomainForm : undefined;

        $('#searchDomain').change(function(e) {
            $('#searchResult').addClass('hidden');
        });


        $('#searchDomainSubmit').click(function(e) {
            e.preventDefault();

            var btn = $(this);
            var action = btn.data('action');
            var input = $('#searchDomain');
            var query = $.trim(input.val());
            var container = $('#searchResultContainer');

            if (!query.length) {
                return false;
            }
            $('#searchResult').addClass('hidden');
            btn.addClass('active');

            $.get(action, {
                'search_domain' : query,
                'zone' : $('#domain_zone').selectpicker('val')
            }, function (response) {
                btn.removeClass('active');
                if (response.content) {
                    container.html(response.content);
                    $('#searchResult').removeClass('hidden');
                    $('.domain_zone').trigger('change');
                }
            });

            return false;
        });

        $('#continueDomainSearch').click(function(e) {
            e.preventDefault();

            var link = $(this);

            if (link.hasClass('disabled')) {
                return false;
            }

            var modal =  $('#orderDomainModal');
            var errorBlock = $('#orderDomainError', modal);
            var domain = $('.domain_zone:checked').data('domain');

            modal.modal('show');

            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('#modal_domain_name', modal).val(domain);

            return false;
        });

        $(document).on('change', '.domain_zone', function() {
            if ($('.domain_zone:checked').length) {
                $('#domain_zone').selectpicker('val', $('.domain_zone:checked').val());
                $('#continueDomainSearch').removeClass('disabled');
            } else {
                $('#continueDomainSearch').addClass('disabled');
            }
        });

        $('#orderDomainBtn').click(function (e) {
            e.preventDefault();

            var btn = $(this);
            var modal =  $('#orderDomainModal');
            var errorBlock = $("#orderDomainError", modal);
            var action = btn.data('action');
            var data = modal.find("select, textarea, input").serialize();

            errorBlock.addClass('hidden');

            btn.addClass('active');

            if ('undefined' !== typeof orderDomainForm) {
                var form = $(orderDomainForm);
                action = form.data('action');
                data = form.serialize();
            }

            $.post(action, data, function(response) {
                btn.removeClass('active');

                if ('success' == response.status) {
                    modal.modal('hide');

                    if ('undefined' !== typeof response.redirect) {
                        window.location.href = response.redirect;
                        return;
                    }

                    $('#orderPanelBlock').removeClass('hidden');
                    $('#orderDomainBlock').addClass('hidden');

                    $('#helpDomain').addClass('hidden');

                    $('#domain').val($('#modal_domain_name', modal).val()).prop('readonly', true);
                }

                if ('error' == response.status) {
                    errorBlock.removeClass('hidden');
                    errorBlock.html(response.error);
                }
            });

            return false;
        });
    }
};
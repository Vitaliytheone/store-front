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

        $(document).on('change', '.domain_zone', function() {
            if ($('.domain_zone:checked').length) {
                $('#domain_zone').selectpicker('val', $('.domain_zone:checked').val());
                $('#continueDomainSearch').removeClass('disabled');
            } else {
                $('#continueDomainSearch').addClass('disabled');
            }
        });

        $('#continueDomainSearch').click(function (e) {
            e.preventDefault();

            var btn = $(this);
            var errorBlock = $("#orderDomainError");
            var action = btn.data('action');
            var data = $("#order-form").serialize();
            var domain = $('.domain_zone:checked').data('domain');

            $('#modal_domain_name').val(domain);

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

                    if ('undefined' !== typeof response.redirect) {
                        window.location.href = response.redirect;
                        return;
                    }

                    $('#orderBlock').removeClass('hidden');
                    $('#orderDomainBlock').addClass('hidden');

                    $('#helpDomain').addClass('hidden');

                    $('#domain').val($('#modal_domain_name').val()).prop('readonly', true);
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
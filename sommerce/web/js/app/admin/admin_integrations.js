/**
 * /admin/settings/payments custom js module
 * @type {{run: customModule.settings.run}}
 */
customModule.adminIntegrations = {
    run: function (params) {
        /******************************************************************
         *            Toggle `store integration` active status
         ******************************************************************/
        $(document).on('change', '.toggle-active', function (e) {
            var $checkbox = $(e.currentTarget),
                actionUrl = $checkbox.data('action_url'),
                active = $checkbox.prop('checked') | 0;
                category = $checkbox.data('category');

            $.ajax({
                url: actionUrl,
                type: "POST",
                data: {
                    active: active
                },
                success: function (data, textStatus, jqXHR) {
                    $('.toggle-active' + '.' + category).not($checkbox).prop('checked', false);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log('Error on update', jqXHR, textStatus, errorThrown);
                }
            });
        });

        $(document).on('click', '#editIntegrationButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#editIntegrationForm');
            var errorBlock = $('#editIntegrationError', form);

            errorBlock.addClass('hidden');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {

                    if ('success' == response.status) {
                        location.replace(response.redirect);
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
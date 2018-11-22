customModule.superadminSslController = {
    run : function(params) {

        $('#sslSearch').on('submit', function(e) {
            e.preventDefault();

            var form = $('#sslSearch');
            var link = form.attr('action');

            window.location.href = link + (link.match(/\?/) ? '&' : '?') + form.serialize();
        });

        $('.ssl-details').click(function (e) {
            e.preventDefault();

            var link = $(this);
            var url = link.attr('href');
            var modal = $('#sslDetailsModal');
            var modalContainer = $('.modal-body', modal);

            modal.modal('show');

            modalContainer.html('<span class="spinner"><i class="fa fa-spinner fa-spin"></i></span>');

            $.get(url, function (response) {
                modalContainer.html(response.content);
            });

            return false;
        });

        $('.ssl-disable').click(function (e) {
            e.preventDefault();

            var link = $(this);
            var url = link.attr('href');
            var modal = $('#sslSubmitDisableModal');
            var modalContainer = $('.modal-body', modal);

            modal.find('.disable_ssl_submit').data('href', url);

            modal.modal('show');
        });

        $('.disable_ssl_submit').click(function(e) {

            var submitBnt = $(this);
            var actionUrl = submitBnt.data('href');
            var modal = $(this).find('sslSubmitDisableModal');

            custom.sendBtn(submitBnt, {
                url: actionUrl,
                callback : function(response) {
                    if ('success' === response.status) {
                        modal.modal('hide');
                        location.reload();
                    }
                }
            });
        });

    }
};
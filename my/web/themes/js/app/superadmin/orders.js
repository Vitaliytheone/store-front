customModule.superadminOrdersController = {
    run : function(params) {
        $('#ordersSearch').on('submit', function(e) {
            e.preventDefault();

            var form = $('#ordersSearch');
            var link = form.attr('action');

            window.location.href = link + (link.match(/\?/) ? '&' : '?') + form.serialize();
        });

        $('.order-details').click(function (e) {
            e.preventDefault();

            var link = $(this);
            var url = link.attr('href');
            var modal = $('#ordersDetailsModal');
            var modalContainer = $('.modal-body', modal);

            modal.modal('show');

            modalContainer.html('<span class="spinner"><i class="fa fa-spinner fa-spin"></i></span>');

            $.get(url, function (response) {
                modalContainer.html(response.content);
            });

            return false;
        });
    }
};
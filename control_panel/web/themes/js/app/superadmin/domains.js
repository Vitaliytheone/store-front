customModule.superadminDomainsController = {
    run : function(params) {
        $('#domainsSearch').on('submit', function(e) {
            e.preventDefault();

            var form = $('#domainsSearch');
            var link = form.attr('action');

            window.location.href = link + (link.match(/\?/) ? '&' : '?') + form.serialize();
        });

        $('.domain-details').click(function (e) {
            e.preventDefault();

            var link = $(this);
            var url = link.attr('href');
            var modal = $('#domainDetailsModal');
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
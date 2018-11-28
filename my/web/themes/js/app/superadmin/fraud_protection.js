customModule.superadminFraudProtectionController = {
    run : function(params) {

        $('.report-details').click(function (e) {
            e.preventDefault();

            var link = $(this);
            var url = link.attr('href');
            var modal = $('#reportsDetailsModal');
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
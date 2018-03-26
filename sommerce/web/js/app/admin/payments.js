/**
 * Payments custom js module
 */
customModule.payments = {
    run: function (params) {

        /******************************************************************
         *                    Get payment details
         ******************************************************************/

        var $modal = $('.payments_detail'),
            $modalTitle = $modal.find('.modal-title'),
            $detailsContainer = $modal.find('.details-container'),
            $modalLoader = $modal.find('.modal-loader');

        $modal.on('show.bs.modal', function (e) {
            var $target = $(e.relatedTarget),
                paymentId = $target.data('id'),
                modalTitle = $target.data('modal_title'),
                actionUrl = $target.data('action_url');

            if (paymentId === undefined || actionUrl === undefined ) {
                return;
            }

            $modalLoader.removeClass('hidden');
            $.ajax({
                url: actionUrl,
                type: "GET",
                success: function (data) {
                    $modalLoader.addClass('hidden');
                    if (data === undefined) {
                        return;
                    }
                    renderLogs(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log('Something is wrong!');
                    console.log(jqXHR, textStatus, errorThrown);
                    $modalLoader.addClass('hidden');
                    $modal.modal('hide');
                }
            });

            function renderLogs(details) {
                $modalTitle.html(modalTitle);
                _.each(details, function(detail){
                    $detailsContainer.append('<pre class="sommerce-pre details-item">' + detail.time  + '<br><br>' + detail.data + '</pre>');
                });
            }
        });

        $modal.on('hidden.bs.modal', function (e) {
            $modalTitle.html('');
            $detailsContainer.empty();
        });
    }
};


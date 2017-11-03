customModule.ordersDetails = {
    run : function(params) {
        $(document).ready(function () {
            var ajaxEndpoint = '/admin/orders/get-order-details';
            var $detailsModal = $('#suborder-details-modal'),
                $modatTitle = $detailsModal.find('.modal-title'),
                $provider = $detailsModal.find('#order-detail-provider'),
                $providerOrderId = $detailsModal.find('#order-detail-provider-order-id'),
                $providerResponce = $detailsModal.find('#order-detail-provider-response'),
                $providerUpdate = $detailsModal.find('#order-detail-lastupdate');

            $detailsModal.on('show.bs.modal', function(e) {
                var suborderId = $(e.relatedTarget).data('suborder-id');
                if (suborderId === undefined || isNaN(suborderId)) {
                    return;
                }
                $.ajax({
                    url: ajaxEndpoint,
                    type: "GET",
                    data: {
                        'suborder_id': suborderId
                    },
                    success: function (data) {
                        if (data.details === undefined) {
                            return;
                        }
                        renderLogs(data.details);
                    },
                    error: function(jqXHR, textStatus, errorThrown){
                        console.log('Something is wrong!');
                        console.log(jqXHR, textStatus, errorThrown);
                    }
                });

                function renderLogs(details){
                    $modatTitle.html('Order ' + suborderId + ' details');
                    $provider.val(details.provider);
                    $providerOrderId.val(details.provider_order_id);
                    $providerResponce.html(details.provider_response);
                    $providerUpdate.val(details.updated_at);
                }
            });

            $detailsModal.on('hidden.bs.modal',function(e) {
                var $currentTarget = $(e.currentTarget);
                $currentTarget.find('input').val('');
                $providerResponce.html('');
            });
        });
    }
};
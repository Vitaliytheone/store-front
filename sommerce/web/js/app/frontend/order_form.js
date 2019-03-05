customModule.orderFormFrontend = {

    run : function(params) {

        console.log(params);

        var self = this;

        var paymentMethods = params.payment_methods,
            orderDataUrl = params.order_data_url;

        var $paymentModal;

        $('body').append(templates['order/order_modal']);

        $paymentModal = $('#order-package-modal');

        $('.buy-package').on('click', function (event) {
            event.preventDefault();
            event.stopPropagation();

            var $this =  $(this);
            var packageId = $this.data('id');

            if (!packageId) {
                throw 'Package id is undefined!';
            }

            orderDataUrl = orderDataUrl.replace('_id_', packageId);

            $paymentModal.modal('show');

            custom.ajax({
                url: orderDataUrl,
                type: 'GET',
                success: function(data, textStatus, jqXHR) {
                    if (!data.success || !data.orderData) {
                        console.log('Bad response data!', data, textStatus, jqXHR);
                        return;
                    }
                    // openPaymentModal(data.orderData);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log('Bad response data!', jqXHR, textStatus, errorThrown);
                }
            });
        });

    }
};
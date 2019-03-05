customModule.orderFormFrontend = {

    run : function(params) {

        console.log(params);

        var self = this;

        var paymentMethods = params.payment_methods,
            orderDataUrl = params.order_data_url,
            formActionUrl = params.form_action_url,
            formValidateUlr = params.form_validate_ulr;

        if (!paymentMethods || !orderDataUrl || !formActionUrl || !formValidateUlr) {
            console.log('Bad config!');
            return;
        }

        var $paymentModal = function(){ return $('#order-package-modal'); },
            $form = function(){ return $paymentModal().find('form'); },
            $submit = function(){ return $paymentModal.find('#proceed_checkout'); };

        $('.buy-package').on('click', function (event) {
            event.preventDefault();
            event.stopPropagation();

            var $this =  $(this);
            var packageId = $this.data('id');

            $paymentModal().remove();

            if (!packageId) {
                throw 'Package id is undefined!';
            }

            orderDataUrl = orderDataUrl.replace('_id_', packageId);

            custom.ajax({
                url: orderDataUrl,
                type: 'GET',
                success: function(data, textStatus, jqXHR) {
                    if (!data.success || !data.data) {
                        console.log('Bad response data!', data, textStatus, jqXHR);
                        return;
                    }
                    showModal(data.data);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log('Bad response data!', jqXHR, textStatus, errorThrown);
                }
            });
        });

        $(document).on('click', '#proceed_checkout', function (event) {
            hideValidationError();

            custom.ajax({
                url: formValidateUlr,
                type: 'POST',
                data: $form().serialize(),
                success: function(data, textStatus, jqXHR) {
                    if (!data.success || !data.data) {
                        console.log('Bad response data!', data, textStatus, jqXHR);
                    }
                    $form().submit();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    if (jqXHR.responseJSON.hasOwnProperty('error_message')) {
                        showValidationError(jqXHR.responseJSON.error_message);
                    }
                }
            });
        });

        function hideValidationError() {
            $paymentModal().find('.sommerce-modals__alert').html('').css('display', 'none');
        }

        function showValidationError(errorMessage) {
            $paymentModal().find('.sommerce-modals__alert').html(errorMessage).css('display', 'block');
        }

        function showModal(data) {
            $('body').append(templates['order/order_modal']({
                'package_id': data.id,
                'package_name': data.name,
                'package_price': data.price,
                'payment_methods': paymentMethods,
                'form_action_url': formActionUrl
            }));

            _.defer(function(){
                hideValidationError();
                $paymentModal().modal('show');
            });
        }

    }
};
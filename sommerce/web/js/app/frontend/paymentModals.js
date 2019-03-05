customModule.paymentResultModal = {
    run : function(params) {
        var selector = null;
        var modal = null;

        switch(params.type) {
            case 'payment_fail':
                modal = templates['payments_modal/failed']();
                selector = '#modal-payment-failed';
                break;
            case 'payment_success':
                modal = templates['payments_modal/success'](params.data);
                selector = '#modal-payment-success';
                break;
            case 'payment_awaiting':
                modal = templates['payments_modal/awaiting']();
                selector = '#modal-payment-awaiting';
                break;
        }

        $('body').append(modal);
        $(selector).modal('show');
    }
};
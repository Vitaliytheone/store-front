customModule.adminNotifyLayout = {
    run : function(params) {
        var self = this;

        /*****************************************************************************************************
         *                     Popup notifications init
         *****************************************************************************************************/
        toastr.options = {
            "closeButton": false,
            "debug": false,
            "newestOnTop": false,
            "progressBar": false,
            "positionClass": "toast-bottom-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "5000",
            "timeOut": "5000",
            "extendedTimeOut": "5000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };


        /*****************************************************************************************************
         *                     Page notifications init
         *****************************************************************************************************/
        /* Check if page have messages */
        var messages = params.messages || null;

        if (!params.messages) {
            return;
        }

        _.forEach(params.messages, function(message) {
            self.send(message);
        });
    },
    send: function (message) {
        if (message.success) {
            toastr.success(message.success);
        }
        if (message.warning) {
            toastr.warning(message.warning);
        }
        if (message.error) {
            toastr.error(message.error);
        }
    }
};
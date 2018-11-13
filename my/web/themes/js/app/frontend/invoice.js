customModule.invoiceController = {
    notes: [],
    run : function(params) {
        var self = this;

        var live = params.live;

        if ('undefined' == typeof params.live) {
            live = true;
        }

        self.notes = params.notes;
        self.showContent(params.code);

        if (live) {
            $(document).on('change', '#code', function(e) {
                var num = $(this).val();

                self.showContent(num);
            });
        }
    },
    showContent: function(num) {
        var self = this;

        var paymentContent = $('#paymentContent');

        paymentContent.addClass('hidden');

        $('.content', paymentContent).text('');
        if ('undefined' != typeof self.notes[num] && self.notes[num].length) {
            $('.content', paymentContent).html(self.notes[num]);
            paymentContent.removeClass('hidden');
        }
    }
};
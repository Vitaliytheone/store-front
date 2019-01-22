customModule.paymentsCheckout = {
    fieldsOptions: undefined,
    fieldsContainer: undefined,
    run : function(params) {
        var self = this;

        self.fieldsContainer = $('form');
        self.fieldOptions = params.fieldOptions;
        self.params = params;

        if ('undefined' != typeof params.options) {
            if ('undefined' != typeof params.options.stripe) {
                self.initStripe(params.options.stripe);
            }
        }

        self.updateFields();
    },
    updateFields: function () {
        var self = this;

        $('button[type=submit]', self.fieldsContainer).show();
        $('.fields', self.fieldsContainer).remove();
        $('input,select', self.fieldsContainer).prop('disabled', false);

        if ('undefined' == typeof self.fieldOptions
            || !self.fieldOptions) {
            return;
        }

        var fieldContent = [];
        var inputTemplate = templates['checkout/input'];
        var hiddenTemplate = templates['checkout/hidden'];
        var checkboxTemplate = templates['checkout/checkbox'];

        $.each(self.fieldOptions, function(key, field) {
            if ('undefined' == typeof field || null == field || !field) {
                return;
            }
            if ('input' == field.type) {
                fieldContent.push(inputTemplate(field));
            }

            if ('hidden' == field.type) {
                fieldContent.push(hiddenTemplate(field));
            }

            if ('checkbox' == field.type) {
                fieldContent.push(checkboxTemplate(field));
            }
        });

        $("input", self.fieldsContainer).last().after(fieldContent.join("\r\n"));
    },
    initStripe: function(params)
    {
        var self = this;
        var handler = StripeCheckout.configure($.extend({}, true, params.configure, {
            token: function(token) {
                $("#field-token").val(token.id);
                $("#field-email").val(token.email);
                self.fieldsContainer.submit();
            }
        }));

        handler.open(params.open);

        // Close Checkout on page navigation
        $(window).on('popstate', function() {
            handler.close();
        });
    },
};
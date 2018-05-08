customModule.cartFrontend = {
    fieldsOptions: undefined,
    fieldsContainer: undefined,
    run : function(params) {
        var self = this;

        self.fieldsContainer = $('form');
        self.fieldOptions = params.fieldOptions;

        $(document).on('change', 'input[name="OrderForm[method]"]', function() {
            var method = $(this).val();

            self.updateFields(method);
        });

        $('input[name="OrderForm[method]"]:checked').trigger('change');
    },
    updateFields: function (method) {
        var self = this;

        $('button[type=submit]', self.fieldsContainer).show();
        $('.fields', self.fieldsContainer).remove();
        $('input,select', self.fieldsContainer).prop('disabled', false);

        if ('undefined' == typeof self.fieldOptions
            || 'undefined' == typeof self.fieldOptions[method]
            || !self.fieldOptions[method]) {
            return;
        }

        var fieldContent = [];
        var inputTemplate = templates['cart/input'];
        $.each(self.fieldOptions[method], function(key, field) {
            if ('undefined' == typeof field || null == field || !field) {
                return;
            }
            if ('input' == field.type) {
                fieldContent.push(inputTemplate(field));
            }
        });

        $(".form-group", self.fieldsContainer).last().after(fieldContent.join("\r\n"));
    }
};
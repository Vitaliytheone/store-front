var custom = new function() {
    var self = this;

    self.request = null;

    self.confirm = function (title, text, options, callback, cancelCallback) {
        var confirmPopupHtml;
        var compiled = templates['global/modal/confirm'];
        confirmPopupHtml = compiled($.extend({}, true, {
            confirm_button : 'OK',
            cancel_button : 'Cancel',
            width: '600px'
        }, options, {
            'title': title,
            'confirm_message': text
        }));

        $(window.document.body).append(confirmPopupHtml);
        $('#confirmModal').modal({});

        $('#confirmModal').on('hidden.bs.modal', function (e) {
            $('#confirmModal').remove();

            if ('function' == typeof cancelCallback) {
                return cancelCallback.call();
            }
        });

        return $('#confirm_yes').on('click', function (e) {
            $("#confirm_yes").unbind("click");
            $('#confirmModal').modal('hide');
            return callback.call();
        });
    };

    self.ajax = function(options) {
        var settings = $.extend({}, true, options);
        if ("object" === typeof options) {
            options.beforeSend = function() {
                if ('function' === typeof settings.beforeSend) {
                    settings.beforeSend();
                }
            };
            options.success = function(response) {
                if ('function' === typeof settings.success) {
                    settings.success(response);
                }
            };
            null         != self.request ? self.request.abort() : '';
            self.request = $.ajax(options);
        }
    }

    self.notify = function(notifyData) {
        var notifyContainer = $('body'),
            key, value;
        notifyContainer.addClass('bottom-right');

        if ('object' != typeof notifyData) {
            return false;
        }
        for (key in notifyData) {

            value = $.extend({}, true, {
                type	: 'success',
                delay	: 8000,
                text	: '',
            }, notifyData[key]);

            if ('undefined' == typeof value.text || null == value.text) {
                continue;
            }

            $.notify({
                message	: value.text.toString(),
            }, {
                type: value.type,
                placement: {
                    from: "bottom",
                    align: "right"
                },
                z_index : 2000,
                delay: value.delay,
                animate: {
                    enter: 'animated fadeInDown',
                    exit: 'animated fadeOutUp'
                }
            });
        }
    }

    self.sendBtn = function(btn, settings)
    {
        if ('object' != typeof settings) {
            settings = {};
        }

        if (btn.hasClass('active')) {
            return;
        }

        btn.addClass('has-spinner');

        var options = $.extend({}, true, settings);

        options.url = btn.attr('href');

        $('.spinner', btn).remove();

        btn.prepend('<span class="spinner"><i class="fa fa-spinner fa-spin"></i></span>');

        options.beforeSend = function() {
            btn.addClass('active');
        };

        options.success = function(response) {
            btn.removeClass('active');
            $('.spinner', btn).remove();

            if ('success' == response.status) {
                if ('function' === typeof settings.callback) {
                    settings.callback(response);
                }
            } else if ('error' == response.status) {
                self.notify({0: {
                    type : 'danger',
                    text : response.message
                }});
            }
        };

        self.ajax(options);
    }

    self.sendFrom = function(btn, form, settings)
    {
        if ('object' != typeof settings) {
            settings = {};
        }

        if (btn.hasClass('active')) {
            return;
        }

        btn.addClass('has-spinner');

        var options = $.extend({}, true, settings);
        var errorSummary = $('.error-summary', form);

        options.url = form.attr('action');
        options.type = 'POST';

        $('.spinner', btn).remove();

        btn.prepend('<span class="spinner"><i class="fa fa-spinner fa-spin"></i></span>');

        options.beforeSend = function() {
            btn.addClass('active');

            if (errorSummary.length) {
                errorSummary.addClass('hidden');
                errorSummary.html('');
            }
        };

        options.success = function(response) {
            btn.removeClass('active');
            $('.spinner', btn).remove();

            if ('success' == response.status) {
                if ('function' === typeof settings.callback) {
                    settings.callback(response);
                }
            } else if ('error' == response.status) {
                if (response.message) {

                    if (errorSummary.length) {
                        errorSummary.html(response.message);
                        errorSummary.removeClass('hidden');
                    } else {
                        self.notify({0: {
                            type : 'danger',
                            text : response.message
                        }});
                    }
                }

                if (response.errors) {
                    $.each(response.errors, function(key, val) {
                        alert(val);
                        form.yiiActiveForm('updateAttribute', key, val);
                    });
                }

                if ('function' === typeof settings.errorCallback) {
                    settings.errorCallback(response);
                }
            }
        };

        self.ajax(options);
    };

    /**
     * Generate Url path from string
     * a-z, -_ ,0-9
     * @param string
     */
    self.generateUrlFromString = function(string)
    {
        var url = string.replace(/[^a-z0-9_\-\s]/gmi, "").replace(/\s+/g, '-').toLowerCase();

        if (url === '-' || url === '_') {
            url = '';
        }

        return url;
    };

    /**
     * Generate unique url
     * @param url
     * @param exitingUrls
     * @returns {*}
     */
    self.generateUniqueUrl = function(url, exitingUrls)
    {
        var generatedUrl = url,
            exiting,
            prefixCounter;

        prefixCounter = 1;

        do {
            exiting = _.find(exitingUrls, function(exitingUrl){
                return exitingUrl === generatedUrl;
            });

            if (exiting) {
                generatedUrl = url + '-' + prefixCounter;
                prefixCounter ++;
            }
        }
        while (exiting);

        return generatedUrl;
    };

};
var customModule = {};
window.modules = {};

$(function() {
    if ('object' == typeof window.modules) {
        $.each(window.modules, function(name, options) {
            if ('undefined' != typeof customModule[name]) {
                customModule[name].run(options);
            }
        });
    }
});
                var templates = templates || {};
                

templates['global/modal/confirm'] = _.template("<div class=\"modal fade confirm-modal\" id=\"confirmModal\" tabindex=\"-1\" data-backdrop=\"static\">\n    <div class=\"modal-dialog modal-md\" role=\"document\">\n        <div class=\"modal-content\">\n            <% if (typeof(confirm_message) !== \"undefined\" && confirm_message != \'\') { %>\n            <div class=\"modal-header\">\n                <h3 id=\"conrirm_label\"><%= title %><\/h3>\n                <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\"><span aria-hidden=\"true\">&times;<\/span><\/button>\n            <\/div>\n\n            <div class=\"modal-body\">\n                <p><%= confirm_message %><\/p>\n            <\/div>\n\n\n            <div class=\"modal-footer justify-content-start\">\n                <button class=\"btn btn-primary m-btn--air\" id=\"confirm_yes\"><%= confirm_button %><\/button>\n                <button class=\"btn btn-secondary m-btn--air\" data-dismiss=\"modal\" aria-hidden=\"true\"><%= cancel_button %><\/button>\n            <\/div>\n            <% } else { %>\n            <div class=\"modal-body\">\n                <div class=\"text-center\">\n                    <h3 id=\"conrirm_label\"><%= title %><\/h3>\n                <\/div>\n\n                <div class=\"text-center\">\n                    <button class=\"btn btn-primary m-btn--air\" id=\"confirm_yes\"><%= confirm_button %><\/button>\n                    <button class=\"btn btn-secondary m-btn--air\" data-dismiss=\"modal\" aria-hidden=\"true\"><%= cancel_button %><\/button>\n                <\/div>\n            <\/div>\n            <% } %>\n        <\/div>\n    <\/div>\n<\/div>");
customModule.cartFrontend = {
    fieldsOptions: undefined,
    fieldsContainer: undefined,
    cartTotal: undefined,
    run : function(params) {
        var self = this;

        self.fieldsContainer = $('form');
        self.fieldOptions = params.fieldOptions;
        self.cartTotal = params.cartTotal;

        if ('undefined' != typeof params.options) {
            if ('undefined' != typeof params.options.authorize) {
                self.initAuthorize(params.options.authorize);
            }
            if ('undefined' != typeof params.options.stripe) {
                self.initStripe(params.options.stripe);
            }
            if ('undefined' != typeof params.options.stripe_3d_secure) {
                self.initStripe3dSecure(params.options.stripe_3d_secure);
            }
        }

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
        var hiddenTemplate = templates['cart/hidden'];
        $.each(self.fieldOptions[method], function(key, field) {
            if ('undefined' == typeof field || null == field || !field) {
                return;
            }
            if ('input' == field.type) {
                fieldContent.push(inputTemplate(field));
            }

            if ('hidden' == field.type) {
                fieldContent.push(hiddenTemplate(field));
            }
        });

        $(".form-group", self.fieldsContainer).last().after(fieldContent.join("\r\n"));
    },
    initAuthorize: function(params)
    {
        var self = this;
        var email = $('input[name="OrderForm[email]');
        var configure = params.configure;
        var submitBtn = $('button[type=submit]', self.fieldsContainer);
        var submitMethodBtn = $("<button />", configure).hide();
        submitBtn.after(submitMethodBtn);

        submitBtn.on('click', function (e) {
            if ('' == ($.trim(email.val()))) {
                return;
            }
            if ($('input[name="OrderForm[method]"]:checked').val() == params.type) {
                e.stopImmediatePropagation();

                submitMethodBtn.trigger('click');

                $('body,html').animate({
                    scrollTop: 0
                }, 100);

                return false;
            }
        });
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

        $('button', self.fieldsContainer).on('click', function(e) {
            if (params.type != $('input[name="OrderForm[method]"]:checked').val()) {
                return true;
            }
            var isValid = false;
            $.ajax({
                url: self.fieldsContainer.attr('action') + '/validate',
                data: self.fieldsContainer.serialize(),
                async: false,
                method: "POST",
                success: function(response) {
                    if ('success' == response.status) {
                        isValid = true;
                    }
                }
            });

            if (!isValid) {
               return true;
            }

            // Open Checkout with further options
            var openOptions = $.extend({}, true, params.open);
            openOptions.amount = $('#amount').val() * 100;

            handler.open(openOptions);

            e.preventDefault();
            return false;
        });

        // Close Checkout on page navigation
        $(window).on('popstate', function() {
            handler.close();
        });
    },
    initStripe3dSecure: function(params)
    {
        var self = this;

        if (Boolean(params.configure.key.trim())) {
            var stripe = Stripe(params.configure.key);

            // Create Checkout's handler
            var handler = StripeCheckout.configure($.extend({}, true, params.configure, {
                token: function (token) {

                    // use Checkout's card token to create a card source
                    stripe.createSource({
                        type: 'card',
                        token: token.id
                    }).then(function (result) {
                        if (result.error || !result.source) {
                            console.log('ERROR!', result.error.message);
                            window.location.replace(params.return_url);
                        } else {
                            // Send the source to your server
                            stripeSourceHandler(result.source);
                        }
                    });
                }
            }));
        }

        $('button', self.fieldsContainer).on('click', function(e) {
            if (params.type != $('input[name="OrderForm[method]"]:checked').val()) {
                return true;
            }
            var isValid = false;
            $.ajax({
                url: self.fieldsContainer.attr('action') + '/validate',
                data: self.fieldsContainer.serialize(),
                async: false,
                method: "POST",
                success: function(response) {
                    if ('success' === response.status) {
                        isValid = true;
                    }
                }
            });

            if (!isValid) {
               return true;
            }

            // Open Checkout with further options
            var openOptions = $.extend({}, true, params.open);
            openOptions.amount = self.cartTotal.amount * 100;
            openOptions.currency = self.cartTotal.currency;

            handler.open(openOptions);

            e.preventDefault();
            return false;
        });

        // Close Checkout on page navigation
        $(window).on('popstate', function() {
            handler.close();
        });

        function stripeSourceHandler(source) {
            // check if the card supports 3DS
            if (source.card.three_d_secure === 'not_supported') {
                console.log("This card does not support 3D Secure!");
                window.location.replace(params.return_url);
                return;
            }

            var returnURL = params.return_url + '?method=' + params.type + '&email=' + $('input[name="OrderForm[email]').val();

            // create the 3DS source from the card source
            stripe.createSource({
                type: 'three_d_secure',
                amount: self.cartTotal.amount * 100,
                currency: self.cartTotal.currency,
                three_d_secure: {
                    card: source.id
                },
                redirect: {
                    return_url: returnURL
                }
            }).then(function(result) {
                if (result.error) {
                    console.log('ERROR!', result.error.message);
                    window.location.replace(params.return_url);
                } else {
                    stripe3DSourceHandler(result.source);
                }
            });
        }

        function stripe3DSourceHandler(source) {

            if (source.redirect && source.redirect.failure_reason) {
                console.log('REDIRECT ERROR!', source.redirect.failure_reason);
                window.location.replace(params.return_url);
            }

            // Redirect to 3D secure window
            window.location.replace(source.redirect.url);
        }
    },
    responseAuthorizeHandler: function(response)
    {
        if (response.messages.resultCode === "Error") {
            var i = 0;
            while (i < response.messages.message.length) {
                alert(
                    response.messages.message[i].code + ": " +
                    response.messages.message[i].text
                );
                i = i + 1;
            }
        } else {
            $("#field-data_descriptor").val(response.opaqueData.dataDescriptor);
            $("#field-data_value").val(response.opaqueData.dataValue);
            $('form').submit();
        }
    }

};

var responseAuthorizeHandler = customModule.cartFrontend.responseAuthorizeHandler;
/******************************************************************
 *            Contact form
 ******************************************************************/
$('#contactForm').on('click', '.block-contactus__form-button', function (e) {
    e.preventDefault();
    var form = $('#contactForm');
    var errorBlock = $('#contactFormError', form);
    var actionUrl = '/site/contact-us';
    var csrfParam = $('meta[name="csrf-param"]').attr("content");
    var csrfToken = $('meta[name="csrf-token"]').attr("content");
    var postData = form.serializeArray();
    postData.push({name: csrfParam, value:csrfToken});

    $.ajax({
        url: actionUrl,
        async: false,
        type: "POST",
        dataType: 'json',
        data: postData,
        success: function (data) {
            if (data.error == false) {
                errorBlock.removeClass('alert-danger');
                errorBlock.addClass('alert-success');
                errorBlock.html(data.success);
            } else {
                errorBlock.removeClass('alert-success');
                errorBlock.addClass('alert-danger');
                errorBlock.html(data.error_message);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log('Error on send', textStatus, errorThrown);
        }
    });
});

customModule.orderFormFrontend = {

    run : function(params) {

        var self = this;

        self.currentMethod = null;
        self.packageId = null;

        self.fieldsContainer = null;
        self.modal = null;
        self.fieldOptions = params.fieldOptions;
        self.cartTotal = {};

        self.paymentMethods = params.payment_methods;
        self.orderDataUrl = params.order_data_url;
        self.formActionUrl = params.form_action_url;
        self.formValidateUlr = params.form_validate_ulr;

        self.formValidated = false;

        if (!self.paymentMethods || !self.orderDataUrl || !self.formActionUrl || !self.formValidateUlr) {
            console.log('Bad config!');
            return;
        }

        if ('undefined' != typeof params.options) {
            if ('undefined' != typeof params.options.authorize) {
                self.initAuthorize(params.options.authorize);
            }
            if ('undefined' != typeof params.options.stripe) {
                self.initStripe(params.options.stripe);
            }
            if ('undefined' != typeof params.options.stripe_3d_secure) {
                self.initStripe3dSecure(params.options.stripe_3d_secure);
            }
        }

        $('.buy-package').on('click', function (event) {
            event.preventDefault();
            event.stopPropagation();

            var $this =  $(this);
            self.packageId = $this.data('id');

            if ($(self.modal).length) {
                self.modal.remove();
            }

            if (!self.packageId) {
                throw 'Package id is undefined!';
            }

            self.orderDataUrl = self.orderDataUrl.replace('_id_', self.packageId);

            custom.ajax({
                url: self.orderDataUrl,
                type: 'GET',
                success: function(data, textStatus, jqXHR) {
                    if (!data.success || !data.data) {
                        console.log('Bad response data!', data, textStatus, jqXHR);
                        return;
                    }
                    self.initModal(data.data);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log('Bad response data!', jqXHR, textStatus, errorThrown);
                }
            });
        });

    },
    initModal: function(data) {
        var self = this;

        self.cartTotal.amount = data.price_raw;
        self.cartTotal.currency = data.currency;

        $('body').append(templates['order/order_modal']({
            'package_id': data.id,
            'package_name': data.name,
            'package_price': data.price,
            'payment_methods': self.paymentMethods,
            'form_action_url': self.formActionUrl
        }));

        self.modal = $('#order-package-modal');
        self.fieldsContainer = $('form', '#order-package-modal');

        _.defer(function(){
            hideValidationError();
            $('#order-package-modal').modal('show');
        });

        $(document).on('click', '#proceed_checkout', function (event) {
            hideValidationError();
            custom.ajax({
                url: self.formValidateUlr,
                type: 'POST',
                data: self.fieldsContainer.serialize(),
                success: function(data, textStatus, jqXHR) {
                    if (!data.success || !data.data) {
                        console.log('Bad response data!', data, textStatus, jqXHR);
                        return;
                    }
                    if (
                       self.currentMethod != '19' && // authorize
                       self.currentMethod != '21' && // stripe
                       self.currentMethod != '26'    // stripe_3d_secure
                    ) {
                        self.fieldsContainer.submit();
                    } else {
                        self.fieldsContainer.trigger('validated');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    if (_.has(jqXHR,'responseJSON') && _.has(jqXHR.responseJSON, 'error_message')) {
                        showValidationError(jqXHR.responseJSON.error_message);
                        return;
                    }
                    console.log('Bad error data!', data, textStatus, jqXHR);
                }
            });
        });

        $(document).on('change', 'input[name="OrderForm[method]"]', function() {
            var method = $(this).val();
            self.currentMethod = method;
            self.updateFields(method);
        });

        $('input[name="OrderForm[method]"]:checked').trigger('change');

        function hideValidationError() {
            self.modal.find('.sommerce-modals__alert').html('').css('display', 'none');
        }

        function showValidationError(errorMessage) {
            self.modal.find('.sommerce-modals__alert').html(errorMessage).css('display', 'block');
        }
    },
    updateFields: function (method) {
        var self = this;

        $('button[type=submit]', self.fieldsContainer).show();
        $('.fields', self.fieldsContainer).remove();
        $('input,select', self.fieldsContainer).prop('disabled', false);

        if ('undefined' == typeof self.fieldOptions
            || 'undefined' == typeof self.fieldOptions[method]
            || !self.fieldOptions[method]
        ) {
            return;
        }

        var fieldContent = [];
        var inputTemplate = templates['order/input'];
        var hiddenTemplate = templates['order/hidden'];
        $.each(self.fieldOptions[method], function(key, field) {
            if ('undefined' == typeof field || null == field || !field) {
                return;
            }
            if ('input' == field.type) {
                fieldContent.push(inputTemplate(field));
            }

            if ('hidden' == field.type) {
                fieldContent.push(hiddenTemplate(field));
            }
        });

        $(self.fieldsContainer).prepend(fieldContent.join("\r\n"));
    },
    initAuthorize: function(params)
    {
        var self = this;
        var email = $('input[name="OrderForm[email]');
        var configure = params.configure;
        var submitBtn = $('button[type=submit]', self.fieldsContainer);
        var submitMethodBtn = $("<button />", configure).hide();
        submitBtn.after(submitMethodBtn);

        $(document).on('validated', self.fieldsContainer, function(e) {
            if ('' == ($.trim(email.val()))) {
                return;
            }
            if ($('input[name="OrderForm[method]"]:checked').val() == params.type) {
                e.stopImmediatePropagation();

                submitMethodBtn.trigger('click');

                $('body,html').animate({
                    scrollTop: 0
                }, 100);

                return false;
            }
        });
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

        $(document).on('validated', self.fieldsContainer, function(e) {

            if (params.type != $('input[name="OrderForm[method]"]:checked').val()) {
                return true;
            }

            // Open Checkout with further options
            var openOptions = $.extend({}, true, params.open);
            openOptions.amount = $('#amount').val() * 100;

            handler.open(openOptions);

            e.preventDefault();
            return false;
        });

        // Close Checkout on page navigation
        $(window).on('popstate', function() {
            handler.close();
        });
    },
    initStripe3dSecure: function(params)
    {
        var self = this;

        if (Boolean(params.configure.key.trim())) {
            var stripe = Stripe(params.configure.key);

            // Create Checkout's handler
            var handler = StripeCheckout.configure($.extend({}, true, params.configure, {
                token: function (token) {

                    // use Checkout's card token to create a card source
                    stripe.createSource({
                        type: 'card',
                        token: token.id
                    }).then(function (result) {
                        if (result.error || !result.source) {
                            console.log('ERROR!', result.error.message);
                            window.location.replace(params.return_url);
                        } else {
                            // Send the source to your server
                            stripeSourceHandler(result.source);
                        }
                    });
                }
            }));
        }

        $(document).on('validated', self.fieldsContainer, function(e) {

            if (params.type != $('input[name="OrderForm[method]"]:checked').val()) {
                return true;
            }

            // Open Checkout with further options
            var openOptions = $.extend({}, true, params.open);
            openOptions.amount = self.cartTotal.amount * 100;
            openOptions.currency = self.cartTotal.currency;

            handler.open(openOptions);

            e.preventDefault();
            return false;
        });

        // Close Checkout on page navigation
        $(window).on('popstate', function() {
            handler.close();
        });

        function stripeSourceHandler(source) {
            // check if the card supports 3DS
            if (source.card.three_d_secure === 'not_supported') {
                console.log("This card does not support 3D Secure!");
                window.location.replace(params.return_url);
                return;
            }

            var returnURL = params.return_url + '?' + $.param({
                    "method": params.type,
                    "email": $('input[name="OrderForm[email]').val(),
                    "package_id": self.packageId,
                    "link": $('input[name="OrderForm[link]').val()
            });

            console.log(self.fieldsContainer.serialize());
            console.log(returnURL);

            //    '?method=' + params.type + '&email=' + $('input[name="OrderForm[email]').val();

            // create the 3DS source from the card source
            stripe.createSource({
                type: 'three_d_secure',
                amount: self.cartTotal.amount * 100,
                currency: self.cartTotal.currency,
                three_d_secure: {
                    card: source.id
                },
                redirect: {
                    return_url: returnURL
                }
            }).then(function(result) {
                if (result.error) {
                    console.log('ERROR!', result.error.message);
                    window.location.replace(params.return_url);
                } else {
                    stripe3DSourceHandler(result.source);
                }
            });
        }

        function stripe3DSourceHandler(source) {

            if (source.redirect && source.redirect.failure_reason) {
                console.log('REDIRECT ERROR!', source.redirect.failure_reason);
                window.location.replace(params.return_url);
            }

            // Redirect to 3D secure window
            window.location.replace(source.redirect.url);
        }
    },
    responseAuthorizeHandler: function(response)
    {
        if (response.messages.resultCode === "Error") {
            var i = 0;
            while (i < response.messages.message.length) {
                alert(
                    response.messages.message[i].code + ": " +
                    response.messages.message[i].text
                );
                i = i + 1;
            }
        } else {
            $("#field-data_descriptor").val(response.opaqueData.dataDescriptor);
            $("#field-data_value").val(response.opaqueData.dataValue);
            $('form').submit();
        }
    }
};
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
                var templates = templates || {};
                

templates['order/hidden'] = _.template("<input class=\"fields\" name=\"OrderForm[fields][<%= name %>]\" value=\"<%= value %>\" type=\"hidden\" id=\"field-<%= name %>\"/>");

templates['order/input'] = _.template("<div class=\"form-group fields\" id=\"order_<%= name %>\">\n    <label class=\"control-label\" for=\"orderform-<%= name %>\"><%= label %><\/label>\n    <input class=\"form-control\" name=\"OrderForm[fields][<%= name %>]\" value=\"<%= value %>\" type=\"text\" id=\"field-<%= name %>\">\n<\/div>");

templates['order/order_modal'] = _.template("<div class=\"modal fade\" id=\"order-package-modal\" tabindex=\"-1\" role=\"dialog\">\n    <div class=\"modal-dialog sommerce-modals__dialog\" role=\"document\">\n        <div class=\"modal-content sommerce-modals__content\">\n            <div class=\"modal-body sommerce-modals__body\">\n                <div class=\"sommerce-modals__header\">\n                    <div class=\"sommerce-modals__header-title\">Order details<\/div>\n                    <div class=\"sommerce-modals__header-close\" data-dismiss=\"modal\" aria-label=\"Close\">\n                        <span class=\"sommerce-modals__order-icons-close\"><\/span>\n                    <\/div>\n                <\/div>\n                <div class=\"sommerce-modals__alert sommerce-modals__alert-danger\">\n                    <span>Please enter your link in format <strong>https://instagram.com/nickname<\/strong><\/span>\n                <\/div>\n                <div class=\"sommerce-modals__order-details\">\n                    <table class=\"sommerce-modals__order-table\">\n                        <tbody>\n                        <tr>\n                            <td class=\"sommerce-modals__order-name\">Package:<\/td>\n                            <td class=\"sommerce-modals__order-value\"><%= package_name %><\/td>\n                        <\/tr>\n                        <tr>\n                            <td class=\"sommerce-modals__order-name\">Price:<\/td>\n                            <td class=\"sommerce-modals__order-value\"><%= package_price %><\/td>\n                        <\/tr>\n                        <\/tbody>\n                    <\/table>\n                <\/div>\n                <form action=\"<%= form_action_url %>\" method=\"post\">\n                    <input type=\"hidden\" name=\"OrderForm[package_id]\" value=\"<%= package_id %>\">\n                    <div class=\"sommerce-modals__forms\">\n                        <div class=\"form-group sommerce-modals__form-group\">\n                            <label for=\"link\">Link<\/label>\n                            <input type=\"text\" class=\"form-control\" id=\"link\" name=\"OrderForm[link]\">\n                        <\/div>\n                        <div class=\"form-group sommerce-modals__form-group\">\n                            <label for=\"email\">Email<\/label>\n                            <input type=\"email\" class=\"form-control\" id=\"email\" name=\"OrderForm[email]\">\n                            <small class=\"form-text text-muted\">You will be notified on this email address<\/small>\n                        <\/div>\n                    <\/div>\n                    <% if (payment_methods) { %>\n                        <% if (payment_methods.length > 1) { %>\n                        <div class=\"sommerce-modals__payments\">\n                            <div class=\"form-group\">\n                                <div class=\"sommerce-modals__payments-title\">Payment methods<\/div>\n                                <% _.each(payment_methods, function(method) { %>\n                                <div class=\"form-check form-check-inline\">\n                                    <input class=\"form-check-input\" type=\"radio\" name=\"OrderForm[method]\"\n                                           id=\"method-<%= method.id %>\"\n                                           <% if (method.id === payment_methods[0].id) { %> checked <% } %>\n                                           value=\"<%= method.id %>\">\n                                    <label class=\"form-check-label\" for=\"method-<%= method.id %>\"><%= method.name %><\/label>\n                                <\/div>\n                                <% }); %>\n                            <\/div>\n                        <\/div>\n                        <% } else { %>\n                        <input type=\"hidden\" name=\"OrderForm[method]\" value=\"<%= payment_methods[0].id %>\">\n                        <% }; %>\n                    <% }; %>\n                    <div class=\"sommerce-modals__actions\">\n                        <button type=\"button\" class=\"btn btn-block sommerce-modals__btn-primary\" id=\"proceed_checkout\">Proceed to Checkout<\/button>\n                    <\/div>\n                <\/form>\n            <\/div>\n        <\/div>\n    <\/div>\n<\/div>");

templates['payments_modal/awaiting'] = _.template("<div class=\"modal fade\" id=\"modal-payment-awaiting\" tabindex=\"-1\" role=\"dialog\" data-backdrop=\"static\">\n    <div class=\"modal-dialog sommerce-modals__dialog\" role=\"document\">\n        <div class=\"modal-content sommerce-modals__content\">\n            <div class=\"modal-body sommerce-modals__body\">\n                <div class=\"sommerce-modals__header\">\n                    <div class=\"sommerce-modals__header-title\"><\/div>\n                    <div class=\"sommerce-modals__header-close\" data-dismiss=\"modal\" aria-label=\"Close\">\n                        <span class=\"sommerce-modals__order-icons-close\"><\/span>\n                    <\/div>\n                <\/div>\n\n                <div class=\"sommerce-modals__order-header\">\n                    <div class=\"sommerce-modals__order-header-icon\">\n                        <div class=\"sommerce-modals__order-icons-awaiting\"><\/div>\n                    <\/div>\n                    <div class=\"sommerce-modals__order-header-title\">\n                        Payment awaiting\n                    <\/div>\n                    <div class=\"sommerce-modals__order-header-description\">\n                        Your payment is being verified. You will be informed on your email.\n                    <\/div>\n                <\/div>\n                <div class=\"sommerce-modals__actions text-center\">\n                    <button class=\"btn sommerce-modals__btn-default sommerce-modals__actions-btn-center\" data-dismiss=\"modal\">Ok, got it!<\/button>\n                <\/div>\n            <\/div>\n        <\/div>\n    <\/div>\n<\/div>");

templates['payments_modal/failed'] = _.template("<div class=\"modal fade\" id=\"modal-payment-failed\" tabindex=\"-1\" role=\"dialog\" data-backdrop=\"static\">\n    <div class=\"modal-dialog sommerce-modals__dialog\" role=\"document\">\n        <div class=\"modal-content sommerce-modals__content\">\n            <div class=\"modal-body sommerce-modals__body\">\n                <div class=\"sommerce-modals__header\">\n                    <div class=\"sommerce-modals__header-title\"><\/div>\n                    <div class=\"sommerce-modals__header-close\" data-dismiss=\"modal\" aria-label=\"Close\">\n                        <span class=\"sommerce-modals__order-icons-close\"><\/span>\n                    <\/div>\n                <\/div>\n\n                <div class=\"sommerce-modals__order-header\">\n                    <div class=\"sommerce-modals__order-header-icon\">\n                        <div class=\"sommerce-modals__order-icons-failed\"><\/div>\n                    <\/div>\n                    <div class=\"sommerce-modals__order-header-title\">\n                        Payment failed\n                    <\/div>\n                    <div class=\"sommerce-modals__order-header-description\">\n                        Your payment was failed by some reasons. Try to do it again.\n                    <\/div>\n                <\/div>\n                <div class=\"sommerce-modals__actions text-center\">\n                    <button class=\"btn sommerce-modals__btn-default sommerce-modals__actions-btn-center\" data-dismiss=\"modal\">Ok, got it!<\/button>\n                <\/div>\n            <\/div>\n        <\/div>\n    <\/div>\n<\/div>");

templates['payments_modal/success'] = _.template("<div class=\"modal fade\" id=\"modal-payment-success\" tabindex=\"-1\" role=\"dialog\" data-backdrop=\"static\">\n    <div class=\"modal-dialog modal-lg\" role=\"document\">\n        <div class=\"modal-content sommerce-modals__content\">\n            <div class=\"modal-body sommerce-modals__body\">\n                <div class=\"sommerce-modals__header\">\n                    <div class=\"sommerce-modals__header-title\"><\/div>\n                    <div class=\"sommerce-modals__header-close\" data-dismiss=\"modal\" aria-label=\"Close\">\n                        <span class=\"sommerce-modals__order-icons-close\"><\/span>\n                    <\/div>\n                <\/div>\n\n                <div class=\"sommerce-modals__order-header\">\n                    <div class=\"sommerce-modals__order-header-icon\">\n                        <div class=\"sommerce-modals__order-icons-success\"><\/div>\n                    <\/div>\n                    <div class=\"sommerce-modals__order-header-title\">\n                        Successfull payment\n                    <\/div>\n                    <div class=\"sommerce-modals__order-header-description\">\n                        Thank you! Your payment was successful. Here is your order details.\n                    <\/div>\n                <\/div>\n\n                <div class=\"sommerce-modals__order-result\">\n                    <table class=\"sommerce-modals__order-result-table\">\n                        <thead>\n                        <tr>\n                            <th>Order ID<\/th>\n                            <th>Package<\/th>\n                            <th>Details<\/th>\n                            <th>Price<\/th>\n                            <th>Status<\/th>\n                        <\/tr>\n                        <\/thead>\n                        <tbody>\n                        <tr>\n                            <td data-label=\"Order ID\"><%= order_id %><\/td>\n                            <td data-label=\"Package\"><%= package %><\/td>\n                            <td data-label=\"Details\"><%= details %><\/td>\n                            <td data-label=\"Price\" nowrap=\"\"><%= price %><\/td>\n                            <td data-label=\"Status\">\n                                <span class=\"sommerce-status-text\"><%= status %><\/span>\n                            <\/td>\n                        <\/tr>\n                        <\/tbody>\n                    <\/table>\n                <\/div>\n                <div class=\"sommerce-modals__actions text-center\">\n                    <button class=\"btn sommerce-modals__btn-default sommerce-modals__actions-btn-center\" data-dismiss=\"modal\">Continue<\/button>\n                <\/div>\n            <\/div>\n        <\/div>\n    <\/div>\n<\/div>");
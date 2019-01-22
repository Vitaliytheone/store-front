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

        var stripe = Stripe(params.configure.key);

        // Create Checkout's handler
        var handler = StripeCheckout.configure($.extend({}, true, params.configure, {
            token: function(token) {

                // use Checkout's card token to create a card source
                stripe.createSource({
                    type: 'card',
                    token: token.id
                }).then(function(result){
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
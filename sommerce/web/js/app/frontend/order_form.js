customModule.orderFormFrontend = {

    run : function(params) {

        var self = this;

        self.fieldsContainer = null;
        self.modal = null;
        self.fieldOptions = params.fieldOptions;
        self.cartTotal = {};

        self.paymentMethods = params.payment_methods;
        self.orderDataUrl = params.order_data_url;
        self.formActionUrl = params.form_action_url;
        self.formValidateUlr = params.form_validate_ulr;

        self.packageId = null;

        if (!self.paymentMethods || !self.orderDataUrl || !self.formActionUrl || !self.formValidateUlr) {
            console.log('Bad config!');
            return;
        }

        self.currentMethod = self.paymentMethods[0].id;

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
                       self.currentMethod != '11' && // authorize
                       self.currentMethod != '13' && // stripe
                       self.currentMethod != '17'    // stripe_3d_secure
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

        if (self.paymentMethods.length > 1) {
            $('input[name="OrderForm[method]"]:checked').trigger('change');
        } else {
            $('input:hidden[name="OrderForm[method]"]').trigger('change');
        }

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
        var submitBtn = $('#proceed_checkout');
        var submitMethodBtn = $("<button />", configure);
        submitBtn.after(submitMethodBtn);

        $(document).on('click', submitBtn, function (e) {
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

            if (params.type != self.currentMethod) {
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

            if (params.type != self.currentMethod) {
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
                    "package_id": self.packageId,
                    "email": $('input[name="OrderForm[email]').val(),
                    "link": $('input[name="OrderForm[link]').val()
            });

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
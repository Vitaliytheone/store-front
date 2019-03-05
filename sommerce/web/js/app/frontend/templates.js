                var templates = {};
                

templates['order/order_modal'] = _.template("<div class=\"modal fade\" id=\"order-package-modal\" tabindex=\"-1\" role=\"dialog\">\n    <div class=\"modal-dialog sommerce-modals__dialog\" role=\"document\">\n        <div class=\"modal-content sommerce-modals__content\">\n            <div class=\"modal-body sommerce-modals__body\">\n                <div class=\"sommerce-modals__header\">\n                    <div class=\"sommerce-modals__header-title\">Order details<\/div>\n                    <div class=\"sommerce-modals__header-close\" data-dismiss=\"modal\" aria-label=\"Close\">\n                        <span class=\"sommerce-modals__order-icons-close\"><\/span>\n                    <\/div>\n                <\/div>\n                <div class=\"sommerce-modals__alert sommerce-modals__alert-danger\">\n                    <span>Please enter your link in format <strong>https://instagram.com/nickname<\/strong><\/span>\n                <\/div>\n                <div class=\"sommerce-modals__order-details\">\n                    <table class=\"sommerce-modals__order-table\">\n                        <tbody>\n                        <tr>\n                            <td class=\"sommerce-modals__order-name\">Package:<\/td>\n                            <td class=\"sommerce-modals__order-value\">200 Instagram followers<\/td>\n                        <\/tr>\n                        <tr>\n                            <td class=\"sommerce-modals__order-name\">Price:<\/td>\n                            <td class=\"sommerce-modals__order-value\">$ 2.00<\/td>\n                        <\/tr>\n                        <\/tbody>\n                    <\/table>\n                <\/div>\n                <div class=\"sommerce-modals__forms\">\n                    <div class=\"form-group sommerce-modals__form-group sommerce-modals__form-group-error\">\n                        <label for=\"link\">Link<\/label>\n                        <input type=\"text\" class=\"form-control\" id=\"link\">\n                    <\/div>\n                    <div class=\"form-group sommerce-modals__form-group\">\n                        <label for=\"email\">Email<\/label>\n                        <input type=\"email\" class=\"form-control\" id=\"email\">\n                        <small class=\"form-text text-muted\">You will be notified on this email address<\/small>\n                    <\/div>\n                <\/div>\n                <div class=\"sommerce-modals__payments\">\n                    <div class=\"form-group\">\n                        <div class=\"sommerce-modals__payments-title\">Payment methods<\/div>\n                        <div class=\"form-check form-check-inline\">\n                            <input class=\"form-check-input\" type=\"radio\" name=\"payment_m\" id=\"inlineRadio1\" value=\"option1\">\n                            <label class=\"form-check-label\" for=\"inlineRadio1\">PayPal<\/label>\n                        <\/div>\n                        <div class=\"form-check form-check-inline\">\n                            <input class=\"form-check-input\" type=\"radio\" name=\"payment_m\" id=\"inlineRadio2\" value=\"option2\">\n                            <label class=\"form-check-label\" for=\"inlineRadio2\">2CheckOut<\/label>\n                        <\/div>\n                    <\/div>\n                <\/div>\n                <div class=\"sommerce-modals__actions\">\n                    <button class=\"btn btn-block sommerce-modals__btn-primary\">Proceed to Checkout<\/button>\n                <\/div>\n            <\/div>\n        <\/div>\n    <\/div>\n<\/div>");
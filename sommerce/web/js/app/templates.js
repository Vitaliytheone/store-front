                var templates = {};
                

templates['cart/hidden'] = _.template("<input class=\"fields\" name=\"OrderForm[fields][<%= name %>]\" value=\"<%= value %>\" type=\"hidden\" id=\"field-<%= name %>\"/>");

templates['cart/input'] = _.template("<div class=\"form-group fields\" id=\"order_<%= name %>\">\n    <label class=\"control-label\" for=\"orderform-<%= name %>\"><%= label %><\/label>\n    <input class=\"form-control\" name=\"OrderForm[fields][<%= name %>]\" value=\"<%= value %>\" type=\"text\" id=\"field-<%= name %>\">\n<\/div>");

templates['modal/confirm'] = _.template("<div class=\"modal fade confirm-modal\" id=\"confirmModal\" tabindex=\"-1\" data-backdrop=\"static\">\n    <div class=\"modal-dialog modal-md\" role=\"document\">\n        <div class=\"modal-content\">\n            <% if (typeof(confirm_message) !== \"undefined\" && confirm_message != \'\') { %>\n            <div class=\"modal-header\">\n                <h3 id=\"conrirm_label\"><%= title %><\/h3>\n                <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\"><span aria-hidden=\"true\">&times;<\/span><\/button>\n            <\/div>\n\n            <div class=\"modal-body\">\n                <p><%= confirm_message %><\/p>\n            <\/div>\n\n\n            <div class=\"modal-footer justify-content-start\">\n                <button class=\"btn btn-primary m-btn--air\" id=\"confirm_yes\"><%= confirm_button %><\/button>\n                <button class=\"btn btn-secondary m-btn--air\" data-dismiss=\"modal\" aria-hidden=\"true\"><%= cancel_button %><\/button>\n            <\/div>\n            <% } else { %>\n            <div class=\"modal-body\">\n                <div class=\"text-center\">\n                    <h3 id=\"conrirm_label\"><%= title %><\/h3>\n                <\/div>\n\n                <div class=\"text-center\">\n                    <button class=\"btn btn-primary m-btn--air\" id=\"confirm_yes\"><%= confirm_button %><\/button>\n                    <button class=\"btn btn-secondary m-btn--air\" data-dismiss=\"modal\" aria-hidden=\"true\"><%= cancel_button %><\/button>\n                <\/div>\n            <\/div>\n            <% } %>\n        <\/div>\n    <\/div>\n<\/div>");
                var templates = {};
                

templates['cart/hidden'] = _.template("<input class=\"fields\" name=\"OrderForm[fields][<%= name %>]\" value=\"<%= value %>\" type=\"hidden\" id=\"field-<%= name %>\"/>");

templates['cart/input'] = _.template("<div class=\"form-group fields\" id=\"order_<%= name %>\">\n    <label class=\"control-label\" for=\"orderform-<%= name %>\"><%= label %><\/label>\n    <input class=\"form-control\" name=\"OrderForm[fields][<%= name %>]\" value=\"<%= value %>\" type=\"text\" id=\"field-<%= name %>\">\n<\/div>");
                var templates = templates || {};
                

templates['payments/description'] = _.template("<div class=\"form-group form-group-description\">\n    <span class=\"fa fa-times remove-description\"><\/span>\n    <label for=\"<%= elementId %>\" class=\"control-label\"><%= label %><\/label>\n    <input type=\"text\" class=\"form-control <%= elementClass %>\" name=\"<%= elementName %>\" id=\"<%= elementId %>\" value=\"\">\n<\/div>");
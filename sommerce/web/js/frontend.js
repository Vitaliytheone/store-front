var custom=new function(){var e=this;e.request=null,e.confirm=function(e,t,n,a,i){var o;return o=(0,templates["modal/confirm"])($.extend({},!0,{confirm_button:"OK",cancel_button:"Cancel",width:"600px"},n,{title:e,confirm_message:t})),$(window.document.body).append(o),$("#confirmModal").modal({}),$("#confirmModal").on("hidden.bs.modal",function(e){if($("#confirmModal").remove(),"function"==typeof i)return i.call()}),$("#confirm_yes").on("click",function(e){return $("#confirm_yes").unbind("click"),$("#confirmModal").modal("hide"),a.call()})},e.ajax=function(t){var n=$.extend({},!0,t);"object"==typeof t&&(t.beforeSend=function(){"function"==typeof n.beforeSend&&n.beforeSend()},t.success=function(e){"function"==typeof n.success&&n.success(e)},null!=e.request&&e.request.abort(),e.request=$.ajax(t))},e.notify=function(e){var t,n;if($("body").addClass("bottom-right"),"object"!=typeof e)return!1;for(t in e)void 0!==(n=$.extend({},!0,{type:"success",delay:8e3,text:""},e[t])).text&&null!=n.text&&$.notify({message:n.text.toString()},{type:n.type,placement:{from:"bottom",align:"right"},z_index:2e3,delay:n.delay,animate:{enter:"animated fadeInDown",exit:"animated fadeOutUp"}})},e.sendBtn=function(t,n){if("object"!=typeof n&&(n={}),!t.hasClass("active")){t.addClass("has-spinner");var a=$.extend({},!0,n);a.url=t.attr("href"),$(".spinner",t).remove(),t.prepend('<span class="spinner"><i class="fa fa-spinner fa-spin"></i></span>'),a.beforeSend=function(){t.addClass("active")},a.success=function(a){t.removeClass("active"),$(".spinner",t).remove(),"success"==a.status?"function"==typeof n.callback&&n.callback(a):"error"==a.status&&e.notify({0:{type:"danger",text:a.message}})},e.ajax(a)}},e.sendFrom=function(t,n,a){if("object"!=typeof a&&(a={}),!t.hasClass("active")){t.addClass("has-spinner");var i=$.extend({},!0,a),o=$(".error-summary",n);i.url=n.attr("action"),i.type="POST",$(".spinner",t).remove(),t.prepend('<span class="spinner"><i class="fa fa-spinner fa-spin"></i></span>'),i.beforeSend=function(){t.addClass("active"),o.length&&(o.addClass("hidden"),o.html(""))},i.success=function(i){t.removeClass("active"),$(".spinner",t).remove(),"success"==i.status?"function"==typeof a.callback&&a.callback(i):"error"==i.status&&(i.message&&(o.length?(o.html(i.message),o.removeClass("hidden")):e.notify({0:{type:"danger",text:i.message}})),i.errors&&$.each(i.errors,function(e,t){alert(t),n.yiiActiveForm("updateAttribute",e,t)}),"function"==typeof a.errorCallback&&a.errorCallback(i))},e.ajax(i)}},e.generateUrlFromString=function(e){var t=e.replace(/[^a-z0-9_\-\s]/gim,"").replace(/\s+/g,"-").toLowerCase();return"-"!==t&&"_"!==t||(t=""),t},e.generateUniqueUrl=function(e,t){var n,a,i=e;a=1;do{(n=_.find(t,function(e){return e===i}))&&(i=e+"-"+a,a++)}while(n);return i}},customModule={};window.modules={},$(function(){"object"==typeof window.modules&&$.each(window.modules,function(e,t){void 0!==customModule[e]&&customModule[e].run(t)})});var templates={};templates["cart/hidden"]=_.template('<input class="fields" name="OrderForm[fields][<%= name %>]" value="<%= value %>" type="hidden" id="field-<%= name %>"/>'),templates["cart/input"]=_.template('<div class="form-group fields" id="order_<%= name %>">\n    <label class="control-label" for="orderform-<%= name %>"><%= label %></label>\n    <input class="form-control" name="OrderForm[fields][<%= name %>]" value="<%= value %>" type="text" id="field-<%= name %>">\n</div>'),templates["modal/confirm"]=_.template('<div class="modal fade confirm-modal" id="confirmModal" tabindex="-1" data-backdrop="static">\n    <div class="modal-dialog modal-md" role="document">\n        <div class="modal-content">\n            <% if (typeof(confirm_message) !== "undefined" && confirm_message != \'\') { %>\n            <div class="modal-header">\n                <h3 id="conrirm_label"><%= title %></h3>\n                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span aria-hidden="true">&times;</span></button>\n            </div>\n\n            <div class="modal-body">\n                <p><%= confirm_message %></p>\n            </div>\n\n\n            <div class="modal-footer justify-content-start">\n                <button class="btn btn-primary m-btn--air" id="confirm_yes"><%= confirm_button %></button>\n                <button class="btn btn-secondary m-btn--air" data-dismiss="modal" aria-hidden="true"><%= cancel_button %></button>\n            </div>\n            <% } else { %>\n            <div class="modal-body">\n                <div class="text-center">\n                    <h3 id="conrirm_label"><%= title %></h3>\n                </div>\n\n                <div class="text-center">\n                    <button class="btn btn-primary m-btn--air" id="confirm_yes"><%= confirm_button %></button>\n                    <button class="btn btn-secondary m-btn--air" data-dismiss="modal" aria-hidden="true"><%= cancel_button %></button>\n                </div>\n            </div>\n            <% } %>\n        </div>\n    </div>\n</div>'),customModule.cartFrontend={fieldsOptions:void 0,fieldsContainer:void 0,run:function(e){var t=this;t.fieldsContainer=$("form"),t.fieldOptions=e.fieldOptions,void 0!==e.options&&void 0!==e.options.authorize&&t.initAuthorize(e.options.authorize),$(document).on("change",'input[name="OrderForm[method]"]',function(){var e=$(this).val();t.updateFields(e)}),$('input[name="OrderForm[method]"]:checked').trigger("change")},updateFields:function(e){var t=this;if($("button[type=submit]",t.fieldsContainer).show(),$(".fields",t.fieldsContainer).remove(),$("input,select",t.fieldsContainer).prop("disabled",!1),void 0!==t.fieldOptions&&void 0!==t.fieldOptions[e]&&t.fieldOptions[e]){var n=[],a=templates["cart/input"],i=templates["cart/hidden"];$.each(t.fieldOptions[e],function(e,t){void 0!==t&&null!=t&&t&&("input"==t.type&&n.push(a(t)),"hidden"==t.type&&n.push(i(t)))}),$(".form-group",t.fieldsContainer).last().after(n.join("\r\n"))}},initAuthorize:function(e){var t=this,n=$('input[name="OrderForm[email]'),a=e.configure,i=$("button[type=submit]",t.fieldsContainer),o=$("<button />",a).hide();i.after(o),i.on("click",function(t){if(""!=$.trim(n.val()))return $('input[name="OrderForm[method]"]:checked').val()==e.type?(t.stopImmediatePropagation(),o.trigger("click"),$("body,html").animate({scrollTop:0},100),!1):void 0})},responseAuthorizeHandler:function(e){if("Error"===e.messages.resultCode)for(var t=0;t<e.messages.message.length;)alert(e.messages.message[t].code+": "+e.messages.message[t].text),t+=1;else $("#field-data_descriptor").val(e.opaqueData.dataDescriptor),$("#field-data_value").val(e.opaqueData.dataValue),$("form").submit()}};var responseAuthorizeHandler=customModule.cartFrontend.responseAuthorizeHandler;
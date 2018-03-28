var custom=new function(){var e=this;e.request=null,e.confirm=function(e,t,a,r){var o;return o=(0,templates["modal/confirm"])($.extend({},!0,{confirm_button:"OK",cancel_button:"Cancel",width:"600px"},r,{title:e,confirm_message:t})),$(window.document.body).append(o),$("#confirmModal").modal({}),$("#confirmModal").on("hidden.bs.modal",function(e){$("#confirmModal").remove()}),$("#confirm_yes").on("click",function(e){return $("#confirm_yes").unbind("click"),$("#confirmModal").modal("hide"),a.call()})},e.ajax=function(t){var a=$.extend({},!0,t);"object"==typeof t&&(t.beforeSend=function(){"function"==typeof a.beforeSend&&a.beforeSend()},t.success=function(e){"function"==typeof a.success&&a.success(e)},null!=e.request&&e.request.abort(),e.request=$.ajax(t))},e.sendBtn=function(t,a){if("object"!=typeof a&&(a={}),!t.hasClass("active")){t.addClass("has-spinner");var r=$.extend({},!0,a);r.url=t.attr("href"),$(".spinner",t).remove(),t.prepend('<span class="spinner"><i class="fa fa-spinner fa-spin"></i></span>'),r.beforeSend=function(){t.addClass("active")},r.success=function(r){t.removeClass("active"),$(".spinner",t).remove(),"success"==r.status?"function"==typeof a.callback&&a.callback(r):"error"==r.status&&e.notify({0:{type:"danger",text:r.message}})},e.ajax(r)}},e.sendFrom=function(t,a,r){if("object"!=typeof r&&(r={}),!t.hasClass("active")){t.addClass("has-spinner");var o={},n=$(".error-summary",a);o.url=a.attr("action"),o.type="POST",o=$.extend({},!0,o,r),$(".spinner",t).remove(),t.prepend('<span class="spinner"><i class="fa fa-spinner fa-spin"></i></span>'),o.beforeSend=function(){t.addClass("active"),n.length&&(n.addClass("hidden"),n.html(""))},o.success=function(o){t.removeClass("active"),$(".spinner",t).remove(),"success"==o.status?"function"==typeof r.callback&&r.callback(o):"error"==o.status&&(o.message&&(n.length?(n.html(o.message),n.removeClass("hidden")):e.notify({0:{type:"danger",text:o.message}})),o.errors&&$.each(o.errors,function(e,t){a.yiiActiveForm("updateAttribute",e,t)}),"function"==typeof r.errorCallback&&r.errorCallback(o))},e.ajax(o)}},e.generatePassword=function(e,t){void 0===e&&(e=8),void 0===t&&(t="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789");var a,r="",o=t.length;for(a=0;a<e;++a)r+=t.charAt(Math.floor(Math.random()*o));return r}},customModule={};window.modules={},$(function(){"object"==typeof window.modules&&$.each(window.modules,function(e,t){void 0!==customModule[e]&&customModule[e].run(t)})});var templates={};templates["modal/confirm"]=_.template('<div class="modal fade confirm-modal" id="confirmModal" tabindex="-1" data-backdrop="static">\n    <div class="modal-dialog" role="document">\n        <div class="modal-content">\n            <% if (typeof(confirm_message) !== "undefined" && confirm_message != \'\') { %>\n            <div class="modal-header">\n                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>\n                <h3 id="conrirm_label"><%= title %></h3>\n            </div>\n\n            <div class="modal-body">\n                <p><%= confirm_message %></p>\n            </div>\n\n\n            <div class="modal-footer">\n                <button class="btn btn-primary" id="confirm_yes"><%= confirm_button %></button>\n                <button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><%= cancel_button %></button>\n            </div>\n            <% } else { %>\n            <div class="modal-body">\n                <div class="text-center">\n                    <h3 id="conrirm_label"><%= title %></h3>\n                </div>\n\n                <div class="text-center">\n                    <button class="btn btn-primary" id="confirm_yes"><%= confirm_button %></button>\n                    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><%= cancel_button %></button>\n                </div>\n            </div>\n            <% } %>\n        </div>\n    </div>\n</div>'),customModule.activityController={filters:void 0,highCharts:void 0,searchForm:void 0,activityLogContainer:void 0,itemsContainer:void 0,paginationContainer:void 0,errorContainer:void 0,viewContainer:void 0,run:function(e){var t=this;t.viewContainer=$("#page-wrapper"),t.activityLogContainer=$("#activityLogContainer"),t.searchForm=$("#activitySearch"),t.itemsContainer=$("#itemsContainer"),t.paginationContainer=$("#paginationContainer"),t.highCharts=Highcharts,t.filters=e.filters,t.errorContainer=$("#errorContainer"),t.hideError(),t.updateData(),$("#date-from").datetimepicker({format:"YYYY-MM-DD"}),$("#date-to").datetimepicker({format:"YYYY-MM-DD",maxDate:new Date}),$(".selectpicker").selectpicker({style:"btn-default",size:"auto"}).on("rendered.bs.select",function(e){var t=$(this),a=t.parent(".bootstrap-select");$("option:checked",t).length||$(".filter-option",a).html(t.data("title"))}),$('[data-toggle="tooltip"]').tooltip(),$(document).on("click",".pagination a",function(e){e.preventDefault();var a=$(this);return t.updateData(a.attr("href"),["items"]),!1})},updateData:function(e,t){var a=this,r={};void 0===e&&(e=document.location.protocol+"//"+document.location.hostname+document.location.pathname,r=$.extend({},!0,a.filters)),void 0!==t&&(r.actions=t.join(",")),a.hideError(),a.showLoader(a.activityLogContainer),$.ajax({url:e,data:r,method:"GET",success:function(e){if(a.scrollTop(),a.hideLoader(a.activityLogContainer),e.error)a.showError(e.error);else{if(e.items&&a.itemsContainer.html(e.items),e.pagination&&a.paginationContainer.html(e.pagination),e.activity&&a.initHighCharts(e.interval,e.activity),e.accounts){var t=$("#account",a.searchForm),r=void 0!==a.filters.account?a.filters.account:[];t.html(""),$.each(e.accounts,function(e,a){t.append($("<option></option>").attr("value",e).text(a))}),$.each(r,function(e,a){$("option[value='"+a+"']",t).prop("selected",!0)}),t.selectpicker("refresh")}if(e.events){var t=$("#event",a.searchForm),r=void 0!==a.filters.event?a.filters.event:[];t.html(""),$.each(e.events,function(e,a){var r=$("<optgroup></optgroup>").attr("label",a.title);$.each(a.events,function(e,t){r.append($("<option></option>").attr("value",e).text(t))}),t.append(r)}),$.each(r,function(e,a){$("option[value='"+a+"']",t).prop("selected",!0)}),t.selectpicker("refresh")}}},statusCode:{403:function(){location.reload()}},error:function(e,t,r){a.showError("Can not load activity log data."),a.hideLoader(a.activityLogContainer)}})},initHighCharts:function(e,t){var a=this,r=[];$.each(t,function(e,t){console.log(t),r.push([1e3*parseInt(t.point),parseInt(t.count)])}),console.log(r);var o={chart:{type:"area",zoomType:!1,marginTop:0,marginLeft:0,marginRight:0,marginBottom:0},title:!1,subtitle:!1,xAxis:{type:"datetime",gridLineWidth:1,tickInterval:1e3*parseInt(e),tickWidth:0,labels:{align:"left",x:3,y:-3}},yAxis:{title:!1,minPadding:0,maxPadding:0,min:0,minRange:.1,labels:{enabled:!1}},legend:!1,plotOptions:{series:{fillOpacity:.2},area:{marker:{radius:2},lineWidth:2,threshold:null}},series:[{name:"Events",data:r}]};a.highCharts.chart("events-count",o)},showLoader:function(e){var t=this;void 0!==e&&e.length&&(t.hideLoader(e),e.append($("<div></div>").attr("id","cover")))},hideLoader:function(e){void 0!==e&&e.length&&$("#cover",e).remove()},hideError:function(){var e=this;$(".message-container",e.errorContainer).text(""),e.errorContainer.addClass("hidden")},showError:function(e){var t=this;$(".message-container",t.errorContainer).text(e),t.errorContainer.removeClass("hidden")},scrollTop:function(){var e=this;$("html, body").animate({scrollTop:e.viewContainer.offset().top},200)}},customModule.indexController={run:function(e){function t(){for(var e="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",t="",a=0,r=e.length;a<8;++a)t+=e.charAt(Math.floor(Math.random()*r));return t}$('[data-toggle="tooltip"]').tooltip(),$(".set-staff-password").click(function(e){e.preventDefault();var t=$(this),a=$("#setStaffPasswordModal"),r=$("#setStaffPasswordForm"),o=$("#setStaffPasswordError",r),n=t.data("details");return o.addClass("hidden"),o.html(""),$("#setstaffpasswordform-username",r).val(n.login),r.attr("action",t.attr("href")),a.modal("show"),!1}),$(document).on("click","#setStaffPasswordButton",function(e){e.preventDefault();var t=$("#setStaffPasswordForm"),a=$("#setStaffPasswordError",t);return a.addClass("hidden"),$.post(t.attr("action"),t.serialize(),function(e){"success"==e.status&&window.location.reload(),"error"==e.status&&(a.removeClass("hidden"),a.html(e.error))}),!1}),$(document).on("click",".random-password",function(e){e.preventDefault();var a=$(this).parents("form");return $(".password",a).val(t()),!1}),$("#createStaff").click(function(e){e.preventDefault();$(this);var t=$("#createStaffModal"),a=$("#createStaffForm"),r=$("#createStaffError",a);return r.addClass("hidden"),r.html(""),$('input[type="text"]',a).val(""),$('input[type="checkbox"]').prop("checked",!0),$("#hide-providers").prop("checked",!1),$("select",t).prop("selectedIndex",0),t.modal("show"),!1}),$(document).on("click","#createStaffButton",function(e){e.preventDefault();var t=$("#createStaffForm"),a=$("#createStaffError",t);return a.addClass("hidden"),$.post(t.attr("action"),t.serialize(),function(e){"success"==e.status&&window.location.reload(),"error"==e.status&&(a.removeClass("hidden"),a.html(e.error))}),!1}),$(document).on("click",".edit-staff",function(e){e.preventDefault();var t=$(this),a=$("#editStaffModal"),r=$("#editStaffForm"),o=$("#editStaffError",r),n=t.data("details");return o.addClass("hidden"),o.html(""),$('input[type="checkbox"]').prop("checked",!1),$("#editstaffform-account",r).val(n.login),$("#editstaffform-status",r).val(n.status),void 0!==n.accessList&&$.each(n.accessList,function(e,t){t&&$('input[name="EditStaffForm[access]['+e+']"]').prop("checked","checked")}),r.attr("action",t.attr("href")),a.modal("show"),!1}),$("#editStaffModal, #createStaffModal").each(function(){var e=$(this),t=$('input[name="EditStaffForm[access][settings]"], input[name="CreateStaffForm[access][settings]"]',e),a=$("#wrap-settings",e),r=$('.settings[type="checkbox"]',e);e.on("show.bs.modal",function(){r.trigger("change"),t.prop("checked")?a.show():a.hide()}),t.on("change",function(){this.checked?(a.show(),r.prop("checked",!0)):(a.hide(),r.prop("checked",!1))}),r.on("change",function(){$('.settings[type="checkbox"]:checked',e).length?t.prop("checked",!0):(a.hide(),t.prop("checked",!1))})}),$(document).on("click","#editStaffButton",function(e){e.preventDefault();var t=$("#editStaffForm"),a=$("#editStaffError",t);return a.addClass("hidden"),$.post(t.attr("action"),t.serialize(),function(e){"success"==e.status&&window.location.reload(),"error"==e.status&&(a.removeClass("hidden"),a.html(e.error))}),!1}),$("#editAccount").on("show.bs.modal",function(e){$("#editStaffBody").html('<div class="modal-body"><img src="/themes/img/ajax-loader.gif" border="0" alt="loading"></div>'),$.get("/staff/edit/"+$(e.relatedTarget).data("href"),function(e){$("#editStaffBody").html(e)})}),$("#staff_edit_gen").click(function(){$("#staff_edit_passwd").val(t())}),$("#staff_create_account").click(function(){$.post("/staff/create",{id:$("#staff_add_id").val(),account:$("#staff_add_account").val(),password:$("#staff_add_passwd").val(),status:$("#staff_add_status").val(),rules_1:$("#staff_add_rules_1").is(":checked"),rules_2:$("#staff_add_rules_2").is(":checked"),rules_3:$("#staff_add_rules_3").is(":checked"),rules_4:$("#staff_add_rules_4").is(":checked"),rules_5:$("#staff_add_rules_5").is(":checked"),rules_6:$("#staff_add_rules_6").is(":checked"),rules_7:$("#staff_add_rules_7").is(":checked")}).done(function(e){switch(e){case"5":$("#staffCreate_error").html("Account cannot be blank."),$("#staffCreate_error").removeClass("hidden");break;case"4":$("#staffCreate_error").html("Password cannot be blank."),$("#staffCreate_error").removeClass("hidden");break;case"3":$("#staffCreate_error").html("Please use between 5 and 20 characters"),$("#staffCreate_error").removeClass("hidden");break;case"2":$("#staffCreate_error").html("Account already exist"),$("#staffCreate_error").removeClass("hidden");break;case"1":$("#staffCreate_error").addClass("hidden"),window.location="/staff/"+$("#staff_add_id").val()}})}),$("#changeEmailBtn").on("click",function(e){e.preventDefault(),$("#changeEmail").modal("show");var t=$("#change-email-form");return $("#changeEmail_email, #changeEmail_password",t).val(""),$("#changeEmailError",t).addClass("hidden"),!1}),$("#changePasswordBtn").on("click",function(e){e.preventDefault(),$("#changePassword").modal("show");var t=$("#change-password-form");return $("input",t).val(""),$("#changePasswordError",t).addClass("hidden"),!1}),$("#changeEmailSubmit").click(function(e){e.preventDefault();var t=$("#change-email-form");return $("#changeEmailError",t).addClass("hidden"),$.post("/changeEmail",t.serialize(),function(e){"success"==e.status&&(window.location="/settings"),"error"==e.status&&($("#changeEmailError",t).removeClass("hidden"),$("#changeEmailError",t).html(e.error))}),!1}),$("#changePasswdSubmit").click(function(e){e.preventDefault();var t=$("#change-password-form");return $("#changePasswordError",t).addClass("hidden"),$.post("/changePassword",t.serialize(),function(e){"success"==e.status&&(window.location="/settings"),"error"==e.status&&($("#changePasswordError",t).removeClass("hidden"),$("#changePasswordError",t).html(e.error))}),!1}),$(document).on("submit","#ticketForm",function(e){e.preventDefault();var t=$(this),a=$("#htmlText");return a.html('<img src="/themes/img/ajax-loader.gif" border="0">'),$("#ticketMessageError",t).addClass("hidden"),$.post(t.attr("action"),t.serialize(),function(e){"success"==e.status&&$("#message").val(""),"error"==e.status&&($("#ticketMessageError",t).removeClass("hidden"),$("#ticketMessageError",t).html(e.error)),$.get(a.data("action"),function(e){a.html(e)})}),!1}),$(".show-ticket").on("click",function(e){e.preventDefault();var t=$(this),a=$("#viewTicket"),r=$("#modal_content",a),o=t.data("subject");return r.html('<img src="/themes/img/ajax-loader.gif" border="0">'),a.modal("show"),$("#titleName",a).html(o),$.get(t.attr("href"),function(e){r.html(e)}),!1}),$(document).on("click",".create-order",function(e){var t=$(this),a=$(".error-hint");return a.addClass("hidden"),!t.data("error")||(e.preventDefault(),$(".content",a).html(t.data("error")),a.removeClass("hidden"),!1)}),$(document).on("click",".create-ticket",function(e){e.preventDefault();var t=$(this),a=$("#submitTicket"),r=$("#support-form"),o=$("#createTicketError",r),n=$(".error-hint");if(n.addClass("hidden"),t.data("error"))return $(".content",n).html(t.data("error")),n.removeClass("hidden"),!1;o.addClass("hidden"),o.html(""),$("input, textarea",r).val(""),a.modal("show")}),$(document).on("submit","#support-form",function(e){e.preventDefault();var t=$(this),a=$("#createTicketError",t);return a.addClass("hidden"),$.post(t.attr("action"),t.serialize(),function(e){"success"==e.status&&window.location.reload(),"error"==e.status&&(a.show(),a.removeClass("hidden"),a.html(e.error))}),!1}),$(document).on("click",".check-limit",function(e){e.preventDefault();var t=$(this),a=t.data("url"),r=t.data("errorBlock"),o=!1;return $(r).addClass("hidden"),$.ajax({url:a,async:!1,success:function(t){"success"==t.status&&(o=!0),"error"==t.status&&(e.stopImmediatePropagation(),$(r).removeClass("hidden"),$(r).html(t.error))}}),o})}},customModule.invoiceController={notes:[],run:function(e){var t=this,a=e.live;void 0===e.live&&(a=!0),t.notes=e.notes,t.showContent(e.pgid),a&&$(document).on("change","#pgid",function(e){var a=$(this).val();t.showContent(a)})},showContent:function(e){var t=this,a=$("#paymentContent");a.addClass("hidden"),$(".content",a).text(""),void 0!==t.notes[e]&&t.notes[e].length&&($(".content",a).html(t.notes[e]),a.removeClass("hidden"))}},customModule.orderDomainController={run:function(e){var t=void 0!==e.orderDomainForm?e.orderDomainForm:void 0;$("#searchDomain").change(function(e){$("#searchResult").addClass("hidden")}),$("#searchDomainSubmit").click(function(e){e.preventDefault();var t=$(this),a=t.data("action"),r=$("#searchDomain"),o=$.trim(r.val()),n=$("#searchResultContainer");return!!o.length&&($("#searchResult").addClass("hidden"),t.addClass("active"),$.get(a,{search_domain:o,zone:$("#domain_zone").selectpicker("val")},function(e){t.removeClass("active"),e.content&&(n.html(e.content),$("#searchResult").removeClass("hidden"),$(".domain_zone").trigger("change"))}),!1)}),$("#continueDomainSearch").click(function(e){if(e.preventDefault(),$(this).hasClass("disabled"))return!1;var t=$("#orderDomainModal"),a=$("#orderDomainError",t),r=$(".domain_zone:checked").data("domain");return t.modal("show"),a.addClass("hidden"),a.html(""),$("#modal_domain_name",t).val(r),!1}),$(document).on("change",".domain_zone",function(){$(".domain_zone:checked").length?($("#domain_zone").selectpicker("val",$(".domain_zone:checked").val()),$("#continueDomainSearch").removeClass("disabled")):$("#continueDomainSearch").addClass("disabled")}),$("#orderDomainBtn").click(function(e){e.preventDefault();var a=$(this),r=$("#orderDomainModal"),o=$("#orderDomainError",r),n=a.data("action"),i=r.find("select, textarea, input").serialize();if(o.addClass("hidden"),a.addClass("active"),void 0!==t){var d=$(t);n=d.data("action"),i=d.serialize()}return $.post(n,i,function(e){if(a.removeClass("active"),"success"==e.status){if(r.modal("hide"),void 0!==e.redirect)return void(window.location.href=e.redirect);$("#orderPanelBlock").removeClass("hidden"),$("#orderDomainBlock").addClass("hidden"),$("#helpDomain").addClass("hidden"),$("#domain").val($("#modal_domain_name",r).val()).prop("readonly",!0)}"error"==e.status&&(o.removeClass("hidden"),o.html(e.error))}),!1})}},customModule.orderPanelController={run:function(e){$(".has_domain").change(function(e){return e.preventDefault(),1==$("input.has_domain:checked").val()?($("#orderPanelBlock").removeClass("hidden"),$("#orderDomainBlock").addClass("hidden"),$("#orderNote").removeClass("hidden"),$("#domain").val("").prop("readonly",!1)):($("#orderDomainBlock").removeClass("hidden"),$("#orderPanelBlock").addClass("hidden"),$("#searchResult").addClass("hidden"),$("#searchResultContainer").html(""),$("#orderNote").addClass("hidden")),!1}),$("#order-panel-form").on("submit",function(){return!$("#orderPanelBlock").hasClass("hidden")||($("#orderDomainModal").modal("hide"),$("#searchDomainSubmit").trigger("click"),!1)})}},customModule.superadminContentController={run:function(e){$(".edit-content").click(function(e){e.preventDefault();var t=$(this),a=$("#editContentModal"),r=$("#editContentForm"),o=$("#editContentError",r),n=t.data("details");return o.addClass("hidden"),o.html(""),$("#editcontentform-name",r).val(n.name),$("#editcontentform-text",r).val(n.text),r.attr("action",t.attr("href")),a.modal("show"),!1}),$(document).on("click","#editContentButton",function(e){e.preventDefault();var t=$(this),a=$("#editContentForm");return custom.sendFrom(t,a,{data:a.serialize(),callback:function(e){$("#editContentModal").modal("hide"),location.reload()}}),!1})}},customModule.superadminCustomersController={run:function(e){$(".edit").click(function(e){e.preventDefault();var t=$(this),a=$("#editCustomerModal"),r=$("#editCustomerForm"),o=$("#editCustomerError",r),n=t.data("details");return o.addClass("hidden"),o.html(""),$("#editcustomerform-email",r).val(n.email),$("#editcustomerform-first_name",r).val(n.first_name),$("#editcustomerform-last_name",r).val(n.last_name),r.attr("action",t.attr("href")),a.modal("show"),!1}),$(document).on("click",".set-password",function(e){e.preventDefault();var t=$(this).attr("href"),a=$("#setPasswordModal"),r=$("#setPasswordForm"),o=$("#setPasswordError",r);return $("#customerpasswordform-email",r).val(""),r.attr("action",t),o.addClass("hidden"),o.html(""),a.modal("show"),!1}),$(".random-password").click(function(e){e.preventDefault();var t=$(this).parents("form");return $(".password",t).val(custom.generatePassword()),!1}),$(document).on("click","#editCustomerButton",function(e){e.preventDefault();var t=$(this),a=$("#editCustomerForm"),r=$("#editCustomerError",a);return r.addClass("hidden"),custom.sendFrom(t,a,{data:a.serialize(),callback:function(e){"success"==e.status&&($("#editCustomerModal").modal("hide"),location.reload()),"error"==e.status&&(r.removeClass("hidden"),r.html(e.error))}}),!1}),$(document).on("click","#setPasswordButton",function(e){e.preventDefault();var t=$(this),a=$("#setPasswordForm"),r=(a.attr("action"),$("#setPasswordError",a));return r.addClass("hidden"),custom.sendFrom(t,a,{data:a.serialize(),callback:function(e){"success"==e.status&&($("#setPasswordModal").modal("hide"),location.reload()),"error"==e.status&&(r.removeClass("hidden"),r.html(e.error))}}),!1})}},customModule.superadminDomainsController={run:function(e){$("#domainsSearch").on("submit",function(e){e.preventDefault();var t=$("#domainsSearch"),a=t.attr("action");window.location.href=a+(a.match(/\?/)?"&":"?")+t.serialize()}),$(".domain-details").click(function(e){e.preventDefault();var t=$(this).attr("href"),a=$("#domainDetailsModal"),r=$(".modal-body",a);return a.modal("show"),r.html('<span class="spinner"><i class="fa fa-spinner fa-spin"></i></span>'),$.get(t,function(e){r.html(e.content)}),!1})}},customModule.superadminEditPanelController={run:function(e){new Clipboard(".copy"),$("#generateApikey").click(function(e){e.preventDefault();var t=$(this);return $.get(t.attr("href"),function(e){$("#editprojectform-apikey").val(e.key)}),!1})}},customModule.superadminInvoicesController={run:function(e){$(document).on("click",".cancel-menu",function(e){e.preventDefault();var t=$(this);return custom.confirm(t.data("confirm-message"),"",function(){location.href=t.attr("href")}),!1}),$("#invoicesSearch").on("submit",function(e){e.preventDefault();var t=$("#invoicesSearch"),a=t.attr("action");window.location.href=a+(a.match(/\?/)?"&":"?")+t.serialize()}),$(".add-payment").click(function(e){e.preventDefault();var t=$(this).attr("href"),a=$("#addPaymentForm"),r=$("#addPaymentModal"),o=$("#addPaymentError",a);return a.attr("action",t),o.addClass("hidden"),o.html(""),$('input[type="text"]',a).val(""),$("select",a).prop("selectedIndex",0),r.modal("show"),!1}),$(".edit-credit").click(function(e){e.preventDefault();var t=$(this),a=t.attr("href"),r=$("#editCreditForm"),o=$("#editCreditModal"),n=$("#editCreditError",r),i=t.data("details");return $('input[type="text"]',r).val(""),$("select",r).prop("selectedIndex",0),$("#editinvoicecreditform-credit",r).val(i.credit),r.attr("action",a),n.addClass("hidden"),n.html(""),o.modal("show"),!1}),$(".edit-invoice").click(function(e){e.preventDefault();var t=$(this),a=t.attr("href"),r=$("#editInvoiceForm"),o=$("#editInvoiceModal"),n=$("#editInvoiceError",r),i=t.data("details");return $('input[type="text"]',r).val(""),$("select",r).prop("selectedIndex",0),$("#editinvoiceform-total",r).val(i.total),r.attr("action",a),n.addClass("hidden"),n.html(""),o.modal("show"),!1}),$(document).on("click","#editInvoiceButton",function(e){e.preventDefault();var t=$(this),a=$("#editInvoiceForm");return custom.sendFrom(t,a,{data:a.serialize(),callback:function(e){$("#editInvoiceModal").modal("hide"),location.reload()}}),!1}),$(document).on("click","#addPaymentButton",function(e){e.preventDefault();var t=$(this),a=$("#addPaymentForm");return custom.sendFrom(t,a,{data:a.serialize(),callback:function(e){$("#addPaymentModal").modal("hide"),location.reload()}}),!1}),$(document).on("click","#editCreditButton",function(e){e.preventDefault();var t=$(this),a=$("#editCreditForm");return custom.sendFrom(t,a,{data:a.serialize(),callback:function(e){$("#editCreditModal").modal("hide"),location.reload()}}),!1}),$("#createInvoice").click(function(e){e.preventDefault();var t=$(this).attr("href"),a=$("#createInvoiceModal"),r=($(".modal-body",a),$("#createInvoiceForm"));$("#createInvoiceError",r);return $('input[type="text"], textarea',r).val(""),$("select",r).prop("selectedIndex",0),r.attr("action",t),a.modal("show"),!1}),$(document).on("click","#createInvoiceButton",function(e){e.preventDefault();var t=$(this),a=$("#createInvoiceForm");return custom.sendFrom(t,a,{data:a.serialize(),callback:function(e){$("#createInvoiceModal").modal("hide"),location.reload()}}),!1}),new Clipboard(".copy",{text:function(e){return e.getAttribute("data-link")}})}},customModule.superadminOrdersController={run:function(e){$("#ordersSearch").on("submit",function(e){e.preventDefault();var t=$("#ordersSearch"),a=t.attr("action");window.location.href=a+(a.match(/\?/)?"&":"?")+t.serialize()}),$(".order-details").click(function(e){e.preventDefault();var t=$(this).attr("href"),a=$("#ordersDetailsModal"),r=$(".modal-body",a);return a.modal("show"),r.html('<span class="spinner"><i class="fa fa-spinner fa-spin"></i></span>'),$.get(t,function(e){r.html(e.content)}),!1})}},customModule.superadminPanelsController={run:function(e){$("#panelsSearch").on("submit",function(e){e.preventDefault();var t=$("#panelsSearch"),a=t.attr("action");window.location.href=a+(a.match(/\?/)?"&":"?")+t.serialize()}),$(".change-domain").click(function(e){e.preventDefault();var t=$(this),a=t.attr("href"),r=$("#changeDomainForm"),o=$("#changeDomainModal"),n=$("#changeDomainError",r),i=t.data("domain"),d=t.data("subdomain");return r.attr("action",a),n.addClass("hidden"),n.html(""),$("#changedomainform-domain",r).val(i),$("#changedomainform-subdomain",r).prop("checked",d),o.modal("show"),!1}),$(document).on("click","#changeDomainButton",function(e){e.preventDefault();var t=$(this),a=$("#changeDomainForm");return custom.sendFrom(t,a,{data:a.serialize(),callback:function(e){$("#changeDomainModal").modal("hide"),location.reload()}}),!1}),$(".edit-expiry").click(function(e){e.preventDefault();var t=$(this),a=t.attr("href"),r=$("#editExpiryForm"),o=$("#editExpiryModal"),n=$("#editExpiryError",r);r.attr("action",a),n.addClass("hidden"),n.html(""),$('input[type="text"]',r).val("");var i=t.data("expired");return $("#editexpiryform-expired").val(i),o.modal("show"),!1}),$(document).on("click","#editExpiryButton",function(e){e.preventDefault();var t=$(this),a=$("#editExpiryForm");return custom.sendFrom(t,a,{data:a.serialize(),callback:function(e){$("#editExpiryModal").modal("hide"),location.reload()}}),!1}),$(".edit-providers").click(function(e){e.preventDefault();var t=$(this),a=t.attr("href"),r=$("#editProvidersForm"),o=$("#editProvidersModal"),n=$("#editProvidersError",r);r.attr("action",a),n.addClass("hidden"),n.html(""),$('input[type="checkbox"]',r).prop("checked",!1);var i=t.data("providers");return void 0!==i&&i.length&&$.each(i,function(e,t){$('input[value="'+t+'"]',r).prop("checked",!0)}),o.modal("show"),!1}),$(document).on("click","#editProvidersButton",function(e){e.preventDefault();var t=$(this),a=$("#editProvidersForm");return custom.sendFrom(t,a,{data:a.serialize(),callback:function(e){$("#editProvidersModal").modal("hide"),location.reload()}}),!1}),$(".downgrade").click(function(e){e.preventDefault();var t=$(this),a=t.attr("href"),r=$("#downgradePanelForm"),o=$("#downgradePanelModal"),n=$("#downgradePanelError",r),i=t.data("providersurl"),d=$("#providers",r);return r.attr("action",a),n.addClass("hidden"),n.html(""),$("option",d).remove(),$.get(i,function(e){if(void 0===e.providers)return!1;$.each(e.providers,function(e,t){d.append($("<option></option>").attr("value",e).text(t))}),o.modal("show")}),!1}),$(document).on("click","#downgradePanelButton",function(e){e.preventDefault();var t=$(this),a=$("#downgradePanelForm");return custom.sendFrom(t,a,{data:a.serialize(),callback:function(e){$("#downgradePanelModal").modal("hide"),location.reload()}}),!1}),$(".upgrade").click(function(e){e.preventDefault();var t=$(this),a=t.attr("href"),r=$("#upgradePanelForm"),o=$("#upgradePanelModal"),n=$("#upgradePanelError",r);$('input[type="text"]',r).val("");var i=t.data("total");return $("#upgradepanelform-total").val(i),r.attr("action",a),n.addClass("hidden"),n.html(""),o.modal("show"),!1}),$(document).on("click",".upgrade-panel-button",function(e){e.stopImmediatePropagation();var t=$(this),a=$("#upgradePanelForm"),r=t.data("mode");return $("#upgradepanelform-mode",a).prop("checked",!!r),custom.sendFrom(t,a,{data:a.serialize(),callback:function(e){$("#upgradePanelModal").modal("hide"),location.reload()}}),!1})}},customModule.superadminPaymentGatewayController={run:function(e){$(".edit-payment").click(function(e){e.preventDefault();var t=$(this),a=$("#editPaymentModal");return $.get(t.attr("href"),function(e){e.content&&($(".modal-body",a).html(e.content),a.modal("show"))}),!1}),$(document).on("click","#editPaymentButton",function(e){e.preventDefault();var t=$(this),a=$("#editPaymentForm");return custom.sendFrom(t,a,{data:a.serialize(),callback:function(e){"success"==e.status&&($("#editPaymentModal").modal("hide"),location.reload())}}),!1})}},customModule.superadminPaymentsController={run:function(e){$("#paymentsSearch").on("submit",function(e){e.preventDefault();var t=$("#paymentsSearch"),a=t.attr("action");window.location.href=a+(a.match(/\?/)?"&":"?")+t.serialize()}),$(".payment-details").click(function(e){e.preventDefault();var t=$(this).attr("href"),a=$("#paymentDetailsModal"),r=$(".modal-body",a);return a.modal("show"),r.html('<span class="spinner"><i class="fa fa-spinner fa-spin"></i></span>'),$.get(t,function(e){r.html(e.content)}),!1})}},customModule.superadminPlanController={run:function(e){$(document).on("click","#createPlan",function(e){e.preventDefault();$(this);var t=$("#createPlanForm"),a=$("#createPlanModal"),r=$("#createPlanError",t);return r.addClass("hidden"),r.html(""),$('input[type="text"]',t).val(""),$('input[type="checkbox"]').prop("checked",!0),$("select",a).prop("selectedIndex",0),a.modal("show"),!1}),$(".edit-plan").click(function(e){e.preventDefault();var t=$(this),a=$("#editPlanModal"),r=$("#editPlanForm"),o=$("#editPlanError",r),n=t.data("details");return o.addClass("hidden"),o.html(""),$("#editplanform-title",r).val(n.title),$("#editplanform-price",r).val(n.price),$("#editplanform-description",r).val(n.description),$("#editplanform-of_orders",r).val(n.of_orders),$("#editplanform-before_orders",r).val(n.before_orders),$("#editplanform-up",r).val(n.up),$("#editplanform-down",r).val(n.down),r.attr("action",t.attr("href")),a.modal("show"),!1}),$(document).on("click","#editPlanButton",function(e){e.preventDefault();var t=$(this),a=$("#editPlanForm");return custom.sendFrom(t,a,{data:a.serialize(),callback:function(e){$("#editPlanModal").modal("hide"),location.reload()}}),!1}),$(document).on("click","#createPlanButton",function(e){e.preventDefault();var t=$(this),a=$("#createPlanForm");return custom.sendFrom(t,a,{data:a.serialize(),callback:function(e){$("#createPlanModal").modal("hide"),location.reload()}}),!1})}},customModule.superadminProviderLogsController={run:function(e){$("#logsSearch").on("submit",function(e){e.preventDefault();var t=$("#logsSearch"),a=t.attr("action");window.location.href=a+(a.match(/\?/)?"&":"?")+t.serialize()})}},customModule.superadminProvidersController={run:function(e){$("#providersSearch").on("submit",function(e){e.preventDefault();var t=$("#providersSearch"),a=t.attr("action");window.location.href=a+(a.match(/\?/)?"&":"?")+t.serialize()}),$(document).on("click",".show-panels",function(e){e.preventDefault();var t=$(this),a=$("#providerPanelsModal"),r=$(".modal-body",a),o=t.data("header");$(".modal-title",a).text(o),r.html('<img src="/themes/img/ajax-loader.gif" border="0">'),a.modal("show");var n=t.data("projects");if(!n||!n.length)return r.html(""),!1;var i=[];return $.each(n,function(e,t){i.push('<div class="row"> <div class="col-md-12"> '+t.site+" </div> </div>")}),r.html(i.join("")),!1}),$(".edit-provider").click(function(e){e.preventDefault();var t=$(this),a=t.attr("href"),r=$("#editProviderForm"),o=$("#editProviderModal"),n=$("#editProviderError",r);r.attr("action",a),n.addClass("hidden"),n.html(""),$('input[type="text"]',r).val("");var i=t.data("details");return $("#editproviderform-skype").val(i.skype),$("#editproviderform-name").val(i.name),o.modal("show"),!1}),$(document).on("click","#editProviderButton",function(e){e.preventDefault();var t=$(this),a=$("#editProviderForm");return custom.sendFrom(t,a,{data:a.serialize(),callback:function(e){$("#editProviderModal").modal("hide"),location.reload()}}),!1}),$("#providersTable").tablesorter()}},customModule.superadminReferralsController={run:function(e){$("#referralsTable").tablesorter()}},customModule.superadminSslController={run:function(e){$("#sslSearch").on("submit",function(e){e.preventDefault();var t=$("#sslSearch"),a=t.attr("action");window.location.href=a+(a.match(/\?/)?"&":"?")+t.serialize()}),$(".ssl-details").click(function(e){e.preventDefault();var t=$(this).attr("href"),a=$("#sslDetailsModal"),r=$(".modal-body",a);return a.modal("show"),r.html('<span class="spinner"><i class="fa fa-spinner fa-spin"></i></span>'),$.get(t,function(e){r.html(e.content)}),!1})}},customModule.superadminStaffsController={run:function(e){$(document).on("click","#createStaff",function(e){e.preventDefault();$(this);var t=$("#createStaffForm"),a=$("#createStaffModal"),r=$("#createStaffError",t);return r.addClass("hidden"),r.html(""),$('input[type="text"]',t).val(""),$('input[type="checkbox"]').prop("checked",!0),$("select",a).prop("selectedIndex",0),a.modal("show"),!1}),$(".edit-account").click(function(e){e.preventDefault();var t=$(this),a=$("#editStaffModal"),r=$("#editStaffForm"),o=$("#editStaffError",r),n=t.data("details");return o.addClass("hidden"),o.html(""),$("#editstaffform-username",r).val(n.username),$("#editstaffform-first_name",r).val(n.first_name),$("#editstaffform-last_name",r).val(n.last_name),$("#editstaffform-status",r).val(n.status),$(".access",r).prop("checked",!1),void 0!==n.access&&n.access.length&&$.each(n.access,function(e,t){$('input[name="EditStaffForm[access]['+t+']"]').prop("checked","checked")}),r.attr("action",t.attr("href")),a.modal("show"),!1}),$(document).on("click",".change-password",function(e){e.preventDefault();var t=$(this).attr("href"),a=$("#changePasswordModal"),r=$("#changePasswordForm"),o=$("#changePasswordError",r);return $(".password",r).val(""),r.attr("action",t),o.hide(),o.html(""),a.modal("show"),!1}),$(".random-password").click(function(e){e.preventDefault();var t=$(this).parents("form");return $(".password",t).val(custom.generatePassword()),!1}),$(document).on("click","#editStaffButton",function(e){e.preventDefault();var t=$(this),a=$("#editStaffForm");return custom.sendFrom(t,a,{data:a.serialize(),callback:function(e){$("#editStaffModal").modal("hide"),location.reload()}}),!1}),$(document).on("click","#createStaffButton",function(e){e.preventDefault();var t=$(this),a=$("#createStaffForm");return custom.sendFrom(t,a,{data:a.serialize(),callback:function(e){$("#createStaffModal").modal("hide"),location.reload()}}),!1}),$(document).on("click","#changePasswordButton",function(e){e.preventDefault();var t=$(this),a=$("#changePasswordForm");a.attr("action");return custom.sendFrom(t,a,{data:a.serialize(),callback:function(e){$("#changePassword").modal("hide"),location.reload()}}),!1})}},customModule.superadminStoresController={run:function(e){$("#storesSearch").on("submit",function(e){e.preventDefault();var t=$("#storesSearch"),a=t.attr("action");window.location.href=a+(a.match(/\?/)?"&":"?")+t.serialize()}),$(".edit-expiry").click(function(e){e.preventDefault();var t=$(this),a=t.attr("href"),r=$("#editExpiryForm"),o=$("#editExpiryModal"),n=$("#editExpiryError",r);r.attr("action",a),n.addClass("hidden"),n.html(""),$('input[type="text"]',r).val("");var i=t.data("expired");return $("#editstoreexpiryform-expired").val(i),o.modal("show"),!1}),$(document).on("click","#editExpiryButton",function(e){e.preventDefault();var t=$(this),a=$("#editExpiryForm");return custom.sendFrom(t,a,{data:a.serialize(),callback:function(e){$("#editExpiryModal").modal("hide"),location.reload()}}),!1})}},customModule.superadminTicketsController={run:function(e){$("#domainsSearch").on("submit",function(e){e.preventDefault();var t=$("#domainsSearch"),a=t.attr("action");window.location.href=a+(a.match(/\?/)?"&":"?")+t.serialize()}),$("#createTicket").click(function(e){e.preventDefault();var t=$(this).attr("href"),a=$("#createTicketModal"),r=($(".modal-body",a),$("#createTicketForm"));$("#createTicketError",r);return $('input[type="text"], textarea',r).val(""),$("select",r).prop("selectedIndex",0),r.attr("action",t),a.modal("show"),!1}),$(document).on("click","#createTicketButton",function(e){e.preventDefault();var t=$(this),a=$("#createTicketForm");return custom.sendFrom(t,a,{data:a.serialize(),callback:function(e){$("#createTicketModal").modal("hide"),location.reload()}}),!1})}},customModule.superadminToolsControllerLevopanelAction={run:function(e){$(document).on("click","#addDomainButton",function(e){e.preventDefault();var t=$(this),a=$("#addDomainForm");return custom.sendFrom(t,a,{data:a.serialize(),callback:function(e){$("#add_domain_modal").modal("hide"),location.reload()}}),!1}),$("#add_domain_modal").on("show.bs.modal",function(e){$(this).find("#addDomainError").addClass("hidden"),$(this).find("#adddomainform-domain").val("")})}};
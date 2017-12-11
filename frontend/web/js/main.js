var custom=new function(){var e=this;e.request=null,e.confirm=function(e,t,n,o){var i;return i=(0,templates["modal/confirm"])($.extend({},!0,{confirm_button:"OK",cancel_button:"Cancel",width:"600px"},o,{title:e,confirm_message:t})),$(window.document.body).append(i),$("#confirmModal").modal({}),$("#confirmModal").on("hidden.bs.modal",function(e){$("#confirmModal").remove()}),$("#confirm_yes").on("click",function(e){return $("#confirm_yes").unbind("click"),$("#confirmModal").modal("hide"),n.call()})},e.ajax=function(t){var n=$.extend({},!0,t);"object"==typeof t&&(t.beforeSend=function(){"function"==typeof n.beforeSend&&n.beforeSend()},t.success=function(e){"function"==typeof n.success&&n.success(e)},null!=e.request&&e.request.abort(),e.request=$.ajax(t))},e.notify=function(e){var t,n;if($("body").addClass("bottom-right"),"object"!=typeof e)return!1;for(t in e)void 0!==(n=$.extend({},!0,{type:"success",delay:8e3,text:""},e[t])).text&&null!=n.text&&$.notify({message:n.text.toString()},{type:n.type,placement:{from:"bottom",align:"right"},z_index:2e3,delay:n.delay,animate:{enter:"animated fadeInDown",exit:"animated fadeOutUp"}})},e.sendBtn=function(t,n){if("object"!=typeof n&&(n={}),!t.hasClass("active")){t.addClass("has-spinner");var o=$.extend({},!0,n);o.url=t.attr("href"),$(".spinner",t).remove(),t.prepend('<span class="spinner"><i class="fa fa-spinner fa-spin"></i></span>'),o.beforeSend=function(){t.addClass("active")},o.success=function(o){t.removeClass("active"),$(".spinner",t).remove(),"success"==o.status?"function"==typeof n.callback&&n.callback(o):"error"==o.status&&e.notify({0:{type:"danger",text:o.message}})},e.ajax(o)}},e.sendFrom=function(t,n,o){if("object"!=typeof o&&(o={}),!t.hasClass("active")){t.addClass("has-spinner");var i=$.extend({},!0,o),a=$(".error-summary",n);i.url=n.attr("action"),i.type="POST",$(".spinner",t).remove(),t.prepend('<span class="spinner"><i class="fa fa-spinner fa-spin"></i></span>'),i.beforeSend=function(){t.addClass("active"),a.length&&(a.addClass("hidden"),a.html(""))},i.success=function(i){t.removeClass("active"),$(".spinner",t).remove(),"success"==i.status?"function"==typeof o.callback&&o.callback(i):"error"==i.status&&(i.message&&(a.length?(a.html(i.message),a.removeClass("hidden")):e.notify({0:{type:"danger",text:i.message}})),i.errors&&$.each(i.errors,function(e,t){alert(t),n.yiiActiveForm("updateAttribute",e,t)}),"function"==typeof o.errorCallback&&o.errorCallback(i))},e.ajax(i)}},e.generateUrlFromString=function(e){return e.replace(/[^a-z0-9_\-\s]/gim,"").replace(/\s+/g,"-").toLowerCase()},e.generateUniqueUrl=function(e,t){var n,o,i=e;o=1;do{(n=_.find(t,function(e){return e===i}))&&(i=e+"-"+o,o++)}while(n);return i}},customModule={};window.modules={},$(function(){"object"==typeof window.modules&&$.each(window.modules,function(e,t){void 0!==customModule[e]&&customModule[e].run(t)})}),customModule.adminGeneral={run:function(e){$(".edit-seo__title").length>0&&function(){for(var e=["edit-seo__title","edit-seo__meta"],t=0;t<e.length;t++)!function(t){$("."+e[t]+"-muted").text($("#"+e[t]).val().length),$("#"+e[t]).on("input",function(n){2==t?$("."+e[t]).text($(n.target).val().replace(/\s+/g,"-")):($("."+e[t]+"-muted").text($(n.target).val().length),$("."+e[t]).text($(n.target).val()))}).trigger("input")}(t)}(),$(".delete-uploaded-images").click("click",function(e){var t=$(e.currentTarget),n=t.data("field");n&&($(document).find("#"+n).attr("value",null),t.closest(".uploaded-image").empty())})}},customModule.adminPages={run:function(e){var t,n,o=$("#delete-modal"),i=o.find(".modal-loader");o.find("#feature-delete").on("click",function(){i.removeClass("hidden"),$.ajax({url:t,type:"DELETE",success:function(e,t,o){_.delay(function(){$(location).attr("href",n)},500)},error:function(e,t,n){i.addClass("hidden"),o.modal("hide"),console.log("Error on service save",e,t,n)}})}),o.on("show.bs.modal",function(e){var i=$(e.relatedTarget);t=i.data("action_url"),n=o.data("success_redirect")}),o.on("hidden.bs.modal",function(){t=null})}},customModule.adminPageEdit={run:function(e){function t(e){var t,n=$(e.target).val();t=custom.generateUrlFromString(n),t=custom.generateUniqueUrl(t,s),r.url.val(t).trigger("input"),r.seo_title.val(n).trigger("input")}function n(){r.name.off("input",t),r.seo_title.off("input",t)}var o=$("#pageForm"),i=o.find(".collapse"),a=o.data("new_page"),r={name:o.find(".form_field__name"),content:o.find(".form_field__content"),url:o.find(".form_field__url"),visibility:o.find(".form_field__visibility"),seo_title:o.find(".form_field__seo_title"),seo_description:o.find(".form_field__seo_description")},s=e.urls||[],d=e.url_error||!1;$(".edit-seo__title").length>0&&function(){for(var e=["edit-seo__title","edit-seo__meta","edit-seo__url"],t=0;t<e.length;t++)!function(t){$("."+e[t]+"-muted").text($("#"+e[t]).val().length),$("#"+e[t]).on("input",function(n){2===t?$("."+e[t]).text($(n.target).val().toLowerCase()):($("."+e[t]+"-muted").text($(n.target).val().length),$("."+e[t]).text($(n.target).val()))}).trigger("input")}(t)}(),r.content,r.content.summernote({minHeight:300,focus:!0,toolbar:[["style",["style","bold","italic"]],["lists",["ul","ol"]],["para",["paragraph"]],["color",["color"]],["insert",["link","picture","video"]],["codeview",["codeview"]]],disableDragAndDrop:!0,styleTags:["p","h1","h2","h3","h4","h5","h6"],popover:{image:[["float",["floatLeft","floatRight","floatNone"]],["remove",["removeMedia"]]]},dialogsFade:!0}),o.keypress(function(e){if(13===e.which)return o.submit(),!1}),d&&i.collapse("show"),a&&(r.name.focus(),r.name.on("input",t),r.url.on("focus",n),r.seo_title.on("focus",n)),r.url.on("input",function(e){var t,n=e.currentTarget,o=$(n),i=o.val(),a=n.selectionStart;(t=custom.generateUrlFromString(i)).length>=200&&(t=t.substring(0,199)),o.val(t),n.selectionEnd=a})}},customModule.adminPayments={run:function(e){$(document).on("change",".toggle-active",function(e){var t=$(e.currentTarget),n=t.data("action_url"),o=(t.data("payment_method"),0|t.prop("checked"));$.ajax({url:n,type:"POST",data:{active:o},success:function(e,t,n){},error:function(e,t,n){console.log("Error on update",e,t,n)}})})}},customModule.adminLayout={run:function(e){$(".dropdown-collapse").on("click",function(e){e.preventDefault(),e.stopPropagation(),$(this).next().hasClass("show")?$($(this).attr("href")).collapse("hide"):$($(this).attr("href")).collapse("show")});var t=document.querySelectorAll(".inputfile");Array.prototype.forEach.call(t,function(e){e.nextElementSibling.innerHTML;e.addEventListener("change",function(e){if((this.files&&this.files.length>1?(this.getAttribute("data-multiple-caption")||"").replace("{count}",this.files.length):e.target.value.split("\\").pop())&&this.files&&this.files[0]){var t=new FileReader;t.onload=function(e){var t='<div class="sommerce-settings__theme-imagepreview">\n                              <a href="#" class="sommerce-settings__delete-image"><span class="fa fa-times-circle-o"></span></a>\n                              <img src="'+e.target.result+'" alt="...">\n                          </div>';$("#image-preview").html(t)},t.readAsDataURL(this.files[0])}}),$(document).on("click",".sommerce_v1.0-settings__delete-image",function(t){$("#image-preview").html("<span></span>"),e.value=""})}),$("#select-menu-link").change(function(){$(".hide-link").hide();var e=$("#select-menu-link option:selected").val();$(".link-"+e).fadeIn()}),$('[data-toggle="tooltip"]').tooltip()}},customModule.adminNotifyLayout={run:function(e){toastr.options={closeButton:!1,debug:!1,newestOnTop:!1,progressBar:!1,positionClass:"toast-bottom-right",preventDuplicates:!1,onclick:null,showDuration:"300",hideDuration:"5000",timeOut:"5000",extendedTimeOut:"5000",showEasing:"swing",hideEasing:"linear",showMethod:"fadeIn",hideMethod:"fadeOut"};e.messages;e.messages&&_.forEach(e.messages,function(e){e.success&&toastr.success(e.success),e.warning&&toastr.warning(e.warning),e.error&&toastr.error(e.error)})}},customModule.ordersDetails={run:function(e){$(document).ready(function(){var e=$("#suborder-details-modal"),t=e.find(".modal-title"),n=e.find("#order-detail-provider"),o=e.find("#order-detail-provider-order-id"),i=e.find("#order-detail-provider-response"),a=e.find("#order-detail-lastupdate"),r=e.find(".modal-loader");e.on("show.bs.modal",function(e){function s(e){t.html(c),n.val(e.provider),o.val(e.provider_order_id),i.html(e.provider_response),a.val(e.updated_at)}var d=$(e.relatedTarget),l=d.data("suborder-id"),c=d.data("modal_title");void 0===l||isNaN(l)||(r.removeClass("hidden"),$.ajax({url:"/admin/orders/get-order-details",type:"GET",data:{suborder_id:l},success:function(e){r.addClass("hidden"),void 0!==e.details&&s(e.details)},error:function(e,t,n){console.log("Something is wrong!"),console.log(e,t,n),r.addClass("hidden")}}))}),e.on("hidden.bs.modal",function(e){$(e.currentTarget).find("input").val(""),i.html("")})})}},customModule.ordersClipboard={run:function(e){$(document).ready(function(){var e=function(){var e=function(){new Clipboard("[data-clipboard=true]").on("success",function(e){e.clearSelection(),alert("Copied!")})};return{init:function(){e()}}}();jQuery(document).ready(function(){e.init()})})}},customModule.payments={run:function(e){var t=$(".payments_detail"),n=t.find(".modal-title"),o=t.find(".details-container"),i=t.find(".modal-loader");t.on("show.bs.modal",function(e){function a(e){n.html(d),_.each(e,function(e){o.append('<pre class="sommerce-pre details-item">'+e.time+"<br><br>"+e.data+"</pre>")})}var r=$(e.relatedTarget),s=r.data("id"),d=r.data("modal_title"),l=r.data("action_url");void 0!==s&&void 0!==l&&(i.removeClass("hidden"),$.ajax({url:l,type:"GET",success:function(e){i.addClass("hidden"),void 0!==e&&a(e)},error:function(e,n,o){console.log("Something is wrong!"),console.log(e,n,o),i.addClass("hidden"),t.modal("hide")}}))}),t.on("hidden.bs.modal",function(e){n.html(""),o.empty()})}},customModule.adminProviders={run:function(e){$(document).on("click","#showCreateProviderModal",function(e){e.preventDefault();$(this);var t=$("#createProviderModal"),n=$("#createProviderForm",t),o=$("#createProviderError",n);return o.addClass("hidden"),o.html(""),t.modal("show"),!1}),$(document).on("click","#createProviderButton",function(e){e.preventDefault();var t=$(this),n=$("#createProviderForm");return custom.sendFrom(t,n,{data:n.serialize(),callback:function(e){$("#createProviderModal").modal("hide"),location.reload()}}),!1})}},customModule.frontendLayout={run:function(e){new Swiper(".swiper-container",{pagination:".swiper-pagination",paginationClickable:!0}),new Swiper(".main-slider",{paginationClickable:!0,autoplay:3e3,pagination:".main-slider-pagination"});$(window).width()>991&&$(".navbar .dropdown").hover(function(){$(this).find(".dropdown-menu").first().stop(!0,!0).slideDown(150)},function(){$(this).find(".dropdown-menu").first().stop(!0,!0).slideUp(105)})}};
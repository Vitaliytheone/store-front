var custom=new function(){var e=this;e.request=null,e.confirm=function(e,t,n,i){var o;return o=(0,templates["modal/confirm"])($.extend({},!0,{confirm_button:"OK",cancel_button:"Cancel",width:"600px"},i,{title:e,confirm_message:t})),$(window.document.body).append(o),$("#confirmModal").modal({}),$("#confirmModal").on("hidden.bs.modal",function(e){$("#confirmModal").remove()}),$("#confirm_yes").on("click",function(e){return $("#confirm_yes").unbind("click"),$("#confirmModal").modal("hide"),n.call()})},e.ajax=function(t){var n=$.extend({},!0,t);"object"==typeof t&&(t.beforeSend=function(){"function"==typeof n.beforeSend&&n.beforeSend()},t.success=function(e){"function"==typeof n.success&&n.success(e)},null!=e.request&&e.request.abort(),e.request=$.ajax(t))},e.notify=function(e){var t,n;if($("body").addClass("bottom-right"),"object"!=typeof e)return!1;for(t in e)void 0!==(n=$.extend({},!0,{type:"success",delay:8e3,text:""},e[t])).text&&null!=n.text&&$.notify({message:n.text.toString()},{type:n.type,placement:{from:"bottom",align:"right"},z_index:2e3,delay:n.delay,animate:{enter:"animated fadeInDown",exit:"animated fadeOutUp"}})},e.sendBtn=function(t,n){if("object"!=typeof n&&(n={}),!t.hasClass("active")){t.addClass("has-spinner");var i=$.extend({},!0,n);i.url=t.attr("href"),$(".spinner",t).remove(),t.prepend('<span class="spinner"><i class="fa fa-spinner fa-spin"></i></span>'),i.beforeSend=function(){t.addClass("active")},i.success=function(i){t.removeClass("active"),$(".spinner",t).remove(),"success"==i.status?"function"==typeof n.callback&&n.callback(i):"error"==i.status&&e.notify({0:{type:"danger",text:i.message}})},e.ajax(i)}},e.sendFrom=function(t,n,i){if("object"!=typeof i&&(i={}),!t.hasClass("active")){t.addClass("has-spinner");var o=$.extend({},!0,i),a=$(".error-summary",n);o.url=n.attr("action"),o.type="POST",$(".spinner",t).remove(),t.prepend('<span class="spinner"><i class="fa fa-spinner fa-spin"></i></span>'),o.beforeSend=function(){t.addClass("active"),a.length&&(a.hide(),a.html(""))},o.success=function(o){t.removeClass("active"),$(".spinner",t).remove(),"success"==o.status?"function"==typeof i.callback&&i.callback(o):"error"==o.status&&(o.message&&(a.length?(a.html(o.message),a.show()):e.notify({0:{type:"danger",text:o.message}})),o.errors&&$.each(o.errors,function(e,t){alert(t),n.yiiActiveForm("updateAttribute",e,t)}),"function"==typeof i.errorCallback&&i.errorCallback(o))},e.ajax(o)}}},customModule={};window.modules={},$(function(){"object"==typeof window.modules&&$.each(window.modules,function(e,t){void 0!==customModule[e]&&customModule[e].run(t)})}),customModule.adminLayout={run:function(e){$(".dropdown-collapse").on("click",function(e){e.preventDefault(),e.stopPropagation(),$(this).next().hasClass("show")?$($(this).attr("href")).collapse("hide"):$($(this).attr("href")).collapse("show")}),$(function(){$('[data-toggle="tooltip"]').tooltip()}),$(document).ready(function(){var e=document.querySelectorAll(".inputfile");Array.prototype.forEach.call(e,function(e){e.nextElementSibling.innerHTML;e.addEventListener("change",function(e){if((this.files&&this.files.length>1?(this.getAttribute("data-multiple-caption")||"").replace("{count}",this.files.length):e.target.value.split("\\").pop())&&this.files&&this.files[0]){var t=new FileReader;t.onload=function(e){var t='<div class="sommerce-settings__theme-imagepreview">\n                              <a href="#" class="sommerce-settings__delete-image"><span class="fa fa-times-circle-o"></span></a>\n                              <img src="'+e.target.result+'" alt="...">\n                          </div>';$("#image-preview").html(t)},t.readAsDataURL(this.files[0])}}),$(document).on("click",".sommerce_v1.0-settings__delete-image",function(t){$("#image-preview").html("<span></span>"),e.value=""})})}),$(document).ready(function(){$(".edit-seo__title").length>0&&function(){for(var e=["edit-seo__title","edit-seo__meta","edit-seo__url"],t=0;t<e.length;t++)!function(t){$("."+e[t]+"-muted").text($("#"+e[t]).val().length),$("#"+e[t]).on("input",function(n){2==t?$("."+e[t]).text($(n.target).val().replace(/\s+/g,"-")):($("."+e[t]+"-muted").text($(n.target).val().length),$("."+e[t]).text($(n.target).val()))})}(t)}()}),$(document).ready(function(){$("#select-menu-link").change(function(){$(".hide-link").hide();var e=$("#select-menu-link option:selected").val();$(".link-"+e).fadeIn()})}),$(document).ready(function(){$(document).on("click",".delete-properies",function(){$(this).parent().remove()}),$(document).on("click",".add-properies",function(){var e=$(".input-properties").val();e.length&&($(".list-preperties").append('<li class="list-group-item">'+e+' <span class="fa fa-times delete-properies"></span></li>'),$(".input-properties").val(""))})})}},customModule.ordersDetails={run:function(e){$(document).ready(function(){var e=$("#suborder-details-modal"),t=e.find(".modal-title"),n=e.find("#order-detail-provider"),i=e.find("#order-detail-provider-order-id"),o=e.find("#order-detail-provider-response"),a=e.find("#order-detail-lastupdate");e.on("show.bs.modal",function(e){function s(e){t.html("Order "+r+" details"),n.val(e.provider),i.val(e.provider_order_id),o.html(e.provider_response),a.val(e.updated_at)}var r=$(e.relatedTarget).data("suborder-id");void 0===r||isNaN(r)||$.ajax({url:"/admin/orders/get-order-details",type:"GET",data:{suborder_id:r},success:function(e){void 0!==e.details&&s(e.details)},error:function(e,t,n){console.log("Something is wrong!"),console.log(e,t,n)}})}),e.on("hidden.bs.modal",function(e){$(e.currentTarget).find("input").val(""),o.html("")})})}},customModule.frontendLayout={run:function(e){new Swiper(".swiper-container",{pagination:".swiper-pagination",paginationClickable:!0}),new Swiper(".main-slider",{paginationClickable:!0,autoplay:3e3,pagination:".main-slider-pagination"});$(window).width()>991&&$(".navbar .dropdown").hover(function(){$(this).find(".dropdown-menu").first().stop(!0,!0).slideDown(150)},function(){$(this).find(".dropdown-menu").first().stop(!0,!0).slideUp(105)})}};
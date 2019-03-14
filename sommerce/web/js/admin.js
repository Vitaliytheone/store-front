var custom = new function() {
    var self = this;

    self.request = null;

    self.confirm = function (title, text, options, callback, cancelCallback) {
        var confirmPopupHtml;
        var compiled = templates['global/modal/confirm'];
        confirmPopupHtml = compiled($.extend({}, true, {
            confirm_button : 'OK',
            cancel_button : 'Cancel',
            width: '600px'
        }, options, {
            'title': title,
            'confirm_message': text
        }));

        $(window.document.body).append(confirmPopupHtml);
        $('#confirmModal').modal({});

        $('#confirmModal').on('hidden.bs.modal', function (e) {
            $('#confirmModal').remove();

            if ('function' == typeof cancelCallback) {
                return cancelCallback.call();
            }
        });

        return $('#confirm_yes').on('click', function (e) {
            $("#confirm_yes").unbind("click");
            $('#confirmModal').modal('hide');
            return callback.call();
        });
    };

    self.ajax = function(options) {
        var settings = $.extend({}, true, options);
        if ("object" === typeof options) {
            options.beforeSend = function() {
                if ('function' === typeof settings.beforeSend) {
                    settings.beforeSend();
                }
            };
            options.success = function(response) {
                if ('function' === typeof settings.success) {
                    settings.success(response);
                }
            };
            null         != self.request ? self.request.abort() : '';
            self.request = $.ajax(options);
        }
    }

    self.notify = function(notifyData) {
        var notifyContainer = $('body'),
            key, value;
        notifyContainer.addClass('bottom-right');

        if ('object' != typeof notifyData) {
            return false;
        }
        for (key in notifyData) {

            value = $.extend({}, true, {
                type	: 'success',
                delay	: 8000,
                text	: '',
            }, notifyData[key]);

            if ('undefined' == typeof value.text || null == value.text) {
                continue;
            }

            $.notify({
                message	: value.text.toString(),
            }, {
                type: value.type,
                placement: {
                    from: "bottom",
                    align: "right"
                },
                z_index : 2000,
                delay: value.delay,
                animate: {
                    enter: 'animated fadeInDown',
                    exit: 'animated fadeOutUp'
                }
            });
        }
    }

    self.sendBtn = function(btn, settings)
    {
        if ('object' != typeof settings) {
            settings = {};
        }

        if (btn.hasClass('active')) {
            return;
        }

        btn.addClass('has-spinner');

        var options = $.extend({}, true, settings);

        options.url = btn.attr('href');

        $('.spinner', btn).remove();

        btn.prepend('<span class="spinner"><i class="fa fa-spinner fa-spin"></i></span>');

        options.beforeSend = function() {
            btn.addClass('active');
        };

        options.success = function(response) {
            btn.removeClass('active');
            $('.spinner', btn).remove();

            if ('success' == response.status) {
                if ('function' === typeof settings.callback) {
                    settings.callback(response);
                }
            } else if ('error' == response.status) {
                self.notify({0: {
                    type : 'danger',
                    text : response.message
                }});
            }
        };

        self.ajax(options);
    }

    self.sendFrom = function(btn, form, settings)
    {
        if ('object' != typeof settings) {
            settings = {};
        }

        if (btn.hasClass('active')) {
            return;
        }

        btn.addClass('has-spinner');

        var options = $.extend({}, true, settings);
        var errorSummary = $('.error-summary', form);

        options.url = form.attr('action');
        options.type = 'POST';

        $('.spinner', btn).remove();

        btn.prepend('<span class="spinner"><i class="fa fa-spinner fa-spin"></i></span>');

        options.beforeSend = function() {
            btn.addClass('active');

            if (errorSummary.length) {
                errorSummary.addClass('hidden');
                errorSummary.html('');
            }
        };

        options.success = function(response) {
            btn.removeClass('active');
            $('.spinner', btn).remove();

            if ('success' == response.status) {
                if ('function' === typeof settings.callback) {
                    settings.callback(response);
                }
            } else if ('error' == response.status) {
                if (response.message) {

                    if (errorSummary.length) {
                        errorSummary.html(response.message);
                        errorSummary.removeClass('hidden');
                    } else {
                        self.notify({0: {
                            type : 'danger',
                            text : response.message
                        }});
                    }
                }

                if (response.errors) {
                    $.each(response.errors, function(key, val) {
                        alert(val);
                        form.yiiActiveForm('updateAttribute', key, val);
                    });
                }

                if ('function' === typeof settings.errorCallback) {
                    settings.errorCallback(response);
                }
            }
        };

        self.ajax(options);
    };

    /**
     * Generate Url path from string
     * a-z, -_ ,0-9
     * @param string
     */
    self.generateUrlFromString = function(string)
    {
        var url = string.trim().replace(/^-+|-+$/gmi, '').replace(/[^a-z0-9_\-\s]/gmi, '').replace(/[_\s]+/g, '-').replace(/-+/g, '-').toLowerCase();

        if (url === '-' || url === '_') {
            url = '';
        }

        return url;
    };

    /**
     * Generate unique url
     * @param url
     * @param exitingUrls
     * @returns {*}
     */
    self.generateUniqueUrl = function(url, exitingUrls)
    {
        var generatedUrl = url,
            exiting,
            prefixCounter;

        prefixCounter = 1;

        do {
            exiting = _.find(exitingUrls, function(exitingUrl){
                return exitingUrl === generatedUrl;
            });

            if (exiting) {
                generatedUrl = url + '-' + prefixCounter;
                prefixCounter ++;
            }
        }
        while (exiting);

        return generatedUrl;
    };

};
var customModule = {};
window.modules = {};

$(function() {
    if ('object' == typeof window.modules) {
        $.each(window.modules, function(name, options) {
            if ('undefined' != typeof customModule[name]) {
                customModule[name].run(options);
            }
        });
    }
});
                var templates = templates || {};
                

templates['global/modal/confirm'] = _.template("<div class=\"modal fade confirm-modal\" id=\"confirmModal\" tabindex=\"-1\" data-backdrop=\"static\">\n    <div class=\"modal-dialog modal-md\" role=\"document\">\n        <div class=\"modal-content\">\n            <% if (typeof(confirm_message) !== \"undefined\" && confirm_message != \'\') { %>\n            <div class=\"modal-header\">\n                <h3 id=\"conrirm_label\"><%= title %><\/h3>\n                <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\"><span aria-hidden=\"true\">&times;<\/span><\/button>\n            <\/div>\n\n            <div class=\"modal-body\">\n                <p><%= confirm_message %><\/p>\n            <\/div>\n\n\n            <div class=\"modal-footer justify-content-start\">\n                <button class=\"btn btn-primary m-btn--air\" id=\"confirm_yes\"><%= confirm_button %><\/button>\n                <button class=\"btn btn-secondary m-btn--air\" data-dismiss=\"modal\" aria-hidden=\"true\"><%= cancel_button %><\/button>\n            <\/div>\n            <% } else { %>\n            <div class=\"modal-body\">\n                <div class=\"text-center\">\n                    <h3 id=\"conrirm_label\"><%= title %><\/h3>\n                <\/div>\n\n                <div class=\"text-center\">\n                    <button class=\"btn btn-primary m-btn--air\" id=\"confirm_yes\"><%= confirm_button %><\/button>\n                    <button class=\"btn btn-secondary m-btn--air\" data-dismiss=\"modal\" aria-hidden=\"true\"><%= cancel_button %><\/button>\n                <\/div>\n            <\/div>\n            <% } %>\n        <\/div>\n    <\/div>\n<\/div>");
/**
 * /admin/settings custom js module
 * @type {{run: customModule.settings.run}}
 */
customModule.adminGeneral = {
    run: function (params) {

        /******************************************************************
         *            General settings SEO part interaction
         *******************************************************************/
        if ($('.edit-seo__title').length > 0) {
            (function () {

                var $storeName = $('#store-name'),
                    $seoTitle = $('#edit-seo__title');

                var seoTitleOnInit = $seoTitle.val(),
                    seoTitleTouched = false;

                var seoEdit = ['edit-seo__title', 'edit-seo__meta'];
                var _loop = function _loop(i) {
                    $("." + seoEdit[i] + '-muted').text($("#" + seoEdit[i]).val().length);

                    $("#" + seoEdit[i]).on('input', function (e) {
                        $("." + seoEdit[i] + '-muted').text($(e.target).val().length);
                        $('.' + seoEdit[i]).text($(e.target).val());
                    }).trigger('input');

                };
                for (var i = 0; i < seoEdit.length; i++) {
                    _loop(i);
                }

                $seoTitle.on('focus', function (e){
                   seoTitleTouched = true;
                });

                $storeName.on('input', function(e){
                    if (seoTitleOnInit !== '' || seoTitleTouched) {
                        return;
                    }
                    $seoTitle.val($(this).val()).trigger('input');
                });

            })();
        }

        /******************************************************************
         *            General settings delete logo & favicon
         ******************************************************************/
        var $modal = $('#delete-modal'),
        $deleteBtn = $modal.find('#delete-image');

        $modal.on('show.bs.modal', function (event){
            var button = $(event.relatedTarget),
            actionUrl = button.attr('href');
            $deleteBtn.attr('href', actionUrl);
        });

        $modal.on('hidden.bs.modal', function (){
            $deleteBtn.attr('href', null);
        });


        /******************************************************************
         *            General settings favicon & logo
         ******************************************************************/
        $(document).ready(function () {
            $('.settings-file').on('change', function () {

                var dataTarget = $(this).attr('data-target'),
                    that = this,
                    template = '';

                if (that.files && that.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        template = '<div class="sommerce-settings__theme-imagepreview"><img src="'+e.target.result+'" alt="'+that.files[0].name+'" id="setting-logo__preview"></div>'
                        $(dataTarget).html(template);
                    };

                    reader.readAsDataURL(that.files[0]);
                }
            });
        });
    }
};
/**
 * /admin/settings/payments custom js module
 * @type {{run: customModule.settings.run}}
 */
customModule.adminIntegrations = {
    run: function (params) {
        /******************************************************************
         *            Toggle `store integration` active status
         ******************************************************************/
        $(document).on('change', '.toggle-active', function (e) {
            var $checkbox = $(e.currentTarget),
                actionUrl = $checkbox.data('action_url'),
                active = $checkbox.prop('checked') | 0;
                category = $checkbox.data('category');

            $.ajax({
                url: actionUrl,
                type: "POST",
                data: {
                    active: active
                },
                success: function (data, textStatus, jqXHR) {
                    $('.toggle-active' + '.' + category).not($checkbox).prop('checked', false);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log('Error on update', jqXHR, textStatus, errorThrown);
                }
            });
        });

        $(document).on('click', '#editIntegrationButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#editIntegrationForm');
            var errorBlock = $('#editIntegrationError', form);

            errorBlock.addClass('hidden');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {

                    if ('success' == response.status) {
                        location.replace(response.redirect);
                    }

                    if ('error' == response.status) {
                        errorBlock.removeClass('hidden');
                        errorBlock.html(response.error);
                    }
                }
            });

            return false;
        });
    }
};
customModule.adminStoreLanguages = {
    run: function(params) {

        /*****************************************************************************************************
         *                     Activate store language
         *****************************************************************************************************/
        (function (window, alert){
            'use strict';

            var actionUrl = params.action_activate_lang_url;

            var $langCheckboxes = $('.language-checkbox');

            $langCheckboxes.on('change', function(){

                var langCode = $(this).val();

                $.ajax({
                    url: actionUrl + langCode,
                    type: 'GET',
                    success: function (data, textStatus, jqXHR){
                        if (data.code !== langCode){
                            console.log('Error on updating store language!');
                        } else {
                            // console.log('Store language updated!');
                            // $(location).attr('href', successRedirectUrl);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown){
                        console.log('Error on updating store language!', jqXHR, textStatus, errorThrown);
                    }
                });
            });

        })({}, function (){});

        /*****************************************************************************************************
         *                     Add store language
         *****************************************************************************************************/
        (function (window, alert){
            'use strict';

            var actionUrl = params.action_add_lang_url,
                successUrl = params.success_redirect_url,
                errorUrl = successUrl;

            var $modal = $('.add-language-modal'),
                $form = $('.form-add-language'),
                $languagesList = $('.form_field__languages_list'),
                $submit = $form.find('.btn_submit'),
                $modalLoader = $('.modal-loader');

            $languagesList.on('change', function(event){
                var code = $(this).val();
                $submit.prop('disabled', !code);
            });

            $form.on('submit', function(event){

                event.preventDefault();

                var formData = $(this).serializeArray(),
                    code = $languagesList.find("option:selected").val();

                loading(true);

                $.ajax({
                    url: actionUrl + code,
                    type: 'GET',
                    success: function (data, textStatus, jqXHR){
                        if (data.result !== true){
                            console.log('Error on add store language!');
                        }
                        $(location).attr('href', errorUrl);
                    },
                    error: function (jqXHR, textStatus, errorThrown){
                        loading(false);
                        console.log('Error on updating store language!', jqXHR, textStatus, errorThrown);
                    }
                });
            });

            function loading(toggle) {
                $modalLoader.toggleClass('hidden', !toggle);
                $submit.prop('disabled', toggle);
            }

            loading(false);
            $languagesList.trigger('change');

        })({}, function (){});

    }
};



customModule.adminPages = {
    run: function (params) {
        var existingUrls = params['existingUrls'];

        $('.dropdown-collapse').on('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            if ($(this).next().hasClass('show')) {
                $($(this).attr('href')).collapse('hide');
            } else {
                $($(this).attr('href')).collapse('show');
            }
        });

        $(function () {
            $('[data-toggle="tooltip"]').tooltip();

            $('.sommerce-dropdown__delete-cancel').click(function () {
                $(".sommerce-dropdown__delete").hide();
            });
        });

        var inputs = document.querySelectorAll('.inputfile');
        Array.prototype.forEach.call(inputs, function (input) {
            var label = input.nextElementSibling,
                labelVal = label.innerHTML;

            input.addEventListener('change', function (e) {
                var fileName = '';
                if (this.files && this.files.length > 1) {
                    fileName = (this.getAttribute('data-multiple-caption') || '').replace('{count}', this.files.length);
                } else {
                    fileName = e.target.value.split('\\').pop();
                }
                if (fileName) {
                    //label.querySelector('span').innerHTML = fileName;
                    if (this.files && this.files[0]) {

                        var reader = new FileReader();

                        reader.onload = function (e) {
                            var template = '<div class="sommerce-settings__theme-imagepreview">\n                              <a href="#" class="sommerce-settings__delete-image"><span class="fa fa-times-circle-o"></span></a>\n                              <img src="' + e.target.result + '" alt="...">\n                          </div>';
                            $('#image-preview').html(template);
                        };
                        reader.readAsDataURL(this.files[0]);
                    }
                } else {
                    //label.innerHTML = labelVal;
                }
            });
            $(document).on('click', '.sommerce_v1.0-settings__delete-image', function (e) {
                $('#image-preview').html('<span></span>');
                input.value = '';
            });
        });


        if ($('.edit-seo__title').length > 0) {
            (function () {

                var seoEdit = ['edit-seo__title', 'edit-seo__meta', 'edit-seo__url'];

                var _loop = function _loop(i) {
                    if ($("#" + seoEdit[i]).length) {
                        $("." + seoEdit[i] + '-muted').text($("#" + seoEdit[i]).val().length);
                    }
                    $("#" + seoEdit[i]).on('input', function (e) {
                        if (i == 2) {
                            $('.' + seoEdit[i]).text($(e.target).val().replace(/\s+/g, '-'));
                        } else {
                            $("." + seoEdit[i] + '-muted').text($(e.target).val().length);
                            $('.' + seoEdit[i]).text($(e.target).val());
                        }
                    });
                };

                for (var i = 0; i < seoEdit.length; i++) {
                    _loop(i);
                }
            })();
        }



        $('#select-menu-link').change(function () {
            $('.hide-link').hide();
            var val = $("#select-menu-link option:selected").val();
            $('.link-' + val).fadeIn();
        });


        $('#btn-new-page').click(function(e){
            $('#createPageError').addClass('hidden');
            var flag = true;
            var $this = $(this);
            var $name = $('#editpageform-name');
            $name.val('');
            $name.trigger('input');
            $('#check-visibility').prop('checked', 'checked');
            $('.btn-modal-delete').hide();
            var $keyword = $('#edit-seo__meta-keyword');
            $keyword.val('');
            $keyword.trigger('input');
            var $meta = $('#edit-seo__meta');
            $meta.val('');
            $meta.trigger('input');
            var  $title = $('#edit-seo__title')
            $title.val('');
            $title.trigger('input');
            $('#seo-block').removeClass('show');

            $name.on('input', function(e) {
                var generatedUrl = custom.generateUrlFromString($(this).val());
                generatedUrl = custom.generateUniqueUrl(generatedUrl, existingUrls);
                if (flag) {
                    var $url = $('#edit-seo__url');
                    $url.val(generatedUrl);
                    $url.trigger('input', true);
                }
            });

            var $url = $('#edit-seo__url');
            $url.val('');
            $url.trigger('input', true);

            $url.on('input', function (e, data) {
                if (!data) {
                    flag = false;
                }
            });

            $('#pageForm').attr('action', $this.data('action'));
            $('#exampleModalLabel').text($this.data('modal-title'));
            $('#page-submit').text($this.data('modal-title'));

        });


        $('#pageForm').submit(function (event){
            event.preventDefault();
            var btn = $('#page-submit');
            var form = $(this);

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback: function() {
                    $('#modal-create-page').modal('hide');
                    location.reload();
                }
            });

            return false;
        });

        $('.edit-page').click(function(e) {
            e.preventDefault();
            $('#createPageError').addClass('hidden');
            var $this =  $(this);
            var page = $this.data('page');
            var $name = $('#editpageform-name');
            $name.off('input');
            $name.val(page.name);

            if (parseInt(page.visibility)) {
                $('#check-visibility').prop('checked', 'checked');

            } else {
                $('#check-visibility').prop('checked', false);
            }

            if (page['can_delete']) {
                $('.btn-modal-delete').show();
            }

            var $keyword = $('#edit-seo__meta-keyword');
            $keyword.val(page.seo_keywords);
            $keyword.trigger('input');
            var $meta = $('#edit-seo__meta');
            $meta.val(page.seo_description);
            $meta.trigger('input');
            var  $title = $('#edit-seo__title')
            $title.val(page.seo_title);
            $title.trigger('input');
            $('#seo-block').removeClass('show');

            var $url = $('#edit-seo__url');
            $url.val(page.url);
            $url.trigger('input');

            $('#pageForm').attr('action', $this.data('action'));
            $('#exampleModalLabel').text($this.data('modal-title'));
            $('#page-submit').text($this.data('modal-title'));
            $('.delete-page').data('params', page);

            $('#modal-create-page').modal('show');
        });

        $('.delete-page').click(function(e) {

            var $related = $(this);
            var data = $related.data('params');
            e.preventDefault();

            if (!data['can_delete']) {
                return false;
            }

            var queryParams = {};
            queryParams.id = data.id;

            custom.confirm(params['confirm_message'], '', {}, function () {
                custom.sendBtn($related, {
                    data: addTokenParams(queryParams),
                    type: 'POST',
                    callback: function () {
                        location.reload();
                    }
                });
                return false;
            });
        });

        $('.duplicate-page').click(function(e) {
            e.preventDefault();
            var $related = $(this);
            var page = $related.data('page');
            var $modal = $('#modal-duplicate');
            $modal.data('page', page);
            $('#feature-duplicate').attr('href', $related.data('action'));
            $modal.modal('show');
            return false;
        });

        $('#feature-duplicate').click(function(e){
            e.preventDefault();
            var $this = $(this);
            var page = $('#modal-duplicate').data('page');
            var queryParams = {}
            queryParams.id = page.id;

            var generatedUrl = custom.generateUrlFromString(page.name);
            generatedUrl = custom.generateUniqueUrl(generatedUrl, existingUrls);
            queryParams.url = generatedUrl;
            custom.sendBtn($this, {
                data: addTokenParams(queryParams),
                type: 'POST',
                callback: function () {
                    location.reload();
                }
            });

            return false;
        });

        function addTokenParams($obj) {
            var csrfToken = $('meta[name="csrf-token"]').attr("content"),
                csrfParam = $('meta[name="csrf-param"]').attr("content");

            $obj[csrfParam] = csrfToken;

            return $obj;
        }
    }



};

/**
 * /admin/settings/payments custom js module
 * @type {{run: customModule.settings.run}}
 */
customModule.adminPayments = {
    run: function (params) {
        /******************************************************************
         *            Toggle `payment method` active status
         ******************************************************************/
        $(document).on('change', '.toggle-active', function (e) {
            var $checkbox = $(e.currentTarget),
                actionUrl = $checkbox.data('action_url'),
                method = $checkbox.data('payment_method'),
                active = $checkbox.prop('checked') | 0;

            if (active == true) {
                $('#met-'+method).removeClass('text-muted');
            } else {
                $('#met-'+method).addClass('text-muted');
            }

            $.ajax({
                url: actionUrl,
                type: "POST",
                data: {
                    active: active
                },
                success: function (data, textStatus, jqXHR) {
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log('Error on update', jqXHR, textStatus, errorThrown);
                }
            });
        });

        $('.add-method').click(function(e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#addPaymentMethodModal');
            var form = $('#addPaymentMethodForm');
            var errorBlock = $('#addPaymentMethodError', form);

            errorBlock.addClass('hidden');
            errorBlock.html('');

            form.attr('action', link.attr('href'));

            modal.modal('show');

            return false;
        });

        $(document).on('click', '#addPaymentMethodButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#addPaymentMethodForm');
            var errorBlock = $('#addPaymentMethodError', form);

            errorBlock.addClass('hidden');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {

                    if ('success' == response.status) {
                        $('#editCustomerModal').modal('hide');
                        location.reload();
                    }

                    if ('error' == response.status) {
                        errorBlock.removeClass('hidden');
                        errorBlock.html(response.error);
                    }
                }
            });

            return false;
        });

        $(document).on('click', '.add-multi-input', function(e) {
            e.preventDefault();

            var link = $(this);
            var elementClass = link.data('class');
            var container = $('#multi_input_container_descriptions');
            var label = link.data('label');
            var elementName = link.data('name');
            var elementId = link.data('id');

            if (!container.length) {
                return false;
            }

            var inputTemplate = templates['payments/description'];
            container.append(inputTemplate({
                "elementId":elementId,
                "label":label,
                "elementClass":elementClass,
                "elementName":elementName
            }));

            return false;
        });

        $(document).on('click', '.remove-description', function(e) {
            e.preventDefault();

            var element = $(this);

            element.parents('.form-group-description').remove();

            return false;
        });

        $('#editPaymentMethodOptions textarea').summernote({
            dialogsInBody: true,
            minHeight: 200,
            toolbar: [
                ['style', ['style', 'bold', 'italic']],
                ['lists', ['ul', 'ol']],
                ['para', ['paragraph']],
                ['color', ['color']],
                ['insert', ['link', 'picture', 'video']],
                ['codeview', ['codeview']]
            ],
            disableDragAndDrop: true,
            styleTags: ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
            popover: {
                image: [
                    ['float', ['floatLeft', 'floatRight', 'floatNone']],
                    ['remove', ['removeMedia']]
                ]
            },
            dialogsFade: true
        });

        /*****************************************************************************************************
         *                     Update payment position
         *****************************************************************************************************/
        (function (window, alert){

            var updatePositionUrl = params.action_update_pos;

            var $neatable = $('#nestable');
            if ($neatable.length > 0) {

                $neatable.nestable({
                    maxDepth: 1,
                    handleClass: 'dd-handle-pay',
                }).on('change', updater);

            }

            function updater(e) {

                var positions = $neatable.nestable('serialize');

                $.ajax({
                    url: updatePositionUrl,
                    type: "POST",
                    data: {
                        positions: positions
                    },
                    success: function(data, textStatus, jqXHR) {
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log('Something was wrong...', textStatus, errorThrown, jqXHR);
                    }
                });

            }

        })({}, function (){});
    }
};
customModule.adminEditNotification = {
    run : function(params) {
        var self = this;

        var codeMirroSetting = {},
            codeType = 'twig';

        switch (codeType){
            case 'twig':
                codeMirroSetting = {
                    mode : "text/html",
                    lineNumbers : true,
                    profile: 'xhtml',
                    lineWrapping: true,
                    extraKeys: {"Ctrl-Q": function(cm){ cm.foldCode(cm.getCursor()); }},
                    foldGutter: true,
                    gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"]
                };
                break;
            case 'css':
                codeMirroSetting = {
                    mode : "text/css",
                    lineNumbers : true,
                    lineWrapping: true,
                    extraKeys: {"Ctrl-Q": function(cm){ cm.foldCode(cm.getCursor()); }},
                    foldGutter: true,
                    gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"]
                };
                break;
            case 'js':
                codeMirroSetting = {
                    mode : "text/javascript",
                    lineNumbers : true,
                    lineWrapping: true,
                    extraKeys: {"Ctrl-Q": function(cm){ cm.foldCode(cm.getCursor()); }},
                    foldGutter: true,
                    gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"]
                };
                break;
            default:
                codeMirroSetting = {
                    lineNumbers : true,
                    lineWrapping: true,
                    foldGutter: true,
                    gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"]
                };
                break;
        }

        CodeMirror.fromTextArea(document.getElementById("code"), codeMirroSetting);

        $(document).on('click', '.confirm-link', function (e) {
            e.preventDefault();

            var btn = $(this);

            custom.confirm(btn.data('message'), undefined, {
                confirm_button : btn.data('confirm_button'),
                cancel_button : btn.data('cancel_button')
            }, function() {
                location.href = btn.data('href');
            });

            return false;
        });

        $(document).on('click', '.confirm-link', function (e) {
            e.preventDefault();

            var btn = $(this);

            custom.confirm(btn.data('message'), undefined, {
                confirm_button : btn.data('confirm_button'),
                cancel_button : btn.data('cancel_button')
            }, function() {
                location.href = btn.attr('href');
            });

            return false;
        });

        $(document).on('click', '.send-test-notification', function(e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#sendTestNotificationModal');
            var form = $('#sendTestNotificationForm', modal);
            var errorBlock = $('#sendTestNotificationError', form);
            form.attr('action', link.attr('href'));

            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('select', form).prop('selectedIndex',0);

            modal.modal('show');

            return false;
        });

        $(document).on('click', '#sendTestNotificationButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var modal = $('#sendTestNotificationModal');
            var form = $('#sendTestNotificationForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    if ('success' == response.status) {
                        $('#sendTestNotificationModal').modal('hide');
                        customModule.adminNotifyLayout.send({
                            success: response.message
                        });
                    }
                }
            });

            return false;
        });


        $(document).on('click', '.notification-preview', function(e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#notificationPreviewModal');
            var iframe = $('iframe', modal);
            var container = $('.modal-body', modal);

            iframe.attr('src', link.attr('href'));

            container.addClass('image-loader');

            modal.modal('show');

            return false;
        });

        $('#notificationPreviewModal iframe').on('load', function() {
            var modal = $('#notificationPreviewModal');
            $('.modal-body', modal).removeClass('image-loader');
        });
    }
};
customModule.adminLayout = {
    run : function(params) {
        var self = this;

        $('.dropdown-collapse').on('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            if ($(this).next().hasClass('show')) {
                $($(this).attr('href')).collapse('hide');
            } else {
                $($(this).attr('href')).collapse('show');
            }
        });

        var inputs = document.querySelectorAll('.inputfile');
        Array.prototype.forEach.call(inputs, function (input) {
            var label = input.nextElementSibling,
                labelVal = label.innerHTML;

            input.addEventListener('change', function (e) {
                var fileName = '';
                if (this.files && this.files.length > 1) {
                    fileName = (this.getAttribute('data-multiple-caption') || '').replace('{count}', this.files.length);
                } else {
                    fileName = e.target.value.split('\\').pop();
                }
                if (fileName) {
                    //label.querySelector('span').innerHTML = fileName;
                    if (this.files && this.files[0]) {

                        var reader = new FileReader();

                        reader.onload = function (e) {
                            var template = '<div class="sommerce-settings__theme-imagepreview">\n                              <a href="#" class="sommerce-settings__delete-image"><span class="fa fa-times-circle-o"></span></a>\n                              <img src="' + e.target.result + '" alt="...">\n                          </div>';
                            $('#image-preview').html(template);
                        };
                        reader.readAsDataURL(this.files[0]);
                    }
                } else {
                    //label.innerHTML = labelVal;
                }
            });
            $(document).on('click', '.sommerce_v1.0-settings__delete-image', function (e) {
                $('#image-preview').html('<span></span>');
                input.value = '';
            });
        });

        // TODO:: Commented because conflicted with products page DOM objects. Must be rewrite.
        // /* Edit page */
        // $(document).ready(function () {
        //
        //     if ($('.edit-seo__title').length > 0) {
        //         (function () {
        //
        //             var seoEdit = ['edit-seo__title', 'edit-seo__meta', 'edit-seo__url'];
        //
        //             var _loop = function _loop(i) {
        //                 $("." + seoEdit[i] + '-muted').text($("#" + seoEdit[i]).val().length);
        //                 $("#" + seoEdit[i]).on('input', function (e) {
        //                     if (i == 2) {
        //                         $('.' + seoEdit[i]).text($(e.target).val().replace(/\s+/g, '-'));
        //                     } else {
        //                         $("." + seoEdit[i] + '-muted').text($(e.target).val().length);
        //                         $('.' + seoEdit[i]).text($(e.target).val());
        //                     }
        //                 });
        //             };
        //
        //             for (var i = 0; i < seoEdit.length; i++) {
        //                 _loop(i);
        //             }
        //         })();
        //     }
        // });


        // $('#select-menu-link').change(function () {
        //     $('.hide-link').hide();
        //     var val = $("#select-menu-link option:selected").val();
        //     $('.link-' + val).fadeIn();
        // });
        //

        $('[data-toggle="tooltip"]').tooltip();
    }
};
customModule.adminNotifications = {
    run : function(params) {
        var self = this;

        $(document).on('change', '.change-status', function(e) {
            e.preventDefault();

            var checkbox = $(this);
            var enableUrl = checkbox.data('enable');
            var disableUrl = checkbox.data('disable');
            var url = undefined;

            if (checkbox.prop('checked')) {
                url = enableUrl;
            } else {
                url = disableUrl;
            }

            custom.ajax({
                url: url
            });

            return false;
        });

        $(document).on('click', '.create-email, .edit-email', function(e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#createAdminEmailModal');
            var form = $('#createAdminEmailForm', modal);
            var errorBlock = $('#createAdminEmailError', form);
            var header = link.data('header');
            var email = link.data('email');
            form.attr('action', link.attr('href'));

            $('.modal-title', modal).html(header);

            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('input', form).val('');

            if (link.hasClass('edit-email')) {
                $('#editadminemailform-email', form).val(email);
            }

            modal.modal('show');

            return false;
        });

        $(document).on('click', '#createAdminEmailButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#createAdminEmailForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#createAdminEmailModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });

        $(document).on('click', '.delete-email', function(e) {
            e.preventDefault();

            var link = $(this);
            var url = link.attr('href');
            var modal = $('#deleteAdminEmailModal');
            var form = $('#deleteAdminEmailForm', modal);
            form.attr('action', link.attr('href'));

            modal.modal('show');

            return false;
        });

        $(document).on('click', '#deleteAdminEmailButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#deleteAdminEmailForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#deleteAdminEmailModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });
    }
};
customModule.adminNotifyLayout = {
    run : function(params) {
        var self = this;

        /*****************************************************************************************************
         *                     Popup notifications init
         *****************************************************************************************************/
        toastr.options = {
            "closeButton": false,
            "debug": false,
            "newestOnTop": false,
            "progressBar": false,
            "positionClass": "toast-bottom-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "5000",
            "timeOut": "5000",
            "extendedTimeOut": "5000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };


        /*****************************************************************************************************
         *                     Page notifications init
         *****************************************************************************************************/
        /* Check if page have messages */
        var messages = params.messages || null;

        if (!params.messages) {
            return;
        }

        _.forEach(params.messages, function(message) {
            self.send(message);
        });
    },
    send: function (message) {
        if (message.success) {
            toastr.success(message.success);
        }
        if (message.warning) {
            toastr.warning(message.warning);
        }
        if (message.error) {
            toastr.error(message.error);
        }
    }
};
/**
 * Order details custom js module
 * @type {{run: customModule.ordersDetails.run}}
 */
customModule.ordersDetails = {
    run : function(params) {
        $(document).ready(function () {
            var ajaxEndpoint = '/admin/orders/get-order-details';
            var $detailsModal = $('#suborder-details-modal'),
                $modalTitle = $detailsModal.find('.modal-title'),
                $provider = $detailsModal.find('#order-detail-provider'),
                $providerOrderId = $detailsModal.find('#order-detail-provider-order-id'),
                $providerResponse = $detailsModal.find('#order-detail-provider-response'),
                $providerUpdate = $detailsModal.find('#order-detail-lastupdate'),
                $modalLoader = $detailsModal.find('.modal-loader');

            $detailsModal.on('show.bs.modal', function(e) {
                var $target = $(e.relatedTarget),
                    suborderId = $target.data('suborder-id'),
                    modalTitle = $target.data('modal_title');

                if (suborderId === undefined || isNaN(suborderId)) {
                    return;
                }
                $modalLoader.removeClass('hidden');
                $.ajax({
                    url: ajaxEndpoint,
                    type: "GET",
                    data: {
                        'suborder_id': suborderId
                    },
                    success: function (data) {
                        $modalLoader.addClass('hidden');
                        if (data.details === undefined) {
                            return;
                        }
                        renderLogs(data.details);
                    },
                    error: function(jqXHR, textStatus, errorThrown){
                        console.log('Something is wrong!');
                        console.log(jqXHR, textStatus, errorThrown);
                        $modalLoader.addClass('hidden');
                    }
                });

                function renderLogs(details){
                    $modalTitle.html(modalTitle);
                    $provider.val(details.provider);
                    $providerOrderId.val(details.provider_order_id);
                    $providerResponse.html(details.provider_response);
                    $providerUpdate.val(details.updated_at);
                }
            });

            $detailsModal.on('hidden.bs.modal',function(e) {
                var $currentTarget = $(e.currentTarget);
                $currentTarget.find('input').val('');
                $providerResponse.html('');
            });
        });
    }
};

/**
 * Order clipboard custom js module
 * @type {{run: customModule.ordersClipboard.run}}
 */
customModule.ordersClipboard = {
    run : function(params) {

        var messageCopied = params.messageCopied || 'Copied!';

        $(document).ready(function () {
            var ClipboardDemo = function () {
                var n = function n() {
                    new Clipboard("[data-clipboard=true]").on("success", function (n) {
                        n.clearSelection();
                        // Check toastr notification plugin
                        if (toastr === undefined) {
                            alert("Copied!");
                        }   else {
                            toastr.options = {
                                "positionClass": "toast-bottom-right"
                            };
                            toastr.success(messageCopied);
                        }
                    });
                };return { init: function init() {
                    n();
                } };
            }();jQuery(document).ready(function () {
                ClipboardDemo.init();
            });
        });
    }
};

/**
 * Order change status custom js module
 * @type {{run: customModule.ordersModalAlerts.run}}
 */
customModule.ordersModalAlerts = {
    run : function(params) {

        var $modals = $('.order_modal_alert');

        $modals.on('show.bs.modal', function(event){
            var $modal = $(this),
                $target = $(event.relatedTarget);
            var actionUrl = $target.data('action_url');

            $modal.find('.submit_action').attr('href', actionUrl);
        });
    }
};
/**
 * Payments custom js module
 */
customModule.payments = {
    run: function (params) {

        /******************************************************************
         *                    Get payment details
         ******************************************************************/

        var $modal = $('.payments_detail'),
            $modalTitle = $modal.find('.modal-title'),
            $detailsContainer = $modal.find('.details-container'),
            $modalLoader = $modal.find('.modal-loader');

        $modal.on('show.bs.modal', function (e) {
            var $target = $(e.relatedTarget),
                paymentId = $target.data('id'),
                modalTitle = $target.data('modal_title'),
                actionUrl = $target.data('action_url');

            if (paymentId === undefined || actionUrl === undefined ) {
                return;
            }

            $modalLoader.removeClass('hidden');
            $.ajax({
                url: actionUrl,
                type: "GET",
                success: function (data) {
                    $modalLoader.addClass('hidden');
                    if (data === undefined) {
                        return;
                    }
                    renderLogs(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log('Something is wrong!');
                    console.log(jqXHR, textStatus, errorThrown);
                    $modalLoader.addClass('hidden');
                    $modal.modal('hide');
                }
            });

            function renderLogs(details) {
                $modalTitle.html(modalTitle);
                _.each(details, function(detail){
                    $detailsContainer.append('<pre class="sommerce-pre details-item">' + detail.time  + '<br><br>' + detail.data + '</pre>');
                });
            }
        });

        $modal.on('hidden.bs.modal', function (e) {
            $modalTitle.html('');
            $detailsContainer.empty();
        });
    }
};


customModule.adminProducts = {
    run : function(params) {
        var self = this;
        var exitingUrls = params.exitingUrls;

        $(document).on('click', '.duplicate-package', function(e) {
            e.preventDefault();
            var btn = $(this);
            var confirmBtn = $('#confirm_yes');

            custom.confirm(btn.data('confirm-title'), undefined, {}, function () {
                custom.sendBtn(btn, {
                    data: self.getTokenParams(),
                    method: 'POST',
                    callback : function(response) {
                        location.reload();
                    }
                });
            });

            return false;
        });

        $('#createproductform-name').keyup(function(e) {
            var name = $(this).val();
            var createPageUrl = $('#createPageUrl');
            var urlInput = $('#createproductform-url');
            var generatedUrl;
            generatedUrl = custom.generateUrlFromString(name);
            generatedUrl = custom.generateUniqueUrl(generatedUrl, exitingUrls);

            createPageUrl.text(generatedUrl);
            urlInput.val(generatedUrl);
        });

        $("input[type=number]").on('change',function(){
            this.value = parseFloat(this.value).toFixed(2);
        });

        $(document).on('click', '#createProduct', function(e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#createProductModal');
            var form = $('#createProductForm', modal);
            var errorBlock = $('#createProductError', form);

            form.attr('action', link.attr('href'));
            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('input[type="text"]', form).val('');
            $('input[type="checkbox"]', form).prop('checked', false);

            modal.modal('show');

            return false;
        });

        $(document).on('click', '#createProductButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#createProductForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#createProductModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });

        $(document).on('click', '.edit-product', function(e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#editProductModal');
            var form = $('#editProductForm', modal);
            var errorBlock = $('#editProductError', form);
            var details = link.data('details');
            form.attr('action', link.attr('href'));
            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('#productId', modal).html(details.id);
            $('#editproductform-name', modal).val(details.name);

            modal.modal('show');

            return false;
        });

        $(document).on('click', '#editProductButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#editProductForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#editProductModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });

        $(document).on('click', '.create-package', function(e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#createPackageModal');
            var form = $('#createPackageForm', modal);
            var errorBlock = $('#createPackageError', form);

            form.attr('action', link.attr('href'));
            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('#create-package-auto', form).addClass('hidden');
            $('input[type="text"]', form).val('');
            $('input[type="checkbox"]', form).prop('checked', false);
            $('input[type="checkbox"]', form).prop('checked', false);
            $('select', form).prop('selectedIndex',0);

            modal.modal('show');

            return false;
        });

        $(document).on('click', '#createPackageButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#createPackageForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#createPackageModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });

        $(document).on('click', '.edit-package', function(e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#editPackageModal');
            var form = $('#editPackageForm', modal);
            var errorBlock = $('#editPackageError', form);
            var details = link.data('details');
            form.attr('action', link.attr('href'));
            errorBlock.addClass('hidden');
            errorBlock.html('');

            if (!details) {
                return false;
            }

            $('#packageId', modal).html(details.id);
            $('#editpackageform-name', modal).val(details.name);
            $('#editpackageform-price', modal).val(details.price);
            $('#editpackageform-quantity', modal).val(details.quantity);
            $('#editpackageform-link_type', modal).val(details.link_type);
            $('#editpackageform-visibility', modal).val(details.visibility);
            $('#editpackageform-provider_id', modal).val(details.provider_id);
            $('#editpackageform-provider_service', modal).val(details.provider_service);
            $('#editpackageform-id', modal).val(details.id);
            $('.delete-package', modal).attr('href', link.data('delete_link'));
            $('#editpackageform-mode', modal).val(details.mode).trigger('change');
            modal.modal('show');

            $('#editpackageform-provider_id', modal).trigger('change', [details.provider_service]);

            return false;
        });

        $(document).on('click', '#editPackageButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#editPackageForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#editPackageModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });

        $('#createpackageform-mode, #editpackageform-mode').change(function () {
            var value = $(this).val() * 1;
            var form = $(this).parents('form');
            var container = $('#create-package-auto, #edit-package-auto', form);

            container.addClass('hidden');
            if (value) {
                container.removeClass('hidden');
            }
        });

        $(document).on('click', '.delete-package', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#editPackageForm');

            custom.sendBtn(btn, {
                data: self.getTokenParams(),
                method: 'POST',
                callback : function(response) {
                    $('#editPackageModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });

        self.sortable();

        self.providerServices($('#editPackageModal'), params);
        self.providerServices($('#createPackageModal'), params);
    },
    sortable: function () {
        var productsSortable = $('.sortable'),
            packagesSortable = $(".sortable-packages"),
            self = this;

        // Init sortable
        if (productsSortable.length > 0) {
            // Sort the parents
            productsSortable.sortable({
                containment: "parent",
                items: "> .product-item",
                handle: ".move",
                tolerance: "pointer",
                cursor: "move",
                opacity: 0.7,
                revert: 300,
                delay: 150,
                dropOnEmpty: true,
                placeholder: "movable-placeholder",
                forcePlaceholderSize: true,
                start: function() {
                    $(this).addClass('sorting').sortable('refreshPositions');
                },
                stop: function() {
                    $(this).removeClass('sorting');
                }
            });

            // Sort the children
            packagesSortable.sortable({
                items: "> .package-item",
                handle: ".sommerce-products-editor__packages-drag",
                tolerance: "pointer",
                containment: "parent"
            });

            productsSortable.sortable({
                update: function(event, ui) {
                    var currentItem = ui.item,
                        newPosition = currentItem.index(),
                        actionUrl = currentItem.data('action-url') + newPosition;

                    $.ajax({
                        url: actionUrl,
                        type: "POST",
                        data: self.getTokenParams(),
                        success: function (data, textStatus, jqXHR){
                            if (data.error){
                                return;
                            }
                            //Success
                        },
                        error: function (jqXHR, textStatus, errorThrown){
                            console.log('Error on save', jqXHR, textStatus, errorThrown);
                        }
                    });
                }
            });

            packagesSortable.sortable({
                update: function (event, ui) {
                    var currentItem = ui.item,
                        newPosition = currentItem.index(),
                        actionUrl = currentItem.data('action-url') + newPosition;

                    $.ajax({
                        url: actionUrl,
                        type: "POST",
                        data: self.getTokenParams(),
                        success: function (data, textStatus, jqXHR) {
                            if (data.error) {
                                return;
                            }
                            //Success
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.log('Error on save', jqXHR, textStatus, errorThrown);
                        }
                    });
                }
            });
        }
    },
    getTokenParams: function () {
        var csrfToken = $('meta[name="csrf-token"]').attr("content"),
            csrfParam = $('meta[name="csrf-param"]').attr("content");

        var tokenParams = {};
        tokenParams[csrfParam] = csrfToken;

        return tokenParams;
    },
    providerServices: function (modal, params) {
        var self = this;

        $(document).on('change', '#' + modal.attr('id') + ' .provider-id', function(e, selectedServiceId) {
            var apiErrorBlock = $('.api-error', modal);
            var optionSelected = $("option:selected", this),
                actionUrl = params.servicesUrl;

            clearProviderServisesList();
            if (actionUrl === undefined) {
                apiErrorBlock.addClass('hidden');
                return;
            }

            $.ajax({
                url: actionUrl,
                data: {id: optionSelected.val()},
                type: "GET",
                timeout: 15000,
                success: function(data, textStatus, jqXHR) {
                    if (data.hasOwnProperty('error')) {
                        apiErrorBlock.removeClass('hidden').text(data.message);
                    } else {
                        apiErrorBlock.addClass('hidden');
                        renderProviderServices(data, selectedServiceId);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    var errorMessage = '';
                    // Timeout error
                    if (textStatus === "timeout") {
                        errorMessage = jqXHR.responseJSON.message;
                    }

                    console.log('Something was wrong...', textStatus, errorThrown, jqXHR);
                    apiErrorBlock.removeClass('hidden').text(errorMessage);
                }
            });
        });

        function clearProviderServisesList() {
            $('.provider-service', modal).find("option:not(:eq(0))").remove();
            $('.provider-service', modal).find('option:eq(0)').prop('selected', true);
        }

        function renderProviderServices(services, selectedServiceId){
            var selected,
                $container = $('<div></div>');
            _.each(services, function (s) {
                if (selectedServiceId) {
                    selected = s.service.toString() === selectedServiceId.toString() ? 'selected' : '';
                }
                $container.append('<option value="' + s.service + '"'+ selected + '>' + s.service + ' - ' + s.name + '</option>');
            });
            clearProviderServisesList();
            $('.provider-service', modal).append($container.html());
        }
    }
};
customModule.adminProviders = {
    run : function(params) {
        var self = this;

        $(document).on('click', '#showCreateProviderModal', function(e) {
            e.preventDefault();

            var link = $(this);
            var modal = $('#createProviderModal');
            var form = $('#createProviderForm', modal);
            var errorBlock = $('#createProviderError', form);

            errorBlock.addClass('hidden');
            errorBlock.html('');

            modal.modal('show');

            return false;
        });

        $(document).on('click', '#createProviderButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#createProviderForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#createProviderModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });
    }
};
                var templates = templates || {};
                

templates['payments/description'] = _.template("<div class=\"form-group form-group-description\">\n    <span class=\"fa fa-times remove-description\"><\/span>\n    <label for=\"<%= elementId %>\" class=\"control-label\"><%= label %><\/label>\n    <input type=\"text\" class=\"form-control <%= elementClass %>\" name=\"<%= elementName %>\" id=\"<%= elementId %>\" value=\"\">\n<\/div>");
var custom = new function() {
    var self = this;

    self.request = null;

    self.confirm = function (title, text, callback, options) {
        var confirmPopupHtml;
        var compiled = templates['modal/confirm'];
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
        return string.replace(/[^a-z0-9_\-\s]/gmi, "").replace(/\s+/g, '-').toLowerCase();
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
    }
};
/**
 * /admin/settings/pages custom js module
 * @type {{run: customModule.settings.run}}
 */
customModule.adminPages = {
    run: function (params) {
        /*****************************************************************************************************
         *                      Delete (mark as deleted) Page
         *****************************************************************************************************/
        var $modal = $('#delete-modal'),
            $modalLoader = $modal.find('.modal-loader'),
            $buttonDelete = $modal.find('#feature-delete'),
            actionUrl,
            successRedirectUrl;

        $buttonDelete.on('click', function(){
            $modalLoader.removeClass('hidden');
            $.ajax({
                url: actionUrl,
                type: "DELETE",
                success: function (data, textStatus, jqXHR){
                    //Success
                    _.delay(function(){
                        $(location).attr('href', successRedirectUrl);
                    }, 500);
                },
                error: function (jqXHR, textStatus, errorThrown){
                    $modalLoader.addClass('hidden');
                    $modal.modal('hide');
                    console.log('Error on service save', jqXHR, textStatus, errorThrown);
                }
            });
        });

        $modal.on('show.bs.modal', function (event){
            var button = $(event.relatedTarget);
            actionUrl = button.data('action_url');
            successRedirectUrl = $modal.data('success_redirect');
        });

        $modal.on('hidden.bs.modal', function (){
            actionUrl = null;
        });
    }
};
/**
 * /admin/settings/edit-page custom js module
 * @type {{run: customModule.settings.run}}
 */
customModule.adminPageEdit = {
    run: function (params) {

        /*****************************************************************************************************
         *              Create/Edit Page autofill SEO & URL routine
         *****************************************************************************************************/
        var $pageForm = $('#pageForm'),
            $seoCollapse = $pageForm.find('.collapse');

        var isNewPage = $pageForm.data('new_page');
        var $formFields = {
            name            : $pageForm.find('.form_field__name'),
            content         : $pageForm.find('.form_field__content'),
            url             : $pageForm.find('.form_field__url'),
            visibility      : $pageForm.find('.form_field__visibility'),
            seo_title       : $pageForm.find('.form_field__seo_title'),
            seo_description : $pageForm.find('.form_field__seo_description')
        };

        var exitingUrls = params.urls || [];
        var isValidationUrlError = params.url_error || false;

        initSeoParts();
        initSummernote($formFields.content);

        // Expand collapse if error
        if (isValidationUrlError) {
            $seoCollapse.collapse("show");
        }

        if (isNewPage) {
            $formFields.name.focus();
            // Start autofilling URL
            $formFields.name.on('input', autoFillFields);

            // Stop autofill on first user's touch
            $formFields.url.on('focus', autoFillFieldsOff);
            $formFields.seo_title.on('focus', autoFillFieldsOff);
        }

        // Start cleanup url
        $formFields.url.on('input', cleanupUrl);

        /**
         * Init summernote
         * @param $element
         */
        function initSummernote($element){
            $formFields.content.summernote({
                minHeight: 300,
                focus: true,
                toolbar: [['style', ['style', 'bold', 'italic']], ['lists', ['ul', 'ol']], ['para', ['paragraph']], ['color', ['color']], ['insert', ['link', 'picture', 'video']], ['codeview', ['codeview']]],
                disableDragAndDrop: true,
                styleTags: ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
                popover: {
                    image: [['float', ['floatLeft', 'floatRight', 'floatNone']], ['remove', ['removeMedia']]]
                },
                dialogsFade: true
            });

            /* fix summernote submit form on Enter bug */
            $pageForm.keypress(function (e) {
                if (e.which === 13) {
                    $pageForm.submit();
                    return false;
                }
            });
        }

        /**
         * Init autofill SEO part of the page
         */
        function initSeoParts(){
            if ($('.edit-seo__title').length > 0){
                (function (){
                    var seoEdit = ['edit-seo__title', 'edit-seo__meta', 'edit-seo__url'];

                    var _loop = function _loop(i){
                        $("." + seoEdit[i] + '-muted').text($("#" + seoEdit[i]).val().length);
                        $("#" + seoEdit[i]).on('input', function (e){
                            if (i === 2){
                                $('.' + seoEdit[i]).text($(e.target).val().toLowerCase());
                            } else {
                                $("." + seoEdit[i] + '-muted').text($(e.target).val().length);
                                $('.' + seoEdit[i]).text($(e.target).val());
                            }
                        }).trigger('input');
                    };

                    for (var i = 0; i < seoEdit.length; i++){
                        _loop(i);
                    }
                })();
            }
        }

        /**
         * Return vallid address path by passed string
         * a-z, -_ ,0-9
         * @param string
         */
        function getValidAddressByString(string){
            return string.replace(/[^a-z0-9_\-\s]/gmi, "").replace(/\s+/g, '-');
        }

        /**
         * Autofilling `url` by `product name`
         */
        function autoFillFields(e){
            var inputName = $(e.target).val(),
                generatedUrl;

            generatedUrl = custom.generateUrlFromString(inputName);
            generatedUrl = custom.generateUniqueUrl(generatedUrl, exitingUrls);

            // Autofill Url
            $formFields.url.val(generatedUrl).trigger('input');
            // Autofill Title
            $formFields.seo_title.val(inputName).trigger('input');
        }

        /**
         * Stop autofilling `url` by `product name`
         */
        function autoFillFieldsOff(){
            $formFields.name.off('input', autoFillFields);
            $formFields.seo_title.off('input', autoFillFields);
        }

        /**
         * Cleanup url
         */
        function cleanupUrl(e){
            var urlMaxLenght = 200,
                urlByName,
                target = e.currentTarget,
                $target = $(target),
                inputedName = $target.val();

            var position = target.selectionStart;

            urlByName = custom.generateUrlFromString(inputedName);

            if (urlByName.length >= urlMaxLenght){
                urlByName = urlByName.substring(0, (urlMaxLenght-1));
            }

            $target.val(urlByName);

            target.selectionEnd = position;
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
    }
};
/**
 * /admin/settings/pages custom js module
 * @type {{run: customModule.settings.run}}
 */
customModule.adminThemes = {
    run: function (params) {

        /*****************************************************************************************************
         *                     CodeMirror activation
         *****************************************************************************************************/
        var $codeMirror = $('#codemirror'),
            codeMirror,
            contentOnInit;

        var $modalSubmitClose = $('#modal_submit_close');
        var $modalSubmitReset = $('#modal_submit_reset');

        if ($codeMirror.length > 0) {
            codeMirror = CodeMirror.fromTextArea($codeMirror[0], {
                lineNumbers: true
            });

            contentOnInit = codeMirror.getValue();
        }

        /*****************************************************************************************************
         *                     JS Tree Files init
         *****************************************************************************************************/
        var $filesTree = $('#m_tree_1');
        $filesTree.jstree({
            "core" : {
                "themes" : {
                    "responsive": false
                }
            },
            "types" : {
                "default" : {
                    "icon" : "fa fa-folder"
                },
                "file" : {
                    "icon" : "fa fa-file"
                }
            },
            "plugins": ["types"]
        });

        $filesTree.on('select_node.jstree', function(e, node) {
            var _node = node.node;
            if (_node && _node.hasOwnProperty('a_attr') && (_node.a_attr.href !== '#')) {

                // if (codeMirror && (codeMirror.getValue() !== contentOnInit)) {
                //     $modal.modal('show');
                //     return;
                // }

                window.location = _node.a_attr.href;
            }
        });

        /*****************************************************************************************************
         *               Modal submit close
         *****************************************************************************************************/
        $modalSubmitClose.on('show.bs.modal', function(event){
            var $href = $(event.relatedTarget),
                href = $href.attr('href');

            // Prevent show process if
            if (codeMirror === undefined || codeMirror.getValue() === contentOnInit) {
                event.stopPropagation();
                window.location.href = href;
                return false;
            }
            // Else — show
            $(this).find('.submit_button').attr('href', href);
        });

        /*****************************************************************************************************
         *               Modal submit reset
         *****************************************************************************************************/
        $modalSubmitReset.on('show.bs.modal', function(event){
            var $href = $(event.relatedTarget),
                href = $href.attr('href');

            $(this).find('.submit_button').attr('href', href);
        });

    }
};
customModule.adminBlocks = {
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
    }
};
customModule.adminEditBlock = {
    state: {
        steps: false,
        review: false,
        slider: false,
        feature: false,
        actions: {
            delete: {
                turn: false
            },
            editorText: {
                node: false,
                nodeText: false,
                nodeHeight: false,
                save: false
            },
            slider: {
                link: false,
                type: false
            },
            feature: {
                activeIconId: false,
                activeIcon: false
            },
            steps: {
                activeIconId: false,
                activeIcon: false
            },
            dropdown: {
                id: false
            }
        }
    },
    run : function(params) {
        var self = this;
        var code = 'undefined' !== typeof params.code ? params.code : undefined;

        switch (code) {
            case 'slider':
                self.slider(params);
                break;

            case 'features':
                self.features(params);
                break;

            case 'reviews':
                self.review(params);
                break;

            case 'process':
                self.process(params);
                break;
        }

        self.initTextareaAutosizer();
    },
    slider: function(params) {
        var self = this;
        var state = self.state;

        state.slider = params.block;

        var blockLinks = {
            render: 'http://www.mocky.io/v2/5a98092630000075005c2018',
            save: params.saveUrl,
            upload: params.uploadUrl
        };

        var textAreaResizer = function textAreaResizer() {
            $('textarea.js-auto-size').textareaAutoSize();
        };
        var swiperSlider;
        swiperSlider = new Swiper('.block-slider', {
            pagination: '.swiper-pagination',
            paginationClickable: true,
            scrollbarDraggable: false,
            simulateTouch: false
        });

        var generateSlide = function generateSlide(action, id, title, description, button, image) {
            var template = '<div class="swiper-slide">\n                    <div class="slider__block-wrap slider__block-' + id + ' d-flex flex-wrap">\n\n                        <div class="editor-tooltip bg-danger editor-tooltip__right-top editor-action__delete-review editor-action__delete"  data-id="' + id + '" data-type="review" data-toggle="modal" data-target="#delete-feature-modal">\n                            <span class="fa fa-times"></span>\n                        </div>\n                        <div class="col-md-4">\n                            <label for="slider-image-' + id + '" class="slider__image slider__image_' + id + ' slider-image-' + id + '" style="background-image: url(' + image + ');">\n                                <input id="slider-image-' + id + '" type="file" name="slider-image-' + id + '" class="editor-slider-image-input" data-id="' + id + '">\n                            </label>\n                        </div>\n                        <div class="col">\n                            <div class="editor-block__reviev_name">\n                                <div class="editor-textarea__text-edit-off">\n                                    <textarea class="editor-textarea__h text-left editor-textarea__h3 js-auto-size" data-id="' + id + '" data-textarea-title="title" rows="1" spellcheck="false" placeholder="Add title...">' + title + '</textarea>\n                                    <div class="editor-textarea__text-edit-action">\n                                        <button class="btn btn-sm btn-success cursor-pointer editor-textarea__text-edit-save">Save</button>\n                                        <button class="btn btn-sm btn-secondary cursor-pointer editor-textarea__text-edit-close">Close</button>\n                                    </div>\n                                </div>\n                            </div>\n                            <div class="editor-block__description">\n                                <div class="editor-textarea__text-edit-off">\n                                    <textarea class="editor_textarea__text js-auto-size" data-id="' + id + '" data-textarea-title="description" rows="1" spellcheck="false" placeholder="Add text...">' + description + '</textarea>\n                                    <div class="editor-textarea__text-edit-action">\n                                        <button class="btn btn-sm btn-success cursor-pointer editor-textarea__text-edit-save">Save</button>\n                                        <button class="btn btn-sm btn-secondary cursor-pointer editor-textarea__text-edit-close">Close</button>\n                                    </div>\n                                </div>\n                            </div>\n                            <div class="editor-block__description">\n                                <button class="learn-more learn-more-' + id + '" data-toggle="modal" data-target="#learn-more" data-id="' + id + '">' + button + '</button>\n                            </div>\n                        </div>\n\n                    </div>\n\n                </div>';


            switch (action) {
                case 'render':
                    swiperSlider.appendSlide(template);
                    swiperSlider.slideTo(0);
                    break;
                case 'add':
                    swiperSlider.appendSlide(template);
                    swiperSlider.slideTo(0);
                    state.slider.data.push({
                        "id": id.toString(),
                        "title": title,
                        "description": description,
                        "button": {
                            "title": button,
                            "link": false,
                            "type": false
                        },
                        "image": false
                    });
                    break;
            }

            //textAreaResizer();
        };

        var initData = function(result) {
            $('#preload').remove();

            for (var i = 0; i < result.data.length; i++) {
                generateSlide('render', result.data[i].id, result.data[i].title, result.data[i].description, result.data[i].button.title, result.data[i].image);
            }

            var sliderEffects = $('.slider-effects'),
                sliderInterval = $('.slider-interval');

            for (var i = 0; i < sliderEffects.length; i++) {
                if (sliderEffects[i].value.toLocaleLowerCase() == result.settings.effect.toLocaleLowerCase()) {
                    sliderEffects[i].checked = true;
                    $(sliderEffects[i].parentNode).addClass('active');
                }
            }
            for (var i = 0; i < sliderInterval.length; i++) {
                if (sliderInterval[i].value.toLocaleLowerCase() == result.settings.interval.toLocaleLowerCase()) {
                    sliderInterval[i].checked = true;
                    $(sliderInterval[i].parentNode).addClass('active');
                }
            }
        }

        $('.new-preview').on('click', function (e) {
            e.preventDefault();
            var lastSlide = '';
            if (state.slider.data.length == 0) {
                $('.no-slide').remove();
                swiperSlider = new Swiper('.block-slider', {
                    pagination: '.swiper-pagination',
                    paginationClickable: true,
                    scrollbarDraggable: false,
                    simulateTouch: false
                });
                lastSlide = "1";
            } else {
                lastSlide = parseInt(state.slider.data[state.slider.data.length - 1].id) + 1;
            }
            generateSlide('add', lastSlide, '', '', 'Learn more', false);
        });

        $(document).on('click', '.learn-more', function () {
            var slideID = $(this).attr('data-id');
            state.actions.slider.link = slideID;
            $('.slider-link__type').addClass('hide-link');
            for (var i = 0; i < state.slider.data.length; i++) {
                if (state.slider.data[i].id.indexOf(slideID) == 0) {
                    $('.learn-more__input').val(state.slider.data[i].button.title);
                    state.actions.slider.type = state.slider.data[i].button.type;
                }
            }

            var selectedTypes = $('#select-menu__link')[0],
                dataSlide = '';

            for (var i = 0; i < state.slider.data.length; i++) {
                if (state.slider.data[i].id.indexOf(state.actions.slider.link) == 0) {
                    dataSlide = state.slider.data[i];
                }
            }

            if (dataSlide.button.type) {
                for (var i = 0; i < selectedTypes.length; i++) {
                    if (selectedTypes[i].value.toLocaleLowerCase() == dataSlide.button.type) {
                        selectedTypes[i].selected = true;
                        $('.slider-link__type-' + dataSlide.button.type).removeClass('hide-link');
                    }
                }
            } else {
                selectedTypes[0].selected = true;
            }

            var selectedNode = ".link-input__" + dataSlide.button.type;
            switch (dataSlide.button.type) {
                case "web":
                    $(selectedNode).val(dataSlide.button.link);
                    break;
                case "products":
                case "page":
                    for (var i = 0; i < selectedNode.length; i++) {
                        if (selectedNode[i].value == selectedNode.button.link) {
                            selectedTypes[i].selected = true;
                        }
                    }
                    break;
            }
        });

        $(document).on('click', '#learn-more__save', function () {
            var selectedMenu = $("#select-menu__link option:selected").val();
            for (var i = 0; i < state.slider.data.length; i++) {
                if (state.slider.data[i].id.indexOf(state.actions.slider.link) == 0) {
                    if ($('.learn-more__input').val() == '') {
                        state.slider.data[i].button.title = 'Learn more';
                        state.slider.data[i].button.type = false;
                    } else {
                        state.slider.data[i].button.title = $('.learn-more__input').val();
                        state.slider.data[i].button.type = selectedMenu;
                    }

                    switch (selectedMenu) {
                        case "web":
                            state.slider.data[i].button.link = $('.link-input__' + selectedMenu).val();
                            break;
                        case "products":
                        case "page":
                            var selectedNode = ".slider-link__type-" + selectedMenu;
                            state.slider.data[i].button.link = $(selectedNode + " option:selected").val();
                            break;
                        case "none":
                            state.slider.data[i].button.link = false;
                            break;
                        case "home":
                            state.slider.data[i].button.link = '/';
                            break;
                        default:
                            console.log('default');
                            state.slider.data[i].button.link = false;
                            break;
                    }

                    $('.learn-more-' + state.actions.slider.link).text(state.slider.data[i].button.title);
                }
            }
        });

        $(document).on('change', '.editor-slider-image-input', function () {
            if($(this).val().length) {
                var classId = '.' + this.id,
                    dataID = $(this).attr('data-id');

                $(classId).addClass('image-loader');
                var data = new FormData();
                data.append('file', $(this)[0].files[0]);
                data.append('type', 'slider');

                $.ajax({
                    url: blockLinks.upload,
                    data: data,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    success: function (response) {
                        $(classId).removeClass('image-loader');

                        if ('error' == response.status) {
                            toastr.error(response.error);
                        }

                        if ('success' == response.status) {
                            $(classId).css('background-image', 'url(' + response.link + ')');
                            for (var i = 0; i < state.slider.data.length; i++) {
                                if (state.slider.data[i].id.indexOf(dataID) == 0) {
                                    state.slider.data[i].image = response.link;
                                    return;
                                }
                            }
                        }
                    },
                    error: function error(_error) {
                    }
                });
            }
        });

        $(document).on('click', '.editor-action__delete', function () {
            var slideID = $(this).attr('data-id');
            state.actions.delete.turn = slideID;
        });

        $(document).on('click', '#feature-delete', function () {
            for (var i = 0; i < state.slider.data.length; i++) {
                if (state.slider.data[i].id.indexOf(state.actions.delete.turn) == 0) {
                    state.slider.data.splice(i, 1);
                    swiperSlider.removeSlide(swiperSlider.activeIndex);

                    if(!state.slider.data.length){
                        swiperSlider.destroy(true, true);
                        $('.swiper-wrapper').append('<div class="no-slide">No slides</div>');
                    }
                    return;
                }
            }
        });

        $(document).on('change', '.js-auto-size', function () {
            var slideID = $(this).attr('data-id'),
                slideType = $(this).attr('data-textarea-title'),
                content = $(this).val();

            for (var i = 0; i < state.slider.data.length; i++) {
                if (state.slider.data[i].id.indexOf(slideID) == 0) {
                    state.slider.data[i][slideType] = content;
                    return;
                }
            }
        });

        $(document).on('change', '#select-menu__link', function () {
            var linkType = $(this).find('option:selected').val();
            $('.slider-link__type').addClass('hide-link');
            $('.slider-link__type-' + linkType).removeClass('hide-link');
        });

        /* Settings */

        $(document).on('change', '.slider-effects', function () {
            state.slider.settings.effect = $(this).val();
        });

        $(document).on('change', '.slider-interval', function () {
            state.slider.settings.interval = $(this).val();
        });

        $(document).on('click', '#save-changes', function () {
            var that = this;
            $(that).addClass('m-loader m-loader--light m-loader--right');
            $(that).prop('disabled', true);
            $.ajax({
                url: blockLinks.save,
                data: {
                    content: state.slider
                },
                type: 'POST',
                success: function success(response) {
                    $(that).removeClass('m-loader m-loader--light m-loader--right');
                    $(that).prop('disabled', false);
                    self.saveCallback(response);
                },
                error: function error(_error2) {
                    $(that).removeClass('m-loader m-loader--light m-loader--right');
                    $(that).prop('disabled', false);
                    toastr.error("Error status " + _error2.status);
                }
            });
        });


        initData(state.slider);

    },
    features: function(params) {
        var self = this;
        var state = self.state;

        state.feature = params.block;

        var blockLinks = {
            render: 'http://www.mocky.io/v2/5a9909042e000003265534a8',
            save: params.saveUrl,
            upload: params.uploadUrl
        };

        var textAreaResizer = function textAreaResizer() {
            $('textarea.js-auto-size').textareaAutoSize();
        };

        var initData = function(result) {
            $('#preload').remove();

            var featureColumn = $('.feature-column'),
                featureAlign = $('.feature-align');

            for (var i = 0; i < featureColumn.length; i++) {
                if (state.feature.settings.column == featureColumn[i].value) {
                    featureColumn[i].checked = true;
                    $(featureColumn[i].parentNode).addClass('active');
                }
            }

            for (var i = 0; i < featureAlign.length; i++) {
                if (state.feature.settings.align == featureAlign[i].value) {
                    featureAlign[i].checked = true;
                    $(featureAlign[i].parentNode).addClass('active');
                }
            }

            $("#feature-fontSize").slider({
                min: 12,
                max: 240,
                step: 12,
                value: state.feature.settings.icon_size,
                slide: function slide(event, ui) {
                    $(".feature-icon-size-show").text(ui.value);
                    $("#feature-size-icon").val(ui.value);
                    $('.feature-icon').css({
                        "fontSize": ui.value + 'px'
                    });
                    state.feature.settings.icon_size = ui.value;
                }
            });
            $('.feature-icon-size-show').text(state.feature.settings.icon_size);

            for (var i = 0; i < state.feature.data.length; i++) {
                generateCards('render', state.feature.data[i].id, state.feature.data[i].title, state.feature.data[i].description, state.feature.data[i].icon);
            }

            $("#feature-list").dragsort({
                dragBetween: true,
                dragSelector: ".editor-action__drag",
                dragEnd: function() {
                    var elements = $('#feature-list li'),
                        dataClone = $.extend(true, {}, state.feature.data),
                        dataNew = [];

                    for (var i = 0; i < elements.length; i++){
                        for (var j = 0; j<state.feature.data.length; j++){
                            if ($(elements[i]).data('id') == state.feature.data[j].id) {
                                dataNew.push(state.feature.data[j])
                            }
                        }
                    }
                    console.log(state.feature.data, dataNew);
                    state.feature.data = dataNew;
                },
                placeHolderTemplate: '<li class="col margin-top-bottom editor-placeholder-move"><div class="editor-card editor-card__left editor-tooltip__show placeholder-template d-flex align-items-lg-center justify-content-center"><span>Insert</span></div></li>'
            });

            includeContent();
            textAreaResizer();
        }

        var generateCards = function generateCards(action, id, title, description, icon) {
            var iconSize = state.feature.settings.icon_size,
                column = state.feature.settings.column,
                align = state.feature.settings.align,
                colAlignTitle = '',
                colAlignIcon = '';

            if(align == 'left'){
                colAlignTitle = 'col-7';
                colAlignIcon = 'col-5';
            }else{
                colAlignTitle = 'col-12';
                colAlignIcon = 'col-12';
            }

            var featureCardTemplate = '<li class="col-lg-' + column + ' margin-top-bottom feature-id-' + id + '" data-id="'+id+'">\n                    <div class="editor-card editor-card__left editor-tooltip__show">\n                        <div class="row">\n                            <div class="editor-tooltip bg-success editor-tooltip__right-top editor-action__drag">move</div>\n                            <div class="editor-tooltip bg-danger editor-tooltip__left-top editor-action__delete"  data-id="' + id + '" data-type="feature" data-toggle="modal" data-target="#delete-feature-modal">\n                                <span class="fa fa-times"></span>\n                            </div>\n\n                            <div class="editor-card__icon-block '+colAlignIcon+' ">\n                                <div class="editor-preview__block" data-toggle="modal" data-target="#preview-edit-modal" data-id="' + id + '">\n                                    <div class="editor-preview__tooltip">edit</div>\n                                    <span class="fa ' + icon + ' feature-icon" id="feature-icon-' + id + '" style="font-size: ' + iconSize + 'px;"></span>\n                                </div>\n                            </div>\n                            <div class="editor-card__title-block '+colAlignTitle+' ">\n                                <div class="editor-textarea__text-edit-off">\n                                    <textarea class="editor-textarea__h editor-textarea__h3 js-auto-size" data-id="' + id + '" data-textarea-title="title" rows="1" spellcheck="false" placeholder="Add title...">' + title + '</textarea>\n                                    <div class="editor-textarea__text-edit-action">\n                                        <button class="btn btn-sm btn-success cursor-pointer editor-textarea__text-edit-save">Save</button>\n                                        <button class="btn btn-sm btn-secondary cursor-pointer editor-textarea__text-edit-close">Close</button>\n                                    </div>\n                                </div>\n                            </div>\n                            <div class="editor-card__text-block col-12">\n                                <div class="editor_textarea-block">\n                                    <div class="editor-textarea__text-edit-off">\n                                    <textarea class="editor_textarea__text js-auto-size" rows="1" spellcheck="false" data-id="' + id + '" data-textarea-title="description" placeholder="Add text...">' + description + '</textarea>\n                                        <div class="editor-textarea__text-edit-action">\n                                            <button class="btn btn-sm btn-success cursor-pointer editor-textarea__text-edit-save">Save</button>\n                                            <button class="btn btn-sm btn-secondary cursor-pointer editor-textarea__text-edit-close">Close</button>\n                                        </div>\n                                    </div>\n                                </div>\n                            </div>\n\n                        </div>\n                    </div>\n                </li>';

            $("#feature-list").append(featureCardTemplate);
        };

        var includeContent = function includeContent() {
            $('.feature-title').val(state.feature.header.title);
            $('.feature-description').val(state.feature.header.description);
        };

        $(document).on('click', '.editor-action__delete', function () {
            var dataID = $(this).attr('data-id');
            state.actions.delete.turn = dataID;
        });

        $(document).on('click', '#feature-delete', function () {
            for (var i = 0; i < state.feature.data.length; i++) {
                if (state.feature.data[i].id.indexOf(state.actions.delete.turn) == 0) {
                    state.feature.data.splice(i, 1);
                }
            }
            var removeClass = '.feature-id-' + state.actions.delete.turn;
            $(removeClass).remove();
        });

        $(document).on('change', '.js-auto-size', function () {
            var featureID = $(this).attr('data-id'),
                featureType = $(this).attr('data-textarea-title'),
                content = $(this).val();

            switch (featureType) {
                case "header-title":
                    console.log(state.feature.header.title);
                    console.log(content);
                    state.feature.header.title = content;
                    break;
                case "header-description":
                    state.feature.header.description = content;
                    break;
                default:
                    for (var i = 0; i < state.feature.data.length; i++) {
                        if (state.feature.data[i].id.indexOf(featureID) == 0) {
                            state.feature.data[i][featureType] = content;
                            return;
                        }
                    }
                    break;
            }
        });

        $(document).on('change', '.feature-align', function () {
            var featureAlign = $(this).val();
            state.feature.settings.align = featureAlign;
            switch (featureAlign) {
                case 'left':
                    $('.editor-card__icon-block').removeClass('col-12').addClass('col-5');
                    $('.editor-card__title-block').removeClass('col-12').addClass('col-7');
                    break;
                case 'center':
                    $('.editor-card__icon-block').removeClass('col-5').addClass('col-12');
                    $('.editor-card__title-block').removeClass('col-7').addClass('col-12');
                    break;
            }
        });

        $(document).on('change', '.feature-column', function () {
            state.feature.settings.column = $(this).val();
        });

        $(document).on('click', '#save-changes', function () {
            var that = this;
            $(that).addClass('m-loader m-loader--light m-loader--right');
            $(that).prop('disabled', true);
            $.ajax({
                url: blockLinks.save,
                data: {
                    content: state.feature
                },
                type: 'POST',
                success: function success(response) {
                    $(that).removeClass('m-loader m-loader--light m-loader--right');
                    $(that).prop('disabled', false);
                    self.saveCallback(response);
                },
                error: function error(_error2) {
                    $(that).removeClass('m-loader m-loader--light m-loader--right');
                    $(that).prop('disabled', false);
                    toastr.error("Error status " + _error2.status);
                }
            });
        });

        $(document).on('click', '.edit-modal__icons-action', function () {
            $('.edit-modal__icons-action').removeClass('active-icon');
            $(this).addClass('active-icon');
            state.actions.feature.activeIcon = $(this).attr('data-icon-name');
        });

        $(document).on('click', '.editor-preview__block', function () {
            var dataID = $(this).attr('data-id');
            state.actions.feature.activeIconId = dataID;
        });

        $(document).on('click', '#feature-saveIcon', function () {
            var currentID = state.actions.feature.activeIconId,
                iconClass = '#feature-icon-' + currentID,
                classStroke = 'fa ' + state.actions.feature.activeIcon + ' feature-icon';

            for (var i = 0; i < state.feature.data.length; i++) {
                if (state.feature.data[i].id.indexOf(currentID) == 0) {
                    state.feature.data[i].icon = state.actions.feature.activeIcon;
                }
            }

            $(iconClass).removeAttr('class');
            $(iconClass).addClass(classStroke);
        });

        $(document).on('change', '.feature-column', function () {
            $("#feature-list li").removeClass('col-lg-3 col-lg-4 col-lg-6');
            $('#feature-list li').addClass('col-lg-' + state.feature.settings.column);
        });

        $(document).on('click', '#feature-new', function () {

            var maxID = 0;
            for (var i = 0; i < state.feature.data.length; i++) {
                if (maxID < parseInt(state.feature.data[i].id)) {
                    maxID = parseInt(state.feature.data[i].id);
                }
            }
            maxID++;
            var featureID = maxID.toString();
            state.feature.data.push({
                "id": featureID,
                "title": "",
                "description": "",
                "icon": "fa-picture-o"
            });
            generateCards('add', featureID, '', '', 'fa-picture-o');
        });

        initData(state.feature);

    },
    review: function(params) {
        var self = this;
        var state = self.state;

        state.review = params.block;

        var blockLinks = {
            render: 'http://www.mocky.io/v2/5a9907fe2e00004e255534a3',
            save: params.saveUrl,
            upload: params.uploadUrl
        };

        var textAreaResizer = function textAreaResizer() {
            $('textarea.js-auto-size').textareaAutoSize();
        };

        var swiperSlider;
        swiperSlider = new Swiper('.block-slider', {
            pagination: '.swiper-pagination',
            paginationClickable: true,
            scrollbarDraggable: false,
            simulateTouch: false,
            centeredSlides: false
        });

        var initData = function(result) {
            $('#preload').remove();

            swiperSlider = new Swiper('.swiper-container', {
                pagination: '.swiper-pagination',
                paginationClickable: true,
                scrollbarDraggable: false,
                centeredSlides: false,
                simulateTouch: false,
                slidesPerView: parseInt(state.review.settings.column)
            });

            for (var i = 0; i < state.review.data.length; i++) {
                generateSlide('render', state.review.data[i].id, state.review.data[i].name, state.review.data[i].rating, state.review.data[i].description, state.review.data[i].image);
            }

            var reviewColumn = $('.review-column');

            for (var i = 0; i < reviewColumn.length; i++) {
                if (state.review.settings.column == reviewColumn[i].value) {
                    reviewColumn[i].checked = true;
                    $(reviewColumn[i].parentNode).addClass('active');
                }
            }

            includeContent();
        }

        var generateSlide = function generateSlide(action, id, name, rating, description, image) {

            if (!image) {
                image = '/img/review_no_avatar.gif';
            }

            var templateRating = '';
            for (var i = 1; i < 6; i++) {
                if (i == parseInt(rating)) {
                    templateRating += '<input type="radio" name="rating" class="rating" value=' + i + ' checked/>';
                } else {
                    templateRating += '<input type="radio" name="rating" class="rating" value=' + i + ' />';
                }
            }

            var template = '<div class="swiper-slide">\n                        <div class="editor-review__block">\n                            <div class="editor-tooltip bg-danger editor-tooltip__left-top editor-action__delete-review editor-action__delete"  data-id="' + id + '" data-type="review" data-toggle="modal" data-target="#delete-feature-modal">\n                                <span class="fa fa-times"></span>\n                            </div>\n                            <div class="editor-block__review-avatar">\n                                <div class="editor-tooltip bg-danger editor-tooltip__left-top review-image-delete" data-id="' + id + '" data-type="avatar" data-toggle="modal" data-target="#delete-feature-modal">\n                                    <span class="fa fa-times"></span>\n                                </div>\n                                <label for="review-avatar-' + id + '">\n                                    <div class="editor-preview__block-avatar">\n                                        <div style="background-image: url(\'' + image + '\');" alt="" title="" class="editor-avatar__image rounded-circle review-avatar-' + id + '"></div>\n                                    </div>\n                                    <input id="review-avatar-' + id + '" type="file" class="editor-preview__avatar-input" data-id="' + id + '">\n                                </label>\n                            </div>\n                            <div class="editor-block__reviev_name">\n                                <div class="editor-textarea__text-edit-off">\n                                    <textarea class="editor-textarea__h editor-textarea__h3 js-auto-size" data-id="' + id + '" data-textarea-title="name" rows="1" spellcheck="false" placeholder="Add name...">' + name + '</textarea>\n                                    <div class="editor-textarea__text-edit-action">\n                                        <button class="btn btn-sm btn-success cursor-pointer editor-textarea__text-edit-save">Save</button>\n                                        <button class="btn btn-sm btn-secondary cursor-pointer editor-textarea__text-edit-close">Close</button>\n                                    </div>\n                                </div>\n                            </div>\n                            <div class="editor-rating__block">\n                                <div class="editor-rating_block-stars">\n                                    <div class="star-rating-' + id + '" data-id="' + id + '">\n                                        ' + templateRating + '\n                                    </div>\n                                </div>\n                            </div>\n                            <div class="editor-block__description">\n                                <div class="editor-textarea__text-edit-off">\n                                    <textarea class="editor_textarea__text js-auto-size" data-id="' + id + '" data-textarea-title="description" rows="1" spellcheck="false" placeholder="Add text...">' + description + '</textarea>\n                                    <div class="editor-textarea__text-edit-action">\n                                        <button class="btn btn-sm btn-success cursor-pointer editor-textarea__text-edit-save">Save</button>\n                                        <button class="btn btn-sm btn-secondary cursor-pointer editor-textarea__text-edit-close">Close</button>\n                                    </div>\n                                </div>\n                            </div>\n                        </div>\n                    </div>';

            switch (action) {
                case 'render':
                    swiperSlider.appendSlide(template);
                    swiperSlider.slideTo(0);
                    break;
                case 'add':
                    swiperSlider.appendSlide(template);
                    swiperSlider.slideTo(0);
                    state.review.data.push({
                        "id": id.toString(),
                        "name": name,
                        "description": description,
                        "image": '/img/review_no_avatar.gif',
                        "rating": false
                    });
                    break;
            }

            textAreaResizer();
            $('.star-rating-' + id).rating();
        };

        var includeContent = function includeContent() {
            $('.review-title').val(state.review.header.title);
            $('.review-description').val(state.review.header.description);
        };

        $(document).on('change', '.js-auto-size', function () {
            var reviewID = $(this).attr('data-id'),
                reviewType = $(this).attr('data-textarea-title'),
                content = $(this).val();

            switch (reviewType) {
                case "header-title":
                    state.review.header.title = content;
                    break;
                case "header-description":
                    state.review.header.description = content;
                    break;
                default:
                    for (var i = 0; i < state.review.data.length; i++) {
                        if (state.review.data[i].id.indexOf(reviewID) == 0) {
                            state.review.data[i][reviewType] = content;
                            return;
                        }
                    }
                    break;
            }
        });

        $(document).on('click', '.editor-action__delete', function () {
            var slideID = $(this).attr('data-id');
            state.actions.delete.turn = slideID;
        });

        $(document).on('click', '#feature-delete', function () {
            for (var i = 0; i < state.review.data.length; i++) {
                if (state.review.data[i].id.indexOf(state.actions.delete.turn) == 0) {
                    state.review.data.splice(i, 1);
                    swiperSlider.removeSlide(swiperSlider.activeIndex);

                    if(!state.review.data.length){
                        swiperSlider.destroy(true, true);
                        $('.swiper-wrapper').append('<div class="no-slide">No reviews</div>');
                    }
                    return;
                }
            }
        });

        $(document).on('change', '.review-column', function () {
            state.review.settings.column = parseInt($(this).val());

            swiperSlider = new Swiper('.swiper-container', {
                pagination: '.swiper-pagination',
                paginationClickable: true,
                scrollbarDraggable: false,
                centeredSlides: false,
                simulateTouch: false,
                slidesPerView: parseInt($(this).val())
            });
            textAreaResizer();
        });

        $(document).on('click', '#new-review', function (e) {
            e.preventDefault();
            var lastSlide = '';
            if (state.review.data.length == 0) {
                swiperSlider = new Swiper('.swiper-container', {
                    pagination: '.swiper-pagination',
                    paginationClickable: true,
                    scrollbarDraggable: false,
                    centeredSlides: false,
                    simulateTouch: false,
                    slidesPerView: parseInt(state.review.settings.column)
                });
                lastSlide = "1";
            } else {
                lastSlide = parseInt(state.review.data[state.review.data.length - 1].id) + 1;
            }
            generateSlide('add', lastSlide, '', '', '', false);
        });

        $(document).on('change', '.editor-preview__avatar-input', function () {

            var that = $(this);

            if($(this).val().length) {
                var dataID = $(this).data('id');
                var classId = '.review-avatar-' + dataID;

                $(classId).addClass('image-loader');
                $(that).prop('disabled', true);

                var data = new FormData();
                data.append('file', $(this)[0].files[0]);
                data.append('type', 'review');

                $.ajax({
                    url: blockLinks.upload,
                    data: data,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    success: function (response) {
                        $(classId).removeClass('image-loader');
                        $(that).prop('disabled', false);
                        if ('error' == response.status) {
                            toastr.error(response.error);
                            $(classId).css('background-image', 'url(/img/review_no_avatar.gif)');
                        }

                        if ('success' == response.status) {
                            console.log(dataID);
                            for (var i = 0; i < state.review.data.length; i++) {
                                if (state.review.data[i].id.indexOf(dataID) == 0) {
                                    state.review.data[i].image = response.link;
                                    $(classId).css('background-image', 'url(' + response.link + ')');
                                    return;
                                }
                            }
                        }
                    },
                    error: function error(_error) {
                        $(that).prop('disabled', false);
                        $(classId).removeClass('image-loader');
                        $(classId).css('background-image', 'url(/img/review_no_avatar.gif)');
                    }
                });
            }
        });

        $(document).on('click', '.review-image-delete', function () {
            var dataID = $(this).data('id');
            var classId = '.review-avatar-' + dataID;

            for (var i = 0; i < state.review.data.length; i++) {
                if (state.review.data[i].id.indexOf(dataID) == 0) {
                    state.review.data[i].image = '0';
                    $(classId).css('background-image', 'url(/img/review_no_avatar.gif)');
                    return;
                }
            }
        });

        $(document).on('click', '.fullStar', function () {
            var ratingValue = $(this).attr('title'),
                ratingNode = $(this.parentNode.parentNode).attr('data-id');

            for (var i = 0; i < state.review.data.length; i++) {
                if (state.review.data[i].id.indexOf(ratingNode) == 0) {
                    state.review.data[i].rating = ratingValue;
                }
            }
        });


        $(document).on('click', '#save-changes', function () {
            var that = this;
            $(that).addClass('m-loader m-loader--light m-loader--right');
            $(that).prop('disabled', true);
            $.ajax({
                url: blockLinks.save,
                data: {
                    content: state.review
                },
                type: 'POST',
                success: function success(response) {
                    $(that).removeClass('m-loader m-loader--light m-loader--right');
                    $(that).prop('disabled', false);
                    self.saveCallback(response);
                },
                error: function error(_error2) {
                    $(that).removeClass('m-loader m-loader--light m-loader--right');
                    $(that).prop('disabled', false);
                    toastr.error("Error status " + _error2.status);
                }
            });
        });

        initData(state.review);

    },
    process: function(params) {
        var self = this;
        var state = self.state;

        state.steps = params.block;

        var blockLinks = {
            render: 'http://www.mocky.io/v2/5a9903472e0000d40f55348f',
            save: params.saveUrl,
            upload: params.uploadUrl
        };

        var textAreaResizer = function textAreaResizer() {
            $('textarea.js-auto-size').textareaAutoSize();
        };

        var initData = function(result) {
            $('#preload').remove();

            if(state.steps.settings.icon_size == undefined){
                state.steps.settings.icon_size = 75;
            }

            var stepsLength = state.steps.data.length,
                column = 3;

            if(stepsLength == 2){
                column = 4;
            }

            for (var i = 0; i < state.steps.data.length; i++) {
                generateCards('add', state.steps.data[i].id, state.steps.data[i].title, state.steps.data[i].description, state.steps.data[i].icon, column, state.steps.settings.description);
            }

            if (state.steps.settings.description && 'false' != state.steps.settings.description) {
                $('.steps-description').prop('checked', true);
            }

            var processCount = $('.process-count');

            for (var i = 0; i < processCount.length; i++) {
                if (column.toString() == processCount[i].value) {
                    processCount[i].checked = true;
                    $(processCount[i].parentNode).addClass('active');
                }
            }

            $("#steps-fontSize").slider({
                min: 12,
                max: 240,
                step: 12,
                value: state.steps.settings.icon_size,
                slide: function slide(event, ui) {
                    $(".steps-icon-size-show").text(ui.value);
                    $("#steps-size-icon").val(ui.value);
                    $('.steps-icon').css({
                        "fontSize": ui.value + 'px'
                    });
                    state.steps.settings.icon_size = ui.value;
                    console.log(state.steps.settings.icon_size);
                }
            });
            $('.steps-icon-size-show').text(state.steps.settings.icon_size);
            includeContent();
        }

        var generateCards = function generateCards(action, id, title, description, icon, col, cardDescription) {
            var showDescription = "";

            if(cardDescription == '0'){
                showDescription = 'hide-description';
            }

            var cardTemplate = '<li class="col-lg-' + col + ' margin-top-bottom process-column">\n               <div class="editor-card editor-tooltip__show">\n                   <div class="row">\n                       <div class="editor-card__icon-block col-12">\n                           <div class="editor-preview__block" data-toggle="modal" data-target="#preview-edit-modal" data-id="' + id + '">\n                               <div class="editor-preview__tooltip">edit</div>\n                               <span class="fa ' + icon + ' steps-icon" id="process-icon-' + id + '" style="font-size: '+state.steps.settings.icon_size+'px"></span>\n                           </div>\n                       </div>\n                       <div class="editor-card__title-block col-12">\n                           <div class="editor-textarea__text-edit-off">\n                               <textarea class="editor-textarea__h editor-textarea__h3 js-auto-size" data-id="' + id + '" data-textarea-title="title" rows="1" spellcheck="false" placeholder="Add title...">' + title + '</textarea>\n                               <div class="editor-textarea__text-edit-action">\n                                   <button class="btn btn-sm btn-success cursor-pointer editor-textarea__text-edit-save">Save</button>\n                                   <button class="btn btn-sm btn-secondary cursor-pointer editor-textarea__text-edit-close">Close</button>\n                               </div>\n                           </div>\n                       </div>\n                       <div class="editor-card__text-block col-12 ' + showDescription + '">\n                           <div class="editor_textarea-block">\n                               <div class="editor-textarea__text-edit-off">\n                                   <textarea class="editor_textarea__text js-auto-size" data-id="' + id + '" data-textarea-title="description" rows="1" spellcheck="false" placeholder="Add text...">' + description + '</textarea>\n                                   <div class="editor-textarea__text-edit-action">\n                                       <button class="btn btn-sm btn-success cursor-pointer editor-textarea__text-edit-save">Save</button>\n                                       <button class="btn btn-sm btn-secondary cursor-pointer editor-textarea__text-edit-close">Close</button>\n                                   </div>\n                               </div>\n                           </div>\n                       </div>\n                   </div>\n               </div>\n           </li>';
            $('#process-list').append(cardTemplate);
            textAreaResizer();
        };

        var includeContent = function includeContent() {
            $('.process-title').val(state.steps.header.title);
            $('.process-description').val(state.steps.header.description);
            textAreaResizer();
        };

        $(document).on('change', '.process-count', function () {
            state.steps.settings.column = $(this).val();
            $('#process-list').empty();

            if (parseInt(state.steps.settings.column) == 4) {
                state.steps.data.pop();
            }else{
                state.steps.data.push({
                    id: "4",
                    icon: "fa-picture-o",
                    title: '',
                    description: ''
                });
            }

            for (var i = 0; i < state.steps.data.length; i++) {
                generateCards('add', state.steps.data[i].id, state.steps.data[i].title, state.steps.data[i].description, state.steps.data[i].icon, state.steps.settings.column, state.steps.settings.description);
            }

        });

        $(document).on('change', '.steps-description', function () {

            if (this.checked) {
                state.steps.settings.description = '1';
            }else{
                state.steps.settings.description = '0';
            }

            $('#process-list').empty();

            for (var i = 0; i < state.steps.data.length; i++) {
                generateCards('add', state.steps.data[i].id, state.steps.data[i].title, state.steps.data[i].description, state.steps.data[i].icon, state.steps.settings.column, state.steps.settings.description);
            }


        });

        $(document).on('change', '.js-auto-size', function () {
            var stepsID = $(this).attr('data-id'),
                stepType = $(this).attr('data-textarea-title'),
                content = $(this).val();

            switch (stepType) {
                case "header-title":
                    state.steps.header.title = content;
                    break;
                case "header-description":
                    state.steps.header.description = content;
                    break;
                default:
                    for (var i = 0; i < state.steps.data.length; i++) {
                        if (state.steps.data[i].id.indexOf(stepsID) == 0) {
                            state.steps.data[i][stepType] = content;
                            return;
                        }
                    }
                    break;
            }
        });

        $(document).on('click', '.editor-preview__block', function () {
            var dataID = $(this).attr('data-id');
            state.actions.steps.activeIconId = dataID;
        });

        $(document).on('click', '.edit-modal__icons-action', function () {
            $('.edit-modal__icons-action').removeClass('active-icon');
            $(this).addClass('active-icon');
            state.actions.steps.activeIcon = $(this).attr('data-icon-name');
        });

        $(document).on('click', '.editor-preview__block', function () {
            var dataID = $(this).attr('data-id');
            state.actions.steps.activeIconId = dataID;
        });

        $(document).on('click', '#feature-saveIcon', function () {
            var currentID = state.actions.steps.activeIconId,
                iconClass = '#process-icon-' + currentID,
                classStroke = 'fa ' + state.actions.steps.activeIcon + ' steps-icon';

            for (var i = 0; i < state.steps.data.length; i++) {
                if (state.steps.data[i].id.indexOf(currentID) == 0) {
                    state.steps.data[i].icon = state.actions.steps.activeIcon;
                }
            }

            $(iconClass).removeAttr('class');
            $(iconClass).addClass(classStroke);
        });



        $(document).on('click', '#save-changes', function () {
            var that = this;
            $(that).addClass('m-loader m-loader--light m-loader--right');
            $(that).prop('disabled', true);
            $.ajax({
                url: blockLinks.save,
                data: {
                    content: state.steps
                },
                type: 'POST',
                success: function success(response) {
                    $(that).removeClass('m-loader m-loader--light m-loader--right');
                    $(that).prop('disabled', false);
                    self.saveCallback(response);
                },
                error: function error(_error2) {
                    $(that).removeClass('m-loader m-loader--light m-loader--right');
                    $(that).prop('disabled', false);
                    toastr.error("Error status " + _error2.status);
                }
            });
        });

        initData(state.steps);

    },
    initTextareaAutosizer: function() {
        var self = this;
        $(document).on('keydown', '.js-auto-size', function (e) {
            if (e.ctrlKey && e.keyCode == 13) {
                self.state.actions.editorText.save = true;
                $(self.state.actions.editorText.node).blur();
            }
        });

        $(document).on('focus', '.js-auto-size', function () {
            self.state.actions.editorText.node = this;
            self.state.actions.editorText.nodeText = this.value;
            self.state.actions.editorText.nodeHeight = this.style.height;

            var parentnode = this.parentNode,
                node = self.state.actions.editorText.node,
                nodeHeight = self.state.actions.editorText.nodeHeight,
                nodeText = self.state.actions.editorText.nodeText;

            $(parentnode).removeClass('editor-textarea__text-edit-off').addClass('editor-textarea__text-edit-on');

            $(document).on('click', '.editor-textarea__text-edit-close', function () {
                node.value = nodeText;
                node.style.height = nodeHeight;
                $('.js-auto-size').blur();
                $(parentnode).removeClass('editor-textarea__text-edit-on').addClass('editor-textarea__text-edit-off');
            });

            $('.editor-textarea__text-edit-save').on('mousedown', function () {
                self.state.actions.editorText.save = true;
                $(self.state.actions.editorText.node).blur();
            });
        });

        $(document).on('focusout', '.js-auto-size', function () {

            var node = self.state.actions.editorText.node,
                parentnode = node.parentNode;

            if (self.state.actions.editorText.save) {
                self.state.actions.editorText.save = false;
            } else {
                node.value = self.state.actions.editorText.nodeText;
                node.style.height = self.state.actions.editorText.nodeHeight;
            }

            $(parentnode).removeClass('editor-textarea__text-edit-on').addClass('editor-textarea__text-edit-off');
        });
    },
    saveCallback: function(response) {
        if ('undefined' == typeof response.status) {
            return;
        }

        if ('success' == response.status) {
            toastr.success("Success");
        }

        if ('error' == response.status) {
            toastr.error(response.error);
        }
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

        _.forEach(params.messages, function(message){
            if (message.success) {
                toastr.success(message.success);
            }
            if (message.warning) {
                toastr.warning(message.warning);
            }
            if (message.error) {
                toastr.error(message.error);
            }
        });
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
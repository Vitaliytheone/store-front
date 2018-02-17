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

            case 'review':
                self.review(params);
            break;

            case 'process':
                self.process(params);
            break;
        }
    },
    slider: function(params) {
        var self = this;
        var state = self.state;

        state.slider = params.block;

        var blockLinks = {
            render: 'http://www.mocky.io/v2/5a3d15d8310000471fb593d1',
            save: params.saveUrl,
            upload: params.uploadUrl
        };

        var textAreaResizer = function textAreaResizer() {
            $('textarea.js-auto-size').textareaAutoSize();
        };

        var swiperSlider = new Swiper('.block-slider', {
            pagination: '.swiper-pagination',
            paginationClickable: true,
            scrollbarDraggable: false,
            simulateTouch: false
        });

        var promise = $.ajax({
            method: 'get',
            url: blockLinks.render
        }).then(function (result) {
            $('#preload').remove();

            state.slider = result;

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
                if (sliderInterval[i].value.toLocaleLowerCase() == result.settings.rotationInterval.toLocaleLowerCase()) {
                    sliderInterval[i].checked = true;
                    $(sliderInterval[i].parentNode).addClass('active');
                }
            }
        }).catch(function (err) {
            console.log(err);
            $('.prealoder-text').text('Server is not available');
        });

        var generateSlide = function generateSlide(action, id, title, description, button, image) {
            var template = '<div class="swiper-slide">\n                    <div class="slider__block-wrap slider__block-' + id + ' d-flex flex-wrap">\n\n                        <div class="editor-tooltip bg-danger editor-tooltip__right-top editor-action__delete-review editor-action__delete"  data-id="' + id + '" data-type="review" data-toggle="modal" data-target="#delete-feature-modal">\n                            <span class="fa fa-times"></span>\n                        </div>\n                        <div class="col-md-4">\n                            <label for="slider-image-' + id + '" class="slider__image slider__image_' + id + ' slider-image-' + id + '" style="background-image: url(' + image + ');">\n                                <input id="slider-image-' + id + '" type="file" name="slider-image-' + id + '" class="editor-slider-image-input" data-id="' + id + '">\n                            </label>\n                        </div>\n                        <div class="col">\n                            <div class="editor-block__reviev_name">\n                                <div class="editor-textarea__text-edit-off">\n                                    <textarea class="editor-textarea__h text-left editor-textarea__h3 js-auto-size" data-id="' + id + '" data-textarea-title="title" rows="1" spellcheck="false" placeholder="Add title...">' + title + '</textarea>\n                                    <div class="editor-textarea__text-edit-action">\n                                        <button class="btn btn-sm btn-success cursor-pointer editor-textarea__text-edit-save">Save</button>\n                                        <button class="btn btn-sm btn-secondary cursor-pointer editor-textarea__text-edit-close">Close</button>\n                                    </div>\n                                </div>\n                            </div>\n                            <div class="editor-block__description">\n                                <div class="editor-textarea__text-edit-off">\n                                    <textarea class="editor_textarea__text js-auto-size" data-id="' + id + '" data-textarea-title="description" rows="1" spellcheck="false" placeholder="Add text...">' + description + '</textarea>\n                                    <div class="editor-textarea__text-edit-action">\n                                        <button class="btn btn-sm btn-success cursor-pointer editor-textarea__text-edit-save">Save</button>\n                                        <button class="btn btn-sm btn-secondary cursor-pointer editor-textarea__text-edit-close">Close</button>\n                                    </div>\n                                </div>\n                            </div>\n                            <div class="editor-block__description">\n                                <button class="learn-more learn-more-' + id + '" data-toggle="modal" data-target="#learn-more" data-id="' + id + '">' + button + '</button>\n                            </div>\n                        </div>\n\n                    </div>\n\n                </div>';

            switch (action) {
                case 'render':
                    swiperSlider.prependSlide(template);
                    swiperSlider.slideTo(0);
                    break;
                case 'add':
                    swiperSlider.prependSlide(template);
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

            textAreaResizer();
        };

        $('.new-preview').on('click', function (e) {
            e.preventDefault();
            var lastSlide = '';
            if (state.slider.data.length == 0) {
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
            var classId = '.' + this.id,
                dataID = $(this).attr('data-id');

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
                error: function error(_error) {}
            });
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
            state.slider.settings.rotationInterval = $(this).val();
        });

        $(document).on('click', '#save-changes', function () {
            $.ajax({
                url: blockLinks.save,
                data: {
                    content: state.slider
                },
                type: 'POST',
                success: function success(response) {
                    self.saveCallback(response);
                },
                error: function error(_error2) {
                    toastr.error("Error status " + _error2.status);
                }
            });
        });
    },
    features: function(params) {
        var self = this;
        var state = self.state;

        state.features = params.block;

        var blockLinks = {
            render: 'http://www.mocky.io/v2/5a4108593200006a18ac345e',
            save: params.saveUrl,
            upload: params.uploadUrl
        };

        var textAreaResizer = function textAreaResizer() {
            $('textarea.js-auto-size').textareaAutoSize();
        };

        var promise = $.ajax({
            method: 'get',
            url: blockLinks.render
        }).then(function (result) {
            console.log(result);
            $('#preload').remove();

            state.feature = result;

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
                value: state.feature.settings.iconSize,
                slide: function slide(event, ui) {
                    $(".feature-icon-size-show").text(ui.value);
                    $("#feature-size-icon").val(ui.value);
                    $('.feature-icon').css({
                        "fontSize": ui.value + 'px'
                    });
                    state.feature.settings.iconSize = ui.value;
                }
            });
            $('.feature-icon-size-show').text(state.feature.settings.iconSize);

            for (var i = 0; i < state.feature.data.length; i++) {
                generateCards('render', state.feature.data[i].id, state.feature.data[i].title, state.feature.data[i].description, state.feature.data[i].icon);
            }

            $("#feature-list").dragsort({
                dragBetween: true,
                dragSelector: ".editor-action__drag",
                placeHolderTemplate: '<li class="col margin-top-bottom editor-placeholder-move"><div class="editor-card editor-tooltip__show placeholder-template d-flex align-items-lg-center justify-content-center"><span>Insert</span></div></li>'
            });

            includeContent();
            textAreaResizer();
        }).catch(function (err) {
            console.log(err);
        });

        var generateCards = function generateCards(action, id, title, description, icon) {
            var iconSize = state.feature.settings.iconSize,
                column = state.feature.settings.column;

            var featureCardTemplate = '<li class="col-lg-' + column + ' margin-top-bottom feature-id-' + id + '">\n                    <div class="editor-card editor-tooltip__show">\n                        <div class="row">\n                            <div class="editor-tooltip bg-success editor-tooltip__right-top editor-action__drag">move</div>\n                            <div class="editor-tooltip bg-danger editor-tooltip__left-top editor-action__delete"  data-id="' + id + '" data-type="feature" data-toggle="modal" data-target="#delete-feature-modal">\n                                <span class="fa fa-times"></span>\n                            </div>\n\n                            <div class="editor-card__icon-block col-12">\n                                <div class="editor-preview__block" data-toggle="modal" data-target="#preview-edit-modal" data-id="' + id + '">\n                                    <div class="editor-preview__tooltip">edit</div>\n                                    <span class="fa ' + icon + ' feature-icon" id="feature-icon-' + id + '" style="fonts-size: ' + iconSize + 'px;"></span>\n                                </div>\n                            </div>\n                            <div class="editor-card__title-block col-12">\n                                <div class="editor-textarea__text-edit-off">\n                                    <textarea class="editor-textarea__h editor-textarea__h3 js-auto-size" data-id="' + id + '" data-textarea-title="title" rows="1" spellcheck="false" placeholder="Add title...">' + title + '</textarea>\n                                    <div class="editor-textarea__text-edit-action">\n                                        <button class="btn btn-sm btn-success cursor-pointer editor-textarea__text-edit-save">Save</button>\n                                        <button class="btn btn-sm btn-secondary cursor-pointer editor-textarea__text-edit-close">Close</button>\n                                    </div>\n                                </div>\n                            </div>\n                            <div class="editor-card__text-block col-12">\n                                <div class="editor_textarea-block">\n                                    <div class="editor-textarea__text-edit-off">\n                                    <textarea class="editor_textarea__text js-auto-size" rows="1" spellcheck="false" data-id="' + id + '" data-textarea-title="description" placeholder="Add text...">' + description + '</textarea>\n                                        <div class="editor-textarea__text-edit-action">\n                                            <button class="btn btn-sm btn-success cursor-pointer editor-textarea__text-edit-save">Save</button>\n                                            <button class="btn btn-sm btn-secondary cursor-pointer editor-textarea__text-edit-close">Close</button>\n                                        </div>\n                                    </div>\n                                </div>\n                            </div>\n\n                        </div>\n                    </div>\n                </li>';

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
            state.feature.settings.align = $(this).val();
        });

        $(document).on('change', '.feature-column', function () {
            state.feature.settings.column = $(this).val();
        });

        $(document).on('click', '#save-changes', function () {

            $.ajax({
                url: blockLinks.save,
                data: {
                    content: state.feature
                },
                type: 'POST',
                success: function success(response) {
                    self.saveCallback(response);
                },
                error: function error(_error) {}
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
    },
    review: function(params) {
        var self = this;
        var state = self.state;

        state.review = params.block;

        var blockLinks = {
            render: 'http://www.mocky.io/v2/5a44a9752e00002c03708668',
            save: params.saveUrl,
            upload: params.uploadUrl
        };

        var textAreaResizer = function textAreaResizer() {
            $('textarea.js-auto-size').textareaAutoSize();
        };

        var swiperSlider = new Swiper('.block-slider', {
            pagination: '.swiper-pagination',
            paginationClickable: true,
            scrollbarDraggable: false,
            simulateTouch: false,
            centeredSlides: false
        });

        var promise = $.ajax({
            method: 'get',
            url: blockLinks.render
        }).then(function (result) {
            console.log(result);
            $('#preload').remove();
            state.review = result;

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
        }).catch(function (err) {
            console.log(err);
            $('.prealoder-text').text('Server is not available');
        });

        var generateSlide = function generateSlide(action, id, name, rating, description, image) {

            if (!image) {
                image = 'http://www.breps.be/frontend/core/layout/images/default_author_avatar.gif';
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
                    swiperSlider.prependSlide(template);
                    swiperSlider.slideTo(0);
                    break;
                case 'add':
                    swiperSlider.prependSlide(template);
                    swiperSlider.slideTo(0);
                    state.review.data.push({
                        "id": id.toString(),
                        "name": name,
                        "description": description,
                        "image": false,
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
                    return;
                }
            }
        });

        $(document).on('change', '.review-column', function () {

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
                lastSlide = "1";
            } else {
                lastSlide = parseInt(state.review.data[state.review.data.length - 1].id) + 1;
            }
            generateSlide('add', lastSlide, '', '', '', false);
        });

        $(document).on('change', '.editor-preview__avatar-input', function () {
            var classId = '.' + this.id,
                dataID = $(this).attr('data-id');

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

                    if ('error' == response.status) {
                        toastr.error(response.error);
                    }

                    if ('success' == response.status) {
                        $(classId).css('background-image', 'url(' + response.link + ')');

                        for (var i = 0; i < state.review.data.length; i++) {
                            if (state.review.data[i].id.indexOf(dataID) == 0) {
                                state.review.data[i].image = response.link;
                                $(classId).css('background-image', 'url(http://www.breps.be/frontend/core/layout/images/default_author_avatar.gif)');
                                return;
                            }
                        }
                    }
                },
                error: function error(_error) {
                    $(classId).css('background-image', 'url(http://www.breps.be/frontend/core/layout/images/default_author_avatar.gif)');
                }
            });
        });

        $(document).on('click', '.review-image-delete', function () {
            var classId = '.' + this.id,
                dataID = $(this).attr('data-id');

            for (var i = 0; i < state.review.data.length; i++) {
                if (state.review.data[i].id.indexOf(dataID) == 0) {
                    state.review.data[i].image = false;
                    $(classId).css('background-image', 'url(http://www.breps.be/frontend/core/layout/images/default_author_avatar.gif)');
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
            $.ajax({
                url: blockLinks.save,
                data: {
                    content: state.review
                },
                type: 'POST',
                success: function success(response) {
                    self.saveCallback(response);
                },
                error: function error(_error2) {}
            });
        });
    },
    process: function(params) {
        var self = this;
        var state = self.state;

        state.steps = params.block;

        var blockLinks = {
            render: 'http://www.mocky.io/v2/5a4258c43000000f1a709ddf',
            save: params.saveUrl,
            upload: params.uploadUrl
        };

        var textAreaResizer = function textAreaResizer() {
            $('textarea.js-auto-size').textareaAutoSize();
        };

        var promise = $.ajax({
            method: 'get',
            url: blockLinks.render
        }).then(function (result) {
            console.log(result);
            $('#preload').remove();
            state.steps = result;

            var countStep = "";
            if (state.steps.settings.count == "4") {
                countStep = state.steps.data.length;
            } else {
                countStep = state.steps.data.length - 1;
            }

            var column = 0;
            if (state.steps.settings.count == "3") {
                column = "4";
            } else {
                column = "3";
            }

            for (var i = 0; i < countStep; i++) {
                generateCards('add', state.steps.data[i].id, state.steps.data[i].title, state.steps.data[i].description, state.steps.data[i].icon, column, state.steps.settings.description);
            }

            if (state.steps.settings.description) {
                $('.steps-description').checked = true;
            }

            var processCount = $('.process-count');

            for (var i = 0; i < processCount.length; i++) {
                if (column == processCount[i].value) {
                    processCount[i].checked = true;
                    $(processCount[i].parentNode).addClass('active');
                }
            }

            includeContent();
        }).catch(function (err) {
            console.log(err);
            $('.prealoder-text').text('Server is not available');
            toastr.error("Error");
        });

        var generateCards = function generateCards(action, id, title, description, icon, col, cardDescription) {

            var showDescription = "";

            if (!cardDescription) {
                showDescription = 'hide-description';
            }

            var cardTemplate = '<li class="col-lg-' + col + ' margin-top-bottom process-column">\n               <div class="editor-card editor-tooltip__show">\n                   <div class="row">\n                       <div class="editor-card__icon-block col-12">\n                           <div class="editor-preview__block" data-toggle="modal" data-target="#preview-edit-modal" data-id="' + id + '">\n                               <div class="editor-preview__tooltip">edit</div>\n                               <span class="fa ' + icon + ' feature-icon" id="process-icon-' + id + '"></span>\n                           </div>\n                       </div>\n                       <div class="editor-card__title-block col-12">\n                           <div class="editor-textarea__text-edit-off">\n                               <textarea class="editor-textarea__h editor-textarea__h3 js-auto-size" data-id="' + id + '" data-textarea-title="title" rows="1" spellcheck="false" placeholder="Add title...">' + title + '</textarea>\n                               <div class="editor-textarea__text-edit-action">\n                                   <button class="btn btn-sm btn-success cursor-pointer editor-textarea__text-edit-save">Save</button>\n                                   <button class="btn btn-sm btn-secondary cursor-pointer editor-textarea__text-edit-close">Close</button>\n                               </div>\n                           </div>\n                       </div>\n                       <div class="editor-card__text-block col-12 ' + showDescription + '">\n                           <div class="editor_textarea-block">\n                               <div class="editor-textarea__text-edit-off">\n                                   <textarea class="editor_textarea__text js-auto-size" data-id="' + id + '" data-textarea-title="description" rows="1" spellcheck="false" placeholder="Add text...">' + description + '</textarea>\n                                   <div class="editor-textarea__text-edit-action">\n                                       <button class="btn btn-sm btn-success cursor-pointer editor-textarea__text-edit-save">Save</button>\n                                       <button class="btn btn-sm btn-secondary cursor-pointer editor-textarea__text-edit-close">Close</button>\n                                   </div>\n                               </div>\n                           </div>\n                       </div>\n                   </div>\n               </div>\n           </li>';
            $('#process-list').append(cardTemplate);
            textAreaResizer();
        };

        var includeContent = function includeContent() {
            $('.process-title').val(state.steps.header.title);
            $('.process-description').val(state.steps.header.description);
            textAreaResizer();
        };

        $(document).on('change', '.process-count', function () {
            state.steps.settings.count = $(this).val();
            $('#process-list').empty();
            if (parseInt(state.steps.settings.count) == 4) {
                for (var i = 0; i < 3; i++) {
                    generateCards('add', state.steps.data[i].id, state.steps.data[i].title, state.steps.data[i].description, state.steps.data[i].icon, state.steps.settings.count, state.steps.settings.description);
                }
            } else {
                for (var i = 0; i < state.steps.data.length; i++) {
                    generateCards('add', state.steps.data[i].id, state.steps.data[i].title, state.steps.data[i].description, state.steps.data[i].icon, state.steps.settings.count, state.steps.settings.description);
                }
            }
        });

        $(document).on('change', '.steps-description', function () {
            state.steps.settings.description = this.checked;
            $('#process-list').empty();
            if (this.checked) {
                if (parseInt(state.steps.settings.count) == 4) {
                    for (var i = 0; i < 3; i++) {
                        generateCards('add', state.steps.data[i].id, state.steps.data[i].title, state.steps.data[i].description, state.steps.data[i].icon, state.steps.settings.count, state.steps.settings.description);
                    }
                } else {
                    for (var i = 0; i < state.steps.data.length; i++) {
                        generateCards('add', state.steps.data[i].id, state.steps.data[i].title, state.steps.data[i].description, state.steps.data[i].icon, state.steps.settings.count, state.steps.settings.description);
                    }
                }
            } else {
                if (parseInt(state.steps.settings.count) == 4) {
                    for (var i = 0; i < 3; i++) {
                        generateCards('add', state.steps.data[i].id, state.steps.data[i].title, state.steps.data[i].description, state.steps.data[i].icon, state.steps.settings.count, state.steps.settings.description);
                    }
                } else {
                    for (var i = 0; i < state.steps.data.length; i++) {
                        generateCards('add', state.steps.data[i].id, state.steps.data[i].title, state.steps.data[i].description, state.steps.data[i].icon, state.steps.settings.count, state.steps.settings.description);
                    }
                }
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
                classStroke = 'fa ' + state.actions.steps.activeIcon + ' feature-icon';

            for (var i = 0; i < state.steps.data.length; i++) {
                if (state.steps.data[i].id.indexOf(currentID) == 0) {
                    state.steps.data[i].icon = state.actions.steps.activeIcon;
                }
            }

            $(iconClass).removeAttr('class');
            $(iconClass).addClass(classStroke);
        });

        $(document).on('click', '#save-changes', function () {
            $.ajax({
                url: blockLinks.save,
                data: {
                    content: state.steps
                },
                type: 'POST',
                success: function success(response) {
                    self.saveCallback(response);
                },
                error: function error(_error) {
                    toastr.error("Error status " + _error.status);
                }
            });
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
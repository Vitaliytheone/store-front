"use strict";

(function ($, window, document, undefined) {

    var pluginName = "textareaAutoSize";
    var pluginDataName = "plugin_" + pluginName;

    var containsText = function containsText(value) {
        return value.replace(/\s/g, '').length > 0;
    };

    function Plugin(element, options) {
        this.element = element;
        this.$element = $(element);
        this.init();
    }

    Plugin.prototype = {
        init: function init() {
            var height = this.$element.outerHeight();
            var diff = parseInt(this.$element.css('paddingBottom')) + parseInt(this.$element.css('paddingTop')) || 0;

            if (containsText(this.element.value)) {
                this.$element.height(this.element.scrollHeight - diff);
            }

            // keyup is required for IE to properly reset height when deleting text
            this.$element.on('input keyup', function (event) {
                var $window = $(window);
                var currentScrollPosition = $window.scrollTop();

                $(this).height(0).height(this.scrollHeight - diff);
                $window.scrollTop(currentScrollPosition);
            });
        }
    };

    $.fn[pluginName] = function (options) {
        this.each(function () {
            if (!$.data(this, pluginDataName)) {
                $.data(this, pluginDataName, new Plugin(this, options));
            }
        });
        return this;
    };
})(jQuery, window, document);

$('textarea.js-auto-size').textareaAutoSize();

$(document).ready(function () {
    $(document).on('keydown', '.js-auto-size', function (e) {
        if (e.ctrlKey && e.keyCode == 13) {
            state.actions.editorText.save = true;
            $(state.actions.editorText.node).blur();
        }
    });

    $(document).on('focus', '.js-auto-size', function () {
        state.actions.editorText.node = this;
        state.actions.editorText.nodeText = this.value;
        state.actions.editorText.nodeHeight = this.style.height;

        var parentnode = this.parentNode,
            node = state.actions.editorText.node,
            nodeHeight = state.actions.editorText.nodeHeight,
            nodeText = state.actions.editorText.nodeText;

        $(parentnode).removeClass('editor-textarea__text-edit-off').addClass('editor-textarea__text-edit-on');

        $(document).on('click', '.editor-textarea__text-edit-close', function () {
            node.value = nodeText;
            node.style.height = nodeHeight;
            $('.js-auto-size').blur();
            $(parentnode).removeClass('editor-textarea__text-edit-on').addClass('editor-textarea__text-edit-off');
        });

        $('.editor-textarea__text-edit-save').on('mousedown', function () {
            state.actions.editorText.save = true;
            $(state.actions.editorText.node).blur();
        });
    });

    $(document).on('focusout', '.js-auto-size', function () {

        var node = state.actions.editorText.node,
            parentnode = node.parentNode;

        if (state.actions.editorText.save) {
            state.actions.editorText.save = false;
        } else {
            node.value = state.actions.editorText.nodeText;
            node.style.height = state.actions.editorText.nodeHeight;
        }

        $(parentnode).removeClass('editor-textarea__text-edit-on').addClass('editor-textarea__text-edit-off');
    });
});
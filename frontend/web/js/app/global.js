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
                errorSummary.hide();
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
                        errorSummary.show();
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
    }
};
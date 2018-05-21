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

            iframe.attr('src', link.attr('href'));

            modal.modal('show');

            return false;
        });
    }
};
customModule.adminFiles = {
    run: function (params) {

        /*****************************************************************************************************
         *                     CodeMirror activation
         *****************************************************************************************************/

        var fileType = params.extension || null;

        var $codeMirror = $('#code'),
            codeMirror,
            contentOnInit;

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

        if ($codeMirror.length > 0) {
            codeMirror = CodeMirror.fromTextArea($codeMirror[0], codeMirroSetting);
            contentOnInit = codeMirror.getValue();
        }

        /*****************************************************************************************************
         *               Ajax submit form
         *****************************************************************************************************/

        toastr.options = {
            "positionClass": "toast-bottom-right",
            "preventDuplicates": false
        };

        var $editForm = $('#editThemeForm'),
            $modalLoader = $editForm.find('.modal-loader'),
            actionUrl = $editForm.attr('action');

        $editForm.submit(function (e) {
            e.preventDefault();
            if ($editForm.hasClass('process')) {
                return false;
            }

            $modalLoader.removeClass('hidden');
            $editForm.addClass('process');
            custom.ajax({
                url: actionUrl,
                type: "POST",
                data: $(this).serialize(),

                success: function (data, textStatus, jqXHR){
                    $modalLoader.addClass('hidden');
                    $editForm.removeClass('process');
                    if ('success' === data.status) {
                        // Send message
                        if (data.message !== undefined) {
                            toastr.success(data.message);
                        }
                    } else {
                        // Send message
                        if (data.message !== undefined) {
                            toastr.error(data.message);
                        }
                    }
                },

                error: function (jqXHR, textStatus, errorThrown){
                    $modalLoader.addClass('hidden');
                    $editForm.removeClass('process');
                    console.log('Error on service save', jqXHR, textStatus, errorThrown);
                }
            });
        });


        /*****************************************************************************************************
         *               Modal submit close
         *****************************************************************************************************/
        var $modalSubmitClose = $('#modal_submit_close');

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

        $('.delete-file').click(function(e) {
            e.preventDefault();
            var link = $(this);
            custom.confirm(link.data('title'), '', function() {
                $.ajax({
                    url: link.attr('href'),
                    type: 'POST',
                    dataType: 'json',
                    data: link.data('params'),
                    success: function (response) {
                        if ('error' === response.status) {
                            if (response.message !== undefined) {
                                toastr.error(response.message);
                            }
                        } else {
                            if (response.message !== undefined) {
                                toastr.success(response.message);
                            }
                            location.reload();
                        }
                    }
                });
            });
            return false;
        });

        $('.rename-file').click(function(e) {
            e.preventDefault();
            var link = $(this);
            var action = link.attr('href');
            var form = $('#renameFileForm');
            var modal = $('#renameFileModal');
            var errorBlock = $('#renameFileError', form);
            var details = link.data('details');

            form.attr('action', action);

            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('#renamefileform-name', form).val(details.name);

            modal.modal('show');
            return false;
        });

        $(document).on('click', '#renameFileButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#renameFileForm');

            custom.sendFrom(btn, form, {
                data: form.serialize(),
                callback : function(response) {
                    $('#renameFileModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });

        $('.create-file').click(function(e) {
            e.preventDefault();
            var link = $(this);
            var action = link.attr('href');
            var form = $('#createFileForm');
            var modal = $('#createFileModal');
            var errorBlock = $('#createFileError', form);
            var type = link.data('type');
            var extension = link.data('extension');

            form.attr('action', action);

            errorBlock.addClass('hidden');
            errorBlock.html('');

            $('#createfileform-name', form).val('');
            $('#createfileform-file_type', form).val(type);

            if (extension) {
                $('#extension', form).html('.' + extension);
            }

            modal.modal('show');
            return false;
        });

        $(document).on('click', '#createFileButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#createFileForm');
            var formData = new FormData(form[0]);

            custom.sendFrom(btn, form, {
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                callback : function(response) {
                    $('#createFileModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });

        $('.upload-file').click(function(e) {
            e.preventDefault();
            var link = $(this);
            var action = link.attr('href');
            var form = $('#uploadFileForm');
            var modal = $('#uploadFileModal');
            var errorBlock = $('#uploadFileError', form);

            form.attr('action', action);

            errorBlock.addClass('hidden');
            errorBlock.html('');

            modal.modal('show');
            return false;
        });

        $(document).on('click', '#uploadFileButton', function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $('#uploadFileForm');
            var formData = new FormData(form[0]);

            custom.sendFrom(btn, form, {
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                callback : function(response) {
                    $('#uploadFileModal').modal('hide');
                    location.reload();
                }
            });

            return false;
        });
    }
};
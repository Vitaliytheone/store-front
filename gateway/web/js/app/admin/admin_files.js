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
            codeType = 'js';

        switch (fileType){
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
                window.location = _node.a_attr.href;
            }
        });

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

                        // Update JS-tree item icon
                        if (data.filename) {

                            var treeNode = $filesTree.jstree(true).get_node(data.filename),
                                $nodeDoom = $filesTree.jstree(true).get_node(data.filename, true);

                            // Update icon
                            $filesTree.jstree(true).set_icon(treeNode, 'fa fa-file');

                            // Add modified at
                            if (data.modified_at && $nodeDoom) {
                                $nodeDoom.find('a.jstree-anchor').prepend(data.modified_at);
                            }
                        }

                        // Send message
                        if (data.message !== undefined) {
                            toastr.success(data.message);
                        }
                    } else {

                        // Send message
                        if (data.message !== undefined) {
                            toastr.success(data.message);
                        }
                        toastr.error(data.message);
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
    }
};
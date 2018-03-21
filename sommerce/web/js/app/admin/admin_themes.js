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
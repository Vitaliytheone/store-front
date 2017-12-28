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

        var $modal = $('#cancel-modal');


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
         *                     Modal cancel
         *****************************************************************************************************/
        (function (){
            if (codeMirror === undefined) {
                return false;
            }

            var currentContent;

            $('#cancel-btn').click(function(e) {
                currentContent = codeMirror.getValue();

                if (currentContent !== contentOnInit) {

                    $modal.modal('show');

                    return false;
                }

                return true;
            });

        })({}, function (){});

    }
};
/**
 * /admin/settings/pages custom js module
 * @type {{run: customModule.settings.run}}
 */
customModule.adminThemes = {
    run: function (params) {
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
         *                     CodeMirror activation
         *****************************************************************************************************/

        var $codeMirror = $('#codemirror');
        if ($codeMirror.length > 0) {
            CodeMirror.fromTextArea($codeMirror[0], {
                lineNumbers: true
            });
        }
    }
};
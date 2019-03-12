'use strict';

$(document).ready(function () {
    var updateOutput = function updateOutput(e) {
        var list = e.length ? e : $(e.target),
            output = list.data('output');
        if (window.JSON) {
            console.log('Ok');
        } else {
            output.html('JSON browser support required for this demo.');
        }
    };
    if ($('#nestable').length > 0) {

        $('#nestable').nestable({
            group: 0,
            maxDepth: 3
        }).on('change', updateOutput);
        updateOutput($('#nestable').data('output', $('#nestable-output')));
    }
});

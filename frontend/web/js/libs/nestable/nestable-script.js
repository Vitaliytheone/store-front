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

$(document).ready(function () {

    if ($('.sortable').length > 0) {
        // Sort the parents
        $(".sortable").sortable({
            containment: "document",
            items: "> div",
            handle: ".move",
            tolerance: "pointer",
            cursor: "move",
            opacity: 0.7,
            revert: false,
            delay: false,
            placeholder: "movable-placeholder"
        });

        // Sort the children
        $(".group-items").sortable({
            items: "> div",
            handle: ".move",
            tolerance: "pointer",
            containment: "parent"
        });
    }
});
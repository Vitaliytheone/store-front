$(function() {
    $('form').submit(function (e) {
        var form = $(this);

        if (form.hasClass('submitted')) {
            e.preventDefault();
            return false;
        }
        form.addClass('submitted');
    });
});
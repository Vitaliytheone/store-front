$(function() {
    if ($(window).width() > 991) {
        $('.navbar .dropdown').hover(function () {
            $(this).find('.dropdown-menu').first().stop(true, true).slideDown(150);
        }, function () {
            $(this).find('.dropdown-menu').first().stop(true, true).slideUp(105);
        });
    }

    $('form').submit(function (e) {
        var form = $(this);

        if (form.hasClass('submitted')) {
            e.preventDefault();
            return false;
        }

        form.addClass('submitted');
    });
});
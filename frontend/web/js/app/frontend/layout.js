customModule.frontendLayout = {
    run : function(params) {
        var self = this;

        var reviewsSlider = new Swiper('.swiper-container', {
            pagination: '.swiper-pagination',
            paginationClickable: true
        });

        var mainSlider = new Swiper('.main-slider', {
            paginationClickable: true,
            autoplay: 3000,
            pagination: '.main-slider-pagination'
        });

        if ($(window).width() > 991) {
            $('.navbar .dropdown').hover(function () {
                $(this).find('.dropdown-menu').first().stop(true, true).slideDown(150);
            }, function () {
                $(this).find('.dropdown-menu').first().stop(true, true).slideUp(105);
            });
        }
    }
};
;(function ($) {
    "use strict";
    
    /* ------------------------------------------------------------------------- *
     * COMMON VARIABLES
     * ------------------------------------------------------------------------- */
    var $wn = $(window),
        $body = $('body');

    /* ------------------------------------------------------------------------- *
     * CHECK DATA
     * ------------------------------------------------------------------------- */
    var checkData = function (data, value) {
        return typeof data === 'undefined' ? value : data;
    };

    $(function () {
        /* ------------------------------------------------------------------------- *
         * BACKGROUND IMAGE
         * ------------------------------------------------------------------------- */
        var $bgImg = $('[data-bg-img]');
        
        $bgImg.each(function () {
            var $t = $(this);

            $t.css('background-image', 'url(' + $t.data('bg-img') + ')').addClass('bg--img bg--overlay').attr('data-rjs', 2).removeAttr('data-bg-img');
        });

        /* ------------------------------------------------------------------------- *
         * STICKY
         * ------------------------------------------------------------------------- */
        var $sticky = $('[data-trigger="sticky"]');
        
        $sticky.each(function () {
            $(this).sticky({
                zIndex: 999
            });
        });

        /* ------------------------------------------------------------------------- *
         * SLIDER SECTION
         * ------------------------------------------------------------------------- */
        var $banner = $('.banner--section'),
            $bannerSlider = $banner.find('.banner--slider');

        $bannerSlider.on('initialized.owl.carousel', function (e) {
            var $el = $( e.currentTarget );

            $el.css( 'height', $el.outerHeight() );
        });

        /* ------------------------------------------------------------------------- *
         * OWL CAROUSEL
         * ------------------------------------------------------------------------- */
        var $owlCarousel = $('.owl-carousel');
        
        $owlCarousel.each(function () {
            var $t = $(this),
                effectSlide = $t.data('owl-effect');
          
          	switch(effectSlide){
            	case 'slide':
                	effectSlide = 'slideOutDown';
                break;
                case 'fade':
                	effectSlide = 'fadeOut';
                break;
                default: 
                	effectSlide = 'slideOutDown';
                break;
            };
            
            $t.owlCarousel({
                items: checkData( $t.data('owl-items'), 1 ),
                margin: checkData( $t.data('owl-margin'), 0 ),
                loop: checkData( $t.data('owl-loop'), true ),
                autoplay: checkData( $t.data('owl-autoplay'), true ),
                smartSpeed: checkData( $t.data('owl-speed'), 1200 ),
                autoplaySpeed: 1600,
              	animateOut: effectSlide,
                mouseDrag: checkData( $t.data('owl-drag'), true ),
                nav: checkData( $t.data('owl-nav'), false ),
                navText: ['', ''],
                dots: checkData( $t.data('owl-dots'), false ),
                responsive: checkData( $t.data('owl-responsive'), {} )
            });
        });
        
        /* ------------------------------------------------------------------------- *
         * COUNTER UP
         * ------------------------------------------------------------------------- */
        var $counterUp = $('[data-counter-up="numbers"]');
            
        if ( $counterUp.length ) {
            $counterUp.counterUp({
                delay: 10,
                time: 1000
            });
        }
        
        /* -------------------------------------------------------------------------*
         * COUNTDOWN
         * -------------------------------------------------------------------------*/
        var $countDown = $('[data-countdown]');
            
        $countDown.each(function () {
            var $t = $(this);
            
            $t.countdown($t.data('countdown'), function(e) {
                $(this).html( '<ul>' + e.strftime('<li><strong>%D</strong><span>Day</span></li><li><strong>%H</strong><span>Hour</span></li><li><strong>%M</strong><span>Min</span></li><li><strong>%S</strong><span>Sec</span></li>') + '</ul>' );
            });
        });

        /* ------------------------------------------------------------------------- *
         * BACK TO TOP BUTTON
         * ------------------------------------------------------------------------- */
        var $backToTop = $('.back-to-top-btn');

        $backToTop.on('click', 'a', function (e) {
            e.preventDefault();

            $('html, body').animate({
                scrollTop: 0
            }, 1200);
        });
    });
    
    $wn.on('load', function () {
        /* ------------------------------------------------------------------------- *
         * BODY SCROLLING
         * ------------------------------------------------------------------------- */
        var isBodyScrolling = function () {
            if ( $wn.scrollTop() > 1 ) {
                $body.addClass('isScrolling');
            } else {
                $body.removeClass('isScrolling');
            }
        };

        isBodyScrolling();
        $wn.on('scroll', isBodyScrolling);


    });
  
    $('form').submit(function (e) {
        var form = $(this);

        if (form.hasClass('submitted')) {
            e.preventDefault();
            return false;
        }

        form.addClass('submitted');
    });
})(jQuery);
;(function ($) {
    "use strict";
    
    /* ------------------------------------------------------------------------- *
     * COMMON VARIABLES
     * ------------------------------------------------------------------------- */
    var $wn = $(window),
        $body = $('body'),
        isRTL = $('html').attr('dir') === 'rtl' ? true : false;
    
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
         * TESTIMONIAL SECTION
         * ------------------------------------------------------------------------- */
        var $testimonial = $('.testimonial--section'),
            $testimonialClients = $testimonial.find('.testimonial--clients'),
            testimonialClientsI = $testimonialClients.data('increment');

        $testimonialClients.on('changed.owl.carousel', function (e) {
            var item = $(e.currentTarget).find('.owl-item')[ e.item.index + testimonialClientsI ],
                target = $( item ).children().data('target');

            $( target ).addClass('active in').siblings().removeClass('active in');
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
                rtl: isRTL,
                items: checkData( $t.data('owl-items'), 1 ),
                margin: checkData( $t.data('owl-margin'), 0 ),
                loop: checkData( $t.data('owl-loop'), true ),
                autoplay: checkData( $t.data('owl-autoplay'), true ),
                smartSpeed: checkData( $t.data('owl-speed'), 1200 ),
                autoplaySpeed: 1600,
              	animateOut: effectSlide,
                mouseDrag: checkData( $t.data('owl-drag'), true ),
                nav: checkData( $t.data('owl-nav'), false ),
                navText: ['<i class="fa fa-long-arrow-left"></i>', '<i class="fa fa-long-arrow-right"></i>'],
                dots: checkData( $t.data('owl-dots'), false ),
                responsive: checkData( $t.data('owl-responsive'), {} )
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
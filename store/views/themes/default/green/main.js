;(function ($) {
    "use strict";

    /* ------------------------------------------------------------------------- *
     * COMMON VARIABLES
     * ------------------------------------------------------------------------- */
    var $wn = $(window),
        $body = $('body');

    /* ------------------------------------------------------------------------- *
     * TAWK
     * ------------------------------------------------------------------------- */
    if ( typeof Tawk_API !== 'undefined' ) {
        Tawk_API = Tawk_API || {};
        Tawk_API.onLoad = function(){
            if ( $('#tawkchat-container').length ) {
                $body.addClass('isTawkMobile');
            }
        };
    }

    /* ------------------------------------------------------------------------- *
     * Check Data
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
         * STICKYJS
         * ------------------------------------------------------------------------- */
        var $sticky = $('[data-sticky]');

        $sticky.each(function () {
            $(this).sticky({
                zIndex: 999
            });
        });

        /* ------------------------------------------------------------------------- *
         * MAGNIFIC POPUP
         * ------------------------------------------------------------------------- */
        var $popupVideo = $('[data-popup="video"]');

        if ( $popupVideo.length ) {
            $popupVideo.magnificPopup({
                type: 'iframe'
            });
        }

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
                $(this).html( '<ul>' + e.strftime('<li><strong>%D</strong><span>Days</span></li><li><strong>%H</strong><span>Hours</span></li><li><strong>%M</strong><span>Minutes</span></li><li><strong>%S</strong><span>Seconds</span></li>') + '</ul>' );
            });
        });

        /* ------------------------------------------------------------------------- *
         * OWL CAROUSEL
         * ------------------------------------------------------------------------- */
        var $owlCarousel = $('.owl-carousel');


        $owlCarousel.each(function () {
            var $t = $(this),
                effectSlide = $t.data('owl-effect'),
                sliderSettings = {
                    items: checkData( $t.data('owl-items'), 1 ),
                    margin: checkData( $t.data('owl-margin'), 0 ),
                    loop: checkData( $t.data('owl-loop'), true ),
                    autoplay: checkData( $t.data('owl-autoplay'), true ),
                    smartSpeed: checkData( $t.data('owl-speed'), 500 ),
                    autoplaySpeed: checkData( $t.data('owl-interval'), 5000 ),
                    autoplayTimeout: checkData( $t.data('owl-interval'), 5000 ),
                    mouseDrag: checkData( $t.data('owl-drag'), true ),
                    nav: checkData( $t.data('owl-nav'), false ),
                    navText: ['<i class="fa fa-long-arrow-left"></i>', '<i class="fa fa-long-arrow-right"></i>'],
                    dots: checkData( $t.data('owl-dots'), false ),
                };

            if($t.data('owl-responsive')){
                sliderSettings.responsive = {
                    0: {
                        items: 1
                    },
                    1200: {
                        items: parseInt($t.data('owl-items'))
                    }
                }
            }


            switch(effectSlide){
                case 'fade':
                    sliderSettings.animateOut = 'fadeOut';
                    sliderSettings.animateIn = 'fadeIn';
                    break;
            };

            $t.owlCarousel(sliderSettings);
        });

        /* ------------------------------------------------------------------------- *
         * HEADER SECTION
         * ------------------------------------------------------------------------- */
        var $header = $('#header'),
            $headerTopbar = $header.find('.header--topbar'),
            $headerLogo = $header.find('.header--topbar-logo'),
            $headerNavbarWrapper = $header.find('.header--navbar-wrapper'),
            headerNavbarWrapperHalf = false;

        if ( $headerNavbarWrapper.length ) {
            $header.css('padding-bottom', ($headerNavbarWrapper.outerHeight() / 2) );
            headerNavbarWrapperHalf = true;
        }

        /* ------------------------------------------------------------------------- *
         * PAGE HEADER SECTION
         * ------------------------------------------------------------------------- */
        var $pageHeader = $('#pageHeader');

        if ( headerNavbarWrapperHalf ) {
            $pageHeader.css('padding-top', ( parseInt( $pageHeader.css('padding-top'), 10 ) + ($headerNavbarWrapper.outerHeight() / 2) ) );
        }

        /* ------------------------------------------------------------------------- *
         * SLIDER SECTION
         * ------------------------------------------------------------------------- */
        var $banner = $('#banner'),
            $bannerItem = $banner.find('.banner--item');

        if ( headerNavbarWrapperHalf ) {
            $bannerItem.css('padding-top', ( parseInt( $bannerItem.css('padding-top'), 10 ) + ($headerNavbarWrapper.outerHeight() / 2) ) );
        }

        /* ------------------------------------------------------------------------- *
         * PRICING SECTION
         * ------------------------------------------------------------------------- */
        var $pricingNavSwitch = $('.pricing--nav-switch'),
            $pricingNavSwitchList = $pricingNavSwitch.find('li'),
            $pricingNavSwitchActive = $pricingNavSwitch.find('li.active'),
            $pricingNavSwitchBg = $pricingNavSwitch.find('.item--bg');

        if ( $pricingNavSwitch.length ) {
            $pricingNavSwitchBg.css({
                'left': $pricingNavSwitchActive.position().left,
                'width': $pricingNavSwitchActive.outerWidth()
            });

            $pricingNavSwitchList.on('click', function () {
                var $t = $(this);

                $pricingNavSwitchBg.css({
                    'left': $t.position().left,
                    'width': $t.outerWidth()
                });
            });
        }

        /* ------------------------------------------------------------------------- *
         * PRICING DETAILS SECTION
         * ------------------------------------------------------------------------- */
        var $pricingDetails = $('#pricingDetails'),
            $pricingDetailsHeader = $pricingDetails.find('.pricing--header'),
            $pricingDetailsPrice = $pricingDetails.find('.pricing--price'),
            pricingDetailsHeaderH = 0,
            pricingDetailsPriceH = 0;

        $pricingDetailsHeader.each(function (i) {
            var $t = $(this);

            pricingDetailsHeaderH = $t.outerHeight() > pricingDetailsHeaderH ? $t.outerHeight() : pricingDetailsHeaderH;

            if ( $pricingDetailsHeader.length === (i + 1) ) {
                $pricingDetailsHeader.css('height', pricingDetailsHeaderH);
            }
        });

        $pricingDetailsPrice.each(function (i) {
            var $t = $(this);

            pricingDetailsPriceH = $t.outerHeight() > pricingDetailsPriceH ? $t.outerHeight() : pricingDetailsPriceH;

            if ( $pricingDetailsPrice.length === (i + 1) ) {
                $pricingDetailsPrice.css('min-height', pricingDetailsPriceH);
            }
        });

        /* ------------------------------------------------------------------------- *
         * VPS PRICING SECTION
         * ------------------------------------------------------------------------- */
        var $vpsPricing = $('#vpsPricing'),
            vpsPricingObj = {};

        vpsPricingObj.$slider = $vpsPricing.find('.vps-pricing--slider');
        vpsPricingObj.$putValue = $vpsPricing.find('[data-put-value]');
        vpsPricingObj.$putHref = $vpsPricing.find('[data-put-href]');

        vpsPricingObj.slider = function (res) {
            vpsPricingObj.slider.value = 1;
            vpsPricingObj.slider.max = res.length - 1;

            vpsPricingObj.slider.changeValue = function (e, ui) {
                vpsPricingObj.slider.value = $.isEmptyObject( ui ) ? vpsPricingObj.slider.value : ui.value;

                vpsPricingObj.$putValue.each(function () {
                    var $t = $(this);

                    $t.text( res[ vpsPricingObj.slider.value ][ $t.data('put-value') ] );
                });

                vpsPricingObj.$putHref.each(function () {
                    var $t = $(this);

                    $t.attr( 'href', res[ vpsPricingObj.slider.value ][ vpsPricingObj.$putHref.data('put-href') ] );
                });

                var indx = typeof ui.value === 'undefined' ? vpsPricingObj.slider.value : ui.value;
                vpsPricingObj.$plansListItem.eq( indx ).addClass('active').siblings('li').removeClass('active');
            };

            vpsPricingObj.$slider.slider({
                animate: 'fast',
                range: 'min',
                min: 0,
                max: vpsPricingObj.slider.max,
                value: vpsPricingObj.slider.value,
                step: 1,
                create: function (e, ui) {
                    vpsPricingObj.$plansList = $('<ul class="vps-pricing--plans nav nav-justified"></ul>');
                    $(e.target).append( vpsPricingObj.$plansList );

                    $.each(res, function (i, value) {
                        vpsPricingObj.$plansList.append('<li>' + value.title + '</li>');
                    });
                    vpsPricingObj.$plansListItem = vpsPricingObj.$plansList.children('li');

                    vpsPricingObj.slider.changeValue(e, ui);
                },
                slide: vpsPricingObj.slider.changeValue
            });
        };

        if ( $vpsPricing.length ) {
            $.getJSON('json/vps-plans.json', vpsPricingObj.slider);
        }

        vpsPricingObj.$pricingFeatureHeader = $vpsPricing.find('.vps-pricing--feature .pricing--price');
        vpsPricingObj.$pricingFeatureHeaderH = 0;

        vpsPricingObj.$pricingFeatureHeader.each(function (i) {
            var $t = $(this);

            vpsPricingObj.$pricingFeatureHeaderH = vpsPricingObj.$pricingFeatureHeaderH > $t.outerHeight() ? vpsPricingObj.$pricingFeatureHeaderH : $t.outerHeight();

            if ( vpsPricingObj.$pricingFeatureHeader.length === (i + 1) ) {
                vpsPricingObj.$pricingFeatureHeader.css('height', vpsPricingObj.$pricingFeatureHeaderH);
            }
        });

        /* ------------------------------------------------------------------------- *
         * PRODUCT RATING
         * ------------------------------------------------------------------------- */
        var $productRatingSelect = $('#productRatingSelect');

        if ( $productRatingSelect.length ) {
            $productRatingSelect.barrating({
                theme: 'fontawesome-stars-o',
                hoverState: false
            });
        }

        /* ------------------------------------------------------------------------- *
         * CHECKOUT SECTION
         * ------------------------------------------------------------------------- */
        var $checkout = $('#checkout');

        $checkout.on('click', '.checkout--info-toggle', function (e) {
            e.preventDefault();

            var $t = $(this);

            $t.parent('p').siblings('.checkout--info-form').slideToggle();
        });

        /* ------------------------------------------------------------------------- *
         * FOOTER SECTION
         * ------------------------------------------------------------------------- */
        var $footerTitle = $('.footer--title'),
            $footerTitleLogo = $footerTitle.children('.logo');

        if ( $footerTitleLogo.length ) {
            $footerTitle.css( 'height', $footerTitleLogo.outerHeight() );
        }

        /* ------------------------------------------------------------------------- *
         * BACK TO TOP BUTTON
         * ------------------------------------------------------------------------- */
        var $backToTop = $('#backToTop');

        $backToTop.on('click', 'a', function (e) {
            e.preventDefault();

            $('html, body').animate({
                scrollTop: 0
            }, 800);
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

        /* ------------------------------------------------------------------------- *
         * ADJUST ROW
         * ------------------------------------------------------------------------- */
        var $adjustRow = $('.AdjustRow');

        if ( $adjustRow.length ) {
            $adjustRow.isotope({layoutMode: 'fitRows'});
        }

        /* ------------------------------------------------------------------------- *
         * MASONRY ROW
         * ------------------------------------------------------------------------- */
        var $masonryRow = $('.MasonryRow');

        if ( $masonryRow.length ) {
            $masonryRow.isotope();
        }

        /* ------------------------------------------------------------------------- *
         * FEATRUES SECTION
         * ------------------------------------------------------------------------- */
        var $features = $('#features'),
            $featuresVideo = $features.find('.feature--video');

        if ( $featuresVideo.length ) {
            $featuresVideo.css( 'min-height', $featuresVideo.siblings('.feature--items').outerHeight() );
        }

        /* ------------------------------------------------------------------------- *
         * PORTFOLIO SECTION
         * ------------------------------------------------------------------------- */
        var $portfolio = $('#portfolio'),
            $portfolioFilterNav = $portfolio.find('.portfolio--filter-nav'),
            $portfolioFilterItems = $portfolio.find('.portfolio--items');

        if ( $portfolioFilterItems.length ) {
            $portfolioFilterItems.isotope({
                animationEngine: 'best-available',
                itemSelector: '.portfolio--item'
            });
        }

        $portfolioFilterNav.on('click', 'a', function (e) {
            e.preventDefault();

            var $t = $(this),
                f = $t.attr('href'),
                s = (f !== '*') ? '[data-cat~="'+ f +'"]' : f;

            $portfolioFilterItems.isotope({
                filter: s
            });

            $t.parent('li').addClass('active').siblings().removeClass('active');
        });
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
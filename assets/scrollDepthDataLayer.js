(function($) {
    'use strict';
    $(document).ready(function() {
        if (!$('.scroll-tracking').length) {
            return;
        }

        var storage = '';
        var startDate = new Date();
        var dataLayerPush = function (percentage) {
            dataLayer.push({
                'event': 'contentScroll',
                'contentScrollDepth': percentage + '%'
            });
            storage += "/" + percentage;
        };

        $(window).on('scroll', function () {
            var viewportHeight = $(window).height();
            var viewportTopX = $(window).scrollTop();
            var viewportBottomX = viewportTopX + viewportHeight;
            var elementHeight = $('.scroll-tracking').outerHeight();
            var elementTopX = $('.scroll-tracking').offset().top;
            var elementBottomX = elementTopX + elementHeight;
            var scrollDif = Math.round((viewportBottomX - elementTopX) / (elementHeight / 100));

            if (scrollDif > 100) {
                $(window).off('scroll');
                dataLayer.push({
                    'event': 'contentScroll',
                    'contentScrollDepth': '100%',
                    'contentTextTime': Math.floor((Math.abs(new Date() - startDate)/1000))
                });
            } else if (scrollDif >= 75 && storage.indexOf('75') === -1) {
                dataLayerPush(75);
            } else if (scrollDif >= 50 && storage.indexOf('50') === -1) {
                dataLayerPush(50);
            } else if (scrollDif >= 25 && storage.indexOf('25') === -1) {
                dataLayerPush(25);
            } else if (scrollDif >= 0 && storage.indexOf('0') === -1) {
                dataLayerPush(0);
            }
        });
    });
})(jQuery);
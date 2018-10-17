(function($) {
    'use strict';
    $(document).ready(function() {
        if (!$('.scroll-tracking').length) {
            return;
        }

        var storage = '';
        var startDate;
        var dataLayerPush = function (percentage) {
            if (storage.indexOf('/' + percentage) > -1) {
                return;
            }

            if (startDate === undefined) {
                startDate = new Date();
            }

            dataLayer.push({
                'event': 'contentScroll',
                'contentScrollDepth': percentage + '%',
                'contentTextTime': Math.floor((Math.abs(new Date() - startDate)/1000))
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
                dataLayerPush(100);
            }
            if (scrollDif >= 75) {
                dataLayerPush(75);
            }
            if (scrollDif >= 50) {
                dataLayerPush(50);
            }
            if (scrollDif >= 25) {
                dataLayerPush(25);
            }
            if (scrollDif >= 0) {
                dataLayerPush(0);
            }
        });
    });
})(jQuery);

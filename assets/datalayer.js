window.dataLayer = window.dataLayer || [];
window.dataLayer.push(bpDatalayer);
(function () {
    if ( typeof window.CustomEvent === "function" ) return false;
    function CustomEvent ( event, params ) {
        params = params || { bubbles: false, cancelable: false, detail: undefined };
        var evt = document.createEvent( 'CustomEvent' );
        evt.initCustomEvent( event, params.bubbles, params.cancelable, params.detail );
        return evt;
    }
    CustomEvent.prototype = window.Event.prototype;
    window.CustomEvent = CustomEvent;

    if (!Element.prototype.matches)
        Element.prototype.matches = Element.prototype.msMatchesSelector ||
            Element.prototype.webkitMatchesSelector;

    if (!Element.prototype.closest) {
        Element.prototype.closest = function(s) {
            var el = this;
            if (!document.documentElement.contains(el)) return null;
            do {
                if (el.matches(s)) return el;
                el = el.parentElement || el.parentNode;
            } while (el !== null && el.nodeType === 1);
            return null;
        };
    }
})();

(function () {
    window.addEventListener('newsletterEvent', function(e) {
        window.dataLayer.push(e.detail);
    });
    //For link box widget click event
    document.addEventListener("DOMContentLoaded", function () {
        var linkBoxes = document.querySelectorAll(".link-box-collection-item");
        linkBoxes.length > 0 && linkBoxes.forEach(function (linkBox, i) {
            linkBox.addEventListener('click', function (e) {
                var title = this.querySelector('h2').innerHTML;
                var link = this.querySelector('a').href;
                var data = {
                    'event': 'widgetClick',
                    'widgetPlacement': 'linkBoxCollection' + i,
                    'widgetDestinationTitle': title,
                    'widgetDestinationUrl': link
                }
                window.dataLayer.push(data);
            })
        })
    });
})();

(function ($) {

    "use strict";

    $.stecExtend(function (master) {
        
        var $instance = master.$instance;
        
        /**
         * Handles reponsive classes
         */
        var media = {
            
            init: function () {

                var parent = this;

                $(window).on("resize", function () {
                    parent.mediaTrigger();
                });

                parent.mediaTrigger();
            },
            
            mediaTrigger: function () {

                if ($instance.width() <= 600) {
                    $instance.removeClass("stec-media-med");
                    $instance.addClass("stec-media-small");
                }

                else if ($instance.width() <= 870) {
                    $instance.removeClass("stec-media-small");
                    $instance.addClass("stec-media-med");
                }

                else {
                    $instance.removeClass("stec-media-med stec-media-small");
                }
            }
        };
        
        media.init();
            

    }, 'onLayoutSet');


})(jQuery);
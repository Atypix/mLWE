if (typeof stecAnimate === 'undefined') {

    var stecAnimate;

    (function ($) {

        "use strict";
        
        /**
         *  Helper function for calendar animations 
         */
        stecAnimate = function($instance) {
            
            this.eventHolder = {
                
                open: function($eventHolder, callback){
                    
                    $eventHolder.show();
                    
                    $eventHolder.find('.stec-layout-event').addClass('stec-layout-event-perspective');
                            
                    $eventHolder.find('.stec-layout-event-preview').each(function(i){
                        
                        var $event = $(this);
                        
                        setTimeout(function(){
                            $event.addClass('stec-layout-event-preview-animate');
                            
                            setTimeout(function(){
                                $event.removeClass('stec-layout-event-preview-animate');
                                $event.addClass('stec-layout-event-preview-animate-complete');
                            }, 200);
                        }, i*200);
                        
                    });
                    
                    setTimeout(function () {
                        $eventHolder.find('.stec-layout-event').removeClass('stec-layout-event-perspective');
                    }, $eventHolder.find('.stec-layout-event-preview').length * 200);
                    
                    
                    if (typeof callback === 'function') {
                        callback();
                    }
                }
            };
            
            this.agenda = {
                
                openSingle: function($el, callback) {
                    
                    $el.addClass('stec-layout-event-perspective');

                    var $event = $el.find('.stec-layout-event-preview');

                    $event.addClass('stec-layout-event-preview-animate');

                    setTimeout(function () {
                        $event.removeClass('stec-layout-event-preview-animate');
                        $event.addClass('stec-layout-event-preview-animate-complete');
                    }, 200);
                        
                    setTimeout(function () {
                        $el.removeClass('stec-layout-event-perspective');
                    }, 200);

                    if (typeof callback === 'function') {
                        callback();
                    }
                    
                },
                
                fillList: function($container, callback){
                    
                    $container
                            .find('.stec-layout-agenda-events-all')
                            .find('.stec-layout-event')
                            .addClass('stec-layout-event-perspective');
                    
                    $container
                            .find('.stec-layout-agenda-events-all')
                            .find('.stec-layout-event-preview')
                            .not('.stec-layout-event-preview-animate-complete')
                            .each(function(i){
                        
                        var $event = $(this);
                        
                        setTimeout(function(){
                            $event.addClass('stec-layout-event-preview-animate');
                            
                            setTimeout(function () {
                                $event.removeClass('stec-layout-event-preview-animate');
                                $event.addClass('stec-layout-event-preview-animate-complete');
                            }, 200);
                        }, i*200);
                        
                    });
                    
                    setTimeout(function(){
                        $container.find('.stec-layout-agenda-events-all').find('.stec-layout-event').removeClass('stec-layout-event-perspective');
                    }, $container.find('.stec-layout-agenda-events-all').find('.stec-layout-event-preview').not('.stec-layout-event-preview-animate').length * 200);
                    
                    if (typeof callback === 'function') {
                        callback();
                    }
                }
            };
            
            this.tooltip = {
                
                t: null,
                
                clear: function(){
                    clearTimeout(this.t);
                },
                
                show: function ($el, callback) {
                    
                    $el.show().addClass('stec-tooltip-show');
                    
                    this.t = setTimeout(function () {
                        if (typeof callback === 'function') {
                            callback();
                        }
                    }, 250);
                    
                }, 
                
                hide: function($el, callback){
                    
                    $el.removeClass('stec-tooltip-show');
                    
                    this.t = setTimeout(function () {
                        if (typeof callback === 'function') {
                            callback();
                        }
                    }, 250);
                }
            };
        };

    })(jQuery);

}
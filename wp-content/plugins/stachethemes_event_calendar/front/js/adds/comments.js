(function ($) {

    "use strict";

    $.stecExtend(function (master) {
        
        var $instance = master.$instance;
        var helper    = master.helper;
        
        var disqus = {
            
            loadCommentsCount: function () {

                var disqus_shortname = master.glob.options.general_settings.disqus_shortname;

                $.ajax({
                    type: "GET",
                    url: "//" + disqus_shortname + ".disqus.com/count.js",
                    dataType: "script",
                    cache: true
                });
            },
            
            loadComments: function ($tabCont) {

                $instance.$events.find("#disqus_thread").remove();

                $('<div id="disqus_thread"></div>').appendTo($tabCont);
                
                var eventId          = $tabCont.parents('.stec-layout-event').attr('data-id');
                var disqus_shortname = master.glob.options.general_settings.disqus_shortname;
                var disqus_title     = $tabCont.parents('.stec-layout-event')
                                           .find('.stec-layout-event-preview-left-text-title')
                                           .text();

                window.disqus_url        = window.location.href + "#!stecEventDiscussion" + eventId;
                window.disqus_identifier = "stecEventID" + eventId;
                window.disqus_title      = disqus_title;

                if (typeof window.DISQUS === "undefined") {
                    $.ajax({
                        type: "GET",
                        url: "//" + disqus_shortname + ".disqus.com/embed.js",
                        dataType: "script",
                        cache: true
                    });
                } else {
                    window.DISQUS.reset({
                        reload: true
                    });
                }
            }
        };
        
        function tabToggle($event) {
            if ($event
                    .find('.stec-layout-event-inner-top-tabs')
                    .children('.active[data-tab="stec-layout-event-inner-comments"]')
                    .length > 0) {
                
                loadComments($event);
            }
        }
        
        function loadComments($event) {
            if ($event
                    .find('.stec-layout-event-inner-comments')
                    .find('#disqus_thread').length <= 0) {
                disqus.loadComments($event.find('.stec-layout-event-inner-comments'));
            }
        }
        
        $(document).on(helper.clickHandle(), $instance.$events.path + ' .stec-layout-event-inner-top-tabs li', function () {
            if ($(this).attr('data-tab') == "stec-layout-event-inner-comments") {
                var $event = $(this).parents('.stec-layout-event-inner');
                loadComments($event);
            }
        });
        
        $(document).on(helper.clickHandle(), $instance.$events.path + (".stec-layout-event-preview-right-event-toggle"), function (e) {
            e.preventDefault();
            e.stopPropagation();
            tabToggle($(this).parents('.stec-layout-event'));

        });

        $(document).on(helper.clickHandle(), $instance.$events.path + '.stec-layout-event', function (e) {
            e.preventDefault();
            tabToggle($(this));
            
        });
        
        

    });

})(jQuery);
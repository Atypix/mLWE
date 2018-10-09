(function ($) {

    "use strict";
    
    /**
     * Helper function allows binding to certain listeners
     * Used by adds/...
     */
    
    $.stecExtend = function(fn, sub){
        
        if (typeof stachethemes_ec_extend === "undefined") {
            window.stachethemes_ec_extend = [];
        }
        
        if (sub) {
            
            if (typeof stachethemes_ec_extend[sub] === "undefined") {
                window.stachethemes_ec_extend[sub] = [];
            }
            
            stachethemes_ec_extend[sub].push(fn);
            
        } else {
            stachethemes_ec_extend.push(fn);
        }
        
    };
    

})(jQuery);
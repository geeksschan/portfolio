(function( $ ) {

    $.fn.ssc_carousel = function(options) {
        var settings = {

        };
    };

}( jQuery ));

$(function() {

    $(".ssc-carousel-navigation").find("button").on("click", function() {
        var $ssc_carousel_window = $(".ssc-carousel-window");

        if($(this).hasClass("prev-btn")) {
            $ssc_carousel_window.animate({
                left: "-=300"
            }, 700);
        } else {
            $ssc_carousel_window.animate({
                left: "+=300"
            }, 700);
        }

    });

});
window.onload = function() {
    var $body = $("body"),
        $init_loading = $("#init-loading"),
        $loading_div = $init_loading.find("div"),
        $loading_div_height = $loading_div.height();

    $init_loading.css("height", 0).css("top", -$loading_div_height+"px");

    setTimeout(function() {
        console.log("test");
        $loading_div.css("display", "none");
        $body.removeClass("loading");
    }, 3000);
};

$(function() {

    // drawer menu open/close
    $(".ssc-btn-sidebar").on("click", function() {
        var $menu = $(".menu"),
            $navbar_backdrop = $("#ssc-navbar-backdrop");
        if($menu.length > 0) {
            if($menu.hasClass("open")) {
                $menu.removeClass("open");
                $navbar_backdrop.removeClass("open");
            } else {
                $menu.addClass("open");
                $navbar_backdrop.addClass("open");
            }
        }
    });

    $(window).resize(function() {
        var $window_width = $(window).width(),
            $menu = $(".menu");
        if($window_width <= 767) {
            $menu.addClass("opening");
            setTimeout(function() {
                $menu.removeClass("opening");
            }, 100);

        }
    });
});
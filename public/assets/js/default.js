window.onload = function() {
    var $body = $("body"),
        $init_loading = $("#init-loading"),
        $loading_div = $init_loading.find("div"),
        $loading_div_height = $loading_div.height();

    $init_loading.css("height", 0).css("top", -$loading_div_height+"px");

    setTimeout(function() {
        $loading_div.css("display", "none");
        $body.removeClass("loading");
    }, 3000);
};

$(function() {

    // drawer menu open/close
    // touch 이벤트 유무에 따라서 이벤트 다르게 적용
    if ('ontouchstart' in document.documentElement) {
        $(".ssc-btn-sidebar").on("touchstart", function() {
            var $menu = $(".menu"),
                $navbar_backdrop = $("#ssc-navbar-backdrop"),
                $ssc_right_to = $(this).find(".ssc-right-to");
            $(this).removeClass("touch-end");
            if($menu.length > 0) {
                if($menu.hasClass("open")) {
                    $menu.removeClass("open");
                    $navbar_backdrop.removeClass("open");
                    $(this).removeClass("active");
                    //$ssc_right_to.css("transform", "rotate(45deg)");
                } else {
                    $menu.addClass("open");
                    $navbar_backdrop.addClass("open");
                    $(this).addClass("active");
                    //$ssc_right_to.css("transform", "rotate(225deg)");
                }
            }
        }).on("touchend", function() {
            var $this_btn = $(this);
            setTimeout(function() {
                $this_btn.addClass("touch-end");
            }, 1200);
        });
    } else {
        $(".ssc-btn-sidebar").on("click", function() {
            var $menu = $(".menu"),
                $navbar_backdrop = $("#ssc-navbar-backdrop"),
                $ssc_right_to = $(this).find(".ssc-right-to");
            if($menu.length > 0) {
                if($menu.hasClass("open")) {
                    $menu.removeClass("open");
                    $navbar_backdrop.removeClass("open");
                    $(this).removeClass("active");
                    //$ssc_right_to.css("transform", "rotate(45deg)");
                } else {
                    $menu.addClass("open");
                    $navbar_backdrop.addClass("open");
                    $(this).addClass("active");
                    //$ssc_right_to.css("transform", "rotate(225deg)");
                }
            }
        });
    }



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
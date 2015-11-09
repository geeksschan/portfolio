window.onload = function() {

    var $body = $("body"),
        $window = $(window),
        $window_width = $window.width(),
        $window_height = $window.height(),
        $section_2 = $(".section-2"),
        $skill_row = $section_2.find(".skill-row"),
        $init_loading = $("#init-loading"),
        $loading_div = $init_loading.find("div"),
        $loading_div_height = $loading_div.height(),
        $skill_item = $(".skill-row").find(".item"),
        $skill_item_width = 0,
        $skill_row_height = 0,
        $section_2_padding_height = 0;

    $init_loading.css("height", 0).css("top", -$loading_div_height+"px");

    setTimeout(function() {
        $loading_div.css("display", "none");
        $body.removeClass("loading");
    }, 3000);

    if($skill_item.length > 0) {
        $section_2_padding_height = $section_2.closest(".section").find(".border-section").innerHeight();
        $section_2.css("padding-top", $section_2_padding_height+"px");
        if($section_2.height() * 0.3 > $skill_row.width()*0.2) {
            //가로 길이 사용
            $skill_item_width = $skill_item.width();
            $skill_item.height($skill_item_width);
        } else {
            //세로 길이 사용
            $section_2.addClass("height");
            $skill_row_height = $skill_row.height();
            $skill_item.width($skill_row_height);
            $skill_item.height($skill_row_height);
        }
    }
};

$(function() {

    var $menu = $(".menu"),
        $main = $(".main");


    if($main.length > 0 && $main.hasClass("intro_page")) {

        // 인트로 메인 페이지의 원페이지 스크롤 라이브러리 적용
        $main.onepage_scroll({
            sectionContainer: "section",     // sectionContainer accepts any kind of selector in case you don't want to use section
            easing: "ease",                  // Easing options accepts the CSS3 easing animation such "ease", "linear", "ease-in",
                                             // "ease-out", "ease-in-out", or even cubic bezier value such as "cubic-bezier(0.175, 0.885, 0.420, 1.310)"
            animationTime: 1000,             // AnimationTime let you define how long each section takes to animate
            pagination: true,                // You can either show or hide the pagination. Toggle true for show, false for hide.
            updateURL: false,                // Toggle this true if you want the URL to be updated automatically when the user scroll to each page.
            beforeMove: function(index) {},  // This option accepts a callback function. The function will be called before the page moves.
            afterMove: function(index) {},   // This option accepts a callback function. The function will be called after the page moves.
            loop: false,                     // You can have the page loop back to the top/bottom when the user navigates at up/down on the first/last page.
            keyboard: true,                  // You can activate the keyboard controls
            responsiveFallback: false,        // You can fallback to normal page scroll by defining the width of the browser in which
            // you want the responsive fallback to be triggered. For example, set this to 600 and whenever
            // the browser's width is less than 600, the fallback will kick in.
            direction: "vertical"            // You can now define the direction of the One Page Scroll animation. Options available are "vertical" and "horizontal". The default value is "vertical".
        });

        $menu.find("a").on("click", function(e) {
            e.preventDefault();

            $menu.find("a").each(function() {
                $(this).removeClass("active");
            });

            if($(this).hasClass("intro-page")) {
                $main.moveTo(1);
            } else if($(this).hasClass("skill-page")) {
                $main.moveTo(2);
            } else {
                $main.moveTo(3);
            }

            $(this).addClass("active");

        });


    }



    // 상단 네비게이션 이벤트


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
$(function() {

    var $board_sidebar = $("#board-sidebar"),
        $board_sideber_btn = $(".board-sidebar-btn");


    $board_sideber_btn.on("click", function() {

        // 닫기
        if($(this).hasClass("open")) {
            $(this).removeClass("open");
            $board_sidebar.removeClass("open");
        } else {
            $board_sidebar.addClass("open");
            $(this).addClass("open");

        }

    });

});
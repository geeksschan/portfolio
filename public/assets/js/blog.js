$(function() {

    var $window = $(window),
        $window_height = $window.height(),
        $window_width = $window.width(),
        $board_sidebar = $("#board-sidebar"),
        $board_sidebar_content = $board_sidebar.find(".board-sidebar-content"),
        $board_sidebar_content_height = $board_sidebar_content.height(),
        $board_sidebar_btn_wrapper = $(".sidebar-btn-wrapper"),
        $board_sideber_btn = $(".board-sidebar-btn"),
        $board_body = $("#board-body"),
        $board_list = $board_body.find(".board-list"),
        $blog_content = $board_body.find(".blog-content"),
        $board_list_height = $board_list.height(),
        $window_sidebar_gap = $board_sidebar_content_height - $window_height, // 사이드 메뉴 높이가 윈도우 창 보다 높은지 여부
        $board_backdrop = $("#board-backdrop"),
        current_scroll_top = 0,
        last_scroll_top = 0,
        scroll_direction = 0,
        fixed_top = 0, // 모바일 사이드 메뉴 열림 시 board_list 고정 시키거나 닫을 때 다시 재조정해 줘야 하는 높이 값
        direction_change = false, // 방향 변경 여부
        $summernote = $("#summernote"),
        $summernote_initial_height = 500,
        $submit_btn_wrapper = $(".submit-btn-wrapper"),
        $post_write_form = $("#post-write-form"),
        $post_content_input = null;


    $window.resize(function() {
        $window_width = $window.width();
    });

    $window.scroll(function() {
        current_scroll_top = $window.scrollTop();
        scroll_direction = 0;

        // down : 0 | up : 1
        if(current_scroll_top > last_scroll_top) {
            if(scroll_direction != 0) {
                scroll_direction = 0;
                direction_change = true;
            } else {
                direction_change = false;
            }
        } else {
            if(scroll_direction != 1) {
                scroll_direction = 1;
                direction_change = true;
            } else {
                direction_change = false;
            }
        }

        last_scroll_top = current_scroll_top;
    });

    /**
     * 메뉴 사이드 바에 따른 스크롤 이벤트
     */
    if($window_sidebar_gap > 0) {
        if($board_list_height > $board_sidebar_content_height) {

            $window.scroll(function() {

                if($window_sidebar_gap <= $window.scrollTop()) {
                    if(scroll_direction == 1) {
                        if(direction_change) {
                            $board_sidebar_content.css("position","relative").css("bottom", "initial").css("top", -$window_sidebar_gap + "px");
                        }
                    } else {
                        $board_sidebar.css("position", "fixed").css("top", 0);
                        $board_sidebar_content.css("position","fixed").css("top", "initial").css("bottom", "0");
                    }
                } else {
                    //$board_sidebar.css("position", "absolute").css("top", 0);
                    $board_sidebar_content.css("position","relative").css("top",0);
                }

            });
        }
    } else {
        // fixed
         $board_sidebar.css("position","fixed").css("top", "0");
    }

    /**
     * 메뉴 사이드 바 열림/닫기 버튼
     */
    $board_sideber_btn.on("click", function() {

        var fixed_top_plus = parseInt($board_sidebar_btn_wrapper.height());

        // 닫기
        if($(this).hasClass("open")) {
            if($submit_btn_wrapper.length > 0) {
                $submit_btn_wrapper.removeClass("close");
            }

            $board_sidebar_btn_wrapper.removeClass("open");
            $(this).removeClass("open");
            $board_sidebar.removeClass("open");
            $board_backdrop.removeClass("open");

            fixed_top -= fixed_top_plus;
            if(fixed_top >= 0) {
                fixed_top = 0;
            } else {
                fixed_top *= -1;
            }

            $blog_content.css("position", "relative").css("top","inherit");
            $(window).scrollTop(fixed_top);

        } else {
            if($submit_btn_wrapper.length > 0) {
                $submit_btn_wrapper.addClass("close");
            }

            $board_sidebar.addClass("open");
            $board_sidebar_btn_wrapper.addClass("open");
            $(this).addClass("open");
            $board_backdrop.addClass("open");

            fixed_top = fixed_top_plus - last_scroll_top;
            $blog_content.css("position", "fixed").css("top", fixed_top+"px");
        }
    });

    $board_backdrop.on("click", function() {
        $board_sideber_btn.trigger("click");
    });


    /**
     * 글 작성 영역
     */
    if($post_write_form.length > 0) {

        $post_content_input = $post_write_form.find("input[name='post_content']");

        if($summernote.length > 0) {
            if($window_width < 767) {
                $summernote_initial_height = 300;
            }

            $summernote.summernote({
                lang: 'ko-KR',
                height: "100%",
                minHeight: null,
                maxHeight: null,
                focus: true
            });

            $summernote.summernote("code", $post_content_input.val());

        }

        $post_write_form.submit(function() {

            var content_markup = $summernote.summernote("code");

            $post_content_input.val(content_markup);

            $(this).ajaxSubmit({
                dataType: 'json',
                type: 'post',
                iframe: true,
                target: '#hidden-iframe',
                success: function(json) {
                    if(json.status == "success") {
                        alert(json.message);
                        //location.href = json.success_return_url;
                    }
                    else if(json.status == "failure") {
                        alert(json.message);
                    }
                },
                error: function() {
                    alert("error");
                },
                complete: function() {

                }
            });
            return false;
        });
    }



});
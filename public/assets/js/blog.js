$(function() {

    var $window = $(window),
        $window_height = $window.height(),
        $window_width = $window.width(),
        $blog_sidebar = $("#blog-sidebar"),
        $blog_sidebar_content = $blog_sidebar.find(".blog-sidebar-content"),
        $blog_sidebar_content_height = $blog_sidebar_content.height(),
        $blog_sidebar_btn_wrapper = $(".sidebar-btn-wrapper"),
        $blog_sideber_btn = $(".blog-sidebar-btn"),
        $blog_body = $("#blog-body"),
        $blog_list = $blog_body.find(".blog-list"),
        $blog_content = $blog_body.find(".blog-content"),
        $blog_list_height = $blog_list.height(),
        $window_sidebar_gap = $blog_sidebar_content_height - $window_height, // 사이드 메뉴 높이가 윈도우 창 보다 높은지 여부
        $blog_backdrop = $("#blog-backdrop"),
        current_scroll_top = 0,
        last_scroll_top = 0,
        scroll_direction = 0,
        fixed_top = 0, // 모바일 사이드 메뉴 열림 시 blog_list 고정 시키거나 닫을 때 다시 재조정해 줘야 하는 높이 값
        direction_change = false, // 방향 변경 여부
        $summernote = $("#summernote"),
        $summernote_initial_height = 500,
        $submit_btn_wrapper = $(".submit-btn-wrapper"),
        $post_write_form = $("#post-write-form"),
        $post_content_input = null,
        $note_editor = null,
        $note_editable = null,
        note_editable_img_count = 0,
        editable_img_loaded_count = 0,
        editable_img_loaded = false;


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
        if($blog_list_height > $blog_sidebar_content_height) {

            $window.scroll(function() {

                if($window_sidebar_gap <= $window.scrollTop()) {
                    if(scroll_direction == 1) {
                        if(direction_change) {
                            $blog_sidebar_content.css("position","relative").css("bottom", "initial").css("top", -$window_sidebar_gap + "px");
                        }
                    } else {
                        $blog_sidebar.css("position", "fixed").css("top", 0);
                        $blog_sidebar_content.css("position","fixed").css("top", "initial").css("bottom", "0");
                    }
                } else {
                    //$blog_sidebar.css("position", "absolute").css("top", 0);
                    $blog_sidebar_content.css("position","relative").css("top",0);
                }

            });
        }
    } else {
        // fixed
         $blog_sidebar.css("position","fixed").css("top", "0");
    }

    /**
     * 메뉴 사이드 바 열림/닫기 버튼
     */
    $blog_sideber_btn.on("click", function() {

        var fixed_top_plus = parseInt($blog_sidebar_btn_wrapper.height());

        // 닫기
        if($(this).hasClass("open")) {
            if($submit_btn_wrapper.length > 0) {
                $submit_btn_wrapper.removeClass("close");
            }

            $blog_sidebar_btn_wrapper.removeClass("open");
            $(this).removeClass("open");
            $blog_sidebar.removeClass("open");
            $blog_backdrop.removeClass("open");

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

            $blog_sidebar.addClass("open");
            $blog_sidebar_btn_wrapper.addClass("open");
            $(this).addClass("open");
            $blog_backdrop.addClass("open");

            fixed_top = fixed_top_plus - last_scroll_top;
            $blog_content.css("position", "fixed").css("top", fixed_top+"px");
        }
    });

    $blog_backdrop.on("click", function() {
        $blog_sideber_btn.trigger("click");
    });


    /**
     * 글 작성 영역
     */
    if($post_write_form.length > 0) {

        $post_content_input = $post_write_form.find("[name='post_content']");

        if($summernote.length > 0) {
            /**
             * summernote 이미지 업로드
             * @param file
             * @param editor
             * @param welEditable
             */
            function sendFile(file,editor,welEditable) {
                $post_write_form.ajaxSubmit({
                    dataType: 'json',
                    url: "/blog/upload",
                    type: 'post',
                    cache: false,
                    iframe: true,
                    target: '#hidden-iframe',
                    success: function(json) {
                        if(json.status == "success") {
                            alert(json.message);
                            $summernote.summernote('insertImage', json.data[0].full_path, json.data[0].file_name, json.data[0].file_id);
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
            }

            /**
             * summernote code의 높이 값 얻음
             * @param $note_editable
             * @returns {number}
             */
            function getSummernoteHeight($note_editable) {
                var p_height = 0;
                $note_editable.find("p").each(function() {
                    p_height += $(this).outerHeight();
                });
                return p_height;
            }

            /**
             * summernote의 에디터 부분의 높이값 설정
             * @param $note_editable
             * @param init_plus_height
             */
            function setSummernoteHeight($note_editable, init_plus_height) {
                var set_height = getSummernoteHeight($note_editable);
                if(set_height > 0) {
                    init_plus_height = (typeof init_plus_height == "undefined")?0:init_plus_height;
                    set_height += init_plus_height;
                    $note_editable.height(set_height);
                }
            }

            if($window_width < 767) {
                $summernote_initial_height = 300;
            }

            $summernote.summernote({
                //airMode: true,
                lang: 'ko-KR',
                height: $summernote_initial_height,
                minHeight: "100%",
                maxHeight: "100%",
                focus: true,
                callbacks : {
                    //onInit: function() {
                    //
                    //},
                    onImageUpload : function(files, editor, welEditable) {
                        sendFile(files[0], editor, welEditable);
                    },
                    onChange: function(contents, $editable) {
                        console.log('onChange:', contents, $editable);
                    }
                }
            });

            $summernote.summernote("code", $post_content_input.val());

            $note_editor = $(".note-editor");
            $note_editable = $note_editor.find(".note-editable");

            note_editable_img_count = $note_editable.find("p img").length;
            $note_editable.find("p img").on("load", function() {
                editable_img_loaded_count++;
                if(note_editable_img_count == editable_img_loaded_count) {
                    setSummernoteHeight($note_editable, 40);
                }
            });
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
                        console.log(json.data);
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
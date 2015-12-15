<?php
/**
 * Created by PhpStorm.
 * User: gsshin
 * Date: 2014-12-01
 * Time: 오전 9:58
 */

class BlogController  extends ControllerBase
{
    public function initialize() {
        parent::initialize();
        $this->assets->addCss("//netdna.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.css");
        $this->assets->addCss("//netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.css");
        $this->assets->addJs("//netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js");

        $this->assets->addCss("assets/css/summernote.css");
        $this->assets->addCss("assets/css/blog.css");
        $this->assets->addCss("assets/css/summernote.custom.css");
        $this->assets->addJs("assets/js/summernote.js");
        $this->assets->addJs("assets/lang/summernote-ko-KR.js");
        $this->assets->addJs("assets/js/blog.js");
    }

    public function indexAction()
    {

    }


    /**
     * @param int $post_id
     * @return bool|\Phalcon\Http\Response
     */
    public function writeAction($post_id=0) {

        $is_post = false;
        $post = null;
        // 포스트의 존재 유무 확인
        if($post_id) {
            $post = Post::findFirst($post_id);
            if($post) {
                $is_post = true;
            } else {
                $is_post = false;
            }
        }

        // 포스트가 없는 경우 포스트 생성 후 리다이렉트
        if(!$is_post) {
            $post = new Post();
            if(!$post->save()) {
                return $this->response->redirect("/blog", true);
            }
            return $this->response->redirect("blog/write/".$post->post_id);
        }

        $this->view->setVar("post_id", $post->post_id);
        $this->view->setVar("post_title", $post->title);
        $this->view->setVar("post_sub_title", $post->sub_title);
        $this->view->setVar("post_content", $post->content);


        if($this->request->isPost()) {

            if($post_id and $post) {
                $post_title = $this->request->getPost("post_title");
                $post_sub_title = $this->request->getPost("post_sub_title");
                $post_content = $this->request->getPost("post_content");

                $post->title = $post_title;
                $post->sub_title = $post_sub_title;
                $post->content = $post_content;

                if(!$post->save()) {
                    return $this->responseJsonFailure("저장 되지 않았습니다", $post->getMessages());
                } else {
                    return $this->responseJsonSuccess("성공적으로 저장되었습니다");
                }

            } else {
                return $this->responseJsonFailure("잘못된 파라미터 전달");
            }

        } else {
            return true;
        }


    }

}
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

                $this->db->begin();
                $prev_file_delete_result = Post::deletePrevFiles($post_id, $post_content);
                if(!$prev_file_delete_result) {
                    $this->db->rollback();
                    return $this->responseJsonFailure("이전 이미지 파일 삭제 에러");
                }

                $post->title = $post_title;
                $post->sub_title = $post_sub_title;
                $post->content = $post_content;

                if(!$post->save()) {
                    $this->db->rollback();
                    return $this->responseJsonFailure("저장 되지 않았습니다", $post->getMessages());
                } else {
                    $this->db->commit();
                    return $this->responseJsonSuccess("성공적으로 저장되었습니다");
                }

            } else {
                return $this->responseJsonFailure("잘못된 파라미터 전달");
            }

        } else {
            return true;
        }

    }

    public function uploadAction()
    {
//        if(!$this->logged_user) {
//            return $this->responseJsonNotPermitted();
//        }

//        $data = array();
//        $blog_id = $this->request->getPost("blog_id");
//        $type = $this->request->getPost("type");

//        $blog = Blog::findFirst("blog_id = {$blog_id}");
//        if(!$blog) {
//            return $this->responseJsonFailure(Lang::$msg_invalid_parameters);
//        }

//        if($blog->user_id != $this->logged_user->user_id) {
//            return $this->responseJsonNotPermitted();
//        }
        $type = "post";

        $post_id = $this->request->getPost("post_id", "int", 0);
        if(!$post_id) {
            return $this->responseJsonFailure("잘못된 파라미터 전달.");
        }
        $post = Post::findFirst($post_id);
        if(!$post) {
            return $this->responseJsonFailure("포스트가 존재하지 않습니다");
        }

        if($this->request->hasFiles()) {

            $data = array();
            foreach($this->request->getUploadedFiles() as $val) {
                if($val->getSize()>0) {

                    $file = File::upload($val, $type, $post_id);
                    if($file) {
                        if($type != 'file') {
                            $object = new stdClass();
                            $object->file_id = $file->file_id;
                            $object->width = $file->width;
                            $object->height = $file->height;
                            $object->file_name = $file->file_name;
                            if($file->file_type == 'image/gif') {
                                $object->full_path = $file->getFileUrl();
                            } else {
                                $object->full_path = $file->getThumbnail(1080);
                            }

                            $data[] = $object;
                        }
                        else {
                            $data[] = $file;
                        }
                    }
                }
            }
            return $this->responseJsonSuccess("success", $data);
        }
        else {
            return $this->responseJsonFailure("failed");
        }
    }



}
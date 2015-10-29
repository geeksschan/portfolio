<?php

/**
 * Class ControllerBase
 * @property \Phalcon\Mvc\Model\Transaction $db
 * @property \Phalcon\Mvc\View $view;
 * @property \Phalcon\Http\Response\Cookies $cookies;
 * @property \Phalcon\Assets\Manager $assets;
 * @property \Phalcon\Mvc\Dispatcher $dispatcher;
 * @property \Phalcon\Di\FactoryDefault $di;
 * @property \Phalcon\Flash\Session $flash;
 * @property \Phalcon\Http\Response $response;
 * @property \Phalcon\Http\Request $request;
 */

class ControllerBase extends Phalcon\Mvc\Controller
{

    public function initialize()
    {

    }

    /**
     * @param string $message
     * @param null $data
     * @return bool
     */
    public function responseJsonSuccess($message = "", $data = null, $url = '')
    {
        return $this->responseJsonContent("success", $message, $data, $url);
    }

    /**
     * @param string|\Phalcon\Mvc\Model $message
     * @param null $data
     * @return bool
     */
    public function responseJsonFailure($message = "", $data = null, $url = '')
    {
        if(is_string($message)) {
            return $this->responseJsonContent("failure", $message, $data, '', $url);
        }
        else {
            return $this->responseJsonModel($message);
        }
    }

    /**
     * @param $status
     * @param string $message
     * @param null $data
     * @return bool
     */
    private function responseJsonContent($status, $message = "", $data = null, $success_return_url = '', $failure_return_url = '')
    {
        $this->view->disable();

        $callback = $this->request->get("callback");
        $content = new stdClass();
        $content->status = $status;
        $content->message = $message;
        $content->data = $data;
        $content->success_return_url = $success_return_url;
        $content->failure_return_url = $failure_return_url;

        $json = json_encode($content);

        // jsonp 형식일 경우 callback 함수를 포함하여 리턴
        if($callback)
        {
            $json = $callback.'('.$json.')';
        }
        echo $json;
        return true;
    }

    /**
     * @param \Phalcon\Mvc\Model $model
     * @return mixed
     */
    private function responseJsonModel($model)
    {
        $messages = $model->getMessages();
        if(count($messages)>0) {
            foreach($messages as $message) {
                return $this->responseJsonContent("failure", $message->getMessage(), $message->getField());
            }
        }
        return $this->responseJsonContent("failure", Lang::$msg_undefined_failure);
    }

    /**
     * @param string $message
     * @return bool
     */
    public function responseJsonNotPermitted($message = "") {
        if($message=="") {
            $message = Lang::$msg_login_required;
        }
        return $this->responseJsonContent("no-permission", $message, null, "/auth/login", "/auth/login");
    }

    public function redirect404() {
        return $this->response->redirect(Link::$error_404);
    }




}
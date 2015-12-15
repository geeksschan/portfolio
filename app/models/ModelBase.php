<?php

class ModelBase extends \Phalcon\Mvc\Model
{
    /**
     * @var \Phalcon\Cache\Backend\Libmemcached
     */
//    private $model_cache = null;

    public function initialize() {
//        $this->model_cache = $this->getDI()->get("cache");
    }


    /**
     * @param null $parameters
     * @return \Phalcon\Mvc\Model\Resultset|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

//    public static function setCache($key, $value) {
//        $di = \Phalcon\Di::getDefault();
//
//        $model_cache = $di->get("cache"); /* @var \Phalcon\Cache\Backend\Libmemcached $model_cache */
//        if(!$model_cache->isStarted()) {
//            $model_cache->start($key);
//        }
//        $model_cache->save($key, $value);
//    }
//
//    public static function getCache($key) {
//        $di = \Phalcon\Di::getDefault();
//        $model_cache = $di->get("cache"); /* @var \Phalcon\Cache\Backend\Libmemcached $model_cache */
//
//        if($model_cache->exists($key)) {
//            $data = $model_cache->get($key);
//            if($data) {
//                return $data;
//            }
//        }
//        return null;
//    }
//
//    public static function deleteCache($key) {
//        $di = \Phalcon\Di::getDefault();
//        $model_cache = $di->get("cache"); /* @var \Phalcon\Cache\Backend\Libmemcached $model_cache */
//        return $model_cache->delete($key);
//    }

    /**
     * 첫번째 에러 매세지를 리턴
     * @return string
     */
    public function getFirstMessage() {
        foreach($this->getMessages() as $message) {
            return $message->getMessage();
        }
        return "";
    }

    public function __toString() {
        return json_encode($this);
    }
}
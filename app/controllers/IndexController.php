<?php
/**
 * Created by PhpStorm.
 * User: gsshin
 * Date: 2014-12-01
 * Time: 오전 9:58
 */

class IndexController  extends ControllerBase
{
    public function initialize() {
        parent::initialize();
    }

    public function indexAction()
    {
        $this->view->setVar("page_location", "intro_page");
    }

}
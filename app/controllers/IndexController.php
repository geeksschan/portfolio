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
        $this->assets->addCss("assets/css/onepage-scroll.css");
//        $this->assets->addCss("assets/css/navbar.css");

        $this->assets->addCss("assets/css/owl.carousel.css");
        $this->assets->addCss("assets/css/owl.theme.css");
        $this->assets->addCss("assets/css/owl.transitions.css");
        $this->view->setVar("page_location", "intro_page");
    }

}
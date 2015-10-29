<?php
/**
 * Created by PhpStorm.
 * User: gsshin
 * Date: 2014-12-01
 * Time: 오전 9:58
 */

class PracticeController  extends ControllerBase
{
    public function initialize() {
        parent::initialize();
    }

    public function indexAction()
    {
        $this->assets->addCss("assets/css/practice.css");
        $this->assets->addJs("assets/js/practice.js");
        $this->assets->addJs("assets/js/ssc_carousel.js");
    }

}
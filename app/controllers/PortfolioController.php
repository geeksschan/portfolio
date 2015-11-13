<?php
/**
 * Created by PhpStorm.
 * User: gsshin
 * Date: 2014-12-01
 * Time: 오전 9:58
 */

class PortfolioController  extends ControllerBase
{
    public function initialize() {
        parent::initialize();
    }

    public function indexAction()
    {
        $this->assets->addCss("assets/css/portfolio.css");
        $this->assets->addJs("assets/js/portfolio.js");
//        $this->assets->addJs("assets/js/ssc_carousel.js");
    }

}
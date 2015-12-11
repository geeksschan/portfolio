<?php
/**
 * Created by PhpStorm.
 * User: gsshin
 * Date: 2014-12-01
 * Time: 오전 9:58
 */

class BoardController  extends ControllerBase
{
    public function initialize() {
        parent::initialize();
    }

    public function indexAction()
    {
        $this->assets->addCss("assets/css/board.css");
        $this->assets->addJs("assets/js/board.js");
    }

}
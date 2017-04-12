<?php
/**
 * Created by PhpStorm.
 * User: Wrh
 * Date: 2017/4/6
 * Time: 21:38
 */

class FeeAction extends BaseAction  {

    public function index() {



        $this->assign('title', '运费预算');
        $this->display();
    }

}
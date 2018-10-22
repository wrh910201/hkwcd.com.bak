<?php
/**
 * Created by PhpStorm.
 * User: wrh42
 * Date: 2018/10/22
 * Time: 16:17
 */

class ClientAction extends BaseAction {

    public function index() {
        $this->assign("avatar", "");
        $this->display();
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: Wrh
 * Date: 2016/10/7
 * Time: 18:34
 */

class BaseAction extends Action {

    protected $response = [
        'code' => -1,
        'msg' => '',
    ];

    public function _initialize() {
        if( IS_AJAX ) {
            if (empty(session('hkwcd_user'))) {
                echo json_encode(
                    array(
                        'code' => '0',
                        'msg' => '请先登录',
                        'url' => '/Mobile/User/login',
                    )
                );
                exit;
            }
        }
        if( empty(session('hkwcd_user')) ) {
            $this->error('请先登录', '/Mobile/User/login');
        }
    }
}
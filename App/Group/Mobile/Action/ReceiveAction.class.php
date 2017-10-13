<?php
/**
 * Created by PhpStorm.
 * User: wrh42
 * Date: 2017/10/12
 * Time: 14:20
 */

class ReceiveAction extends Action {

    public function index() {
        $this->display();
    }

    public function getData() {
        $where['client_id'] = session('hkwcd_user.user_id');
        $where['status'] = 1;
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        //分页
        import('ORG.Util.Page');
        $count = M('ReceiveAddress')->where($where)->count();

        $page = I("p", 1, "intval");
        $count = 10;
        $offset = ($page - 1) * $count;
        $limit = "{$offset},{$count}";

        $receive_list =M('ReceiveAddress')->where($where)->limit($limit)->order('is_default desc, id asc')->select();
        if( $receive_list ) {
            foreach( $receive_list  as $k => $v ) {
                $receive_list[$k]['index'] = $k+1;
            }
        } else {
            $receive_list = [];
        }
        echo json_encode($receive_list);
        exit;
    }

    public function detail() {
        $this->display();
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: wrh42
 * Date: 2017/10/12
 * Time: 14:19
 */

class DeliveryAction extends Action {

    public function index() {
        $where['client_id'] = session('hkwcd_user.user_id');
        $where['status'] = 1;
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        //分页
        import('ORG.Util.Page');
        $count = M('DeliveryAddress')->where($where)->count();

        $page = new Page($count, C('usercenter_page_count'));
        $limit = $page->firstRow. ',' .$page->listRows;

        $delivery_list =M('DeliveryAddress')->where($where)->limit($limit)->order('is_default desc, id asc')->select();
        if( $delivery_list ) {
            foreach( $delivery_list  as $k => $v ) {
                $delivery_list[$k]['index'] = $k+1;
            }
        }
        $this->assign('delivery_list',$delivery_list);// 赋值数据集
        $this->assign("json_delivery_list", json_encode($delivery_list));
//        echo json_encode($delivery_list);
        $this->display();

    }

    public function getData() {
        $where['client_id'] = session('hkwcd_user.user_id');
        $where['status'] = 1;
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        //分页
        import('ORG.Util.Page');
        $count = M('DeliveryAddress')->where($where)->count();

        $page = new Page($count, C('usercenter_page_count'));
        $limit = $page->firstRow. ',' .$page->listRows;

        $delivery_list =M('DeliveryAddress')->where($where)->limit($limit)->order('is_default desc, id asc')->select();
        if( $delivery_list ) {
            foreach( $delivery_list  as $k => $v ) {
                $delivery_list[$k]['index'] = $k+1;
            }
        }
//        $this->assign('delivery_list',$delivery_list);// 赋值数据集
//        $this->assign("json_delivery_list", json_encode($delivery_list));
        echo json_encode($delivery_list);
        exit;
    }

    public function detail() {
        $this->display();

    }

}
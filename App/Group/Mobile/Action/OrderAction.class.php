<?php
/**
 * Created by PhpStorm.
 * User: wrh42
 * Date: 2017/10/12
 * Time: 14:18
 */

class OrderAction extends BaseAction {

    public function index() {
        $this->display();
    }

    public function getData() {
        $client_id = session('hkwcd_user.user_id');

        $start_date = I('start_date', '');
        $end_date = I('end_date', '');

        $where = [
            'status' => 1,
            'client_id' => $client_id,
        ];

        $condition = [];

        if( $start_date ) {
            $condition['add_time'] = ['gt', $start_date];
        }
        if( $end_date ) {
            $old_end_date = $end_date;
            $end_date = date('Y-m-d', strtotime($end_date) + 3600 * 24);
            $condition['add_time'] = ['lt', $end_date];
        }
        if( $start_date && $end_date ) {
            $condition['add_time'] = ['between', [$start_date, $end_date]];
        } else {
            if( $start_date ) {
                $condition['add_time'] = ['gt', $start_date];
            }
            if( $end_date ) {
                $condition['add_time'] = ['lt', $end_date];
            }
        }
        if( $condition ) {
            if( count($condition) == 2 ) {
                $condition['_logic'] = 'AND';
            }
            $where['_complex'] = $condition;
        }

        //分页
        import('ORG.Util.Page');
        $count = M('ClientOrder')->where($where)->count();

        $page = I("p", 1, "intval");
        $count = 10;
        $offset = ($page - 1) * $count;
        $limit = "{$offset},{$count}";


        $order_list = M('ClientOrder')->where($where)->limit($limit)->order('id desc')->select();
        if( $order_list ) {
            foreach( $order_list as $k => $v ) {
                $order_list[$k]['status_str'] = _order_status($v);
            }
        } else {
            $order_list = [];
        }
        echo json_encode($order_list);
        exit;
    }

    public function ensure() {
        $this->display();
    }

    public function detail() {
        $this->display();
    }

    public function trace() {
        $this->display();
    }

    public function fee() {
        $this->display();

    }

}
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
        $client_id = session('hkwcd_user.user_id');
        $client = M('Client')->where(['status' => 1, 'id' => $client_id])->find();

        $id = I('id');
        $order = M('ClientOrder')->where(['id' => $id, 'client_id' => $client_id, 'status' => 1])->find();
        if( empty($order) ) {
            $this->error('订单不存在');
        }
        if( (!$order['exam_status'] == 1 && $order['ensure_status'] == 0) ) {
//            $this->error('订单不是待确认状态');
        }
        $order_fee = M('ClientOrderFee')->where(['order_id' => $id])->find();

        $fee_list = C("fee_list");
        $this->assign('order', $order);
        $this->assign('order_fee', $order_fee);
        $this->assign("fee_list", $fee_list);
        $this->display();
    }

    public function detail() {
        $client_id = session('hkwcd_user.user_id');
        $client = M('Client')->where(['status' => 1, 'id' => $client_id])->find();

        $id = I('id');
        $order = M('ClientOrder')->where(['id' => $id, 'client_id' => $client_id, 'status' => 1])->find();
        if( empty($order) ) {
            $this->error('订单不存在');
        }

        $order['status_str'] = _order_status($order);

        $order_detail = M('ClientOrderDetail')->where(['order_num' => $order['order_num']])->select();

        if( $order_detail ) {
            foreach( $order_detail as $k => $v ) {
                $order_detail[$k]["detail_goods_code"] = $v["goods_code"];
                unset($order_detail[$k]["goods_code"]);
                $order_detail[$k]["detail_count"] = $v["count"];
                unset($order_detail[$k]["count"]);
            }
        }
        $order_specifications = M('ClientOrderSpecifications')
            ->alias('s')
            ->field('s.*, m.detail_id, m.number, cd.product_name, cd.en_product_name, cd.unit, cd.goods_code, cd.origin')
            ->join('inner join hx_client_order_map as m on m.specifications_id = s.id')
            ->join('left join hx_client_order_detail as cd on m.detail_id = cd.id')
            ->where(['s.order_num' => $order['order_num']])
            ->select();
//        echo M('ClientOrderSpecifications')->getLastSql();exit;
        $s_cursor = 0;
        if( $order_specifications ) {
            $temp = [];
            foreach( $order_specifications as $k => $v ) {
                if( !isset($temp[$v['id']]) ) {
                    $temp[$v['id']] = $v;
                }
                $temp[$v['id']]['cargo'][] = [
                    'detail_id' => $v['detail_id'],
                    'specifications_id' => $v['id'],
                    'product_name' => $v['product_name'],
                    'en_product_name' => $v['en_product_name'],
                    'detail_goods_code' => $v['goods_code'],
                    'origin' => $v['origin'],
                    'product_count' => $v['number'],
                ];
                $last_index = count($temp[$v['id']]['cargo']) - 1;
                foreach( $order_detail as $key => $d ) {
                    if( $d['id'] == $v['detail_id'] ) {
                        $temp[$v['id']]['cargo'][$last_index]['product_index'] = $key;
                    }
                }
            }
            $order_specifications = $temp;
            $temp = [];
            $start = 1;
            foreach( $order_specifications as $v ) {
                $v['index'] = $v['id'];
                $v['id'] = $start ."-". $v['count'];
                $start = $start + $v['count'];
                $v['rate'] = $v['length'] * $v['width'] * $v['height'] / 5000;
                $temp[] = $v;
            }
            $order_specifications = $temp;
        }

        $order_fee = M('ClientOrderFee')->where(['order_id' => $id])->find();

        $this->assign('order', $order);
        $this->assign('order_detail', json_encode($order_detail));
        $this->assign('order_specifications', json_encode($order_specifications));
        $this->assign('order_fee', $order_fee);
        $this->assign('settlement_method', C('settlement_method'));
        $this->display();
    }

    public function trace() {
        $client_id = session('hkwcd_user.user_id');
        $client = M('Client')->where(['status' => 1, 'id' => $client_id])->find();

        $id = I('id');
        $order = M('ClientOrder')->where(['id' => $id, 'client_id' => $client_id, 'status' => 1])->find();
        if( empty($order) ) {
            $this->error('订单不存在');
        }

        $order['status_str'] = _order_status($order);
        $this->assign("order", $order);
        $this->display();
    }

    public function fee() {
        $this->display();

    }

}
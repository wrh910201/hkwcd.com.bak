<?php
/**
 * Created by PhpStorm.
 * User: Wrh
 * Date: 2016/10/14
 * Time: 16:06
 */
class OrderAction extends CommonAction {

    public function index() {
        $keyword = I('keyword', '', 'htmlspecialchars,trim');//关键字
        $client_id = I('cid', 0, 'intval');
        $status = I('status', 0, 'intval');
        $where = array();
        $condition = array();
        $client_where = array();
        switch( $status ) {
            case 2:
                $where = [
                    'client_status' => 1,
                    'exam_status' => 0,
                ];
                break;   //未审核
            case 3:
                $where = [
                    'client_status' => 1,
                    'exam_status' => 1,
                    'express_status' => 0,
                ];
                break;   //未发货
            case 4:
                $where = [
                    'client_status' => 1,
                    'exam_status' => 1,
                    'express_status' => 1,
                    'receive_status' => 0,
                ];
                break;   //未收货
            case 5:
                $where = [
                    'client_status' => 1,
                    'exam_status' => 1,
                    'express_status' => 1,
                    'receive_status' => 1,
                ];
                break;   //已收货
            case 6:
                $where['error_status'] = 1;
                break;  //异常订单
            default: break;
        }
        if (!empty($keyword)) {
            $condition['order_num'] = ['like', $keyword];
            $condition['full_name'] = ['like', $keyword];
            $condition['company'] = ['like', $keyword];
//            $condition['delivery_consignor'] = ['like', $keyword];
//            $condition['receive_addressee'] = ['like', $keyword];
            $condition['_logic'] = 'OR';
            $where['_complex'] = $condition;
        }
        if( $client_id > 0 ) {
            $where['client_id'] = $client_id;
            $client_where['id'] = $client_id;
        }
        //分页
        import('ORG.Util.Page');
        $count = M('ClientOrder')->where($where)->count();

        $page = new Page($count, 10);
        $limit = $page->firstRow. ',' .$page->listRows;
        $list = M('ClientOrder')
            ->field('hx_client_order.*, hx_client.full_name, hx_client.company')
            ->join('hx_client on hx_client_order.client_id = hx_client.id')
            ->where($where)
            ->order('id desc')
            ->limit($limit)
//            ->buildSql();
//        echo $list;exit;
            ->select();

        if( $list ) {
            foreach( $list as $k => $v ) {
                $list[$k]['status_str'] = $this->_get_order_status($v);
            }
        }

        $this->status = $status;
        $this->page = $page->show();
        $this->order_list = $list;
        $this->type = '客户订单列表';
        $this->keyword = $keyword;

        $this->display();
    }

    public function detail() {
        $id = I('get.id', 0, 'intval');
        $order = M('ClientOrder')->where(['id' => $id])->find();
        if( empty($order) ) {
            $this->error('订单不存在');
            exit;
        }
        $order['status_str'] = $this->_get_order_status($order);
        $order_detail = M('ClientOrderDetail')->where(['order_id' => $id])->select();
        $order_specifications = M('ClientOrderSpecifications')->where(['order_id' => $id])->select();

        foreach( $order_specifications as $k => $v ) {
            $temp = $v['height'] * $v['length'] * $v['width'] / 1000;
            if( $v['weight'] > $temp) {
                $order_specifications[$k]['real_weight'] = $v['weight'];
            } else {
                $order_specifications[$k]['real_weight'] = $temp;
            }
        }

        $this->assign('order', $order);
        $this->assign('order_detail', $order_detail);
        $this->assign('order_specifications', $order_specifications);
        $this->type = '客户订单详情';
        $this->display();
    }

    public function pass() {
        $id = I('get.id', 0, 'intval');
        $order = M('ClientOrder')->where(['id' => $id])->find();
        if( empty($order) ) {
            $this->error('订单不存在');
            exit;
        }
        if( $order['exam_status'] != 0 ) {
            $this->error('订单状态不能进行当前操作，请返回确认');
            exit;
        }
         $data = [
             'exam_time' => date('Y-m-d H:i:s', time()),
             'exam_status' => 1,
             'exam_operator_id' => session('yang_adm_uid'),
         ];
        $map = [
            'id' => $id,
            'exam_status' => 0,
            'client_status' => 1,
        ];
        $result = M('ClientOrder')->where($map)->save($data);
        if( is_numeric($result) ) {
            //添加订单日志
            $log_data = [
                'order_num' => $order['order_num'],
                'order_id' => $id,
                'operator_id' => session('yang_adm_uid'),
                'type' => 2,
                'content' => '通过订单',
            ];
            M('ClientOrderLog')->add($log_data);
            $this->success('订单已审核通过');
        } else {
            $this->error('订单通过失败');
        }
    }

    public function reject() {
        $id = I('get.id', 0, 'intval');
        $order = M('ClientOrder')->where(['id' => $id])->find();
        if( empty($order) ) {
            $this->error('订单不存在');
            exit;
        }
        if( $order['exam_status'] != 0 ) {
            $this->error('订单状态不能进行当前操作，请返回确认');
            exit;
        }
        $this->assign('order', $order);
        $this->display();
    }

    public function doReject() {
        $id = I('post.id', 0, 'intval');
        $order = M('ClientOrder')->where(['id' => $id])->find();
        if( empty($order) ) {
            $this->error('订单不存在');
            exit;
        }
        if( $order['exam_status'] != 0 ) {
            $this->error('订单状态不能进行当前操作，请返回确认');
            exit;
        }
        $message = I('post.content', '', 'trim');
        if( empty($message) ) {
            $this->error('请输入问题描述');
            exit;
        }
        M('ClientOrder')->startTrans();
        $transaction = true;

        //修改订单状态
        $map = [
            'id' => $id,
            'client_status' => 1,
            'exam_status' => 0,
            'express_status' => 0,
            'receive_status' => 0,
        ];

        $data['client_status'] = '0';
        $data['is_rejected'] = 1;
        $result = M('ClientOrder')->where($map)->save($data);
        if( !$result ) {
            $transaction = false;
        }

        //添加驳回原因
        if( $transaction ) {
            $message_data = [
                'content' => $message,
                'order_num' => $order['order_num'],
                'order_id' => $order['id'],
                'user_id' => $order['client_id'],
                'operator_id' => session('yang_adm_uid'),
            ];
            $result = M('ClientOrderMessage')->add($message_data);
            if( !$result ) {
                $transaction = false;
            }
        }
        if( $transaction ) {
            //添加订单日志
            M('ClientOrder')->commit();
            $log_data = [
                'order_num' => $order['order_num'],
                'order_id' => $id,
                'operator_id' => session('yang_adm_uid'),
                'type' => 2,
                'content' => '驳回订单',
            ];
            M('ClientOrderLog')->add($log_data);
            $this->success('订单已驳回', U('Order/detail', array('id' => $order['id'])));
        } else {
            M('ClientOrder')->rollback();
            $this->error('驳回订单失败');
        }
    }

    public function delivery() {
        $id = I('get.id', 0, 'intval');
        $order = M('ClientOrder')->where(['id' => $id])->find();
        if( empty($order) ) {
            $this->error('订单不存在');
            exit;
        }
        if( $order['express_status'] != 0 ) {
            $this->error('订单状态不能进行当前操作，请返回确认');
            exit;
        }
        $this->assign('order', $order);

        $order_detail = M('ClientOrderDetail')->where(['order_id' => $id])->select();
//        echo M('ClientOrderDetail')->getLastSql();exit;
        $this->assign('order_detail', $order_detail);

        $express_type = M('ExpressType')->select();
        $this->assign('express_type', $express_type);
        $this->display();
    }

    public function doDelivery() {
        $id = I('post.id', 0, 'intval');
        $order = M('ClientOrder')->where(['id' => $id])->find();
        if( empty($order) ) {
            $this->error('订单不存在');
            exit;
        }
        if( $order['express_status'] != 0 ) {
            $this->error('订单状态不能进行当前操作，请返回确认');
            exit;
        }

        $express_type_id = I('post.express_type_id', 0, 'intval');
        $express_order_num = I('post.express_order_num', '', 'trim');
        $invoice_num = I('post.invoice_num', '', 'trim');
        $origin = I('post.origin', '', 'trim');
        $payment_term = I('post.payment_term', '', 'trim');
        $transportation_mode = I('post.transportation_mode', '', 'trim');

        $express = M('ExpressType')->where(['id' => $express_type_id, 'status' => 1])->find();
        if( empty($express) ) {
            $this->error('请选择下家');
        }
        if( empty($express_order_num) ) {
            $this->error('请输入转单号');
        }
        if( empty($invoice_num) ) {
            $this->error('请输入发票号');
        }
        if( empty($origin) ) {
            $this->error('请输入原产地');

        }
        if( empty($payment_term) ) {
            $this->error('请输入付款条款');

        }
        if( empty($transportation_mode) ) {
            $this->error('请输入运输方式');
        }

        $order_detail = M('ClientOrderDetail')->where(['order_id' => $id])->select();
        foreach( $order_detail as $k => $v ) {
            $quantity = I('post.quantity_'.$v['id'], 0 , 'intval');
            $weight = I('post.weight_'.$v['id'], 0, 'floatval');
            $volume_weight = I('post.volume_weight_'.$v['id'], 0, 'floatval');
            if( $quantity <= 0 ) {
                $this->error('请输入【'.$v['product_name'].'】的箱数');
            }
            if( $weight <= 0 ) {
                $this->error('请输入【'.$v['product_name'].'】的过磅重量');
            }
            if( $volume_weight <= 0 ) {
                $this->error('请输入【'.$v['product_name'].'】的材积重量');
            }
            $order_detail[$k]['quantity'] = $quantity;
            $order_detail[$k]['weight'] = $weight;
            $order_detail[$k]['volume_weight'] = $volume_weight;
        }

        $data['express_status'] = 1;
        $data['express_type_id'] = $express_type_id;
        $data['express_type'] = $express['type'];
        $data['express_type_name'] = $express['name'];
        $data['express_order_num'] = $express_order_num;
        $data['invoice_num'] = $invoice_num;
        $data['origin'] = $origin;
        $data['payment_term'] = $payment_term;
        $data['transportation_mode'] = $transportation_mode;
        $data['express_time'] = date('Y-m-d H:i:s', time());

        //事务开始
        M('ClientOrder')->startTrans();
        $transaction = true;

        if( $transaction ) {
            $result = M('ClientOrder')->where(['id' => $id])->save($data);
            if( !$result ) {
                $transaction = false;
            }
        }
        if( $transaction ) {
            foreach( $order_detail as $k => $v ) {
                if( false == $transaction ) {
                    break;
                }
                $detail_data = [
                    'quantity' => $v['quantity'],
                    'weight' => $v['weight'],
                    'volume_weight' => $v['volume_weight'],
                ];
                $result = M('ClientOrderDetail')->where(['id' => $v['id']])->save($detail_data);
                if( !$result ) {
                    $transaction = false;
                }
            }
        }

        if( $transaction ) {
            M('ClientOrder')->commit();
            $log_data = [
                'order_num' => $order['order_num'],
                'order_id' => $id,
                'operator_id' => session('yang_adm_uid'),
                'type' => 2,
                'content' => '订单发货',
            ];
            M('ClientOrderLog')->add($log_data);
            $this->success('发货成功', U('Order/detail', array('id' => $id)));
        } else {
            $this->error('发货失败');
        }

    }

    public function packing() {
        $id = I('get.id', 0, 'intval');
        $order = M('ClientOrder')->where(['id' => $id])->find();
        if( empty($order) ) {
            $this->error('订单不存在');
            exit;
        }
        $order['receive_mobile_phone'] = $order['receive_mobile'];
        if( $order['receive_phone'] ) {
            if( $order['receive_mobile_phone'] ) {
                $order['receive_mobile_phone'] .= '&nbsp;';
            }
            $order['receive_mobile_phone'] .= $order['receive_phone'];
        }
        $order_detail = M('ClientOrderDetail')->where(['order_id' => $id])->select();
        $total = [
            'count' => '',
            'quantity' => '',
            'goods_code' => '',
            'weight' => '',
            'volume_weight' => '',
        ];
        if( $order_detail ) {
            $total = [
                'count' => 0,
                'quantity' => 0,
                'goods_code' => '',
                'weight' => 0,
                'volume_weight' => 0,
            ];
            foreach( $order_detail as $k => $v ) {
                $total['count'] += $v['count'];
                $total['quantity'] += $v['quantity'];
                $total['weight'] += $v['weight'];
                $total['volume_weight'] += $v['volume_weight'];
            }
        }
        $client = M('Client')->where(['id' => $order['client_id']])->find();
        $this->assign('order_detail', $order_detail);
        $this->assign('total', $total);
        $this->assign('client', $client);
        $this->assign('today', date('Y-m-d', time()));
        $this->assign('order', $order);
        $this->display();
    }

    public function invoice() {
        $id = I('get.id', 0, 'intval');
        $order = M('ClientOrder')->where(['id' => $id])->find();
        if( empty($order) ) {
            $this->error('订单不存在');
            exit;
        }
        $order['receive_mobile_phone'] = $order['receive_mobile'];
        if( $order['receive_phone'] ) {
            if( $order['receive_mobile_phone'] ) {
                $order['receive_mobile_phone'] .= '&nbsp;';
            }
            $order['receive_mobile_phone'] .= $order['receive_phone'];
        }
        $order_detail = M('ClientOrderDetail')->where(['order_id' => $id])->select();
        $total = [
            'count' => '',
            'quantity' => '',
            'goods_code' => '',
            'weight' => '',
            'volume_weight' => '',
        ];
        if( $order_detail ) {
            $total = [
                'count' => 0,
                'quantity' => 0,
                'goods_code' => '',
                'weight' => 0,
                'volume_weight' => 0,
            ];
            foreach( $order_detail as $k => $v ) {
                $total['count'] += $v['count'];
                $total['quantity'] += $v['quantity'];
                $total['weight'] += $v['weight'];
                $total['volume_weight'] += $v['volume_weight'];
            }
        }
        $client = M('Client')->where(['id' => $order['client_id']])->find();
        $this->assign('order_detail', $order_detail);
        $this->assign('total', $total);
        $this->assign('client', $client);
        $this->assign('today', date('Y-m-d', time()));
        $this->assign('order', $order);
        $this->display();
    }

    private function _get_order_status($order) {
        $status_str = '';
        if( $order['client_status'] == 0 ) {
            $status_str = '未提交';
            if( $order['is_rejected'] == 1 ) {
                $status_str .= '(驳回)';
            }
        }
        if( $order['client_status'] == 1 && $order['exam_status'] == 0 ) {
            $status_str = '待审核';
        }
        if( $order['client_status'] == 1 && $order['exam_status'] == 1 && $order['express_status'] == 0 ) {
            $status_str = '待发货';
        }
        if( $order['client_status'] == 1 && $order['exam_status'] == 1 && $order['express_status'] == 1 && $order['receive_status'] == 0 ) {
            $status_str = '已发货（待收货）';
        }
        if( $order['client_status'] == 1 && $order['exam_status'] == 1 && $order['express_status'] == 1 && $order['receive_status'] == 1 ) {
            $status_str = '已收货';
        }
        if( $status_str == '' ) {
            $status_str = '订单异常';
        }
        return $status_str;
    }

}
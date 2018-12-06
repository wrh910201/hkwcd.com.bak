<?php
/**
 * Created by PhpStorm.
 * User: Wrh
 * Date: 2016/12/12
 * Time: 21:02
 */
class ClientorderAction extends CommonContentAction {

    public $has_error = false;
    public $error_msg = '';
    public $response = [
        'code' => -1,
        'msg' => '',
    ];
    public $order_detail_unit = [];

    public function _initialize()
    {
        parent::_initialize();
        $order_detail_unit = M("ProductUnit")
            ->select();
        if( $order_detail_unit ) {
            $temp = [];
            foreach( $order_detail_unit as $k => $v ) {
                $temp[$v["en_name"]] = $v["name"];
            }
            $this->order_detail_unit = $temp;
        }
    }

    public function index() {
        $keyword = I('keyword', '', 'htmlspecialchars,trim');//关键字
        $start_date = I('start_date', '');
        $end_date = I('end_date', '');
        $where = array();
        $condition = array();
        if (!empty($keyword)) {
            $condition['o.order_num'] = ['like', '%'.$keyword.'%'];
            $condition['o.delivery_company'] = ['like', '%'.$keyword.'%'];
            $condition['o.receive_company'] = ['like', '%'.$keyword.'%'];
            $condition['c.username'] = ['like', '%'.$keyword.'%'];
            $condition['_logic'] = 'OR';
            $where['_complex'] = $condition;
        }
        if( $start_date ) {
            $where['o.add_time'] = ['gt', $start_date];
        }
        if( $end_date ) {
            $old_end_date = $end_date;
            $end_date = date('Y-m-d', strtotime($end_date) + 3600 * 24);
            $where['o.add_time'] = ['lt', $end_date];
        }
        if( $start_date && $end_date ) {
            $where['o.add_time'] = ['between', [$start_date, $end_date]];
        } else {
            if( $start_date ) {
                $where['o.add_time'] = ['gt', $start_date];
            }
            if( $end_date ) {
                $where['o.add_time'] = ['lt', $end_date];
            }
        }

        $status = I('status', 'all', 'trim');
        switch($status) {
            case 'exam': $where['exam_status'] = 0;break;
            case 'pay': $where['ensure_status'] = 1;$where['pay_status'] = 0;break;
            case 'express':$where['ensure_status'] = 1;$where['pay_status'] = 1;$where['express_status'] = 0; break;
            case 'receive': $where['ensure_status'] = 1;$where['pay_status'] = 1;$where['express_status'] = 1;$where['receive_status'] = 0;break;
            case 'all': break;
            default: break;
        }


        $where['client_status'] = 1;
        //分页
        import('ORG.Util.Page');
        $count = M('ClientOrder')
            ->alias('o')
            ->field('o.*, c.username')
            ->join('left join hx_client as c on o.client_id = c.id')
            ->where($where)
            ->count();

        $page = new Page($count, 10);
        $limit = $page->firstRow. ',' .$page->listRows;
        $list = M('ClientOrder')
            ->alias('o')
            ->field('o.*, c.username')
            ->join('left join hx_client as c on o.client_id = c.id')
            ->where($where)
            ->order('o.add_time desc')
            ->limit($limit)
            ->select();
        if( $list ) {
            foreach( $list as $k => $v ) {
                $list[$k]['company'] = mb_strlen($v['company'], 'utf-8') > 13 ? mb_substr($v['company'], 0, 13, 'utf-8').'...' : $v['company'];
                $list[$k]['status_str'] = _order_status($v);
            }
        }


        $this->page = $page->show();
        $this->order_list = $list;
        $this->type = '客户订单列表';
        $this->keyword = $keyword;
        $this->assign('start_date', $start_date);
        $this->assign('end_date', $old_end_date);

        $this->display();
    }

    public function doExam() {
        if( $this->hkwcd_admin['order_exam'] != 1 && $this->hkwcd_admin['order_manage'] != 1 ) {
            $this->error('您没有审核订单的权限');
        }
        $id = I('id', 0, 'intval');
        $order = M('ClientOrder')->where(['id' => $id])->find();
        if( empty($order) ) {
            $this->error('订单不存在');
        }
        if( !($order['client_status'] == 1 && $order['exam_status'] == 0) ) {
            $this->error('当前订单不是待审核状态');
        }
        $order_fee = M('ClientOrderFee')->where(['order_id' => $id])->find();

        if( empty($order_fee) ) {
            $this->error('您还未录入费用');
        }
        $data = [
            'exam_status' => 1,
            'exam_time' => date('Y-m-d H:i:s', time()),
        ];
        $result = M('ClientOrder')->where(['id' => $id])->save($data);
        if( is_numeric($result) ) {
            //插入操作日志
            $log_data = [
                'order_num' => $order['order_num'],
                'order_id' => $order['id'],
                'operator_id' => session('yang_adm_user_id'),
                'type' => 2,
                'content' => '返回客户确认',
            ];
            M('ClientOrderLog')->add($log_data);

            $this->success('订单已审核，等待客户确认');
        } else {
            $this->error('系统繁忙，请稍后重试');
        }
    }

    public function complete() {
//        if( $this->hkwcd_admin['order_exam'] != 1 && $this->hkwcd_admin['order_manage'] != 1 ) {
//            $this->error('您没有审核订单的权限');
//        }
        $id = I('id', 0, 'intval');
        $order = M('ClientOrder')->where(['id' => $id])->find();
        if( empty($order) ) {
            $this->error('订单不存在');
        }
        if( !($order['express_status'] == 1 && $order['receive_status'] == 0 ) ) {
            $this->error('当前订单不是待收货状态');
        }
        $data = [
            'receive_status' => 1,
        ];
        $result = M('ClientOrder')->where(['id' => $id])->save($data);
        if( is_numeric($result) ) {
            //插入操作日志
            $log_data = [
                'order_num' => $order['order_num'],
                'order_id' => $order['id'],
                'operator_id' => session('yang_adm_user_id'),
                'type' => 2,
                'content' => '订单完成',
            ];
            M('ClientOrderLog')->add($log_data);

            $this->success('操作成功');
        } else {
            $this->error('系统繁忙，请稍后重试');
        }
    }

    public function detail() {
        $id = I('id', 0, 'intval');
        $order = M('ClientOrder')->where(['id' => $id])->find();
        if( empty($order) ) {
            $this->error('订单不存在');
        }
        $order['package_type_name'] = $order['package_type'] == 1 ? '文件' : '包裹';
        $order['status_str'] = _order_status($order);
        if( $order['delivery_mobile'] && $order['delivery_phone'] ) {
            $order['delivery_contact'] = $order['delivery_mobile'].'、'.$order['delivery_phone'];
        } else {
            if( $order['delivery_mobile'] ) {
                $order['delivery_contact'] = $order['delivery_mobile'];
            }
            if( $order['delivery_phone'] ) {
                $order['delivery_contact'] = $order['delivery_phone'];
            }
        }
        if( $order['receive_mobile'] && $order['receive_phone'] ) {
            $order['receive_contact'] = $order['receive_mobile'].'、'.$order['receive_phone'];
        } else {
            if( $order['receive_mobile'] ) {
                $order['receive_contact'] = $order['receive_mobile'];
            }
            if( $order['receive_phone'] ) {
                $order['receive_contact'] = $order['receive_phone'];
            }
        }
        if( $order['enable_spare'] ) {
            if ($order['spare_mobile'] && $order['spare_phone']) {
                $order['spare_contact'] = $order['spare_mobile'] . '、' . $order['spare_phone'];
            } else {
                if ($order['spare_mobile']) {
                    $order['spare_contact'] = $order['spare_mobile'];
                }
                if ($order['spare_phone']) {
                    $order['spare_contact'] = $order['spare_phone'];
                }
            }
        }

        $order_detail = M('ClientOrderDetail')
            ->where(['order_num' => $order['order_num']])
            ->select();
        $order['detail_declared_total'] = 0;
        if( $order_detail ){
            foreach( $order_detail as $k => $v ) {
                $order_detail[$k]['single_declared'] = sprintf('%.2f', $v['single_declared']);
                $order_detail[$k]['declared'] = sprintf('%.2f', $v['single_declared'] * $v['count']);
                $order['detail_declared_total'] += $order_detail[$k]['declared'];
            }
        }
        $order['detail_declared_total'] = sprintf('%.2f', $order['detail_declared_total']);

        $order_specifications = M('ClientOrderSpecifications')
            ->alias('s')
            ->field('s.*, m.detail_id,m.number, d.product_name, d.en_product_name, d.unit')
            ->join('left join hx_client_order_map as m on m.specifications_id = s.id')
            ->join('left join hx_client_order_detail as d on d.id = m.detail_id')
            ->where(['s.order_num' => $order['order_num']])
            ->select();
        if( $order_specifications ) {
            $temp = [];
            foreach( $order_specifications as $k => $v ) {
                if( !isset($temp['item-'.$v['id']]) ) {
                    $temp['item-'.$v['id']] = $v;
                }
                $temp['item-'.$v['id']]['detail'][] = [
                    'product_name' => $v['product_name'],
                    'en_product_name' => $v['en_product_name'],
                    'unit' => $v['unit'],
                    'number' => $v['number']
                ];
            }
            $order_specifications = $temp;
        }
        $order['specifications_total_weight'] = 0;
        $order['specifications_total_rate'] = 0;
        $order['specifications_calculate_weight'] = 0;
        $order['specifications_total_count'] = 0;
        if( $order_specifications ) {
            $start = 1;
            $real_start = 1;
            foreach( $order_specifications as $k => $v ) {
                $end = $start + $v['count'] - 1;
                $order_specifications[$k]['no'] = $start.'-'.$end;
                $order_specifications[$k]['weight'] = sprintf('%.2f', $v['weight']);
                $order_specifications[$k]['length'] = sprintf('%.2f', $v['length']);
                $order_specifications[$k]['width'] = sprintf('%.2f', $v['width']);
                $order_specifications[$k]['height'] = sprintf('%.2f', $v['height']);
                $order_specifications[$k]['rate'] = ($v['height'] * $v['length'] * $v['width'] / 5000);
                $order_specifications[$k]['calculate_weight'] = $v['weight'] > $order_specifications[$k]['rate'] ? $v['weight'] : $order_specifications[$k]['rate'];
                $order['specifications_total_weight'] += $v['weight'] * $v['count'];
                $order['specifications_total_rate'] += $order_specifications[$k]['rate'] * $v['count'];
                $order['specifications_total_count'] += $v['count'];
                $order_specifications[$k]['rowspan'] = count($v['detail']);
                $start = $end + 1;
                //real
                $real_end = $real_start + $v['real_count'] - 1;
                $order_specifications[$k]['real_no'] = $real_start.'-'.$real_end;
                $order_specifications[$k]['real_weight'] = sprintf('%.2f', $v['real_weight']);
                $order_specifications[$k]['real_length'] = sprintf('%.2f', $v['real_length']);
                $order_specifications[$k]['real_width'] = sprintf('%.2f', $v['real_width']);
                $order_specifications[$k]['real_height'] = sprintf('%.2f', $v['real_height']);
                $order_specifications[$k]['real_rate'] = ($v['real_height'] * $v['real_length'] * $v['real_width'] / 5000);
                $order_specifications[$k]['real_calculate_weight'] = $v['real_weight'] > $order_specifications[$k]['real_rate'] ? $v['real_weight'] : $order_specifications[$k]['real_rate'];
                $order['real_specifications_total_weight'] += $v['real_weight'] * $v['real_count'];
                $order['real_specifications_total_rate'] += $order_specifications[$k]['real_rate'] * $v['real_count'];
                $order['real_specifications_total_count'] += $v['real_count'];
                $order_specifications[$k]['rowspan'] = count($v['detail']);
                $real_start = $real_end + 1;
            }
            $order['specifications_calculate_weight'] = $order['specifications_total_weight'] > $order['specifications_total_rate'] ? $order['specifications_total_weight']:$order['specifications_total_rate'];
            $order['real_specifications_calculate_weight'] = $order['real_specifications_total_weight'] > $order['real_specifications_total_rate'] ? $order['real_specifications_total_weight'] : $order['real_specifications_total_rate'];

        }
//        var_dump($order_specifications);exit;
        $order_log = M('ClientOrderLog')
            ->alias('l')
            ->field('l.created_at, l.content, c.full_name as client_name, a.realname as operator_name, l.type')
            ->join('left join hx_client as c on c.id = l.user_id and l.type = 1')
            ->join('left join hx_admin as a on a.id = l.operator_id and l.type = 2')
            ->where(['l.order_id' => $id])
            ->order('l.created_at')
            ->select();
//        echo M('ClientOrderLog')->getLastSql();exit;
//        var_dump($order_log);exit;

        $channel_list = M('Channel')->where(['status' => 1])->select();

        $order_fee = M('ClientOrderFee')->where(['order_id' => $id])->find();
        $express_type = M('ExpressType')->where(1)->select();

        $this->assign('order_fee', $order_fee);
        $this->assign('settlement_method', C('settlement_method'));
        $this->assign('order_log', $order_log);
        $this->assign('channel_list', $channel_list);
        $this->assign('order', $order);
        $this->assign('order_specifications', $order_specifications);
        $this->assign('order_detail', $order_detail);
        $this->assign('express_type', $express_type);
        $this->type = '客户订单详情';

        $has_next = false;
        $has_previous = false;
        $next_id = "";
        $previous_id = "";

        $next_order = M("ClientOrder")->where("id < {$id}  and client_status= 1")
            ->order("id desc")
            ->find();
        $previous_order = M("ClientOrder")->where("id > {$id}  and client_status= 1")
            ->order("id asc")
            ->find();
        if( $next_order ) {
            $has_next = true;
            $next_id = $next_order["id"];
        }
        if( $previous_order ) {
            $has_previous = true;
            $previous_id = $previous_order["id"];
        }

        $this->assign("has_next", $has_next);
        $this->assign("has_previous", $has_previous);
        $this->assign("next_id", $next_id);
        $this->assign("previous_id", $previous_id);

        $this->display();

    }

    public function inputFee() {
        if( $this->hkwcd_admin['order_pay'] != 1 && $this->hkwcd_admin['order_manage'] != 1 ) {
            $this->response['msg'] = '您没有订单收款的权限';
            echo json_encode($this->response);
        }
        $id = I('id', 0, 'intval');
        $order = M('ClientOrder')->where(['id' => $id])->find();
        if( empty($order) ) {
            $this->error('订单不存在');
        }
        if( !(($order['client_status'] == 1 && $order['exam_status'] == 0) || ($order['client_status'] == 1 && $order['exam_status'] == 1 && 0 == $order['ensure_status'])) ) {
//            $this->error('当前订单不是待审核状态');
            $this->error('当前订单不能输入费用');
        }
        $data['delivery_fee'] = I('delivery_fee', 0, 'floatval');
        $data['fuel_cost'] = I('fuel_cost', 0, 'floatval');
        $data['customs_charges'] = I('customs_charges', 0, 'floatval');
        $data['pick_up_charge'] = I('pick_up_charge', 0, 'floatval');
        $data['express_fee'] = I('express_fee', 0, 'floatval');
        $data['insurance_premium'] = I('insurance_premium', 0, 'floatval');
        $data['packing_charges'] = I('packing_charges', 0, 'floatval');
        $data['inspection_fee'] = I('inspection_fee', 0, 'floatval');
        $data['remote_fee'] = I('remote_fee', 0, 'floatval');
        $data['over_weight_fee'] = I('over_weight_fee', 0, 'floatval');
        $data['over_length_fee'] = I('over_length_fee', 0, 'floatval');
        $data['storage_fee'] = I('storage_fee', 0, 'floatval');
        $data['incidental'] = I('incidental', 0, 'floatval');
        $data['other_fee'] = I('other_fee', 0, 'floatval');
        $data['total_fee'] = I('total_fee', 0, 'floatval');
        $data['settlement_method'] = I('settlement_method', '', 'trim');


        //总费用必须有
        if( $data['total_fee'] <= 0 ) {
            $this->response['msg'] = '请至少输入总费用';
            echo json_encode($this->response);
            exit;
        }
        //结算方式必须有
        if( !in_array($data['settlement_method'], C('settlement_method')) ) {
            $this->response['msg'] = '请选择一种结算方式';
            echo json_encode($this->response);
            exit;
        }

        $exists = M('ClientOrderFee')->where(['order_id' => $id])->find();
        if( $exists ) {
            $msg = '修改费用';
            $result = M('ClientOrderFee')->where(['order_id' => $id])->save($data);
            $result = is_numeric($result) ? true : false;
        } else {
            $msg = '录入费用';
            $data['order_id'] = $id;
            $result = M('ClientOrderFee')->add($data);

        }

        if( $result ) {
            //插入操作日志
            $log_data = [
                'order_num' => $order['order_num'],
                'order_id' => $order['id'],
                'operator_id' => session('yang_adm_user_id'),
                'type' => 2,
                'content' => $msg,
            ];
            M('ClientOrderLog')->add($log_data);

            $this->response['code'] = 1;
            $this->response['msg'] = $msg.'成功';
            $this->response['url'] = U('/Manage/Clientorder/detail', ['id' => $id]);
        } else {
            $this->response['msg'] = $msg.'失败';
        }
        echo json_encode($this->response);
        exit;

    }

    public function exam() {
        if( $this->hkwcd_admin['order_exam'] != 1 && $this->hkwcd_admin['order_manage'] != 1 ) {
            $this->error('您没有审核订单的权限');
        }
        $id = I('id', 0, 'intval');
        $order = M('ClientOrder')->where(['id' => $id])->find();
        if( empty($order) ) {
            $this->error('订单不存在');
        }
        $order['package_type_name'] = $order['package_type'] == 1 ? '文件' : '包裹';
        $order['status_str'] = _order_status($order);
        $order_specifications = M('ClientOrderSpecifications')
            ->where(['order_num' => $order['order_num']])
            ->select();
        if( $order_specifications ) {
            foreach( $order_specifications as $k => $v ) {
                $order_specifications[$k]['weight'] = sprintf('%.2f', $v['weight']).'kg';
                $order_specifications[$k]['length'] = sprintf('%.2f', $v['length']).'cm';
                $order_specifications[$k]['width'] = sprintf('%.2f', $v['width']).'cm';
                $order_specifications[$k]['height'] = sprintf('%.2f', $v['height']).'cm';
            }
        }
        $order_detail = M('ClientOrderDetail')
            ->where(['order_num' => $order['order_num']])
            ->select();
        if( $order_detail ){
            foreach( $order_detail as $k => $v ) {
                $order_detail[$k]['single_declared'] = sprintf('%.2f', $v['single_declared']);
                $order_detail[$k]['declared'] = sprintf('%.2f', $v['declared']);
            }
        }

        $channel_list = M('Channel')->where(['status' => 1])->select();
        $currency_list = M('Currency')->where(['status' => 1])->select();

        $this->assign('channel_list', $channel_list);
        $this->assign('currency_list', $currency_list);
        $this->assign('order', $order);
        $this->assign('order_specifications', $order_specifications);
        $this->assign('order_detail', $order_detail);
        $this->type = '客户订单详情';
        $this->display();
    }

    public function reject() {
        if( $this->hkwcd_admin['order_exam'] != 1 && $this->hkwcd_admin['order_manage'] != 1 ) {
            $this->error('您没有审核订单的权限');
        }
        $id = I('id', 0, 'intval');
        $order = M('ClientOrder')->where(['id' => $id])->find();
        if( empty($order) ) {
            $this->error('订单不存在');
        }
        if( !($order['client_status'] == 1 && $order['exam_status'] == 0) ) {
            $this->error('当前订单不是待审核状态');
        }
        $this->type = '驳回客户订单';
        $this->assign('order', $order);
        $this->display();
    }

    public function doReject() {
        if( $this->hkwcd_admin['order_exam'] != 1 && $this->hkwcd_admin['order_manage'] != 1 ) {
            $this->error('您没有审核订单的权限');
        }
        $id = I('id', 0, 'intval');
        $order = M('ClientOrder')->where(['id' => $id])->find();
        if( empty($order) ) {
            $this->error('订单不存在');
        }
        if( !($order['client_status'] == 1 && $order['exam_status'] == 0) ) {
            $this->error('当前订单不是待审核状态');
        }
        $reject_reason = I('reject_reason', '', 'trim');
        if( empty($reject_reason) ) {
            $this->error('请输入驳回原因');
        }

        $map = [
            'id' => $id,
            'client_status' => 1,
            'exam_status' => 0
        ];

        $data = [
            'client_status' => 0,
            'is_rejected' => 1,
        ];

        $message_data = [
            'content' => $reject_reason,
            'order_num' => $order['order_num'],
            'order_id' => $order['id'],
            'user_id' => $order['client_id'],
            'status' => 1,
        ];
        $model = new Model();
        $model->startTrans();
        $transaction = true;

        $result = M('ClientOrder')->where($map)->save($data);
        if( is_numeric($result) ) {
            $transaction = true;
        } else {
            $transaction = false;
        }

        if( $transaction ) {
            $result = M('ClientOrderMessage')->add($message_data);
            if( !$result ) {
                $transaction = false;
            }
        }
        if( $transaction ) {
            $model->commit();
            //插入操作日志
            $log_data = [
                'order_num' => $order['order_num'],
                'order_id' => $order['id'],
                'operator_id' => session('yang_adm_user_id'),
                'type' => 2,
                'content' => '驳回订单',
            ];
            M('ClientOrderLog')->add($log_data);
            $this->success('订单驳回成功', U('Clientorder/index'));
        } else{
            $model->rollback();
            $this->error('系统繁忙，请稍后重试');
        }
    }

    public function remark() {
        $id = I('id', 0, 'intval');
        $order = M('ClientOrder')->where(['id' => $id])->find();
        if( empty($order) ) {
            $this->error('订单不存在');
        }
        $this->type = '订单处理备注';
        $this->assign('order', $order);
        $this->display();
    }

    public function doRemark() {
        $id = I('id', 0, 'intval');
        $order = M('ClientOrder')->where(['id' => $id])->find();
        if( empty($order) ) {
            $this->error('订单不存在');
        }
        $operate_remark = I('operate_remark', '', 'trim');
        if( empty($operate_remark) ) {
            $this->error('请输入处理备注');
        }

        $map = [
            'id' => $id,
        ];

        $data = [
            'operate_remark' => $operate_remark,
        ];

        $model = new Model();
        $model->startTrans();
        $transaction = true;

        $result = M('ClientOrder')->where($map)->save($data);
        if( is_numeric($result) ) {
            $transaction = true;
        } else {
            $transaction = false;
        }

        if( $transaction ) {
            $model->commit();
            //插入操作日志
            $log_data = [
                'order_num' => $order['order_num'],
                'order_id' => $order['id'],
                'operator_id' => session('yang_adm_user_id'),
                'type' => 2,
                'content' => '订单处理备注',
            ];
            M('ClientOrderLog')->add($log_data);
            $this->success('订单备注成功', U('Clientorder/index'));
        } else{
            $model->rollback();
            $this->error('系统繁忙，请稍后重试');
        }
    }

    public function edit() {
        $id = I('id');
        $order = M('ClientOrder')->where(['id' => $id, 'status' => 1])->find();
        if( empty($order) ) {
            $this->error('订单不存在');
        }
//        if( $order['client_status'] != 0 ) {
//            $this->error('当前订单不是可编辑状态');
//        }
        if( $order['pay_status'] == 1 ) {
            $this->error('订单已收款，已不能编辑');
        }

        $order_detail = M('ClientOrderDetail')->where(['order_id' => $id])->select();
        $d_cursor = 0;
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
//        var_dump($order_specifications);exit;
        $selected_delivery = M('DeliveryAddress')->where(['id' => $order['delivery_id']])->find();
        $selected_receive = M('ReceiveAddress')->where(['id' => $order['receive_id']])->find();

        $this->assign('order', $order);
        $this->assign('order_detail', json_encode($order_detail));
        $this->assign('order_specifications', json_encode($order_specifications));

        $this->assign('has_default_delivery', true);
        $this->assign('has_default_receive', true);
        $this->assign('json_delivery', json_encode($selected_delivery));
        $this->assign('json_receive', json_encode($selected_receive));
        $this->assign('s_cursor', $s_cursor);
        $this->assign('d_cursor', $d_cursor);


        $this->assign('order_detail_unit', $this->order_detail_unit);
        $this->assign('package_type', C('package_type'));
        $this->assign('price_terms', C('price_terms'));
        $this->assign('tariff_payment', C('tariff_payment'));
        $this->assign('settlement', C('settlement'));
        $this->assign('express_service', C('express_service'));
        $this->assign('export_reason', C('export_reason'));
        $this->assign('export_nature', C('export_nature'));
        $channel_list = M('Channel')->where(['status' => 1])->select();
        $this->assign('channel_list', $channel_list);
        //国家
        $where = array('pid' => 0,'types'=>0);
        $country_list = M('country')->where($where)->order('sort,ename')->select();
        $this->assign('country_list', $country_list);
        if( $country_list ) {
            $temp = [];
            foreach( $country_list as $k => $v ) {
                $temp['item-'.$v['id']] = $v;
            }
            $country_list = $temp;
        }
        $this->assign('json_country_list', json_encode($country_list));


        if( empty($client['company']) ) {
            $this->assign('default_company', json_encode(''));
        } else {
            $this->assign('default_company', json_encode($client['company']));
        }
        $this->assign('title', '编辑订单');
        $this->display();
    }

    public function trace() {
        $id = I('id', 0, 'intval');
        $order = M('ClientOrder')->where(['id' => $id, 'status' => 1])->find();
        if( empty($order) ) {
            $this->error('订单不存在');
        }
        if( !($order['express_status'] == 1) ) {
            $this->error('当前订单未发货');
        }
        $order['package_type_name'] = $order['package_type'] == 1 ? '文件' : '包裹';
        $order['status_str'] = _order_status($order);
        if( 0 == $order["self_express"] ) {
            $trace_result = S('hkwcd_trace_result_' . $order['order_num']);
            if (!$trace_result) {
                $trace_result = query_express($order['express_type'], $order['express_order_num']);
//            var_dump($trace_result);exit;
                S('hkwcd_trace_result_' . $order['order_num'], $trace_result, 7200);
            }
        } else {
            S('hkwcd_trace_result_' . $order['order_num'], null);
            $express_detail = M("ClientOrderSelfExpress")->where(['order_id' => $id])->select();
            $this->assign("express_detail", $express_detail);

        }
        $trace_result_array = json_decode($trace_result, true);
        $this->assign('order', $order);
        $this->assign('trace_result', $trace_result_array);

        $this->type = '订单跟踪';
        $this->display();

    }

    public function pay() {
        if( $this->hkwcd_admin['order_exam'] != 1 && $this->hkwcd_admin['order_manage'] != 1 ) {
            $this->error('您没有订单收款的权限');
        }
        $id = I('id', 0, 'intval');
        $order = M('ClientOrder')->where(['id' => $id, 'status' => 1])->find();
        if( empty($order) ) {
            $this->error('订单不存在');
        }
        if( !($order['ensure_status'] == 1 && $order['pay_status'] == 0) ) {
            $this->error('当前订单不是待收款状态');
        }
        $data = [
            'pay_status' => 1
        ];
        $result = M('ClientOrder')->where(['id' => $id, 'status' => 1])->save($data);
        if( is_numeric($result) ) {
            //插入操作日志
            $log_data = [
                'order_num' => $order['order_num'],
                'order_id' => $order['id'],
                'operator_id' => session('yang_adm_user_id'),
                'type' => 2,
                'content' => '已收款',
            ];
            M('ClientOrderLog')->add($log_data);
            $this->success('订单收款成功');
        } else {
            $this->error('订单收款失败');
        }
    }

    public function delivery() {
        if( $this->hkwcd_admin['order_express'] != 1 && $this->hkwcd_admin['order_express'] != 1 ) {
            $this->error('您没有订单发货的权限');
        }
        $id = I('id', 0, 'intval');
        $order = M('ClientOrder')->where(['id' => $id, 'status' => 1])->find();
        if( empty($order) ) {
            $this->error('订单不存在');
        }
        if( !($order['exam_status'] == 1 && $order['express_status'] == 0) ) {
            $this->error('当前订单不是待发货状态');
        }
        $order['package_type_name'] = $order['package_type'] == 1 ? '文件' : '包裹';
        $order['status_str'] = _order_status($order);
        $order_detail = M('ClientOrderDetail')->where(['order_num' => $order['order_num']])->select();
        if( $order_detail ){
            foreach( $order_detail as $k => $v ) {
                $order_detail[$k]['single_declared'] = sprintf('%.2f', $v['single_declared']);
                $order_detail[$k]['declared'] = sprintf('%.2f', $v['declared']);
            }
        }

        $express_type = M('ExpressType')->where(1)->select();

        $this->assign('order', $order);
        $this->assign('order_detail', $order_detail);
        $this->assign('express_type', $express_type);

        $this->type = '订单装箱发货';
        $this->display();
    }

    public function doDelivery() {
        if( $this->hkwcd_admin['order_express'] != 1 && $this->hkwcd_admin['order_express'] != 1 ) {
            $this->error('您没有订单发货的权限');
        }
        $id = I('id', 0, 'intval');
        $order = M('ClientOrder')->where(['id' => $id, 'status' => 1])->find();
        if( empty($order) ) {
            $this->error('订单不存在');
        }
        if( !($order['pay_status'] == 1) ) {
            $this->error('当前订单未付款，无法编辑发货信息');
        }
//        var_dump($_POST);exit;
        $express_type_id = I('express_type_id', 0, 'intval');
        $express_order_num = I('express_order_num', '', 'trim');
        $customs_broker = I('customs_broker', '', 'trim');
        $exit_end = I('exit_end', '', 'trim');
        $invoice_num = I('invoice_num', '', 'trim');
        $obl = I('obl', '', 'trim');
        $awb = I('awb', '', 'trim');
        $delivery = I('delivery', 0, 'intval');
        $delivery = $delivery == 1 ? 1 : 0;
        $specifications = $_POST['specifications'];

        $express_type_exists = M('ExpressType')->where(['id' => $express_type_id])->find();
        if( empty($express_type_exists) ) {
            $this->error('请选择下家');
        }
        if( empty($express_order_num) ) {
            $this->error('请输入转运单号');
        }

        if( empty($customs_broker) ) {
            $this->error('请输入报关员');
        }

        if( empty($exit_end) ) {
            $this->error('请输入出口端');
        }

        if( empty($invoice_num) ) {
            $this->error('请输入发票号码');
        }

        if( $order['express_type'] == 'air' ) {
            if( empty($awb) ) {
                $this->error('请输入空运提单号');
            }
        }

        if( $order['express_type'] == 'sea shipping' ) {
            if( empty($obl) ) {
                $this->error('请输入海运提单号');
            }
        }

        if( !is_array($specifications) ) {
            $this->error('参数错误');
        }
        foreach( $specifications as $s ) {
            if( !is_array($s) ) {
                $this->error('参数错误');
            }
            if( !isset($s['real_weight']) || floatval($s['real_weight']) < 0 ) {
                $this->error('请输入重量');
            }
            if( !isset($s['real_length']) || floatval($s['real_length']) < 0 ) {
                $this->error('请输入长度');
            }
            if( !isset($s['real_width']) || floatval($s['real_width']) < 0 ) {
                $this->error('请输入宽度');
            }
            if( !isset($s['real_height']) || floatval($s['real_height']) < 0 ) {
                $this->error('请输入高度');
            }
            if( !isset($s['real_count']) || floatval($s['real_count']) < 0 ) {
                $this->error('请输入箱数');
            }
        }
        $data = [
            'express_type_id' => $express_type_exists['id'],
            'express_type' => $express_type_exists['type'],
            'express_type_name' => $express_type_exists['name'],
            'express_order_num' => $express_order_num,
            'customs_broker' => $customs_broker,
            'exit_end' => $exit_end,
            'invoice_num' => $invoice_num,
            'awb' => $awb,
            'obl' => $obl,
        ];
        if( $delivery == 1 ) {
            $data['express_status'] = 1;
            $data['express_time'] = date('Y-m-d H:i:s', time());
            $msg = '发货成功';
            $log_content = '发货'.$express_type_exists['name'];
        } else {
            $msg = '装箱信息已保存';
            $log_content = '编辑装箱信息';
        }
        //事务开始
        $model = new Model;
        $transaction = true;
        $model->startTrans();

        $result = M('ClientOrder')->where(['id' => $id])->save($data);
        if( !is_numeric($result) ) {
            $transaction = false;
        }
        $order_data = [
            'total_weight' => 0,
            'total_rate' => 0,
            'delivery_weight' => 0,
            'total_count' => 0,
        ];

        if( $transaction ) {
            foreach( $specifications as $k => $v ) {
                $detail_data = [
                    'real_weight' => floatval($v['real_weight']),
                    'real_length' => floatval($v['real_length']),
                    'real_width' => floatval($v['real_width']),
                    'real_height' => floatval($v['real_height']),
                    'real_count' => intval($v['real_count']),
                ];
                $order_data['total_weight'] += floatval($v['real_weight']) * intval($v['real_count']);
                $order_data['total_rate'] += floatval($v['real_length']) * floatval($v['real_width']) * floatval($v['real_height']) / 5000 * intval($v['real_count']);
                //算错了。
                $order_data['delivery_weight'] += $order_data['total_weight'] > $order_data['total_rate'] ? $order_data['total_weight'] : $order_data['total_rate'];
                $order_data['total_count'] += intval($v['real_count']);

                $result = M('ClientOrderSpecifications')->where(['id' => intval($k), 'order_num' => $order['order_num']])->save($detail_data);
                if( !is_numeric($result) ) {
                    $transaction = false;
                    break;
                }
            }
        }

        if( $transaction ) {
            $result = M('ClientOrder')->where(['id' => $id])->save($order_data);
            if( !is_numeric($result) ) {
                $transaction = false;
            }
        }

        if( $transaction ) {
            $model->commit();
            //插入操作日志
            $log_data = [
                'order_num' => $order['order_num'],
                'order_id' => $order['id'],
                'operator_id' => session('yang_adm_user_id'),
                'type' => 2,
                'content' => $log_content,
            ];
            M('ClientOrderLog')->add($log_data);
            $this->success($msg, U('Clientorder/detail', ['id' => $id]));
        } else {
            $model->rollback();
            $this->error('系统繁忙，请稍后重试');
        }
    }

    public function invoice() {
        $id = I('id', 0, 'intval');
        $order = M('ClientOrder')->where(['id' => $id, 'status' => 1])->find();
        if( empty($order) ) {
            $this->error('订单不存在');
        }
        if( !($order['express_status'] == 1) ) {
            $this->error('当前订单不能打印商业发票');
        }

        $order['express_time'] = date('Y-m-d', strtotime($order['express_time']));
        $country = M('Country')->where(['id' => $order['receive_country_id']])->find();
        $order['country_name'] = $country['name'];
        $order['country_ename'] = $country['ename'];

        $order_detail = M('ClientOrderDetail')->where(['order_num' => $order['order_num']])->select();
//        $order_specifications = M('ClientOrderSpecifications')->where(['order_num' => $order['order_num']])->select();
//        $order['total_box_num'] = 0;
//        $order['total_weight'] = 0;
        $order['total_declared'] = 0;
        $order_detail_remain =  [];
        if( $order_detail ) {
            foreach( $order_detail as $k => $v ) {
                $order_detail[$k]['declared'] = $v['single_declared'] * $v['count'];
//                $order['total_box_num'] += $v['box'];
//                $order['total_weight'] += $v['cubic_of_volume'] > $v['weighting_weight'] ? $v['cubic_of_volume'] : $v['weighting_weight'];
                $order['total_declared'] += $order_detail[$k]['declared'];
                $order_detail[$k]['declared'] = sprintf('%.2f', $order_detail[$k]['declared']);
                $order_detail[$k]['single_declared'] = sprintf('%.2f', $order_detail[$k]['single_declared']);
            }
        }
        $order['total_declared'] = sprintf('%.2f', $order['total_declared']);
        for( $i = 0; $i < 4 - count($order_detail); $i++ ) {
            $order_detail_remain[] = [];
        }

        $order_specifications = M('ClientOrderSpecifications')
            ->alias('s')
            ->field('s.*, m.detail_id,m.number, d.product_name, d.en_product_name, d.unit, d.origin, d.goods_code')
            ->join('left join hx_client_order_map as m on m.specifications_id = s.id')
            ->join('left join hx_client_order_detail as d on d.id = m.detail_id')
            ->where(['s.order_num' => $order['order_num']])
            ->select();
        if( $order_specifications ) {
            $temp = [];
            foreach( $order_specifications as $k => $v ) {
                if( !isset($temp['item-'.$v['id']]) ) {
                    $temp['item-'.$v['id']] = $v;
                }
                $temp['item-'.$v['id']]['detail'][] = [
                    'product_name' => $v['product_name'],
                    'en_product_name' => $v['en_product_name'],
                    'unit' => $v['unit'],
                    'number' => $v['number'],
                    'origin' => $v['origin'],
                    'goods_code' => $v['goods_code'],
                ];
            }
            $order_specifications = $temp;
        }
        $order['specifications_total_weight'] = 0;
        $order['specifications_total_rate'] = 0;
        $order['specifications_calculate_weight'] = 0;
        $order['specifications_total_count'] = 0;
        if( $order_specifications ) {
            $start = 1;
            $real_start = 1;
            foreach( $order_specifications as $k => $v ) {
                $end = $start + $v['count'] - 1;
                $order_specifications[$k]['no'] = $start.'-'.$end;
                $order_specifications[$k]['weight'] = sprintf('%.2f', $v['weight']);
                $order_specifications[$k]['length'] = sprintf('%.2f', $v['length']);
                $order_specifications[$k]['width'] = sprintf('%.2f', $v['width']);
                $order_specifications[$k]['height'] = sprintf('%.2f', $v['height']);
                $order_specifications[$k]['rate'] = ($v['height'] * $v['length'] * $v['width'] / 5000);
                $order_specifications[$k]['calculate_weight'] = $v['weight'] > $order_specifications[$k]['rate'] ? $v['weight'] : $order_specifications[$k]['rate'];
                $order['specifications_total_weight'] += $v['weight'] * $v['count'];
                $order['specifications_total_rate'] += $order_specifications[$k]['rate'] * $v['count'];
                $order['specifications_total_count'] += $v['count'];
                $order['specifications_calculate_weight'] += $order_specifications[$k]['calculate_weight'] * $v['count'];
                $order_specifications[$k]['rowspan'] = count($v['detail']);
                $start = $end + 1;
                //real
                $real_end = $real_start + $v['real_count'] - 1;
                $order_specifications[$k]['real_no'] = $real_start.'-'.$real_end;
                $order_specifications[$k]['real_weight'] = sprintf('%.2f', $v['real_weight']);
                $order_specifications[$k]['real_length'] = sprintf('%.2f', $v['real_length']);
                $order_specifications[$k]['real_width'] = sprintf('%.2f', $v['real_width']);
                $order_specifications[$k]['real_height'] = sprintf('%.2f', $v['real_height']);
                $order_specifications[$k]['real_rate'] = ($v['real_height'] * $v['real_length'] * $v['real_width'] / 5000);
                $order_specifications[$k]['real_calculate_weight'] = $v['real_weight'] > $order_specifications[$k]['real_rate'] ? $v['real_weight'] : $order_specifications[$k]['real_rate'];
                $order['real_specifications_total_weight'] += $v['real_weight'] * $v['real_count'];
                $order['real_specifications_total_rate'] += $order_specifications[$k]['real_rate'] * $v['real_count'];
                $order['real_specifications_total_count'] += $v['real_count'];
                $order['real_specifications_calculate_weight'] += $order_specifications[$k]['real_calculate_weight'] * $v['real_count'];
                $order_specifications[$k]['rowspan'] = count($v['detail']);
                $real_start = $real_end + 1;
            }
        }
        $order["mode_of_transportation"] = str_replace("From ", "From: ", $order["mode_of_transportation"]);
        $order["mode_of_transportation"] = str_replace("to ", "To: ", $order["mode_of_transportation"]);
        $client = M("Client")->find($order["client_id"]);

        $this->assign("client", $client);
        $this->assign("is_client", 0);
        $this->assign('order', $order);
        $this->assign('order_detail', $order_detail);
        $this->assign('order_detail_remain', $order_detail_remain);
        $this->type = '商业发票';
        $this->display();
    }

    public function packing() {
        $id = I('id', 0, 'intval');
        $order = M('ClientOrder')->where(['id' => $id, 'status' => 1])->find();
        if( empty($order) ) {
            $this->error('订单不存在');
        }
        if( !($order['express_status'] == 1) ) {
            $this->error('当前订单不能打印装箱单');
        }
        $order['express_time'] = date('Y-m-d', strtotime($order['express_time']));
        $country = M('Country')->where(['id' => $order['receive_country_id']])->find();
        $order['country_name'] = $country['name'];
        $order['country_ename'] = $country['ename'];


        $order_detail_remain =  [];
        $order_specifications = M('ClientOrderSpecifications')
            ->alias('s')
            ->field('s.*, m.detail_id,m.number, d.product_name, d.en_product_name, d.unit, d.origin, d.goods_code')
            ->join('left join hx_client_order_map as m on m.specifications_id = s.id')
            ->join('left join hx_client_order_detail as d on d.id = m.detail_id')
            ->where(['s.order_num' => $order['order_num']])
            ->select();
        if( $order_specifications ) {
            $temp = [];
            foreach( $order_specifications as $k => $v ) {
                if( !isset($temp['item-'.$v['id']]) ) {
                    $temp['item-'.$v['id']] = $v;
                }
                $temp['item-'.$v['id']]['detail'][] = [
                    'product_name' => $v['product_name'],
                    'en_product_name' => $v['en_product_name'],
                    'unit' => $v['unit'],
                    'number' => $v['number'],
                    'origin' => $v['origin'],
                    'goods_code' => $v['goods_code'],
                ];
            }
            $order_specifications = $temp;
        }
        $order['specifications_total_weight'] = 0;
        $order['specifications_total_rate'] = 0;
        $order['specifications_calculate_weight'] = 0;
        $order['specifications_total_count'] = 0;
        if( $order_specifications ) {
            $start = 1;
            $real_start = 1;
            foreach( $order_specifications as $k => $v ) {
                $end = $start + $v['count'] - 1;
                $order_specifications[$k]['no'] = $start.'-'.$end;
                $order_specifications[$k]['weight'] = sprintf('%.2f', $v['weight']);
                $order_specifications[$k]['length'] = sprintf('%.2f', $v['length']);
                $order_specifications[$k]['width'] = sprintf('%.2f', $v['width']);
                $order_specifications[$k]['height'] = sprintf('%.2f', $v['height']);
                $order_specifications[$k]['rate'] = ($v['height'] * $v['length'] * $v['width'] / 5000);
                $order_specifications[$k]['calculate_weight'] = $v['weight'] > $order_specifications[$k]['rate'] ? $v['weight'] : $order_specifications[$k]['rate'];
                $order['specifications_total_weight'] += $v['weight'] * $v['count'];
                $order['specifications_total_rate'] += $order_specifications[$k]['rate'] * $v['count'];
                $order['specifications_total_count'] += $v['count'];
                $order['specifications_calculate_weight'] += $order_specifications[$k]['calculate_weight'] * $v['count'];
                $order_specifications[$k]['rowspan'] = count($v['detail']);
                $start = $end + 1;
                //real
                $real_end = $real_start + $v['real_count'] - 1;
                $order_specifications[$k]['real_no'] = $real_start.'-'.$real_end;
                $order_specifications[$k]['real_weight'] = sprintf('%.2f', $v['real_weight']);
                $order_specifications[$k]['real_length'] = sprintf('%.2f', $v['real_length']);
                $order_specifications[$k]['real_width'] = sprintf('%.2f', $v['real_width']);
                $order_specifications[$k]['real_height'] = sprintf('%.2f', $v['real_height']);
                $order_specifications[$k]['real_rate'] = ($v['real_height'] * $v['real_length'] * $v['real_width'] / 5000);
                $order_specifications[$k]['real_calculate_weight'] = $v['real_weight'] > $order_specifications[$k]['real_rate'] ? $v['real_weight'] : $order_specifications[$k]['real_rate'];
                $order['real_specifications_total_weight'] += $v['real_weight'] * $v['real_count'];
                $order['real_specifications_total_rate'] += $order_specifications[$k]['real_rate'] * $v['real_count'];
                $order['real_specifications_total_count'] += $v['real_count'];
                $order['real_specifications_calculate_weight'] += $order_specifications[$k]['real_calculate_weight'] * $v['real_count'];
                $order_specifications[$k]['rowspan'] = count($v['detail']);
                $real_start = $real_end + 1;
            }
        }
        for( $i = 0; $i < 4 - count($order_specifications); $i++ ) {
            $order_detail_remain[] = [];
        }
        $order["mode_of_transportation"] = str_replace("From ", "From: ", $order["mode_of_transportation"]);
        $order["mode_of_transportation"] = str_replace("to ", "To: ", $order["mode_of_transportation"]);
//        var_dump($order_specifications['detail']);exit;
        $this->assign("is_client", 0);

        $this->assign('order', $order);
        $this->assign('order_specifications', $order_specifications);
        $this->assign('order_detail_remain', $order_detail_remain);
        $this->type = '装箱单';
        $this->display();
    }

    public function transfer() {
        $id = I('id', 0, 'intval');
        $order = M('ClientOrder')->where(['id' => $id, 'status' => 1])->find();
        if( empty($order) ) {
            $this->error('订单不存在');
        }
        if( !($order['express_status'] == 1) ) {
            $this->error('当前订单不能打印交接清单');
        }

        $order_fee = M('ClientOrderFee')->where(['order_id' => $order['id']])->find();

        $today_time = strtotime(date('Y-m-d', strtotime($order['add_time'])));
        $tomorrow_time = $today_time + 3600 * 24;
        $start_time = date('Y-m-d H:i:s', $today_time);
        $end_time = date('Y-m-d H:i:s', $tomorrow_time);
        $map = [
            "client_id" => $order['client_id'],
            'add_time' => ['between', [$start_time, $end_time]],
            'express_status' => 1,
        ];

        $client = M('Client')->where(['id' => $order['client_id']])->find();

        $order_package_total_count = 0;     //包裹数量
        $order_document_total_count = 0;    //文件数量
        $order_total_count = 0;             //总箱数
        $order_num_list = [];
        $order_list = M('ClientOrder')->where($map)->select();
        if( $order_list ) {
            foreach( $order_list as $k => $v ) {
                if( $v['package_type'] == 1 ) {
                    $order_document_total_count++;
                }
                if( $v['package_type'] == 2 ) {
                    $order_package_total_count++;
                }
                $order_list[$k]["total_count"] = M("ClientOrderSpecifications")
                    ->where(["order_num" => $v["order_num"]])
                    ->sum("real_count");
                $s_list = M("ClientOrderSpecifications")->where(["order_num" => $v["order_num"]])
                    ->select();
                $temp_weight = 0;
                $temp_rate = 0;
                foreach( $s_list as $s ) {
                    $temp_weight += $s["real_weight"] * $s["real_count"];
                    $temp_rate += sprintf("%.2f", ($s["real_length"] * $s["real_width"] * $s["real_height"] * $s["real_count"] / 5000));
                }
                $order_list[$k]["total_weight"] = $temp_weight;
                $order_list[$k]["total_rate"] = $temp_rate;
                $order_list[$k]["delivery_weight"] = $temp_weight > $temp_rate ? $temp_weight : $temp_rate;

                $order_num_list[] = $v["order_num"];
            }
            if( $order_num_list ) {
                $order_total_count = M("ClientOrderSpecifications")
                    ->where(["order_num" => ["IN", $order_num_list]])
                    ->sum("real_count");
            }
        }

        $order_count = count($order_list);
        $detail_end = $order_count > 3 ? $order_count : 3;

        $this->assign('order_fee', $order_fee);
        $this->assign('client', $client);
        $this->assign('order_list', $order_list);
        $this->assign('order_count', $order_count);
        $this->assign('detail_start', $order_count);
        $this->assign('detail_end', $detail_end);
        $this->assign('order_total_count', $order_total_count);
        $this->assign('order_package_total_count', $order_package_total_count);
        $this->assign('order_document_total_count', $order_document_total_count);
        $this->display();
    }

    public function express() {
        $id = I('id', 0, 'intval');
        $order = M('ClientOrder')->where(['id' => $id, 'status' => 1])->find();
        if( empty($order) ) {
            $this->error('订单不存在');
        }
        if( !($order['express_status'] == 1) ) {
            $this->error('当前订单不能打印快递单');
        }

        if( $order['receive_mobile'] && $order['receive_phone'] ) {
            $order['receive_contact'] = $order['receive_mobile'].'、'.$order['receive_phone'];
        } else {
            if( $order['receive_mobile'] ) {
                $order['receive_contact'] = $order['receive_mobile'];
            }
            if( $order['receive_phone'] ) {
                $order['receive_contact'] = $order['receive_phone'];
            }
        }
        $order_detail = M('ClientOrderDetail')
            ->where(['order_num' => $order['order_num']])
            ->select();
        $order['detail_declared_total'] = 0;
        if( $order_detail ){
            foreach( $order_detail as $k => $v ) {
                $order_detail[$k]['single_declared'] = sprintf('%.2f', $v['single_declared']);
                $order_detail[$k]['declared'] = sprintf('%.2f', $v['single_declared'] * $v['count']);
                $order['detail_declared_total'] += $order_detail[$k]['declared'];
            }
        }
        $order['detail_declared_total'] = sprintf('%.2f', $order['detail_declared_total']);

        $this->assign('detail_start', count($order_detail));
        $this->assign('detail_end', 10);
        $this->assign('order_detail', $order_detail);
        $this->assign('order', $order);
        $this->display();
    }

    public function createUPCA() {
        vendor('Barcode.class.BCGFontFile');
        vendor('Barcode.class.BCGColor');
        vendor('Barcode.class.BCGDrawing');
        vendor('Barcode.class.BCGcode39');
// Loading Font
        $font = new BCGFontFile(THINK_PATH.'/Extend/Vendor/Barcode/font/Arial.ttf', 18);

// Don't forget to sanitize user inputs
        $text = isset($_GET['order_num']) ? $_GET['order_num'] : 'nothing';

// The arguments are R, G, B for color.
        $color_black = new BCGColor(0, 0, 0);
        $color_white = new BCGColor(255, 255, 255);

        $drawException = null;
        try {
            $code = new BCGcode39();
            $code->setScale(2); // Resolution
            $code->setThickness(30); // Thickness
            $code->setForegroundColor($color_black); // Color of bars
            $code->setBackgroundColor($color_white); // Color of spaces
            $code->setFont($font); // Font (or 0)
            $code->parse($text); // Text
        } catch(Exception $exception) {
            $drawException = $exception;
        }

        /* Here is the list of the arguments
        1 - Filename (empty : display on screen)
        2 - Background color */
        $drawing = new BCGDrawing('', $color_white);
        if($drawException) {
            $drawing->drawException($drawException);
        } else {
            $drawing->setBarcode($code);
            $drawing->draw();
        }

// Header that says it is an image (remove it if you save the barcode to a file)
        header('Content-Type: image/png');
        header('Content-Disposition: inline; filename="barcode.png"');

// Draw (or save) the image into PNG format.
        $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);

    }


    public function ajaxEdit() {

        $id = I('order_id');
        $order = M('ClientOrder')->where(['id' => $id,  'status' => 1])->find();
        if( empty($order) ) {
            $this->response['msg'] = '订单不存在';
            echo json_encode($this->response);
            exit;
        }
        if( $order['pay_status'] == 1 ) {
            $this->response['msg'] = '订单已收款，已不能编辑';
            echo json_encode($this->response);
            exit;
        }
//        if( $order['client_status'] != 0 ) {
//            $this->response['msg'] = '当前订单不是可编辑状态';
//            echo json_encode($this->response);
//            exit;
//        }
        $data = $this->_get_order_params();
        $data['client_id'] = $order['client_id'];
        $data = $this->_build_delivery_address($data);
        $data = $this->_build_receive_address($data);
        $data = $this->_build_spare_address($data);

        if( $this->has_error ) {
            $this->response['msg'] = $this->error_msg;
            echo json_encode($this->response);
            exit;
        }

        $data = $this->_build_order_info($data);

        if( $this->has_error ) {
            $this->response['msg'] = $this->error_msg;
            echo json_encode($this->response);
            exit;
        }

        $order_detail = $data['order_detail'];
        unset($data['order_detail']);
        $order_detail = $this->_build_order_detail($order_detail);

        if( $this->has_error ) {
            $this->response['msg'] = $this->error_msg;
            echo json_encode($this->response);
            exit;
        }
        $order_specifications = $data['order_specifications'];
        unset($data['order_specifications']);
        $order_specifications = $this->_build_order_specification($order_specifications);
//        var_dump($order_specifications);exit;

        if( $this->has_error ) {
            $this->response['msg'] = $this->error_msg;
            echo json_encode($this->response);
            exit;
        }

        $msg = "修改订单成功";
        unset($data['commit']);
        $data['client_id'] = $order['client_id'];


        //事务开始
        //修改订单

        $model = new Model();
        $transaction = true;
        $model->startTrans();

        /**
         * 1、更新订单
         * 2、删除原订单详情，删除原订单规格，删除原详情和规格的映射
         * 3、插入新的订单详情
         * 4、插入新的订单规格
         * 5、插入新的详情和规格的映射
         */
        $result = M('ClientOrder')->where(['id' => $id, 'client_id' => $data['client_id']])->save($data);

        if( !is_numeric($result) ) {
            $transaction = false;
        }

        $old_specifications_list = [];
        if( $transaction ) {
            $old_specifications = M("ClientOrderSpecifications")
                ->field("id")
                ->where(["order_id" => $id])
                ->select();
            if( $old_specifications ) {
                foreach( $old_specifications as $v ) {
                    $old_specifications_list[] = $v['id'];
                }
            }
        }

        if( $transaction ) {
            $truncate_order_detail = M("ClientOrderDetail")
                ->where(['order_id' => $id])
                ->delete();
            if( !$truncate_order_detail ) {
                $transaction = false;
            }
        }

        if( $transaction ) {
            $truncate_order_specifications = M("ClientOrderSpecifications")
                ->where(['order_id' => $id])
                ->delete();
            if( !$truncate_order_specifications ) {
                $transaction = false;
            }
        }



        if( $transaction ) {
            $truncate_map = M("ClientOrderMap")
                ->where("specifications_id in (".implode(',', $old_specifications_list).")")
                ->delete();
            if( !is_numeric($truncate_map) ) {
                $transaction = false;
            }
        }


        if( $transaction ) {
            foreach( $order_detail as $k => $v ) {
                $v['order_num'] = $order['order_num'];
                $v['order_id'] = $id;
                $temp_result = M('ClientOrderDetail')->add($v);
                if( !$temp_result ) {
                    $transaction = false;
                    break;
                } else {
                    $order_detail[$k]['id'] = M('ClientOrderDetail')->getLastInsID();
                }
            }
        }



        if( $transaction ) {
            foreach( $order_specifications as $k => $v ) {
                unset($v['id']);
                $v['order_num'] = $order['order_num'];
                $v['order_id'] = $id;
                $temp_result = M('ClientOrderSpecifications')->add($v);
                if( !$temp_result ) {
                    $transaction = false;
                    break;
                } else {
                    $order_specifications[$k]['id'] = M('ClientOrderSpecifications')->getLastInsID();
                }
            }
        }

//        $this->response['transaction'] = $transaction;
//        $this->response['msg'] = M("ClientOrderSpecifications")->getLastSql();
//        $model->rollback();
//        var_dump($order_specifications);
//        echo json_encode($this->response);exit;

        if( $transaction ) {
            foreach( $order_specifications as $k => $v ) {
                foreach( $v['cargo'] as $d ) {
//                    if( empty($order_detail[$d['product_index']]['id']) ) {
//                        continue;
//                    }
                    $temp = [
                        'specifications_id' => $v['id'],
                        'detail_id' => $order_detail[$d['product_index']]['id'],
//                        'number' => $v['detail_number'][$d],
                        'number' => $d['product_count'],
                    ];
                    $temp_result = M('ClientOrderMap')->add($temp);
                    if( !$temp_result ) {
                        $transaction = false;
                        break;
                    }
                }
                if( !$transaction ) {
                    break;
                }
            }
        }


        if( $transaction ) {
            $model->commit();
            //插入操作日志
            $log_data = [
                'order_num' => $order['order_num'],
                'order_id' => $order['id'],
                'operator_id' => session('yang_adm_user_id'),
                'type' => 2,
                'content' => $msg,
            ];
            M('ClientOrderLog')->add($log_data);

            $this->response['code'] = 1;
            $this->response['msg'] = $msg;
            $this->response['url'] = U('/Manage/Clientorder/detail', ["id" => $id]);
        } else {
            $model->rollback();
            $this->response['msg'] = '系统繁忙，请稍后重试';
//            $this->response['msg'] = $model->getDbError();
        }
        echo json_encode($this->response);
        exit;

    }

    public function ajaxAddDetail() {

        $order_id = I('order_id');

        $order = M('ClientOrder')->where(['id' => $order_id, 'status' => 1])->find();
        if( empty($order) ) {
            $this->response['msg'] = '订单不存在';
            echo json_encode($this->response);
            exit;
        }
//        if( $order['client_status'] != 0 ) {
//            $this->response['msg'] = '当前订单不是可编辑状态';
//            echo json_encode($this->response);
//            exit;
//        }
        $has_error = false;
        $error_msg = '';

        $product_name = trim(I('product_name'));
        $en_product_name = trim(I('en_product_name'));
        $goods_code = trim(I('goods_code'));
        $count = intval(I('count'));
        $single_declared = floatval(I('single_declared'));
        $unit = trim(I('unit'));
        $declared = floatval(I('declared'));
        $origin = trim(I('origin'));

        if( $product_name == '' ) {
            $has_error = true;
            $error_msg = $error_msg ? $error_msg.'<br />请输入中文品名' : '请输入品名';
        }

        if( $en_product_name == '' ) {
            $has_error = true;
            $error_msg = $error_msg ? $error_msg.'<br />请输入英文品名' : '请输入英文品名';
        }

        if( $goods_code == '' ) {
            $has_error = true;
            $error_msg = $error_msg ? $error_msg.'<br />请输入商品编码' : '请输入商品编码';
        }

        if( $count <= 0 ) {
            $has_error = true;
            $error_msg = $error_msg ? $error_msg.'<br />请输入数量' : '请输入数量';
        }

        if( $single_declared <= 0 ) {
            $has_error = true;
            $error_msg = $error_msg ? $error_msg.'<br />请输入单件申报价值' : '请输入单件申报价值';
        }

        if( $origin == '' ) {
            $has_error = true;
            $error_msg = $error_msg ? $error_msg.'<br />请输入原产地' : '请输入原产地';
        }

        if( $has_error ) {
            $this->response['msg'] = $error_msg;
            echo json_encode($this->response);
            exit;
        }
        $data = [
            'product_name' => $product_name,
            'en_product_name' => $en_product_name,
            'goods_code' => $goods_code,
            'count' => $count,
            'unit' => $unit,
            'single_declared' => $single_declared,
            'origin' => $origin,
            'order_id' => $order_id,
            'order_num' => $order['order_num'],
        ];
        $result = M('ClientOrderDetail')->add($data);
        if( $result ) {
            $data['id'] = M('ClientOrderDetail')->getLastInsID();
            //插入操作日志
            $log_data = [
                'order_num' => $order['order_num'],
                'order_id' => $order['id'],
                'operator_id' => session(C('USER_AUTH_KEY')),
                'type' => 2,
                'content' => '添加商品信息成功',
            ];
            M('ClientOrderLog')->add($log_data);

            $this->response['code'] = 1;
            $this->response['msg'] = '添加商品信息成功';
            $this->response['url'] = U('Order/edit', ['id' => $order_id]);
            $this->response['data'] = $data;
        } else {
            $this->response['msg'] = '系统繁忙，请稍后重试';
        }
        echo json_encode($this->response);
        exit;
    }

    public function ajaxEditDetail() {
//        $client_id = session('hkwcd_user.user_id');
//        $client = M('Client')->where(['status' => 1, 'id' => $client_id])->find();

        $detail_id = I('id');
        $order_id = I('order_id');

        $order = M('ClientOrder')->where(['id' => $order_id, 'status' => 1])->find();
        if( empty($order) ) {
            $this->response['msg'] = '订单不存在';
            echo json_encode($this->response);
            exit;
        }
//        if( $order['client_status'] != 0 ) {
//            $this->response['msg'] = '当前订单不是可编辑状态';
//            echo json_encode($this->response);
//            exit;
//        }
        $has_error = false;
        $error_msg = '';

        $product_name = trim(I('product_name'));
        $en_product_name = trim(I('en_product_name'));
        $goods_code = trim(I('goods_code'));
        $count = intval(I('count'));
        $single_declared = floatval(I('single_declared'));
        $unit = trim(I('unit'));
        $declared = floatval(I('declared'));
        $origin = trim(I('origin'));

        if( $product_name == '' ) {
            $has_error = true;
            $error_msg = $error_msg ? $error_msg.'<br />请输入中文品名' : '请输入品名';
        }

        if( $en_product_name == '' ) {
            $has_error = true;
            $error_msg = $error_msg ? $error_msg.'<br />请输入英文品名' : '请输入英文品名';
        }

        if( $goods_code == '' ) {
            $has_error = true;
            $error_msg = $error_msg ? $error_msg.'<br />请输入商品编码' : '请输入商品编码';
        }

        if( $count <= 0 ) {
            $has_error = true;
            $error_msg = $error_msg ? $error_msg.'<br />请输入数量' : '请输入数量';
        }

        if( $single_declared <= 0 ) {
            $has_error = true;
            $error_msg = $error_msg ? $error_msg.'<br />请输入单件申报价值' : '请输入单件申报价值';
        }

        if( $origin == '' ) {
            $has_error = true;
            $error_msg = $error_msg ? $error_msg.'<br />请输入原产地' : '请输入原产地';
        }

        if( $has_error ) {
            $this->response['msg'] = $error_msg;
            echo json_encode($this->response);
            exit;
        }
        $data = [
            'product_name' => $product_name,
            'en_product_name' => $en_product_name,
            'goods_code' => $goods_code,
            'count' => $count,
            'unit' => $unit,
            'single_declared' => $single_declared,
            'declared' => $declared,
            'origin' => $origin,
        ];

        $result = M('ClientOrderDetail')->where(['id' => $detail_id, 'order_id' => $order_id])->save($data);
        if( is_numeric($result) ) {
            $data['id'] = $detail_id;
            $data['order_id'] = $order_id;
            $data['order_num'] = $order['order_num'];
            //插入操作日志
            $log_data = [
                'order_num' => $order['order_num'],
                'order_id' => $order['id'],
                'operator_id' => session("yang_adm_user_id"),
                'type' => 2,
                'content' => '编辑商品信息成功',
            ];
            M('ClientOrderLog')->add($log_data);
            $this->response['code'] = 1;
            $this->response['msg'] = '编辑商品信息成功';
            $this->response['url'] = U('Order/edit', ['id' => $order_id]);
            $this->response['data'] = $data;
        } else {
            $this->response['msg'] = '系统繁忙，请稍后重试';
        }
        echo json_encode($this->response);
        exit;

    }

    public function ajaxDeleteDetail() {
//        $client_id = session('hkwcd_user.user_id');
//        $client = M('Client')->where(['status' => 1, 'id' => $client_id])->find();

        $detail_id = I('id');
        $order_id = I('order_id');
        $index = I('index');

        $order = M('ClientOrder')->where(['id' => $order_id,  'status' => 1])->find();
        if( empty($order) ) {
            $this->response['msg'] = '订单不存在';
            echo json_encode($this->response);
            exit;
        }
//        if( $order['client_status'] != 0 ) {
//            $this->response['msg'] = '当前订单不是可编辑状态';
//            echo json_encode($this->response);
//            exit;
//        }
        $temp = M('ClientOrderDetail')->where(['order_id' => $order_id])->select();
        $remain_count = count($temp);
        if( $remain_count <= 1 ) {
            $this->response['msg'] = '至少保留一项商品';
            echo json_encode($this->response);
            exit;
        }
        $exists = M('ClientOrderMap')->where(['detail_id' => $detail_id])->find();
        if( $exists ) {
            $this->response['msg'] = '该商品已被使用，无法删除';
            echo json_encode($this->response);
            exit;
        }

        $result = M('ClientOrderDetail')->where(['order_id' => $order_id, 'id' => $detail_id])->delete();
        if( $result ) {
            //插入操作日志
            $log_data = [
                'order_num' => $order['order_num'],
                'order_id' => $order['id'],
                'operator_id' => session('yang_adm_user_id'),
                'type' => 2,
                'content' => '删除商品信息成功',
            ];
            M('ClientOrderLog')->add($log_data);

            $this->response['code'] = 1;
            $this->response['msg'] = '删除商品信息成功';
            $this->response['url'] = U('Order/edit', ['id' => $order_id]);
            $this->response['data']['index'] = $index;
        } else {
            $this->response['msg'] = '系统繁忙，请稍后重试';
        }
        echo json_encode($this->response);
        exit;
    }

    public function ajaxAddSpecifications() {
//        $client_id = session('hkwcd_user.user_id');
//        $client = M('Client')->where(['status' => 1, 'id' => $client_id])->find();

        $order_id = I('order_id');

        $order = M('ClientOrder')->where(['id' => $order_id,  'status' => 1])->find();
        if( empty($order) ) {
            $this->response['msg'] = '订单不存在';
            echo json_encode($this->response);
            exit;
        }
//        if( $order['client_status'] != 0 ) {
//            $this->response['msg'] = '当前订单不是可编辑状态';
//            echo json_encode($this->response);
//            exit;
//        }

        $weight = floatval(I('weight'));
        $length = floatval(I('length'));
        $width = floatval(I('width'));
        $height = floatval(I('height'));
        $count = floatval(I('count'));
        $remark = trim(I('remark'));
        $detail = $_POST['detail'];

        $has_error = false;
        $error_msg = '';

        if( $weight <= 0 ) {
            $has_error = true;
            $error_msg = $error_msg ? $error_msg.'<br />请输入重量' : '请输入重量';
        }

        if( $length <= 0 ) {
            $has_error = true;
            $error_msg = $error_msg ? $error_msg.'<br />请输入长度' : '请输入长度';
        }

        if( $width <= 0 ) {
            $has_error = true;
            $error_msg = $error_msg ? $error_msg.'<br />请输入宽度' : '请输入宽度';
        }

        if( $height <= 0 ) {
            $has_error = true;
            $error_msg = $error_msg ? $error_msg.'<br />请输入高度' : '请输入高度';
        }

        if( !is_array($detail) ) {
            $has_error = true;
            $error_msg = $error_msg ? $error_msg.'<br />请输入商品' : '请输入商品';
        } else {
            foreach( $detail as $k => $v ) {
                $v['detail_number'] = intval($v['detail_number']);
                $map = ['id' => $v['detail_id'], 'order_id' => $order_id];
                if( !M('ClientOrderDetail')->where($map)->find() || $v['detail_number'] <= 0 ) {
                    $has_error = true;
                    $error_msg = $error_msg ? $error_msg.'<br />请输入商品以及每箱数量' : '请输入商品以及每箱数量';
                    break;
                }
            }
        }


        if( $count <= 0 ) {
            $has_error = true;
            $error_msg = $error_msg ? $error_msg.'<br />请输入箱数' : '请输入箱数';
        }

        if( $has_error ) {
            $this->response['msg'] = $error_msg;
            echo json_encode($this->response);
            exit;
        }

        $data = [
            'weight' => $weight,
            'length' => $length,
            'width' => $width,
            'height' => $height,
            'count' => $count,
            'remark' => $remark,
            'order_id' => $order_id,
            'order_num' => $order['order_num'],
        ];

        $model = new Model;
        $model->startTrans();
        $transaction = true;

        $result = M('ClientOrderSpecifications')->add($data);
        if( !$result ) {
            $transaction = false;
        } else {
            $sid = M('ClientOrderSpecifications')->getLastInsID();
        }

        if( $transaction ) {
            foreach( $detail as $k => $v ) {
                $map_data = [
                    'detail_id' => $v['detail_id'],
                    'specifications_id' => $sid,
                    'number' => $v['detail_number'],
                ];
                $result = M('ClientOrderMap')->add($map_data);
                if( !$result ) {
                    $transaction = false;
                    break;
                }
            }
        }

        if( $transaction ) {
            $model->commit();
            $order_specifications = M('ClientOrderSpecifications')
                ->alias('s')
                ->field('s.*, m.detail_id, m.number')
                ->join('inner join hx_client_order_map as m on m.specifications_id = s.id')
                ->where(['s.id' => $sid])
                ->select();
            $key = '';
            if( $order_specifications ) {
                $temp = [];
                foreach( $order_specifications as $k => $v ) {
                    if( !isset($temp['item-'.$v['id']]) ) {
                        $temp['item-'.$v['id']] = $v;
                    }
                    $temp['item-'.$v['id']]['detail'][] = 'item-'.$v['detail_id'];
                    $temp['item-'.$v['id']]['detail_number']['item-'.$v['detail_id']] = $v['number'];
                    $key = 'item-'.$v['id'];
                }
                $order_specifications = $temp;
            }
            //插入操作日志
            $log_data = [
                'order_num' => $order['order_num'],
                'order_id' => $order['id'],
                'operator_id' => session('yang_adm_user_id'),
                'type' => 2,
                'content' => '添加订单规格成功',
            ];
            M('ClientOrderLog')->add($log_data);

            $this->response['code'] = 1;
            $this->response['msg'] = '添加订单规格成功';
            $this->response['url'] = U('Order/edit', ['id' => $order_id]);
            $this->response['data'] = $order_specifications[$key];
            $this->response['key'] = $key;
        } else {
            $model->rollback();
            $this->response['msg'] = '系统繁忙，请稍后重试';
        }
        echo json_encode($this->response);
        exit;
    }

    public function ajaxEditSpecifications() {

        $specifications_id = I('id');
        $order_id = I('order_id');

        $order = M('ClientOrder')->where(['id' => $order_id, 'status' => 1])->find();
        if( empty($order) ) {
            $this->response['msg'] = '订单不存在';
            echo json_encode($this->response);
            exit;
        }
//        if( $order['client_status'] != 0 ) {
//            $this->response['msg'] = '当前订单不是可编辑状态';
//            echo json_encode($this->response);
//            exit;
//        }

        $weight = floatval(I('weight'));
        $length = floatval(I('length'));
        $width = floatval(I('width'));
        $height = floatval(I('height'));
        $count = floatval(I('count'));
        $remark = trim(I('remark'));
        $detail = $_POST['detail'];

        $has_error = false;
        $error_msg = '';

        if( $weight <= 0 ) {
            $has_error = true;
            $error_msg = $error_msg ? $error_msg.'<br />请输入重量' : '请输入重量';
        }

        if( $length <= 0 ) {
            $has_error = true;
            $error_msg = $error_msg ? $error_msg.'<br />请输入长度' : '请输入长度';
        }

        if( $width <= 0 ) {
            $has_error = true;
            $error_msg = $error_msg ? $error_msg.'<br />请输入宽度' : '请输入宽度';
        }

        if( $height <= 0 ) {
            $has_error = true;
            $error_msg = $error_msg ? $error_msg.'<br />请输入高度' : '请输入高度';
        }

        if( !is_array($detail) ) {
            $has_error = true;
            $error_msg = $error_msg ? $error_msg.'<br />请输入商品' : '请输入商品';
        } else {
            foreach( $detail as $k => $v ) {
                $v['detail_number'] = intval($v['detail_number']);
                $map = ['id' => $v['detail_id'], 'order_id' => $order_id];
                if( !M('ClientOrderDetail')->where($map)->find() || $v['detail_number'] <= 0 ) {
                    $has_error = true;
                    $error_msg = $error_msg ? $error_msg.'<br />请输入商品以及每箱数量' : '请输入商品以及每箱数量';
                    break;
                }
            }
        }

        if( $count <= 0 ) {
            $has_error = true;
            $error_msg = $error_msg ? $error_msg.'<br />请输入箱数' : '请输入箱数';
        }

        if( $has_error ) {
            $this->response['msg'] = $error_msg;
            echo json_encode($this->response);
            exit;
        }

        $model = new Model;
        $model->startTrans();
        $transaction = true;

        $data = [
            'weight' => $weight,
            'length' => $length,
            'width' => $width,
            'height' => $height,
            'count' => $count,
            'remark' => $remark,
        ];

        $result = M('ClientOrderSpecifications')->where(['order_id' => $order_id, 'id' => $specifications_id])->save($data);
        if( !is_numeric($result) ) {
            $transaction = false;
        }

        if( $transaction ) {
            $result = M('ClientOrderMap')->where(['specifications_id' => $specifications_id])->delete();
            if( !$result ) {
                $transaction = false;
            }
        }

        if( $transaction ) {
            foreach( $detail as $k => $v ) {
                $map_data = [
                    'detail_id' => $v['detail_id'],
                    'specifications_id' => $specifications_id,
                    'number' => $v['detail_number'],
                ];
                $result = M('ClientOrderMap')->add($map_data);
                if( !$result ) {
                    $transaction = false;
                    break;
                }
            }
        }

        if( $transaction ) {
            $model->commit();

            $order_specifications = M('ClientOrderSpecifications')
                ->alias('s')
                ->field('s.*, m.detail_id, m.number')
                ->join('inner join hx_client_order_map as m on m.specifications_id = s.id')
                ->where(['s.id' => $specifications_id])
                ->select();
            $key = '';
            if( $order_specifications ) {
                $temp = [];
                foreach( $order_specifications as $k => $v ) {
                    if( !isset($temp['item-'.$v['id']]) ) {
                        $temp['item-'.$v['id']] = $v;
                    }
                    $temp['item-'.$v['id']]['detail'][] = 'item-'.$v['detail_id'];
                    $temp['item-'.$v['id']]['detail_number']['item-'.$v['detail_id']] = $v['number'];
                    $key = 'item-'.$v['id'];
                }
                $order_specifications = $temp;
            }

            $data['order_id'] = $order_id;
            $data['order_num'] = $order['order_num'];
            //插入操作日志
            $log_data = [
                'order_num' => $order['order_num'],
                'order_id' => $order['id'],
                'operator_id' => session('yang_adm_user_id'),
                'type' => 2,
                'content' => '编辑订单规格成功',
            ];
            M('ClientOrderLog')->add($log_data);

            $this->response['code'] = 1;
            $this->response['msg'] = '编辑订单规格成功';
            $this->response['url'] = U('Order/edit', ['id' => $order_id]);
            $this->response['data'] = $order_specifications[$key];
            $this->response['key'] = $key;
        } else {
            $model->rollback();
            $this->response['msg'] = '系统繁忙，请稍后重试';
        }
        echo json_encode($this->response);
        exit;
    }

    public function ajaxDeleteSpecifications() {

        $specifications_id = I('id');
        $order_id = I('order_id');
        $index = I('index');

        $order = M('ClientOrder')->where(['id' => $order_id,  'status' => 1])->find();
        if( empty($order) ) {
            $this->response['msg'] = '订单不存在';
            echo json_encode($this->response);
            exit;
        }
//        if( $order['client_status'] != 0 ) {
//            $this->response['msg'] = '当前订单不是可编辑状态';
//            echo json_encode($this->response);
//            exit;
//        }
        $temp = M('ClientOrderSpecifications')->where(['order_id' => $order_id])->select();
        $remain_count = count($temp);
        if( $remain_count <= 1 ) {
            $this->response['msg'] = '至少保留一项订单规格';
            echo json_encode($this->response);
            exit;
        }
        $model = new Model;
        $model->startTrans();
        $transaction = true;

        $result = M('ClientOrderSpecifications')->where(['order_id' => $order_id, 'id' => $specifications_id])->delete();
        if( !$result ) {
            $transaction = false;
        }
        if( $transaction ) {
            $result = M('ClientOrderMap')->where(['specifications_id' => $specifications_id])->delete();
            if( !$result ) {
                $transaction = false;
            }
        }

        if( $transaction ) {
            $model->commit();
            //插入操作日志
            $log_data = [
                'order_num' => $order['order_num'],
                'order_id' => $order['id'],
                'operator_id' => session('yang_adm_user_id'),
                'type' => 2,
                'content' => '删除订单规格成功',
            ];
            M('ClientOrderLog')->add($log_data);

            $this->response['code'] = 1;
            $this->response['msg'] = '删除订单规格成功';
            $this->response['url'] = U('Order/edit', ['id' => $order_id]);
            $this->response['data']['index'] = $index;
        } else {
            $model->rollback();
            $this->response['msg'] = '系统繁忙，请稍后重试';
        }
        echo json_encode($this->response);
        exit;
    }

    public function selfExpress() {
        if( !IS_POST ) {
            exit;
        }
        $order_id = I("order_id", 0, 'intval');
        $order = M('ClientOrder')->where(['id' => $order_id,  'status' => 1])->find();
        if( empty($order) ) {
            $this->response['msg'] = '订单不存在';
            echo json_encode($this->response);
            exit;
        }
        if( $order['express_status'] != 1 ) {
            $this->response['msg'] = '未发货';
            echo json_encode($this->response);
            exit;
        }
        $map = ["id" => $order_id];
        $data = ["self_express" => 1];
        $result = M("ClientOrder")->where($map)->save($data);
        if( is_numeric($result) ) {
            $this->response['code'] = 1;
            $this->response['msg'] = "设置成功";

            //插入操作日志
            $log_data = [
                'order_num' => $order['order_num'],
                'order_id' => $order['id'],
                'operator_id' => session('yang_adm_user_id'),
                'type' => 2,
                'content' => '将订单设置为自定义物流信息',
            ];
            M('ClientOrderLog')->add($log_data);

        } else {
            $this->response['msg'] = "设置失败";
        }
        echo json_encode($this->response);
        exit;
    }

    public function inputSelfExpress() {

        $order_id = I("id", 0, "intval");
        $order = M('ClientOrder')->where(['id' => $order_id,  'status' => 1])->find();
        if( empty($order) ) {
            $this->error("订单不存在");
        }

        $order["package_type_name"] = $order['package_type'] == 1 ? "文件" : "包裹";
        $order['status_str'] = _order_status($order);
        $express_detail = M("ClientOrderSelfExpress")->where(['order_id' => $order_id])->select();

        $this->assign("express_detail", $express_detail);
        $this->assign("order", $order);
        $this->display("inputselfexpress");
    }

    public function addExpress() {
        if( IS_GET ) {

            $order_id = I("order_id", 0, "intval");

            $this->type = '添加运单详细资料';
            $this->progress = getArrayOfItem('progress');
            $this->remark = getArrayOfItem('remark');
            $this->fkid = $order_id;
            $this->display("addexpress");
        } else {
            $order_id = I("fkid");
            $time = I("sj");
            $country = I("gj");
            $city = I("chs");
            $status = I("hwz");
            $remark = I("info");

            $order = M('ClientOrder')->where(['id' => $order_id,  'status' => 1])->find();
            if( empty($order) ) {
                $this->error("订单不存在");
            }

            if( empty($time) ) {
                $this->error("请选择时间");
            }

            if( empty($country) ) {
                $this->error("请选择国家");
            }

            if( empty($status) ) {
                $this->error("请选择派件状态");
            }

            $data = [
                "time" => $time,
                "order_id" => $order_id,
                "country" => $country,
                "city" => $city,
                "remark" => $remark,
                "status" => $status,
                "content" => "[{$country}-{$city}] {$status}" . ($remark ? "[{$remark}]" : ""),
            ];
            $result = M("ClientOrderSelfExpress")->add($data);
            if( $result ) {
                //插入操作日志
                $log_data = [
                    'order_num' => $order['order_num'],
                    'order_id' => $order['id'],
                    'operator_id' => session('yang_adm_user_id'),
                    'type' => 2,
                    'content' => '添加运单进展',
                ];
                M('ClientOrderLog')->add($log_data);
                $this->success("添加运单进展成功", U("/Manage/Clientorder/inputSelfExpress", ["id" => $order_id]));
            } else {
                $this->error("添加运单进展失败");
            }
        }
    }

    public function editExpress() {
        if( IS_GET ) {
            $id = I("id");
            $vo = M("ClientOrderSelfExpress")->find($id);
            $this->assign("vo", $vo);
            $this->type = '编辑运单详细资料';
            $this->progress = getArrayOfItem('progress');
            $this->remark = getArrayOfItem('remark');
            $this->display("editexpress");
        } else {
            $id = I("id");
            $order_id = I("fkid");
            $time = I("sj");
            $country = I("gj");
            $city = I("chs");
            $status = I("hwz");
            $remark = I("info");

            $order = M('ClientOrder')->where(['id' => $order_id,  'status' => 1])->find();
            if( empty($order) ) {
                $this->error("订单不存在");
            }

            if( empty($time) ) {
                $this->error("请选择时间");
            }

            if( empty($country) ) {
                $this->error("请选择国家");
            }

            if( empty($status) ) {
                $this->error("请选择派件状态");
            }

            $data = [
                "time" => $time,
                "order_id" => $order_id,
                "country" => $country,
                "city" => $city,
                "remark" => $remark,
                "status" => $status,
                "content" => "[{$country}-{$city}] {$status}" . ($remark ? "[{$remark}]" : ""),
            ];
            $map = ["id" => $id];
            $result = M("ClientOrderSelfExpress")->where($map)->save($data);
            if( is_numeric($result) ) {
                //插入操作日志
                $log_data = [
                    'order_num' => $order['order_num'],
                    'order_id' => $order['id'],
                    'operator_id' => session('yang_adm_user_id'),
                    'type' => 2,
                    'content' => '编辑运单进展',
                ];
                M('ClientOrderLog')->add($log_data);
                $this->success("编辑运单进展成功", U("/Manage/Clientorder/inputSelfExpress", ["id" => $order_id]));
            } else {
                $this->error("编辑运单进展失败");
            }
        }
    }

    public function delExpress() {
        $id = I('id',0 , 'intval');
        $vo = M("ClientOrderSelfExpress")->find($id);
        $order = M('ClientOrder')->where(['id' => $vo['order_id'],  'status' => 1])->find();
        if (M('ClientOrderSelfExpress')->delete($id)) {
            //插入操作日志
            $log_data = [
                'order_num' => $order['order_num'],
                'order_id' => $order['id'],
                'operator_id' => session('yang_adm_user_id'),
                'type' => 2,
                'content' => '删除运单进展',
            ];
            M('ClientOrderLog')->add($log_data);
            $this->success("彻底删除成功", U("/Manage/Clientorder/inputSelfExpress", ["id" => $vo['order_id']]));
        }else {
            $this->error('彻底删除失败');
        }
    }

    private function _get_order_params() {
        $data = [];
        $data['delivery_id'] = I('post.delivery_id', 0, 'intval');
        $data['receive_id'] = I('post.receive_id', 0, 'intval');
        $data['spare_company'] = I('post.spare_receive_company', '', 'trim');
        $data['spare_addressee'] = I('post.spare_receive_addressee', '', 'trim');
        $data['spare_country_id'] = I('post.spare_country', '', 'trim');
        $data['spare_state'] = I('post.spare_state', '', 'trim');
        $data['spare_city'] = I('post.spare_city', '', 'trim');
        $data['spare_detail_address'] = I('post.spare_receive_detail_address', '', 'trim');
        $data['spare_phone'] = I('post.spare_receive_phone', '', 'trim');
        $data['spare_mobile'] = I('post.spare_receive_mobile', '', 'trim');
        $data['spare_postal_code'] = I('post.spare_receive_postal_code', '', 'trim');
        $data['spare_receiver_code'] = I('post.spare_receiver_code', '', 'trim');
        $data['enable_spare'] = I('post.enable_spare', 0, 'intval');
        $data['channel_id'] = I('post.channel', '', 'trim');
        $data['package_type'] = I('post.package_type', 1, 'intval');
        $data['price_terms'] = I('post.price_terms', '', 'trim');
        $data['tariff_payment'] = I('post.tariff_payment', '', 'trim');
        $data['shipment_reference'] = I('post.shipment_reference', '', 'trim');
        $data['settlement'] = I('post.settlement', '', 'trim');
        $data['declaration_of_power_attorney'] = I('post.declaration_of_power_attorney', '', 'trim');
        $data['contract_num'] = I('post.contract_num', '', 'trim');
        $data['mode_of_transportation'] = I('post.mode_of_transportation', '', 'trim');
        $data['express_service'] = I('post.express_service', '', 'trim');
        $data['manufacturer'] = I('post.manufacturer', '', 'trim');
        $data['export_nature'] = I('post.export_nature', '', 'trim');
        $data['export_reason'] = I('post.export_reason', '', 'trim');
        $data['remark'] = I('post.remark', '', 'trim');
        $data['specifications_remark'] = I('post.specifications_remark', '', 'trim');
        $data['commit'] = I('post.commit', 0, 'trim');

        $data['order_detail'] = $_POST['order_detail'];
        $data['order_specifications'] = $_POST['order_specifications'];
//        var_dump($data);exit;
        return $data;
    }

    private function _build_delivery_address($data) {
        //构造发货数据
        $where = ['status' => 1, 'client_id' => $data['client_id'], 'id' => $data['delivery_id']];
        $delivery = M('DeliveryAddress')->where($where)->find();
        if( empty($delivery) ) {
            $this->has_error = true;
            $this->error_msg = '请输入发货信息';
        } else {
//            $data['delivery_id'] = $data['delivery_id'];
            $data['delivery_company'] = $delivery['company'];
            $data['delivery_consignor'] = $delivery['consignor'];
            $data['delivery_country_id'] = $delivery['country_id'];
            $data['delivery_state'] = $delivery['state'];
            $data['delivery_city'] = $delivery['city'];
            $data['delivery_phone'] = $delivery['phone'];
            $data['delivery_mobile'] = $delivery['mobile'];
            $data['delivery_detail_address'] = $delivery['detail_address'];
            $data['delivery_postal_code'] = $delivery['postal_code'];
            $data['exporter_code'] = $delivery['exporter_code'];

            $country = M('Country')->where(['id' => $data['delivery_country_id']])->find();
            if( $country ) {

                $data['delivery_country_name'] = $country['name'];
                $data['delivery_country_en_name'] = $country['ename'];
            }
        }
        return $data;
    }

    private function _build_receive_address($data) {
        //构造收货数据
        $where = ['status' => 1, 'client_id' => $data['client_id'], 'id' => $data['receive_id']];
        $receive = M('ReceiveAddress')->where($where)->find();
        if( empty($receive) ) {
            $this->has_error = true;
            $this->error_msg = $this->error_msg ? $this->error_msg.'<br />请输入发货信息' : '请输入发货信息';
        } else {
//            $data['receive_id'] = $receive_id;
            $data['receive_company'] = $receive['company'];
            $data['receive_addressee'] = $receive['addressee'];
            $data['receive_country_id'] = $receive['country_id'];
            $data['receive_state'] = $receive['state'];
            $data['receive_city'] = $receive['city'];
            $data['receive_phone'] = $receive['phone'];
            $data['receive_mobile'] = $receive['mobile'];
            $data['receive_detail_address'] = $receive['detail_address'];
            $data['receive_postal_code'] = $receive['postal_code'];
            $data['receiver_code'] = $receive['receiver_code'];
            $country = M('Country')->where(['id' => $data['receive_country_id']])->find();
            if( $country ) {
                $data['receive_country_name'] = $country['name'];
                $data['receive_country_en_name'] = $country['ename'];
            }
        }
        return $data;
    }

    private function _build_spare_address($data)
    {

        if (empty($data['enable_spare'])) {
            $data['spare_company'] = '';
            $data['spare_addressee'] = '';
            $data['spare_country_id'] = '';
            $data['spare_state'] = '';
            $data['spare_city'] = '';
            $data['spare_detail_address'] = '';
            $data['spare_phone'] = '';
            $data['spare_mobile'] = '';
            $data['spare_postal_code'] = '';
            $data['spare_receiver_code'] = '';
            $data['spare_country_name'] = '';
            $data['spare_country_en_name'] = '';
        } else {
            foreach ($this->spare_info_array as $v) {
                if (empty($data[$v])) {
                    $this->has_error = true;
                    $this->error_msg = $this->error_msg ? $this->error_msg . '<br />请完善备用信息,*为必填' : '请完善备用信息,*为必填';
                    break;
                }
            }

            $country = M('Country')->where(['id' => $data['spare_country_id']])->find();
            if ($country) {
                $data['spare_country_name'] = $country['name'];
                $data['spare_country_en_name'] = $country['ename'];
            }
        }
        return $data;
    }

    private function _build_order_info($data) {
        //构造其他信息
        $channel = M('Channel')->where(['status' => 1, 'id' => $data['channel_id']])->find();
        if( empty($channel) ) {
            $this->has_error = true;
            $this->error_msg = $this->error_msg ? $this->error_msg.'<br />请选择渠道' : '请选择渠道';
        }
        $data['channel_name'] = $channel['name'];
        $data['channel_en_name'] = $channel['en_name'];
        $data['package_type'] = $data['package_type'] == 1 ? 1 : 2;

        foreach( $this->order_info_array as $k => $v ) {
            if( $data[$k] == '' ) {
                $this->has_error = true;
                $this->error_msg = $this->error_msg ? $this->error_msg.'<br />'.$v : $v;
            }
        }
        return $data;
    }

    private function _build_order_detail($order_detail) {
        //构造产品详情
        $temp = [];
        if( !is_array($order_detail) ) {
            $this->has_error = true;
            $this->error_msg = $this->error_msg ? $this->error_msg.'<br />请输入产品详情' : '请输入产品详情';
        } else {
            if( count($order_detail) < 1 ) {
                $this->has_error = true;
                $this->error_msg = $this->error_msg ? $this->error_msg.'<br />请输入产品详情' : '请输入产品详情';
            } else {
                foreach ($order_detail as $k => $v) {
                    $temp[$k]['product_name'] = isset($v['product_name']) ? $v['product_name'] : '';
                    $temp[$k]['en_product_name'] = isset($v['en_product_name']) ? $v['en_product_name'] : '';
                    $temp[$k]['goods_code'] = isset($v['detail_goods_code']) ? $v['detail_goods_code'] : '';
                    $temp[$k]['count'] = isset($v['detail_count']) ? $v['detail_count'] : 0;
                    $temp[$k]['unit'] = isset($v['unit']) ? $v['unit'] : '';
                    $temp[$k]['single_declared'] = isset($v['single_declared']) ? $v['single_declared'] : 0;
                    $temp[$k]['origin'] = isset($v['origin']) ? $v['origin'] : 'China';
                }
                $order_detail = $temp;
                foreach( $order_detail as $k => $v ) {
                    if( $v['product_name'] == '' || $v['en_product_name'] == '' || $v['goods_code'] == '' || $v['count'] <= 0 || $v['unit'] == '' || $v['single_declared'] <= 0 || $v['origin'] == '' ) {
                        $this->has_error = true;
                        $this->error_msg = $this->error_msg ? $this->error_msg.'<br />产品详情内容有误，请重新输入' : '产品详情内容有误，请重新输入';
                        break;
                    }
                }
            }
        }
        return $order_detail;
    }

    private function _build_order_specification($order_specifications) {
        //构造产品规格
        if( !is_array($order_specifications) ) {
            $this->has_error = true;
            $this->error_msg = $this->error_msg ? $this->error_msg.'<br />请输入产品规格' : '请输入产品规格';
        } else {
            if (count($order_specifications) < 1) {
                $this->has_error = true;
                $this->error_msg = $this->error_msg ? $this->error_msg . '<br />请输入产品规格' : '请输入产品规格';
            } else {
                foreach( $order_specifications as $k => $v ) {
                    $order_specifications[$k]['count'] = intval($v['count']);
                    $order_specifications[$k]['weight'] = floatval($v['weight']);
                    $order_specifications[$k]['length'] = floatval($v['length']);
                    $order_specifications[$k]['width'] = floatval($v['width']);
                    $order_specifications[$k]['height'] = floatval($v['height']);
                }
                foreach( $order_specifications as $k => $v ) {
                    if( $v['weight'] <= 0 || $v['length'] < 0 || $v['width'] < 0 || $v['height'] < 0 || $v['count'] < 0 ) {
                        $this->has_error = true;
                        $this->error_msg = $this->error_msg ? $this->error_msg.'<br />产品规格内容有误，请重新输入' : '产品规格内容有误，请重新输入';
                        break;
                    }
                }
            }
        }
        return $order_specifications;
    }

}
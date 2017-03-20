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
        'error' => -1,
        'msg' => '',
    ];

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
                $list[$k]['status_str'] = $this->_order_status($v);
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
        exit;
        $id = I('id', 0, 'intval');
        $order = M('ClientOrder')->where(['id' => $id])->find();
        if( empty($order) ) {
            $this->error('订单不存在');
        }
        if( !($order['client_status'] == 1 && $order['exam_status'] == 0) ) {
            $this->error('当前订单不是待审核状态');
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
                'operator_id' => session('yang_adm_uid'),
                'type' => 2,
                'content' => '通过订单审核',
            ];
            M('ClientOrderLog')->add($log_data);

            $this->success('订单已审核通过');
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
        $order['status_str'] = $this->_order_status($order);
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
            foreach( $order_specifications as $k => $v ) {
                $end = $start + $v['count'] - 1;
                $order_specifications[$k]['no'] = $start.'-'.$end;
                $order_specifications[$k]['weight'] = sprintf('%.2f', $v['weight']).'kg';
                $order_specifications[$k]['length'] = sprintf('%.2f', $v['length']).'cm';
                $order_specifications[$k]['width'] = sprintf('%.2f', $v['width']).'cm';
                $order_specifications[$k]['height'] = sprintf('%.2f', $v['height']).'cm';
                $order_specifications[$k]['rate'] = ($v['height'] * $v['length'] * $v['width'] / 5000);
                $order_specifications[$k]['real_weight'] = $v['weight'] > $order_specifications[$k]['rate'] ? $v['weight'] : $order_specifications[$k]['rate'];
                $order['specifications_total_weight'] += $v['weight'] * $v['count'];
                $order['specifications_total_rate'] += $order_specifications[$k]['rate'] * $v['count'];
                $order['specifications_total_count'] += $v['count'];
                $order['specifications_calculate_weight'] += $order_specifications[$k]['real_weight'] * $v['count'];
                $order_specifications[$k]['rowspan'] = count($v['detail']);
                $start = $end + 1;
//                var_dump($order_specifications[$k]['detail']);exit;
            }
        }

        $order_log = M('ClientOrderLog')
            ->alias('l')
            ->field('l.created_at, l.content, c.full_name as client_name, a.realname as operator_name, l.type')
            ->join('left join hx_client as c on c.id = l.user_id and l.type = 1')
            ->join('left join hx_admin as a on a.id = l.operator_id and l.type = 2')
            ->where(['l.order_id' => $id])
            ->select();
//        echo M('ClientOrderLog')->getLastSql();exit;
//        var_dump($order_log);exit;

        $channel_list = M('Channel')->where(['status' => 1])->select();

        $this->assign('order_log', $order_log);
        $this->assign('channel_list', $channel_list);
        $this->assign('order', $order);
        $this->assign('order_specifications', $order_specifications);
        $this->assign('order_detail', $order_detail);
        $this->type = '客户订单详情';
        $this->display();

    }

    public function exam() {
        $id = I('id', 0, 'intval');
        $order = M('ClientOrder')->where(['id' => $id])->find();
        if( empty($order) ) {
            $this->error('订单不存在');
        }
        $order['package_type_name'] = $order['package_type'] == 1 ? '文件' : '包裹';
        $order['status_str'] = $this->_order_status($order);
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
            $this->success('订单驳回成功', U('Clientorder/index'));
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

        $order_detail = M('ClientOrderDetail')->where(['order_id' => $id])->select();
        $d_cursor = 0;
        if( $order_detail ) {
            $temp = [];
            foreach( $order_detail as $k => $v ) {
                $temp['item-'.$v['id']] = $v;
                $d_cursor = $d_cursor < $v['id'] ? $v['id'] : $d_cursor;
            }
            $order_detail = $temp;
        }
        $order_specifications = M('ClientOrderSpecifications')
            ->alias('s')
            ->field('s.*, m.detail_id, m.number')
            ->join('inner join hx_client_order_map as m on m.specifications_id = s.id')
            ->where(['s.order_num' => $order['order_num']])
            ->select();
//        echo M('ClientOrderSpecifications')->getLastSql();exit;
        $s_cursor = 0;
        if( $order_specifications ) {
            $temp = [];
            foreach( $order_specifications as $k => $v ) {
                if( !isset($temp['item-'.$v['id']]) ) {
                    $temp['item-'.$v['id']] = $v;
                }
                $temp['item-'.$v['id']]['detail'][] = 'item-'.$v['detail_id'];
                $temp['item-'.$v['id']]['detail_number']['item-'.$v['detail_id']] = $v['number'];
                $s_cursor = $s_cursor < $v['id'] ? $v['id'] : $s_cursor;
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


        $this->assign('order_detail_unit', C('order_detail_unit'));
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
        $country_list = M('country')->where($where)->order('sort,id')->select();
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
        $order['status_str'] = $this->_order_status($order);
        $trace_result = S('hkwcd_trace_result');
        if( !$trace_result ) {
            $trace_result = query_express($order['express_type'], $order['express_order_num']);
            S('hkwcd_trace_result', $trace_result, 7200);
        }
        $trace_result_array = json_decode($trace_result, true);
        $this->assign('order', $order);
        $this->assign('trace_result', $trace_result_array);

        $this->type = '订单跟踪';
        $this->display();

    }

    public function delivery() {
        $id = I('id', 0, 'intval');
        $order = M('ClientOrder')->where(['id' => $id, 'status' => 1])->find();
        if( empty($order) ) {
            $this->error('订单不存在');
        }
        if( !($order['exam_status'] == 1 && $order['express_status'] == 0) ) {
            $this->error('当前订单不是待发货状态');
        }
        $order['package_type_name'] = $order['package_type'] == 1 ? '文件' : '包裹';
        $order['status_str'] = $this->_order_status($order);
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
        $id = I('id', 0, 'intval');
        $order = M('ClientOrder')->where(['id' => $id, 'status' => 1])->find();
        if( empty($order) ) {
            $this->error('订单不存在');
        }
        if( !($order['exam_status'] == 1 && $order['express_status'] == 0) ) {
            $this->error('当前订单不是待发货状态');
        }

        $express_type_id = I('express_type_id', 0, 'intval');
        $express_order_num = I('express_order_num', '', 'trim');
        $invoice_num = I('invoice_num', '', 'trim');
        $services_type = I('services_type', '', 'trim');
        $shipment_reference = I('shipment_reference', '', 'trim');
        $exporter_code = I('exporter_code', '', 'trim');
//        $receiver_code = I('receiver_code', '', 'trim');
//        $price_terms = I('price_terms', '', 'trim');
//        $tariff_payment = I('tariff_payment', '', 'trim');
        $declaration_of_power_attorney = I('declaration_of_power_attorney', '', 'trim');
        $settlement = I('settlement', '', 'trim');
        $mode_of_transportation = I('mode_of_transportation', '', 'trim');
//        $export_nature = I('export_nature', '', 'trim');
        $contract_num = I('contract_num', '', 'trim');
        $order_detail = $_POST['detail'];
        $delivery = I('delivery', 0, 'intval');
        $delivery = $delivery == 1 ? 1 : 0;

        if( empty($invoice_num) ) {
            $this->error('请输入发票号码');
        }

        if( empty($services_type) ) {
            $this->error('请输入服务类型');
        }
        if( empty($shipment_reference) ) {
            $this->error('请输入装运参考');
        }


        $express_type_exists = M('ExpressType')->where(['id' => $express_type_id])->find();
        if( empty($express_type_exists) ) {
            $this->error('请选择下家');
        }
        if( empty($express_order_num) ) {
            $this->error('请输入转运单号');
        }
        if( empty($exporter_code) ) {
            $this->error('请输入出口商代码');
        }
//        if( empty($receiver_code) ) {
//            $this->error('请输入进口商代码');
//        }
//        if( empty($price_terms) ) {
//            $this->error('请输入价格条款');
//        }
//        if( empty($tariff_payment) ) {
//            $this->error('请输入关税支付');
//        }
        if( empty($declaration_of_power_attorney) ) {
            $this->error('请输入相关委托书');
        }
        if( empty($settlement) ) {
            $this->error('请输入结汇方式');
        }
        if( empty($mode_of_transportation) ) {
            $this->error('请输入运输方式');
        }

//        if( empty($export_nature) ) {
//            $this->error('请输入出口性质');
//        }

        if( empty($contract_num) ) {
            $this->error('请输入合同号');
        }

        if( !is_array($order_detail) ) {
            $this->error('参数错误');
        }
        foreach( $order_detail as $detail ) {
            if( !is_array($detail) ) {
                $this->error('参数错误');
            }
            if( !isset($detail['weighting_weight']) || floatval($detail['weighting_weight']) < 0 ) {
                $this->error('请输入过磅重量');
            }
            if( !isset($detail['cubic_of_volume']) || floatval($detail['cubic_of_volume']) < 0 ) {
                $this->error('请输入材积立方数');
            }
            if( !isset($detail['box']) || floatval($detail['box']) < 0 ) {
                $this->error('请输入装箱数');
            }
            if( !isset($detail['box_number']) || floatval($detail['box_number']) < 0 ) {
                $this->error('请输入每箱数量');
            }
        }
        $data = [
            'express_type_id' => $express_type_exists['id'],
            'express_type' => $express_type_exists['type'],
            'express_type_name' => $express_type_exists['name'],
            'express_order_num' => $express_order_num,
            'invoice_num' => $invoice_num,
            'services_type' => $services_type,
            'shipment_reference' => $shipment_reference,
            'exporter_code' => $exporter_code,
            'receiver_code' => $receiver_code,
            'price_terms' => $price_terms,
            'tariff_payment' => $tariff_payment,
            'declaration_of_power_attorney' => $declaration_of_power_attorney,
            'settlement' => $settlement,
            'mode_of_transportation' => $mode_of_transportation,
            'export_nature' => $export_nature,
            'contract_num' => $contract_num,
        ];
        if( $delivery == 1 ) {
            $data['express_status'] = 1;
            $data['express_time'] = date('Y-m-d H:i:s', time());
            $msg = '发货成功';
        } else {
            $msg = '装箱信息已保存';
        }
        //事务开始
        $model = new Model;
        $transaction = true;
        $model->startTrans();

        $result = M('ClientOrder')->where(['id' => $id, 'express_status' => 0])->save($data);
        if( !is_numeric($result) ) {
            $transaction = false;
        }

        if( $transaction ) {
            foreach( $order_detail as $k => $v ) {
                $detail_data = [
                    'weighting_weight' => floatval($v['weighting_weight']),
                    'cubic_of_volume' => floatval($v['cubic_of_volume']),
                    'box' => intval($v['box']),
                    'box_number' => intval($v['box_number']),
                ];
                $result = M('ClientOrderDetail')->where(['id' => intval($k), 'order_num' => $order['order_num']])->save($detail_data);
                if( !is_numeric($result) ) {
                    $transaction = false;
                    break;
                }
            }
        }

        if( $transaction ) {
            $model->commit();
            if( $delivery ) {
                //插入操作日志
                $log_data = [
                    'order_num' => $order['order_num'],
                    'order_id' => $order['id'],
                    'operator_id' => session('yang_adm_uid'),
                    'type' => 2,
                    'content' => '订单发货',
                ];
                M('ClientOrderLog')->add($log_data);
            }
            $this->success($msg, U('Clientorder/index'));
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
        $order['total_box_num'] = 0;
        $order['total_weight'] = 0;
        $order['total_declared'] = 0;
        $order_detail_remain =  [];
        if( $order_detail ) {
            foreach( $order_detail as $k => $v ) {
                $order['total_box_num'] += $v['box'];
                $order['total_weight'] += $v['cubic_of_volume'] > $v['weighting_weight'] ? $v['cubic_of_volume'] : $v['weighting_weight'];
                $order['total_declared'] += $v['declared'];
            }
        }
        for( $i = 0; $i < 4 - count($order_detail); $i++ ) {
            $order_detail_remain[] = [];
        }


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

        $order_detail = M('ClientOrderDetail')->where(['order_num' => $order['order_num']])->select();
//        $order_specifications = M('ClientOrderSpecifications')->where(['order_num' => $order['order_num']])->select();
        $order['total_box_num'] = 0;
        $order['total_weight'] = 0;
        $order['total_declared'] = 0;
        $order_detail_remain =  [];
        if( $order_detail ) {
            foreach( $order_detail as $k => $v ) {
                $order['total_box_num'] += $v['box'];
                $order['total_weight'] += $v['cubic_of_volume'] > $v['weighting_weight'] ? $v['cubic_of_volume'] : $v['weighting_weight'];
                $order['total_declared'] += $v['declared'];
            }
        }
        for( $i = 0; $i < 4 - count($order_detail); $i++ ) {
            $order_detail_remain[] = [];
        }


        $this->assign('order', $order);
        $this->assign('order_detail', $order_detail);
        $this->assign('order_detail_remain', $order_detail_remain);
        $this->type = '装箱单';
        $this->display();
    }


    private function _order_status($order) {
        $status = '';
        if( $order['error_status'] == 1 ) {
            $status = '订单异常';
            return $status;
        }

        if( $order['client_status'] == 0 ) {
            $status = '未提交';
        }
        if( $order['client_status'] == 1 && $order['exam_status'] == 0 ) {
            $status = '待审核';
            if( $order['is_rejected'] ) {
                $status .= '（驳回）';
            }
        }
        if( $order['client_status'] == 1 && $order['exam_status'] == 1 && $order['express_status'] == 0 ) {
            $status = '待发货';
        }
        if( $order['client_status'] == 1 && $order['exam_status'] == 1 && $order['express_status'] == 1 && $order['receive_status'] == 0 ) {
            $status = '已发货';
        }
        if( $order['client_status'] == 1 && $order['exam_status'] == 1 && $order['express_status'] == 1 && $order['receive_status'] == 1 ) {
            $status = '已收货';
        }
        $status = '' == $status ? '订单异常' : $status;
        return $status;
    }


    public function ajaxEdit() {

        $id = I('order_id');
        $order = M('ClientOrder')->where(['id' => $id,  'status' => 1])->find();
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
        $data = $this->_get_order_params();
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
//        $commit = $data['commit'];
        unset($data['commit']);
//        $data['client_status'] = $commit == 0 ? 0 : 1;
        $msg =  '修改订单成功';

//        $data['client_id'] = $client_id;
        //修改订单
        $result = M('ClientOrder')->where(['id' => $id])->save($data);
        if( is_numeric($result) ) {
            //插入操作日志
            $log_data = [
                'order_num' => $order['order_num'],
                'order_id' => $order['id'],
                'operator_id' => session('yang_adm_uid'),
                'type' => 2,
                'content' => $msg,
            ];
            M('ClientOrderLog')->add($log_data);

            $this->response['code'] = 1;
            $this->response['msg'] = $msg;
            $this->response['url'] = U('/Manage/Clientorder/detail', ['id' => $id]);
        } else {
            $this->response['msg'] = '系统繁忙，请稍后重试';
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
                'user_id' => $client_id,
                'type' => 1,
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
                'user_id' => $client_id,
                'type' => 1,
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
                'user_id' => $client_id,
                'type' => 1,
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
                'user_id' => $client_id,
                'type' => 1,
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
                'user_id' => $client_id,
                'type' => 1,
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
                'user_id' => $client_id,
                'type' => 1,
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

        return $data;
    }

    private function _build_delivery_address($data) {
        //构造发货数据
        $where = ['status' => 1, 'id' => $data['delivery_id']];
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
        $where = ['status' => 1, 'id' => $data['receive_id']];
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
        if( !is_array($order_detail) ) {
            $this->has_error = true;
            $this->error_msg = $this->error_msg ? $this->error_msg.'<br />请输入产品详情' : '请输入产品详情';
        } else {
            if( count($order_detail) < 1 ) {
                $this->has_error = true;
                $this->error_msg = $this->error_msg ? $this->error_msg.'<br />请输入产品详情' : '请输入产品详情';
            } else {
                foreach ($order_detail as $k => $v) {
                    $order_detail[$k]['product_name'] = isset($v['product_name']) ? $v['product_name'] : '';
                    $order_detail[$k]['en_product_name'] = isset($v['en_product_name']) ? $v['en_product_name'] : '';
                    $order_detail[$k]['goods_code'] = isset($v['goods_code']) ? $v['goods_code'] : '';
                    $order_detail[$k]['count'] = isset($v['count']) ? $v['count'] : 0;
                    $order_detail[$k]['unit'] = isset($v['unit']) ? $v['unit'] : '';
                    $order_detail[$k]['single_declared'] = isset($v['single_declared']) ? $v['single_declared'] : 0;
                    $order_detail[$k]['origin'] = isset($v['origin']) ? $v['origin'] : 'China';
                }
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
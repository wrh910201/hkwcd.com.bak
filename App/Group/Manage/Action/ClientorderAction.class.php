<?php
/**
 * Created by PhpStorm.
 * User: Wrh
 * Date: 2016/12/12
 * Time: 21:02
 */
class ClientorderAction extends CommonAction {

    public function index() {
        $keyword = I('keyword', '', 'htmlspecialchars,trim');//关键字
        $where = array();
        $condition = array();
        if (!empty($keyword)) {
            $condition['order_num'] = ['like', $keyword];
            $condition['delivery_company'] = ['like', $keyword];
            $condition['receive_company'] = ['like', $keyword];
            $condition['_logic'] = 'OR';
            $where['_complex'] = $condition;
        }
        $where['client_status'] = 1;
        //分页
        import('ORG.Util.Page');
        $count = M('ClientOrder')->where($where)->count();

        $page = new Page($count, 10);
        $limit = $page->firstRow. ',' .$page->listRows;
        $list = M('ClientOrder')->where($where)->order('id')->limit($limit)->select();
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


        $this->display();
    }

    public function exam() {
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
        $receiver_code = I('receiver_code', '', 'trim');
        $price_terms = I('price_terms', '', 'trim');
        $tariff_payment = I('tariff_payment', '', 'trim');
        $declaration_of_power_attorney = I('declaration_of_power_attorney', '', 'trim');
        $settlement = I('settlement', '', 'trim');
        $mode_of_transportation = I('mode_of_transportation', '', 'trim');
        $export_nature = I('export_nature', '', 'trim');
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
        if( empty($receiver_code) ) {
            $this->error('请输入进口商代码');
        }
        if( empty($price_terms) ) {
            $this->error('请输入价格条款');
        }
        if( empty($tariff_payment) ) {
            $this->error('请输入关税支付');
        }
        if( empty($declaration_of_power_attorney) ) {
            $this->error('请输入相关委托书');
        }
        if( empty($settlement) ) {
            $this->error('请输入结汇方式');
        }
        if( empty($mode_of_transportation) ) {
            $this->error('请输入运输方式');
        }

        if( empty($export_nature) ) {
            $this->error('请输入出口性质');
        }

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

}
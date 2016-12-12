<?php
/**
 * Created by PhpStorm.
 * User: Wrh
 * Date: 2016/11/15
 * Time: 18:15
 */

class OrderAction extends BaseAction  {

    public function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub

    }


    public function index() {

        $where = [
            'status' => 1,
        ];
        $order_list = M('ClientOrder')->where($where)->select();
        if( $order_list ) {
            foreach( $order_list as $k => $v ) {
                $order_list[$k]['status_str'] = $this->_order_status($v);
            }
        }

        $this->assign('order_list', $order_list);
        $this->assign('title', '订单列表');
        $this->display();
    }

    public function add() {

        $client_id = session('hkwcd_user.user_id');
        $client = M('Client')->where(['id'  => $client_id])->find();

        $default_delivery = M('DeliveryAddress')->where(['status' => 1, 'is_default' => 1])->find();
        $has_default_delivery = $default_delivery ? 1 : 0;
        $selected_delivery_id = $default_delivery ? $default_delivery['id'] : 0;

        $default_receive = M('ReceiveAddress')->where(['status' => 1, 'is_default' => 1])->find();
        $has_default_receive = $default_receive ? 1 : 0;
        $selected_receive_id = $default_receive ? $default_receive['id'] : 0;

        //国家
        $where = array('pid' => 0,'types'=>0);
        $country_list = M('country')->where($where)->order('sort,id')->select();
        $this->assign('country_list', $country_list);

        //货币
        $where = ['status' => 1];
        $currency_list = M('Currency')->where($where)->select();
        $this->assign('currency_list', $currency_list);

        $channel_list = M('Channel')->where($where)->select();
        $this->assign('channel_list', $channel_list);

        $this->assign('client', $client);

        $this->assign('default_delivery', $default_delivery);
        $this->assign('json_delivery', json_encode($default_delivery));
        $this->assign('default_delivery_id', $selected_delivery_id);
        $this->assign('has_default_delivery', $has_default_delivery);

        $this->assign('default_receive', $default_receive);
        $this->assign('json_receive', json_encode($default_receive));
        $this->assign('default_receive_id', $selected_receive_id);
        $this->assign('has_default_receive', $has_default_receive);

        $this->display();
    }

    public function ajaxAdd() {

        $client_id = session('hkwcd_user.user_id');
        $client = M('Client')->where(['status' => 1, 'id' => $client_id])->find();

        $has_error = false;
        $error_msg = '';

        $delivery_id = I('post.delivery_id', 0, 'intval');
        $receive_id = I('post.receive_id', 0, 'intval');
        $spare_addressee = I('post.spare_receive_addressee', '', 'trim');
        $spare_detail_address = I('post.spare_receive_detail_address', '', 'trim');
        $spare_mobile = I('post.spare_receive_mobile', '', 'trim');
        $spare_phone = I('post.spare_receive_phone', '', 'trim');
        $spare_postal_code = I('post.spare_receive_postal_code', '', 'trim');
        $currency_id = I('post.currency', 1, 'intval');
        $declared_value = I('post.declared_value', 0, 'floatval');
        $channel_id = I('post.channel', 0, 'intval');
        $package_type = I('post.package_type', 0, 'intval');
        $export_reason = I('post.export_reason', '', 'trim');
        $remark = I('post.remark', '', 'trim');
        $commit = I('post.commit', 0, 'intval');

        $commit = $commit == 1 ? 1 : 0;

        $order_detail = $_POST['order_detail'];
        $order_specifications = $_POST['order_specifications'];

        //构造发货数据
        $where = ['status' => 1, 'client_id' => $client_id, 'id' => $delivery_id];
        $delivery = M('DeliveryAddress')->where($where)->find();
        if( empty($delivery) ) {
            $has_error = true;
            $error_msg = '请输入发货信息';
        } else {
            $data['delivery_id'] = $delivery_id;
            $data['delivery_company'] = $client['company'];
            $data['delivery_consignor'] = $delivery['consignor'];
            $data['delivery_country_id'] = $delivery['country_id'];
            $data['delivery_state'] = $delivery['state'];
            $data['delivery_city'] = $delivery['city'];
            $data['delivery_phone'] = $delivery['phone'];
            $data['delivery_mobile'] = $delivery['mobile'];
            $data['delivery_detail_address'] = $delivery['detail_address'];
            $data['delivery_postal_code'] = $delivery['postal_code'];
        }
        //构造收货数据
        $where = ['status' => 1, 'client_id' => $client_id, 'id' => $receive_id];
        $receive = M('ReceiveAddress')->where($where)->find();
        if( empty($receive) ) {
            $has_error = true;
            $error_msg = $error_msg ? $error_msg.'<br />请输入发货信息' : '请输入发货信息';
        } else {
            $data['receive_id'] = $receive_id;
            $data['receive_company'] = $client['company'];
            $data['receive_addressee'] = $receive['addressee'];
            $data['receive_country_id'] = $receive['country_id'];
            $data['receive_state'] = $receive['state'];
            $data['receive_city'] = $receive['city'];
            $data['receive_phone'] = $receive['phone'];
            $data['receive_mobile'] = $receive['mobile'];
            $data['receive_detail_address'] = $receive['detail_address'];
            $data['receive_postal_code'] = $receive['postal_code'];
        }
        //构造备用收货数据
        $data['spare_addressee'] = $spare_addressee;
        $data['spare_detail_address'] = $spare_detail_address;
        $data['spare_phone'] = $spare_phone;
        $data['spare_mobile'] = $spare_mobile;
        $data['spare_postal_code'] = $spare_postal_code;

        if( $has_error ) {
            $this->response['msg'] = $error_msg;
            echo json_encode($this->response);
            exit;
        }

        //构造其他信息
        $currency = M('Currency')->where(['status' => 1, 'id' => $currency_id])->find();
        if( empty($currency) ) {
            $has_error = true;
            $error_msg = $error_msg ? $error_msg.'<br />请选择币种' : '请选择币种';
        }
        $data['currency_id'] = $currency_id;
        $data['currency_name'] = $currency['name'];
        $data['currency_rate'] = $currency['rate'];

        $data['declared_value'] = $declared_value;

        $channel = M('Channel')->where(['status' => 1, 'id' => $channel_id])->find();
        if( empty($channel) ) {
            $has_error = true;
            $error_msg = $error_msg ? $error_msg.'<br />请选择渠道' : '请选择渠道';
        }
        $data['channel_name'] = $channel['name'].'/'.$channel['en_name'];

        $data['package_type'] = $package_type;
        if( empty($export_reason) ) {
            $has_error = true;
            $error_msg = $error_msg ? $error_msg.'<br />请输入出口原因' : '请输入出口原因';
        }
        $data['export_reason'] = $export_reason;
        $data['remark'] = $remark;

        if( $has_error ) {
            $this->response['msg'] = $error_msg;
            echo json_encode($this->response);
            exit;
        }

        //构造产品详情
        if( !is_array($order_detail) ) {
            $has_error = true;
            $error_msg = $error_msg ? $error_msg.'<br />请输入产品详情' : '请输入产品详情';
        } else {
            if( count($order_detail) < 1 ) {
                $has_error = true;
                $error_msg = $error_msg ? $error_msg.'<br />请输入产品详情' : '请输入产品详情';
            } else {
                foreach ($order_detail as $k => $v) {
                    $order_detail[$k]['product_name'] = isset($v['product_name']) ? $v['product_name'] : '';
                    $order_detail[$k]['goods_code'] = isset($v['goods_code']) ? $v['goods_code'] : '';
                    $order_detail[$k]['count'] = isset($v['count']) ? $v['count'] : 0;
                    $order_detail[$k]['single_declared'] = isset($v['single_declared']) ? $v['single_declared'] : 0;
                    $order_detail[$k]['declared'] = isset($v['declared']) ? $v['declared'] : 0;
                    $order_detail[$k]['origin'] = isset($v['origin']) ? $v['origin'] : 'China';
                }
                foreach( $order_detail as $k => $v ) {
                    if( $v['product_name'] == '' || $v['goods_code'] == '' || $v['count'] <= 0 || $v['single_declared'] <= 0 || $v['declared'] <= 0) {
                        $has_error = true;
                        $error_msg = $error_msg ? $error_msg.'<br />产品详情内容有误，请重新输入' : '产品详情内容有误，请重新输入';
                        break;
                    }
                }
            }
        }
        if( $has_error ) {
            $this->response['msg'] = $error_msg;
            echo json_encode($this->response);
            exit;
        }

        //构造产品规格
        if( !is_array($order_specifications) ) {
            $has_error = true;
            $error_msg = $error_msg ? $error_msg.'<br />请输入产品规格' : '请输入产品规格';
        } else {
            if (count($order_specifications) < 1) {
                $has_error = true;
                $error_msg = $error_msg ? $error_msg . '<br />请输入产品规格' : '请输入产品规格';
            } else {
                foreach( $order_specifications as $k => $v ) {
                    $order_specifications[$k]['count'] = intval($v['count']);
                    $order_specifications[$k]['weight'] = floatval($v['weight']);
                    $order_specifications[$k]['length'] = floatval($v['length']);
                    $order_specifications[$k]['width'] = floatval($v['width']);
                    $order_specifications[$k]['height'] = floatval($v['height']);
                    $order_specifications[$k]['remark'] = trim($v['remark']);
                }
                foreach( $order_specifications as $k => $v ) {
                    if( $v['weight'] <= 0 || $v['length'] < 0 || $v['width'] < 0 || $v['height'] < 0 || $v['count'] < 0 ) {
                        $has_error = true;
                        $error_msg = $error_msg ? $error_msg.'<br />产品规格内容有误，请重新输入' : '产品规格内容有误，请重新输入';
                        break;
                    }
                }
            }
        }
        if( $has_error ) {
            $this->response['msg'] = $error_msg;
            echo json_encode($this->response);
            exit;
        }

        $data['client_status'] = $commit == 0 ? 0 : 1;
        $msg = $commit == 0 ? '添加订单成功' : '订单添加成功，并已提交';
        $data['client_id'] = $client_id;

        //事务开始，插入订单
        $model = new Model();
        $transaction = true;
        $model->startTrans();

        /**
         * 1、插入订单，返回订单编号
         * 2、插入订单详情，
         * 3、插入订单规格
         */
        $order_id = 0;
        $order_num = 'HD'.date('Ymd', time()).$client_id.rand(1000, 9999);
        $data['order_num'] = $order_num;
        $add_order_result = M('ClientOrder')->add($data);
        $order_id = M('ClientOrder')->getLastInsID();
//        $this->response['msg'] = $order_num;
//        echo json_encode($this->response);exit;
        if( !$add_order_result ) {
            $transaction = false;
        }

        if( $transaction ) {
            foreach( $order_detail as $k => $v ) {
                $v['order_num'] = $order_num;
                $temp_result = M('ClientOrderDetail')->add($v);
                if( !$temp_result ) {
                    $transaction = false;
                    break;
                }
            }
        }

        if( $transaction ) {
            foreach( $order_specifications as $k => $v ) {
                $v['order_num'] = $order_num;
                $temp_result = M('ClientOrderSpecifications')->add($v);
                if( !$temp_result ) {
                    $transaction = false;
                    break;
                }
            }
        }

        if( $transaction ) {
            $model->commit();
            //插入操作日志
            $log_data = [
                'order_num' => $order_num,
                'order_id' => $order_id,
                'user_id' => $client_id,
                'type' => 1,
                'content' => $msg,
            ];
            M('ClientOrderLog')->add($log_data);

            $this->response['code'] = 1;
            $this->response['msg'] = $msg;
            $this->response['url'] = U('Order/index');
        } else {
            $model->rollback();
            $this->response['msg'] = '系统繁忙，请稍后重试';
        }
        echo json_encode($this->response);
        exit;
    }

    public function getDeliveryList() {
        $client_id = $client_id = session('hkwcd_user.user_id');
        $where = [
            'client_id' => $client_id,
            'status' => 1,
        ];
        $delivery_list = M('DeliveryAddress')->where($where)->order('is_default desc, id asc')->select();
        $this->response['code'] = 1;
        $this->response['msg'] = 'success';
        $this->response['data'] = $delivery_list;
        echo json_encode($this->response);
        exit;

    }

    public function getReceiveList() {
        $client_id = $client_id = session('hkwcd_user.user_id');
        $where = [
            'client_id' => $client_id,
            'status' => 1,
        ];
        $receive_list = M('ReceiveAddress')->where($where)->order('is_default desc, id asc')->select();
        $this->response['code'] = 1;
        $this->response['msg'] = 'success';
        $this->response['data'] = $receive_list;
        echo json_encode($this->response);
        exit;

    }

    public function getDefaultDelivery() {
        $default_delivery = M('DeliveryAddress')->where(['status' => 1, 'is_default' => 1])->find();
        $this->response['code'] = 1;
        $this->response['msg'] = 'success';
        $this->response['data'] = $default_delivery;
        echo json_encode($this->response);
        exit;
    }

    public function getDefaultReceive() {
        $default_receive = M('ReceiveAddress')->where(['status' => 1, 'is_default' => 1])->find();
        $this->response['code'] = 1;
        $this->response['msg'] = 'success';
        $this->response['data'] = $default_receive;
        echo json_encode($this->response);
        exit;
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
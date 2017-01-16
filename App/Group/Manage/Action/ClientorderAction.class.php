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

    }

    public function delivery() {

    }

    public function doDelivery() {

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
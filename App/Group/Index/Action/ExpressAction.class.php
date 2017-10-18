<?php

class ExpressAction extends Action {
	 
	
	public function zh() {
		$this->common(); 
		$this->display();
	}
	
	public function en() {
		$this->common(); 
		$this->display();
	}
	
	public function common() {
		$dda = I('dda', '', 'trim');
		$data = M('express')->where(array('dda'=> $dda ))->find(); 
		if($data['status']=='0'){  
			$child= M('expressdetail')->where(array('fkid' => $data['id']))->order('id DESC')->select();  
		}
		$this->data = $data; 
		$this->vlist = $child;  
	}

	public function query() {
	    $order_id_list = I("order_id", "", "trim");
        $order_id_list = explode(";", $order_id_list);
//        var_dump($order_id_list);exit;
        $order_list = [];
        $order_express_list = [];
        $all_msg = "";
        $msg = [];
        if( count($order_id_list) >= 1 && $order_id_list[0] !== "" ) {
            if( count($order_id_list) > 5 ) {
                $all_msg = "抱歉,最多只能查询五条纪录,运单号之间用“;”分隔开";
            } else {
                foreach( $order_id_list as $k => $v ) {
                    $order = M("ClientOrder")->where(['order_num' => $v])->find();
                    if( $order['self_express'] == 0 ) {
                        $trace_result = S('hkwcd_trace_result_' . $order['order_num']);
                        if (!$trace_result) {
                            $trace_result = query_express($order['express_type'], $order['express_order_num']);
                            S('hkwcd_trace_result_' . $order['order_number'], $trace_result, 7200);
                        }
                        $trace_result_array = json_decode($trace_result, true);
                        if ($trace_result_array['message'] == 'ok') {
                            $msg[$k] = "";
                        } else {
                            $msg[$k] = '抱歉，运单'.$v.'暂无查询记录1';
                        }
                        $order_express_list[$k] = $trace_result_array;
                    } else {
                        $trace_result = M("ClientOrderSelfExpress")->where(['order_id' => $order['id']])->order("id desc")->select();
                        if( $trace_result ) {
                            $result["message"] = "ok";
                            $temp = [];
                            foreach( $trace_result as $v ) {
                                $temp[] = [
                                    "time" => $v["time"],
                                    "context" => $v["content"],
                                    "remark" => $v["remark"],
                                ];
                            }
                            $result["data"] = $temp;
                            $msg[$k] = "";
                        } else {
                            $result = [];
                            $msg[$k] = '抱歉，运单'.$v.'暂无查询记录2';
                        }
                        $order_express_list[$k] = $result;
                    }
                    $order_list[$k] = $order;
                }
            }
        } else {
            $all_msg = "请输入订单号";
        }

        $this->assign("all_msg", $all_msg);
        $this->assign("msg", $msg);
        $this->assign("order_list", $order_list);
        $this->assign("order_express_list", $order_express_list);
        $this->display();
    }
}


?>
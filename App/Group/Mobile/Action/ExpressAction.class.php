<?php

class ExpressAction extends Action {
	
	public function index() { 
	 
		$dda = I('dda', '', 'trim');
		if (IS_POST) {
//            var_dump($dda);exit;
//            $result = $this->old_query($dda);
            $result = $this->new_query($dda);
			$this->data = $result['data'];
			$this->vlist = $result['child'];
		}
		$this->dda = $dda; 
		$this->display();
	}

	private function old_query($dda) {
        $data = M('express')->where(array('dda'=> $dda ))->find();
        if($data['status']=='0'){
            $child= M('expressdetail')->where(array('fkid' => $data['id']))->order('id DESC')->select();
        }
        return ['data' => $data, "child" => $child];
    }

    private function new_query($dda) {
        $child = "";
        $order = M("ClientOrder")
            ->where(['order_num' => $dda, "status" => 1])
            ->find();
        if( empty($order) ) {
//            echo M("ClientOrder")->getLastSql();exit;
            return ['data' => "", "child" => ""];
        }
//        echo M("ClientOrder")->getLastSql();exit;
//        var_dump($dda);exit;
        if( $order['self_express'] == 0 ) {
            $trace_result = S('hkwcd_trace_result_' . $order['order_num']);
//            $trace_result = [];
            if (!$trace_result) {
                $trace_result = query_express($order['express_type'], $order['express_order_num']);
                S('hkwcd_trace_result_' . $order['order_number'], $trace_result, 7200);
            }
            $trace_result_array = json_decode($trace_result, true);
//            var_dump($trace_result_array);exit;
            if ($trace_result_array['message'] == 'ok') {
                $child = $trace_result_array['data'];
            }
        } else {
            $trace_result = M("ClientOrderSelfExpress")->where(['order_id' => $order['id']])->order("id desc")->select();
            if( $trace_result ) {
                $result["message"] = "ok";
                $temp = [];
                foreach( $trace_result as $dda ) {
                    $temp[] = [
                        "time" => $dda["time"],
                        "context" => $dda["content"],
                        "remark" => $dda["remark"],
                    ];
                }
                $child = $temp;
            }
        }
        $data = [
            "dda" => $order["order_num"],
            "leix" => "华度货运",
            "gjj" => $order["receive_country_name"] . $order["receive_country_en_name"],
            "lmdd" => $order["receive_city"],
            "ckh" => $order["receive_postal_code"],
            "qjrq" => $order["express_time"],
        ];
//        var_dump($child);exit;
        return ['data' => $data, "child" => $child];
    }
}


?>
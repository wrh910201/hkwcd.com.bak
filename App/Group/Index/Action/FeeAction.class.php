<?php
/**
 * Created by PhpStorm.
 * User: Wrh
 * Date: 2017/4/6
 * Time: 21:38
 */

class FeeAction extends BaseAction  {

    public function index() {
        $this->client_id = session('hkwcd_user.user_id');
        $this->client = M('Client')->where(['status' => 1, 'id' => $this->client_id])->find();
        $statement = M("Dictionary")
            ->where(["id" => 1])
            ->find();
        $this->content = $statement["content"];
        $this->content = explode("\n", $this->content);
//        var_dump($this->content);exit;
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

        $country = M('Country')->where(['id' => $this->client['single_country_id']])->find();
        $this->assign('country', $country);

        $this->assign('title', '运费预算');
        $this->display();
    }

    public function calculate() {
        if( IS_POST ) {
            $this->client_id = session('hkwcd_user.user_id');
            $country_id = I('post.country_id', 0, 'intval');
            $package_type = I('post.package_type');
            $total_weight = I('post.total_weight', 0, 'floatval');
            $total_rate = I('post.total_rate', 0, 'floatval');

            $this->client_id = session('hkwcd_user.user_id');
            $this->client = M('Client')->where(['status' => 1, 'id' => $this->client_id])->find();
            $group_id = $this->client['group_id'];
            $real_weight = $total_weight > $total_rate ? $total_weight : $total_rate;
            $result = [];
            if( $this->client['single_country'] == 0 ) {

                $result = $this->_multiCountry($country_id, $group_id, $real_weight, $package_type);

            } else {
                $data = M('ClientPirceDetail')->query(
"SELECT c.name,c.en_name, cpd.status, cpd.price, cpd.min_weight, cpd.max_weight, cpd.per_kilo, country.name as country_name, country.ename as country_ename, cp.prescription, c.remark
 FROM `hx_client_price_detail` as cpd
 left join hx_client_price as cp on cp.id = cpd.price_id
 left join hx_channel as c on c.id = cp.channel_id
 left join hx_country as country on country.id = cpd.country_id
 where cpd.country_id = {$country_id} and cp.client_id = {$this->client_id} and cp.type = {$package_type}
 order by cpd.min_weight");
//                var_dump($data);
//                echo M('ClientPirceDetail')->getLastSql();exit;
                foreach($data as $k => $v) {
                    if( $v['status'] == 1 ) {
                        if (sprintf("%.1f", $real_weight) == $v['min_weight']) {
                            $v['real_weight'] = $real_weight;
                            $result[] = $v;
                        }
                    }
                }
                if( empty($result) ) {
                    foreach($data as $k => $v) {
                        if( $v['status'] == 2 ) {
                            if (sprintf("%.1f", $real_weight) >= $v['min_weight'] && sprintf("%.1f", $real_weight) <= $v['max_weight']) {
                                $v['price'] = $v['price'] * $real_weight;
                                $v['real_weight'] = $real_weight;
                                $result[] = $v;
                            }
                        }
                    }
                }

            }

            $this->response['code'] = 1;
            $this->response['msg'] = '成功';
            $this->response['data'] = $result;

        } else {
            $this->response['msg'] = '系统繁忙，请稍后重试';
        }
        echo json_encode($this->response);
        exit;
    }

    private function _multiCountry($country_id, $group_id, $real_weight, $package_type) {
        //是否有满足单独重量的纪录
//        var_dump($real_weight);exit;
        $temp_weight = floor($real_weight);
        $diff = $real_weight - $temp_weight;
//        var_dump($diff);exit;
        if( $diff == 0 ) {
            //整数，无需处理
        } else {
            if( $diff > 0.5 ) {
                $real_weight = $temp_weight + 1;
            } elseif( $diff < 0.5 ) {
                $real_weight = $temp_weight + 0.5;
            }
        }

        $data = M('ChannelMapPrice')->query(
            "SELECT c.name,c.en_name, cm.has_extra_fee, cmp.status, cmp.price, cmp.min_weight, cmp.max_weight, cmp.per_kilo, country.name as country_name, country.ename as country_ename, r.prescription,c.remark
 FROM `hx_channel_map_price` as cmp
 left join hx_channel_map as cm on cmp.map_id = cm.id
 left join hx_region as r on r.id = cmp.region_id
 left join hx_region_map as rm on rm.region_id = r.id
 left join hx_channel as c on c.id = cm.channel_id
 left join hx_country as country on country.id = rm.country_id
 where rm.country_id = {$country_id} and cm.group_id = {$group_id} and cm.type = {$package_type} and cmp.status = 1 and min_weight = {$real_weight}
 order by cmp.min_weight");
//                echo M('ChannelMapPrice')->getLastSql();exit;
//                var_dump($data);
        if( $data ) {
            foreach( $data as $k => $v ) {
                $data[$k]['real_weight'] = $v['min_weight'];
            }
            $result = $data;
        } else {
            $data = M("ChannelMapPrice")->query(
                "SELECT c.name,c.en_name, cm.has_extra_fee, cmp.status, cmp.price, cmp.min_weight, cmp.max_weight, cmp.per_kilo, country.name as country_name, country.ename as country_ename, r.prescription,c.remark
 FROM `hx_channel_map_price` as cmp
 left join hx_channel_map as cm on cmp.map_id = cm.id
 left join hx_region as r on r.id = cmp.region_id
 left join hx_region_map as rm on rm.region_id = r.id
 left join hx_channel as c on c.id = cm.channel_id
 left join hx_country as country on country.id = rm.country_id
 where rm.country_id = {$country_id} and cm.group_id = {$group_id} 
 and cm.type = {$package_type} and cmp.status = 2 and cmp.min_weight < {$real_weight} and cmp.max_weight >= {$real_weight} 
 order by cmp.min_weight");
//            echo M('ChannelMapPrice')->getLastSql();exit;
//            var_dump($data);exit;
            if( empty($data) ) {
                $data = M("ChannelMapPrice")->query(
                    "SELECT c.name,c.en_name, cm.has_extra_fee, cmp.status, cmp.price, cmp.min_weight, cmp.max_weight, cmp.per_kilo, country.name as country_name, country.ename as country_ename, r.prescription,c.remark
 FROM `hx_channel_map_price` as cmp
 left join hx_channel_map as cm on cmp.map_id = cm.id
 inner join hx_region as r on r.id = cmp.region_id
 inner join hx_region_map as rm on rm.region_id = r.id
 left join hx_channel as c on c.id = cm.channel_id
 left join hx_country as country on country.id = rm.country_id
 where rm.country_id = {$country_id} and cm.group_id = {$group_id} 
 and cm.type = {$package_type} and cmp.status = 3 and cmp.max_weight < {$real_weight} 
 order by cmp.min_weight");
            }
        }
        if( $data ) {
            foreach($data as $k => $v) {
                if( $v['status'] == 2 ) {
                    if (sprintf("%.1f", $real_weight) >= $v['min_weight'] && sprintf("%.1f", $real_weight) <= $v['max_weight']) {
                        $v['price'] = $v['price'] * $real_weight;
                        $v['real_weight'] = $real_weight;
                        $result[] = $v;
                    }
                }
                if( $v["status"] == 3 ) {
                    $v['price'] = $v['price'] * $real_weight;
                    $v['real_weight'] = $real_weight;
                    $result[] = $v;
                }
            }
        }
        return $result;
    }

}
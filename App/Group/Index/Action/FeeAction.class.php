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

        $this->assign('title', '运费预算');
        $this->display();
    }

    public function calculate() {
        if( IS_POST ) {
            $country_id = I('post.country_id', 0, 'intval');
            $package_type = I('post.package_type');
            $total_weight = I('post.total_weight', 0, 'floatval');
            $total_rate = I('post.total_rate', 0, 'floatval');

            $this->client_id = session('hkwcd_user.user_id');
            $this->client = M('Client')->where(['status' => 1, 'id' => $this->client_id])->find();
            $group_id = $this->client['group_id'];
            if( $this->client['single_country'] == 0 ) {

                $data = M('ChannelMapPrice')->query("SELECT c.name,c.en_name, cmp.status, cmp.price, cmp.min_weight, cmp.max_weight, cmp.per_kilo FROM `hx_channel_map_price` as cmp
 left join hx_channel_map as cm on cmp.map_id = cm.id
 left join hx_region as r on r.id = cmp.region_id
 left join hx_region_map as rm on rm.region_id = r.id
 left join hx_channel as c on c.id = cm.channel_id
 where rm.country_id = {$country_id} and cm.group_id = {$group_id} and cm.type = {$package_type}
 order by cmp.min_weight");
                var_dump($data);

            } else {
                $data = M('ClientPrice')
                    ->where(['type' => $package_type, 'client_id' => $this->client_id])
                    ->select();
                var_dump($data);
            }

            $this->response['code'] = 1;
            $this->response['msg'] = '成功';
            $this->response['data'] = $_POST;

        } else {
            $this->response['msg'] = '系统繁忙，请稍后重试';
        }
        echo json_encode($this->response);
        exit;
    }

}
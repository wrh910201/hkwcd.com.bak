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

}
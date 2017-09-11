<?php
/**
 * Created by PhpStorm.
 * User: Wrh
 * Date: 2017/1/15
 * Time: 15:44
 */

class RemoteAction extends Action {
    public $key_prefix = 'hkwcd_remote_country_';

    public function index() {
        $cate['pid'] = '13';
        $this->assign('cate', $cate);

        $this->title = '偏远地区查询';
        $country_list = S($this->key_prefix.'ch');
//        $country_list = null;
        if( $country_list ) {
            $this->assign('country_list', $country_list);
        } else {
            $this->_getCountryList();
            $country_list = S($this->key_prefix.'ch');
            $this->assign('country_list', $country_list);
        }
        $this->display();

    }

    public function query() {
        $cate['pid'] = '13';
        $this->assign('cate', $cate);
        $country_list = S($this->key_prefix.'ch');
        if( $country_list ) {
            $this->assign('country_list', $country_list);
        } else {
            $this->_getCountryList();
            $country_list = S($this->key_prefix.'ch');
            $this->assign('country_list', $country_list);
        }

        $ccount = I('ccount');
        $code = I('code');
        $city = I('city');
        $this->title = '偏远地区查询';
        $this->is_result = false;
        $param = [
            'ccount' => $ccount,
//            'countCode2' => $countCode2,
            'code' => $code,
            'city' => $city,
        ];
        $query_result = S($this->key_prefix.'_result_'.md5(json_encode($param)));
        if( !$query_result ) {
//        var_dump($param);exit;
//            $url = 'http://exp.hecny.com/serch_remot.action';
            $url = "http://exp.hecny.com/exp/mainIndex/selectRemote.do";
            $result = post($url, $param);
//            var_dump($result);exit;
            $query_result = json_decode($result, true);
//            $pattern = '#<table width="100%" border="0" align="left" cellpadding="1" cellspacing="1" class="tablelistcontent ">(.*)<\/table>#s';
//            preg_match($pattern, $result, $matches);
//            $query_result = mb_convert_encoding($matches[1], 'utf-8', 'gbk');
            if ($query_result) {
                S($this->key_prefix . '_result_' . md5(json_encode($param)), $query_result, 3600 * 24 * 3);
            }
        }
        $this->assign('query_result',$query_result);
        $this->is_result = true;
        $this->display('index');
    }

    private function _getCountryList() {
        $url = 'http://exp.hecny.com/serch_remote.html';
        $result = get($url);
        $pattern = '#<select class="chosen-select select-box inline"  name="ccount" id="ccount" style="width:250px;">(.*)<\/select>#s';
        preg_match($pattern, $result, $matches);
//        $en_country_list = mb_convert_encoding($matches[1], 'utf-8', 'gbk');
        $en_country_list = $matches[1];
        if( $en_country_list ) {
            S($this->key_prefix.'en', $en_country_list, 3600 * 24 * 3);
        }

        $pattern = '#<select class="chosen-select select-box inline"  name="ccount" id="ccount" style="width:250px;">(.*)<\/select>#s';
        preg_match($pattern, $result, $matches);
//        $ch_country_list = mb_convert_encoding($matches[1], 'utf-8', 'gbk');
        $ch_country_list = $matches[1];
        S($this->key_prefix.'ch', $ch_country_list, 3600 * 24 * 3);
    }

}
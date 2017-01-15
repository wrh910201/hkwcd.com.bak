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

        $countCode2 = I('countCode2');
        $code = I('code');
        $city = I('city');
        $this->title = '偏远地区查询';
        $this->is_result = false;
        $param = [
            'countCode' => '',
            'countCode2' => $countCode2,
            'code' => $code,
            'city' => $city,
        ];
        $query_result = S($this->key_prefix.'_result_'.md5(json_encode($param)), 3600 * 24 * 3);
        if( !$query_result ) {
//        var_dump($param);exit;
            $url = 'http://exp.hecny.com/serch_remot.action';
            $result = post($url, $param);
            $pattern = '#<table width="100%" border="0" align="left" cellpadding="1" cellspacing="1" class="tablelistcontent ">(.*)<\/table>#s';
            preg_match($pattern, $result, $matches);
            $query_result = mb_convert_encoding($matches[1], 'utf-8', 'gbk');
            if ($query_result) {
                S($this->key_prefix . '_result_' . md5(json_encode($param)), $query_result);
            }
        }
        $this->assign('query_result',$query_result);
        $this->is_result = true;
        $this->display('index');
    }

    private function _getCountryList() {
        $url = 'http://exp.hecny.com/serch_remote.html';
        $result = get($url);
        $pattern = '#<select class="dlk" name="countCode">(.*)<\/select>#s';
        preg_match($pattern, $result, $matches);
        $en_country_list = mb_convert_encoding($matches[1], 'utf-8', 'gbk');
        if( $en_country_list ) {
            S($this->key_prefix.'en', $en_country_list, 3600 * 24 * 3);
        }

        $pattern = '#<select class="dlk" name="countCode2">(.*)<\/select>#s';
        preg_match($pattern, $result, $matches);
        $ch_country_list = mb_convert_encoding($matches[1], 'utf-8', 'gbk');
        S($this->key_prefix.'ch', $ch_country_list, 3600 * 24 * 3);
    }

}
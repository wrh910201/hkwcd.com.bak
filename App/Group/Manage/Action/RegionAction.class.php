<?php
/**
 * Created by PhpStorm.
 * User: Wrh
 * Date: 2016/9/27
 * Time: 18:18
 */

class RegionAction extends CommonAction {

    /**
     * 地区列表
     */
    public function index() {
        $keyword = I('keyword', '', 'htmlspecialchars,trim');//关键字
        $where = array();
        if (!empty($keyword)) {
            $where['alias'] = ['like', $keyword];
        }

        $where['status'] = 1;
        //分页
        import('ORG.Util.Page');
        $count = M('region')->where($where)->count();

        $page = new Page($count, 10);
        $limit = $page->firstRow. ',' .$page->listRows;
        $list = M('region')->where($where)->order('id')->limit($limit)->select();

        $this->page = $page->show();
        $this->region_list = $list;
        $this->type = '自定义地区列表';
        $this->keyword = $keyword;


        $this->display();
    }

    public function add() {

        $where = array('pid' => 0,'types'=>0);
        $country_list = M('country')->where($where)->order('sort,id')->select();
        $this->assign('country_list', $country_list);

        $this->type = '添加自定义地区';
        $this->display();
    }

    public function doAdd() {
        if( IS_POST ) {
            $data['alias'] = I('post.name', '', 'htmlspecialchars,trim');
            if( empty($data['alias']) ) {
                $this->error('请输入地区名称');
            }
            if( M('region')->add($data) ) {
                $this->success('添加地区成功');
            } else {
                $this->error('添加地区失败');
            }
        }
    }

    public function edit() {
        $id = I('get.id', 0, 'intval');
        $region = M('region')->where(['id' => $id])->find();
        if( empty($region) ) {
            $this->error('地区不存在');
            exit;
        }

        $country_list = M('regionMap')
            ->field('hx_region_map.id, c.name, c.ename, c.id as country_id')
            ->where(['region_id' => $id])
            ->join('left join hx_country as c on c.id = hx_region_map.country_id')
            ->select();
//            ->buildSql();
//        var_dump($country_list);exit;

        $this->assign('country_list', $country_list);
        $this->assign('region', $region);
        $this->type = '地区详情';

        $this->display();
    }

    public function doEdit() {
        $id = I('post.id', 0, 'intval');
        $region = M('region')->where(['id' => $id])->find();
        if( empty($region) ) {
            $this->error('地区不存在');
            exit;
        }
        $data['alias'] = I('post.name', '', 'htmlspecialchars,trim');
        if( empty($data['alias']) ) {
            $this->error('请输入地区名称');
        }
        if( M('region')->where(['id' => $id])->save($data) ) {
            $this->success('编辑地区成功');
        } else {
            $this->error('编辑地区失败');
        }
    }

    public function country() {
        $rid = I('get.rid', 0, 'intval');
        $region = M('region')->where(['id' => $rid])->find();
        if( empty($region) ) {
            $this->error('地区不存在');
            exit;
        }

        $current_list = M('regionMap')->where(['region_id' => $rid])
            ->join('left join hx_country as c on c.id = hx_region_map.country_id')
            ->select();

        $where = array('pid' => 0,'types'=>0);
        $country_list = M('country')->where($where)->order('sort,id')->select();

        $this->assign('region', $region);
        $this->assign('current_list', $current_list);
        $this->assign('country_list', $country_list);

        $this->type = '添加国家';
        $this->display();
    }

    public function doCountry() {
        $rid = I('post.rid', 0, 'intval');
        $region = M('region')->where(['id' => $rid])->find();
        if( empty($region) ) {
            $this->error('地区不存在');
            exit;
        }
    }

    public function addCountry() {
        $rid = I('get.rid', 0, 'intval');
        $region = M('region')->where(['id' => $rid])->find();
        if( empty($region) ) {
            $this->error('地区不存在');
            exit;
        }
        $idArr = I('key',0 , 'intval');
        if (!is_array($idArr)) {
            $this->error('请选择要添加的国家');
        }
        $transaction = true;
        foreach( $idArr as $v ) {
            $country_exists = M('Country')->where(['id' => $v, 'pid' => 0])->find();
            if( empty($country_exists) ) {
                continue;
            }
            $map = [
                'region_id' => $rid,
                'country_id' => $v,
                'status' => 1,
            ];
            $exists = M('RegionMap')->where($map)->find();
            if( $exists ) {
                continue;
            } else {
                //添加
                $result = M('RegionMap')->add($map);
            }
            if( !$result ) {
                $transaction = false;
                break;
            }
        }
        if( $transaction ) {
            $this->success('添加到自定义地区成功');
        } else {
            $this->success('添加到自定义地区失败或部分失败');
        }
    }

    public function del() {
        $id = I('get.id', 0, 'intval');
        $batchFlag = I('get.batchFlag', 0, 'intval');
        //批量删除
        if ($batchFlag) {
            $this->multiDel();
            return;
        }

        $region = M('Region')->where(['id' => $id])->find();
        if( empty($region) || $region['status'] == 0 ) {
            $this->error('自定义地区不存在');
            exit;
        }

        $result = M('Region')->where(['id' => $id])->delete();
        if( $result ) {
            $this->success('删除自定义地区成功');
        } else {
            $this->error('删除自定义地区失败');
        }
    }

    public function multiDel() {
        $idArr = I('key',0 , 'intval');
        if (!is_array($idArr)) {
            $this->error('请选择要彻底删除的项');
        }

        $where = array('id' => array('in', $idArr));
        $result = M('Region')->where($where)->delete();
        if( $result ) {
            $this->success('删除自定义地区成功');
        } else {
            $this->error('删除自定义地区失败');
        }
    }

    public function delCountry() {
        $rid = I('get.rid', 0, 'intval');
        $id = I('get.id', 0, 'intval');
        $region = M('Region')->where(['id' => $rid])->find();
        if( empty($region) || $region['status'] == 0 ) {
            $this->error('自定义地区不存在');
            exit;
        }
        $map = [
            'region_id' => $rid,
            'id' => $id,
        ];
        $result = M('RegionMap')->where($map)->limit(1)->delete();
//        echo M('RegionMap')->getLastSql();exit;
        if( $result ) {
            $this->success('删除下属国家成功');
        } else {
            $this->error('删除下属国家失败');
        }
    }

    public function multiDelCountry() {
        $rid = I('get.rid', 0, 'intval');
        $id = I('key');
        $region = M('Region')->where(['id' => $rid])->find();
        if( empty($region) || $region['status'] == 0 ) {
            $this->error('自定义地区不存在');
            exit;
        }
        $idArr = I('key',0 , 'intval');
        if (!is_array($idArr)) {
            $this->error('请选择要彻底删除的项');
        }
        $where = array('id' => array('in', $idArr));

        $result = M('RegionMap')->where($where)->delete();
        if( $result ) {
            $this->success('删除下属国家成功');
        } else {
            $this->error('删除下属国家失败');
        }
    }
}
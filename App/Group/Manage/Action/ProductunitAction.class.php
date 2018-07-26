<?php
/**
 * Created by PhpStorm.
 * User: wrh42
 * Date: 2018/7/27
 * Time: 5:46
 */

class ProductunitAction extends CommonContentAction {


    public function index() {
        $keyword = I('keyword', '', 'htmlspecialchars,trim');//关键字
        $where = array();
        $condition = array();
        if (!empty($keyword)) {
            $condition['name'] = ['like', $keyword];
            $condition['en_name'] = ['like', $keyword];
            $condition['_logic'] = 'OR';
            $where['_complex'] = $condition;
        }
        $where['status'] = 1;
        //分页
        import('ORG.Util.Page');
        $count = M('ProductUnit')->where($where)->count();

        $page = new Page($count, 10);
        $limit = $page->firstRow. ',' .$page->listRows;
        $list = M('ProductUnit')->where($where)->order('id')->limit($limit)->select();

        $this->page = $page->show();
        $this->unit_list = $list;
        $this->type = '商品单位列表';
        $this->keyword = $keyword;

        $this->display();
    }

    public function add() {
        $this->type = '添加商品单位';
        $this->display();
    }

    public function doAdd() {
        if( !IS_POST ) {
            exit;
        }
        $data['name'] = I('post.name', '', 'trim');
        $data['en_name'] = I('post.en_name', '', 'trim');

        if( $data['name'] == '' ) {
            $this->error('请输入商品单位名称');
        }

        if( $data['en_name'] == '' ) {
            $this->error('请输入商品单位英文名称');
        }
        

        if( M('ProductUnit')->add($data) ) {
            $this->success('添加商品单位成功', U('Productunit/index'));
        } else {
            $this->error('添加商品单位失败');
        }

    }

    public function edit() {
        $id = I('get.id', 0, 'intval');
        $unit = M('ProductUnit')->where(['id' => $id])->find();
        if( empty($unit) ) {
            $this->error('商品单位不存在');
            exit;
        }
        $this->assign('unit', $unit);
        $this->type = '编辑商品单位';
        $this->display();
    }

    public function doEdit() {
        $id = I('post.id', 0, 'intval');
        $currency = M('ProductUnit')->where(['id' => $id])->find();
        if( empty($currency) ) {
            $this->error('商品单位不存在');
            exit;
        }
        $data['name'] = I('post.name', '', 'trim');
        $data['en_name'] = I('post.en_name', '', 'trim');

        if( $data['name'] == '' ) {
            $this->error('请输入商品单位名称');
        }

        if( $data['en_name'] == '' ) {
            $this->error('请输入商品单位英文名称');
        }
        $result = M('ProductUnit')->where(['id' => $id])->save($data);
        if( is_numeric($result) ) {
            $this->success('编辑商品单位成功');
        } else {
            $this->error('编辑商品单位失败');
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

        $currency = M('ProductUnit')->where(['id' => $id])->find();
        if( empty($currency) ) {
            $this->error('商品单位不存在');
            exit;
        }
        $data['status'] = 0;
        if( M('ProductUnit')->where(['id' => $id])->delete() ) {
            $this->success('商品单位已被删除');
        } else {
            $this->error('删除失败');
        }
    }

    public function multiDel() {
        $idArr = I('key',0 , 'intval');
        if (!is_array($idArr)) {
            $this->error('请选择要删除的商品单位');
        }
        $where = array('id' => array('in', $idArr));
        $data['status'] = 0;
        if( M('ProductUnit')->where($where)->delete() ) {
            $this->success('商品单位已被删除');
        } else {
            $this->error('删除失败');
        }
    }

}
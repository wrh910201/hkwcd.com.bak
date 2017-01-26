<?php
/**
 * Created by PhpStorm.
 * User: Wrh
 * Date: 2016/10/14
 * Time: 16:07
 */

class CurrencyAction extends CommonContentAction {

    public function index() {
        $keyword = I('keyword', '', 'htmlspecialchars,trim');//关键字
        $where = array();
        $condition = array();
        if (!empty($keyword)) {
            $condition['name'] = ['like', $keyword];
            $condition['code'] = ['like', $keyword];
            $condition['_logic'] = 'OR';
            $where['_complex'] = $condition;
        }
        $where['status'] = 1;
        //分页
        import('ORG.Util.Page');
        $count = M('currency')->where($where)->count();

        $page = new Page($count, 10);
        $limit = $page->firstRow. ',' .$page->listRows;
        $list = M('currency')->where($where)->order('id')->limit($limit)->select();

        $this->page = $page->show();
        $this->currency_list = $list;
        $this->type = '币种列表';
        $this->keyword = $keyword;

        $this->display();
    }

    public function add() {
        $this->type = '添加币种';
        $this->display();
    }

    public function doAdd() {
        if( !IS_POST ) {
            exit;
        }
        $data['name'] = I('post.name', '', 'trim');
        $data['code'] = I('post.code', '', 'trim');
        $data['rate'] = I('post.rate', 1, 'floatval');
        $data['symbol'] = I('post.symbol', '', 'trim');

        if( $data['name'] == '' ) {
            $this->error('请输入币种名称');
        }

        if( $data['code'] == '' ) {
            $this->error('请输入币种编码');
        }

        if( $data['rate'] <= 0 ) {
            $this->error('请输入币种汇率');
        }

        if( M('currency')->add($data) ) {
            $this->success('添加币种成功', U('Currency/index'));
        } else {
            $this->error('添加币种失败');
        }

    }

    public function edit() {
        $id = I('get.id', 0, 'intval');
        $currency = M('currency')->where(['id' => $id])->find();
        if( empty($currency) ) {
            $this->error('币种不存在');
            exit;
        }
        $this->assign('currency', $currency);
        $this->type = '编辑币种';
        $this->display();
    }

    public function doEdit() {
        $id = I('post.id', 0, 'intval');
        $currency = M('currency')->where(['id' => $id])->find();
        if( empty($currency) ) {
            $this->error('币种不存在');
            exit;
        }
        $data['name'] = I('post.name', '', 'trim');
        $data['code'] = I('post.code', '', 'trim');
        $data['rate'] = I('post.rate', 1, 'floatval');
        $data['symbol'] = I('post.symbol', '', 'trim');

        if( $data['name'] == '' ) {
            $this->error('请输入币种名称');
        }

        if( $data['code'] == '' ) {
            $this->error('请输入币种编码');
        }

        if( $data['rate'] <= 0 ) {
            $this->error('请输入币种汇率');
        }
        $result = M('currency')->where(['id' => $id])->save($data);
        if( is_numeric($result) ) {
            $this->success('编辑币种成功');
        } else {
            $this->error('编辑币种失败');
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

        $currency = M('currency')->where(['id' => $id])->find();
        if( empty($currency) ) {
            $this->error('币种不存在');
            exit;
        }
        $data['status'] = 0;
        if( M('currency')->where(['id' => $id])->delete() ) {
            $this->success('币种已被删除');
        } else {
            $this->error('删除失败');
        }
    }

    public function multiDel() {
        $idArr = I('key',0 , 'intval');
        if (!is_array($idArr)) {
            $this->error('请选择要删除的币种');
        }
        $where = array('id' => array('in', $idArr));
        $data['status'] = 0;
        if( M('currency')->where($where)->delete() ) {
            $this->success('币种已被删除');
        } else {
            $this->error('删除失败');
        }
    }
}
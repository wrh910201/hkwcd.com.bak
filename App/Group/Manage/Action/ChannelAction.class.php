<?php
/**
 * Created by PhpStorm.
 * User: Wrh
 * Date: 2016/9/26
 * Time: 22:11
 */

class ChannelAction extends CommonContentAction {

    public function index() {

        //分页
        $keyword = I('keyword', '', 'htmlspecialchars,trim');//关键字
        $where = array();
        $condition = array();

        if (!empty($keyword)) {
            $condition['name'] = ['like', '%'.$keyword.'%'];
            $condition['en_name'] = ['like', '%'.$keyword.'%'];
            $condition['_logic'] = 'OR';
            $where['_complex'] = $condition;
        }
        $where['status'] = 1;
        import('ORG.Util.Page');
        $count = M('Channel')->where($where)->count();
        $page = new Page($count, 10);
        $limit = $page->firstRow . ',' . $page->listRows;
        $list = M('Channel')->where($where)->order('id')->limit($limit)->select();
        $this->page = $page->show();
        $this->vlist = $list;

        $this->keyword = $keyword;
        $this->type = '渠道列表';
        $this->display();
    }

    public function add() {
        $this->type = '添加渠道';
        $this->display();
    }

    public function doAdd() {
        $data['name'] = I('post.name', '', 'htmlspecialchars,trim');
        $data['en_name'] = I('post.en_name', '', 'htmlspecialchars,trim');
        $data['extra_payment'] = I('post.extra_payment');
        $data['prescription'] = I('post.prescription', '', 'htmlspecialchars,trim');
        $data['remark'] = I('post.remark', '', 'htmlspecialchars,trim');
        if( empty($data['name']) || empty($data['en_name']) ) {
            $this->error('请输入渠道名');
            exit;
        }
        $data['operator_id'] = session('yang_adm_uid');
        $result = M('Channel')->add($data);
        if( $result ) {
            $this->success('添加渠道成功',U(GROUP_NAME. '/Channel/index'));
        } else {
            $this->error('添加渠道失败');
        }
    }

    public function edit()
    {
        $id = I('get.id', 0, 'intval');
        $channel = M('Channel')->where(['id' => $id])->find();
        if( empty($channel) || $channel['status'] == 0 ) {
            $this->error('渠道不存在');
            exit;
        }
        $this->assign('channel', $channel);
        $this->type = '编辑渠道';
        $this->display();
    }

    public function doEdit()
    {
        $id = I('post.id', 0, 'intval');
        $channel = M('Channel')->where(['id' => $id])->find();
        if( empty($channel) || $channel['status'] == 0 ) {
            $this->error('渠道不存在');
            exit;
        }
        $data['name'] = I('post.name', '', 'htmlspecialchars,trim');
        $data['en_name'] = I('post.en_name', '', 'htmlspecialchars,trim');
        $data['extra_payment'] = I('post.extra_payment');
        $data['prescription'] = I('post.prescription', '', 'htmlspecialchars,trim');
        $data['remark'] = I('post.remark', '', 'htmlspecialchars,trim');
        if( empty($data['name']) || empty($data['en_name']) ) {
            $this->error('请输入渠道名');
            exit;
        }
        $result = M('Channel')->where(['id' => $id])->save($data);
        if( is_numeric($result) ) {
            $this->success('编辑渠道成功');
        } else {
            $this->error('编辑渠道失败');
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
        $channel = M('Channel')->where(['id' => $id])->find();
        if( empty($channel) || $channel['status'] == 0 ) {
            $this->error('渠道不存在');
            exit;
        }

        //是否有下属用户
        $channel = M('ChannelMap')->where(['channel_id' => $id])->find();
        if( $channel ) {
            $this->error('该渠道下使用中，无法删除');
            exit;
        }
        $result = M('Channel')->where(['id' => $id])->save(['status' => 0]);
        if( is_numeric($result) ) {
            $this->success('删除渠道成功');
        } else {
            $this->error('删除渠道失败');
        }
    }

    public function multiDel() {
        $idArr = I('key',0 , 'intval');
        if (!is_array($idArr)) {
            $this->error('请选择要彻底删除的项');
        }

        $channel_where = ['channel_id' => ['in', $idArr]];
        //是否有下属用户
        $channel = M('ChannelMap')->where($channel_where)->select();
        if( $channel ) {
            $this->error('选中的渠道使用中，无法删除');
            exit;
        }

        $where = array('id' => array('in', $idArr));
        $result = M('Channel')->where($where)->save(['status' => 0]);
        if( is_numeric($result) ) {
            $this->success('删除渠道成功');
        } else {
            $this->error('删除渠道失败');
        }
    }
}
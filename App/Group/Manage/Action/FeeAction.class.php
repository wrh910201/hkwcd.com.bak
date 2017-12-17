<?php
/**
 * Created by PhpStorm.
 * User: wrh42
 * Date: 2017/12/17
 * Time: 16:41
 */

class FeeAction extends CommonContentAction {

    public function index() {

        $content = M("Dictionary")->where(["id" => 1])
            ->find();
        $this->assign("content", $content["content"]);
        $this->assign("dictionary_id", 1);
        $this->assign("type", "运费声明");
        $this->display();

    }

    public function addStatement() {
        if( IS_POST ) {
//            var_dump(I("post."));exit;
            $id = I("id");
            $content = I("content");
            $map = [
                "id" => $id,
            ];
            $data = [
                "content" => $content,
            ];
            $result = M("Dictionary")
                ->where("id = {$id}")
                ->save($data);
            if( is_numeric($result) ) {
                $this->success("操作成功");
            } else {
                $this->error("操作失败");
            }


        }

    }

}

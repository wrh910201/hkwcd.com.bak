<?php if (!defined('THINK_PATH')) exit();?><style>
    fieldset {
        border: 0;
    }

    .box_c_item{
        padding: 5px 0 8px 0;
        line-height: 20px;
    }

    fieldset label.l_title {
        display: block;
        padding: 5px 2px;
        font-weight: bold;
        width: 136px;
        float: left;
        text-align: right;
    }
    .btn {
        font-size: 12px;
        display: inline-block;
        font-weight: normal;
        padding: 0 20px;
        height: 32px;
        line-height: 30px;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        cursor: pointer;
        font-family: "Helvetica Neue", "Luxi Sans", "DejaVu Sans", Tahoma, "Hiragino Sans GB", STHeiti, "Microsoft YaHei";
    }
    .btn_commit {
        color: #78cc40;
        border: 1px solid #82838a;
        background-color: #353b4b;
    }
    .btn_cancel {
        color: #000;

    }
    fieldset input.text_input,fieldset select, fieldset textarea {
        padding: 7px;
        font-size: 12px;
        background: #fff url('__PUBLIC__/usercenter/css/images/bg-form-field.gif') top left repeat-x;
        border: 1px solid #d5d5d5;
        color: #333;
    }
</style>
<!--<link href="__PUBLIC__/usercenter/css/style.css" rel="stylesheet" type="text/css" />-->
<form action="/Profile/reset.html" method="post">
        <fieldset>
            <div class="box_c_item">
                <label class="l_title">帐号：</label>
                <input class="text_input w300" type="text" name="username" placeholder="请输入帐号">
                <div class="clear"></div>
            </div>
            <div class="box_c_item">
                <label class="l_title">密码：</label>
                <input class="text_input w300" type="password" name="password" placeholder="请输入密码">
                <div class="clear"></div>
            </div>
            <div class="box_c_item">
                <label class="l_title"></label>
                <input class="text_input w300" type="text" name="vcode" placeholder="请输入验证码">
                <div style="height:30px; display: inline-block">
                    <img style="" src="/Public/verify.html" id="VCode" onclick="javascript:changeVcode(this);" />
                </div>
                <div class="clear"></div>
            </div>
            <div class="box_c_item">
                <label class="l_title"></label>
                <button class="btn btn_commit" type="button">确认</button> &nbsp;<a href="javascript:;" class="btn btn_cancel">取消</a>
                <div class="clear"></div>
            </div>
        </fieldset>
    </form>
<script type="text/javascript">
    function login() {

    }

    function login_handler(response) {

    }
</script>
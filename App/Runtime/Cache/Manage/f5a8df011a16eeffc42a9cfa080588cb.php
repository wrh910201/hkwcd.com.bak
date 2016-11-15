<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>
    <link rel='stylesheet' type="text/css" href="__PUBLIC__/css/style.css" />
    <script type="text/javascript" src="__PUBLIC__/js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="__PUBLIC__/js/common.js"></script>
    <script type="text/javascript" src="__PUBLIC__/js/jquery.form.min.js"></script>
</head>
<body>
<div class="main">
    <div class="pos">添加客户</div>
    <div class="form">
        <form method='post' id="form_do" name="form_do" action="<?php echo U(GROUP_NAME. '/Client/doAdd');?>">
            <dl>
                <dt>用户名/username：</dt>
                <dd>
                    <input type="text" name="username" class="inp_one" id="username"/>
                    <span id="username-alert"></span>
                </dd>
            </dl>
            <dl>
                <dt>用户组/User Group：</dt>
                <dd>
                    <select name="group_id">
                        <?php if(is_array($group_list)): foreach($group_list as $key=>$v): ?><option value="<?php echo ($v["id"]); ?>"><?php echo ($v["name"]); ?>/<?php echo ($v["en_name"]); ?></option><?php endforeach; endif; ?>
                    </select>
                </dd>
            </dl>
            <dl>
                <dt>姓名/Name：</dt>
                <dd>
                    <input type="text" name="full_name" class="inp_one" />
                </dd>
            </dl>
            <!--<dl>-->
                <!--<dt>姓/Last Name：</dt>-->
                <!--<dd>-->
                    <!--<input type="text" name="last_name" class="inp_one" />-->
                <!--</dd>-->
            <!--</dl>-->
            <!--<dl>-->
                <!--<dt>名/First Name：</dt>-->
                <!--<dd>-->
                    <!--<input type="text" name="first_name" class="inp_one " />-->
                <!--</dd>-->
            <!--</dl>-->
            <dl>
                <dt>公司名称/Company：</dt>
                <dd>
                    <input type="text" name="company" class="inp_one inp_large" />
                </dd>
            </dl>
            <dl>
                <dt>C/O：</dt>
                <dd>
                    <input type="text" name="care_of" class="inp_one" />
                </dd>
            </dl>
            <dl>
                <dt>地址/Address：</dt>
                <dd>
                    <input type="text" name="address" class="inp_one inp_large" />
                </dd>
            </dl>
            <dl>
                <dt>地址2/Address 2：</dt>
                <dd>
                    <input type="text" name="address2" class="inp_one inp_large" />
                </dd>
            </dl>
            <dl>
                <dt>国家/Country：</dt>
                <dd>
                    <select name="country">
                        <?php if(is_array($country_list)): foreach($country_list as $key=>$v): ?><option value="<?php echo ($v["id"]); ?>"><?php echo ($v["name"]); ?>/<?php echo ($v["ename"]); ?></option><?php endforeach; endif; ?>
                    </select>
                </dd>
            </dl>
            <dl>
                <dt>城市/City：</dt>
                <dd>
                    <input type="text" name="city" class="inp_one" />
                </dd>
            </dl>
            <dl>
                <dt>省/State：</dt>
                <dd>
                    <input type="text" name="state" class="inp_one" />
                </dd>
            </dl>
            <dl>
                <dt>邮编/Zip Postal Code：</dt>
                <dd>
                    <input type="text" name="postal_code" class="inp_one" />
                </dd>
            </dl>
            <dl>
                <dt>座机/Phone：</dt>
                <dd>
                    <input type="text" name="phone" class="inp_one" />
                </dd>
            </dl>
            <dl>
                <dt>手机/Mobile：</dt>
                <dd>
                    <input type="text" name="mobile" class="inp_one" />
                </dd>
            </dl>
            <dl>
                <dt>传真/Fax：</dt>
                <dd>
                    <input type="text" name="fax" class="inp_one" />
                </dd>
            </dl>
            <dl>
                <dt>邮箱/Email：</dt>
                <dd>
                    <input type="text" name="email" class="inp_one" />
                </dd>
            </dl>
            <dl>
                <dt>代理/Agent：</dt>
                <dd>
                    <input type="text" name="agent" class="inp_one" />
                </dd>
            </dl>
            <dl>
                <dt>备注/Remark：</dt>
                <dd>
                    <input type="text" name="remark" class="inp_one inp_large" />
                </dd>
            </dl>
            <dl>
                <dt> 状态：</dt>
                <dd>
                    <input type="radio" name="islock" value="0" checked="checked"  />正常
                    &nbsp;&nbsp;
                    <input type="radio" name="islock" value="1" />锁定
                </dd>
            </dl>
            <div class="form_b">
                <input type="submit" class="btn_blue" id="submit" value="提 交">
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    $('#username').blur(function() {
        var url = "<?php echo U(GROUP_NAME. '/Client/checkUsername');?>";
        var username = $(this).val();
        if( username == '' ) {
            return;
        }
        var param = { "username":username };
        $.post(url, param, check_name_handler, 'json');
    });

    function check_name_handler(response) {
        $('#username-alert').text(response.msg);
    }
</script>

</body>
</html>
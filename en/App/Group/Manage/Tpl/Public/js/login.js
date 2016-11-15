function change_code(obj) { 
    $("#code").attr("src", verifyUrl + '#' + Math.random());
    return false;
}
   
    function ChkLogin() {
        if ($("#username").val() == '请输入用户名') {
            alert("用户名不能为空");
            return false;
        }

        if ($("#password").val() == '请输入账户密码') {
            alert("密码不能为空");
            return false;
        }

        if ($("#codeshow").val() == '请输入验证码') {
            alert("验证码不能为空");
            return false;
        }
    }
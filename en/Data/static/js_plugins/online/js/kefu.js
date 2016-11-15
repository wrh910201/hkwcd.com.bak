$(function () {
    $('.right_bar').click(function () {
        $(this).hide();
        $('#kefu').show();
    });
    $('#kefu .close').click(function () {
        $('.right_bar').show();
        $('#kefu').hide();
    });
});

var qqshow = ""; var strs = new Array(); strs = str.split("|");
for (i = 0; i < strs.length; i++) {
    qqshow += "<a target=\"_blank\" href=\"http://wpa.qq.com/msgrd?v=3&uin=" + strs[i] + "&site=qq&menu=yes\"><img border=\"0\" src=\"http://wpa.qq.com/pa?p=2:" + strs[i] + ":51\" alt=\"点击这里给我发消息\" title=\"点击这里给我发消息\" /></a><br />";
}
var strsww = new Array(); strsww = strww.split("|");
for (j = 0; j < strsww.length; j++) {
    qqshow += "<a target=\"_blank\" href=\"http://www.taobao.com/webww/ww.php?ver=3&touid=" + strsww[j] + "&siteid=cntaobao&status=1&charset=utf-8\"><img border=\"0\" src=\"http://amos.alicdn.com/online.aw?v=2&uid=" + strsww[j] + "&site=cntaobao&s=10&charset=UTF-8\" alt=\"点击这里给我发消息\" title=\"点击这里给我发消息\" /></a><br />";
}
$("#qqshow").html(qqshow).show;


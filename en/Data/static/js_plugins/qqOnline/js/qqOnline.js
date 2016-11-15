function scrollx(p){
var d = document,dd = d.documentElement,db = d.body,w = window,o = d.getElementById(p.id),oclose=d.getElementById(p.id+"_close"),ie6=!-[1,]&&!window.XMLHttpRequest,style,timer;p.l==undefined?p.l=0:p.l=p.l;p.t==undefined?p.t=0:p.t=p.t;p.m==undefined?p.m=0:p.m=p.m;
if(o){
var ow=o.offsetWidth;o.style.display="block";
o.style.cssText +=";position:"+(p.f&&!ie6?'fixed':'absolute');
if(p.m==1){	o.style.left='50%';//o.style.cssText +=';left:expression((documentElement.clientWidth+'+(-o.offsetWidth)+')/2 + "px")';//IE7
o.style.cssText +=';'+(p.t>=0?'top:'+p.t:'bottom:'+Math.abs(p.t))+'px';
o.style.marginLeft=ie6?'':-o.offsetWidth/2+'px';
}else if(p.m==-1){
o.style.cssText +=';'+(p.l>=0?'left:'+p.l:'right:'+Math.abs(p.l))+'px';
o.style.top='50%';o.style.marginTop=ie6?'':-o.offsetHeight/2+'px';
}else if(p.m==2){o.style.left='50%';o.style.top='50%';o.style.marginLeft=ie6?'':-o.offsetWidth/2+'px';o.style.marginTop=ie6?'':-o.offsetHeight/2+'px';}else{o.style.cssText +=";"+(p.l>=0?'left:'+p.l:'right:'+Math.abs(p.l))+'px;'+(p.t>=0?'top:'+p.t:'bottom:'+Math.abs(p.t))+'px';}

if(p.f&&ie6){
o.style.right='';o.style.bottom='';
if(p.m==1){o.style.cssText +=';left:expression(documentElement.scrollLeft + (documentElement.clientWidth+'+(-o.offsetWidth)+')/2 + "px")';
o.style.cssText +=';top:expression(documentElement.scrollTop +'+(p.t>=0?p.t:'document.documentElement.clientHeight+'+(p.t-o.offsetHeight))+'+ "px" )';}else if(p.m==-1){o.style.cssText +=';left:expression(documentElement.scrollLeft + '+(p.l>=0?p.l:'documentElement.clientWidth+'+(p.l-o.offsetWidth))+' + "px")';o.style.cssText +=';top:expression(documentElement.scrollTop +(documentElement.clientHeight+'+(-o.offsetHeight)+')/2+ "px" )';}else if(p.m==2){o.style.cssText +=';left:expression(documentElement.scrollLeft + (documentElement.clientWidth+'+(-o.offsetWidth)+')/2 + "px")';o.style.cssText +=';top:expression(documentElement.scrollTop +(documentElement.clientHeight+'+(-o.offsetHeight)+')/2+ "px" )';
}else{o.style.cssText +=';left:expression(documentElement.scrollLeft + '+(p.l>=0?p.l:'documentElement.clientWidth+'+(p.l-o.offsetWidth))+' + "px")';o.style.cssText +=';top:expression(documentElement.scrollTop +'+(p.t>=0?p.t:'document.documentElement.clientHeight+'+(p.t-o.offsetHeight))+'+ "px" )';
}
dd.style.cssText +=';background-image: url(about:blank);background-attachment:fixed;';

}else{
if(!p.f){
w.onresize = w.onscroll = function(){
clearInterval(timer);//alert(o.style.cssText);
timer = setInterval(function(){//双选择为了修复chrome 下xhtml解析时dd.scrollTop为 0
	var st = (dd.scrollTop||db.scrollTop),c;
	if(p.m==1){c = st - o.offsetTop + (p.t>=0?p.t:(w.innerHeight||dd.clientHeight)-o.offsetHeight+p.t);
	o.style.left=Math.abs(dd.clientWidth-o.offsetWidth)/2+'px';/*水平居中*/}else if(p.m==-1){
	c = Math.ceil(st - o.offsetTop + ((w.innerHeight||dd.clientHeight)-o.offsetHeight)/2);}else if(p.m==2){
	c = Math.ceil(st - o.offsetTop + ((w.innerHeight||dd.clientHeight)-o.offsetHeight)/2);
	o.style.left=Math.abs(dd.clientWidth-o.offsetWidth)/2+'px';}else{
	c = st - o.offsetTop + (p.t>=0?p.t:(w.innerHeight||dd.clientHeight)-o.offsetHeight+p.t);}
	if(c!=0){o.style.top = o.offsetTop + Math.ceil(Math.abs(c)/10)*(c<0?-1:1) + 'px';}else{clearInterval(timer);}
	},10)
}}}}
if(oclose){oclose.onclick=function(){o.style.display="none";}
//alert(o.style.cssText);
//setTimeout(function(){o.show()},2000)
}}
/**
id:内容id__l:横坐标位置(不写为0,负数靠右,正数靠左)__t:纵坐标位置(默认0,负数靠底,正数靠顶边)___f:1表示固定 0表示滚动___m:1 (2全居中(忽略l,t),1水平(忽略l),-1垂直(忽略t))
new scrollx({id:"KFView",t:1,f:1,m:1});
**/
var qqshow = "";
if (str != "") {
    var strs = new Array(); strs = str.split("|");
    for (i = 0; i < strs.length; i++) {
        qqshow += "<dd class=\"qq\"><a target=\"_blank\" href=\"http://wpa.qq.com/msgrd?v=3&uin=" + strs[i] + "&site=qq&menu=yes\"><img border=\"0\" src=\"http://wpa.qq.com/pa?p=2:" + strs[i] + ":51\" alt=\"Call me\" title=\"Call me\" /></a></dd>";
    }
}
//if (strww != "") {
//    var strsww = new Array(); strsww = strww.split("|");
//    for (j = 0; j < strsww.length; j++) {
//        qqshow += "<dd class=\"qq\"><a target=\"_blank\" href=\"http://www.taobao.com/webww/ww.php?ver=3&touid=" + strsww[j] + "&siteid=cntaobao&status=1&charset=utf-8\"><img border=\"0\" src=\"http://amos.alicdn.com/online.aw?v=2&uid=" + strsww[j] + "&site=cntaobao&s=10&charset=UTF-8\" alt=\"点击这里给我发消息\" title=\"点击这里给我发消息\" /></a></dd>";
//    }
//} 

if (strww != "") {
    var strsww = new Array(); strsww = strww.split("|");
    for (j = 0; j < strsww.length; j++) {
        qqshow += "<dd class=\"qq\"><a target=\"_blank\" href=\"skype:" + strsww[j] + "?call\" onclick=\"return skypeCheck();\"><img border=\"0\" src=\"http://mystatus.skype.com/smallclassic/" + strsww[j] + "\" alt=\"Call me\" title=\"Call me\" /></a></dd>";
    }
} 
$("#qqshow").html(qqshow).show;
if (document.getElementById("qqOnlineView")) { new scrollx({ id: "qqOnlineView", l: lr, t: 1, f: 1, m: -1 }); }


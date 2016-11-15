jQuery(function ($) {
    $('#SlideBoxItems').slideBox({
        duration: 0.3, //滚动持续时间，单位：秒
        easing: 'linear', //swing,linear//滚动特效
        delay: 5, //滚动延迟时间，单位：秒
        hideClickBar: false, //不自动隐藏点选按键 
        clickBarRadius: 10
    }); 
});
 
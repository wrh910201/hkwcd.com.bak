/**
 * Created by Carson on 2016/12/30.
 */
var vueSwiper = function (params) {
    var $tag = params.tag || 'swiper', $fatherCtn = params.fatherCtn || "#swiper",
        $imgArr = params.imgArr, $config = params.config || {
                direction: 'horizontal'
            },$easyClose = params.easyClose;
    console.log("imageArray", $imgArr);
    Vue.component($tag, {
        template: '<div id="vue-swiper" class="swiper-container" @click="easyClose"><div class="swiper-close" @click="closeSwiper"></div>' +
        '<div class="swiper-wrapper"><div class="swiper-slide flex-row flex-center" v-for="img in images">' +
        '<img :src="img.src"></div></div><slot></slot></div>',
        data: function () {
            return {
                images: $imgArr
            }
        },
        methods: {
            closeSwiper: function () {
                $("#swiper").hide().html("<swiper></swiper>");
                this.$emit('closeSwiper');
                console.log("close swiper");
                $("body").css({"overflow":"scroll"});
            },
            easyClose :function () {
                if($easyClose){
                    this.closeSwiper()
                }
            }
        }
    });
    new Vue({
        el: $fatherCtn
    });

    Swiper(".swiper-container", $config)
};
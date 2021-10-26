function isMobile(mobile) {
    var mobile = $.trim(mobile);
    if (!/^(13[0-9]|17[0-9]|15[0-9]|18[0-9])\d{8}$/i.test(mobile)) {
        return false;
    }
    return true;
}
//二级菜单hover
$(function(){
    var slideFlag = false;
    $(".sCode").hover(function(){
        $(".sCode-nav").slideDown(50);
    },function(){
        $(".sCode-nav").slideUp(50);
    });

    //侧边栏hover
    $(".rightSlide_ul li").hover(function(){
        if ($(this).index()<5) {
            $(".rightSlide_real li").hide();
            $(".rightSlide_ul li").not($(this)).css("background-color","#0076ca");
            $(this).css("background-color","#0076a0");
            switch ($(this).index()){
                case 1:
                    $(".rightSlide_real li").eq(0).show();
                    break;
                case 2:
                    $(".rightSlide_real li").eq(1).show();
                    break;
                case 3:
                    $(".rightSlide_real li").eq(2).show();
                    break;
            }
            slideFlag = true;
        }
    },function(){});
    $(".rightSlide_ul li").click(function(){
        if (slideFlag) {
            $(".rightSlide_real li").hide();
            $(this).css("background-color","#0076ca");
        }
    });
    $(".closeFree").click(function(){
        $(this).parent().hide();
    });
    $(".rightSlide_real li").on("mouseleave",function(){
        $(this).hide();
        $(".rightSlide_ul li").css("background-color","#0076ca");
    });
    $(".rightSlide").on("mouseleave",function(){
        $(".rightSlide_ul li").css("background-color","#0076ca");
        $(".rightSlide_real li").hide();
    });

    var InstID = 'html,body';
    var ScrollID = window;
    $(ScrollID).scroll(function () {
        if ($(ScrollID).scrollTop() > 480) {
            $(".rightSlide_backTop").fadeIn(1000);
        
        } else {
            $(".rightSlide_backTop").fadeOut(1000);
            $(".nav").removeAttr("style");
        }
    });

    //当点击跳转链接后，回到页面顶部位置
    $(".rightSlide_backTop").click(function () {
        var a = $(InstID).scrollTop();
        $(InstID).animate({scrollTop: 0}, 800, 'swing', function (){});
        return false;
    });

    //回到顶部
    $(".rightSlide_backTop").hover(function(){
        $(this).text("");
        $(this).css("background-image","url(/static/zhuanti/rocket.gif)");
    },function(){
        setTimeout(function(){
            $(".rightSlide_backTop").html("返回<br>顶部");
            $(".rightSlide_backTop").css("background-image","none");
        })
    });
    $(".phoneFlexClose").click(function(){
        $(this).parent().hide();
        $(".phoneFlex").hide();
    });
    $("#myform #submit").click(function () {
        var mobile = $("#myform input[name='mobile']").val();
        if (!isMobile(mobile)) {
            layer.msg("请输入正确的手机号");
            return false;
        }
        layer.load(1);
        $.ajax({
                type: 'post',
                url: '/zhuanti/btmJoin',
            data: {
            "join_phone": mobile,     //电话
                "user_id": $("input[name='user_id']").val()
        },
        dataType: 'json',
            success: function (data) {
            console.log(data);
            layer.closeAll();  //关闭加载层
            if (data['status'] == 1) {
                $(".biaodan_x input[type='reset']").click();
                layer.msg(data['content']);
            } else {
                layer.alert(data['content']);
            }
        },
        error: function () {
            layer.closeAll();  //关闭加载层
            layer.alert('提交失败，请重试！');
        }
    });
    });

    $(".talkBtn").on("click", function () {
        var mobile = $("input[name='right_mobile']").val();
        if (!isMobile(mobile)) {
            layer.msg("请输入正确的手机号");
            return false;
        }
        layer.load(1);
        $.ajax({
            type: 'post',
            url: '/zhuanti/btmJoin',
            data: {
                "join_phone": mobile,     //电话
                "user_id": $("input[name='user_id']").val()
            },
            dataType: 'json',
            success: function (data) {
                console.log(data);
                layer.closeAll();  //关闭加载层
                if (data['status'] == 1) {
                    $(".biaodan_x input[type='reset']").click();
                    layer.msg(data['content']);
                    $("#myform input[name='mobile']").val("");
                } else {
                    layer.alert(data['content']);
                }
            },
            error: function () {
                layer.closeAll();  //关闭加载层
                layer.alert('提交失败，请重试！');
            }
        });
    });

    $(".get_price .submit").on("click", function () {
        var join_msg = $('input[name=join_msg]').val();
        if (join_msg == '') {
            layer.msg("请输入QQ或微信号");
            return false;
        }
        layer.load(1);
        $.ajax({
            type: 'post',
            url: '/zhuanti/btmJoin',
            data: {
                "join_phone": join_msg,
                "user_id": $("input[name='user_id']").val()
            },
            dataType: 'json',
            success: function (data) {
                //console.log(data);
                layer.closeAll();  //关闭加载层
                if (data['status'] == 1) {
                    $(".get_price input[name='join_msg']").val("");
                    layer.msg(data['content']);
                } else {
                    layer.alert(data['content']);
                }
            },
            error: function () {
                layer.closeAll();  //关闭加载层
                layer.alert('提交失败，请重试！');
            }
        });
    });
});

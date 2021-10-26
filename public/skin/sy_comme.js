$(function () {
    //首页推荐切换
	function left_an(){
		 $('.tj_item .btn_R').removeClass('on');
	$('.itembox .item').stop().animate({'margin-left':'2px'},1000,function(){click_lr()});
	}
	function right_an(){
		$('.tj_item .btn_L').removeClass('on');
        $('.itembox .item').stop().animate({'margin-left':'-1055px'},1000,function(){click_lr()});
	}
	function click_lr(){
		$('.tj_item .btn_L').click(function () {
				$(this).addClass('on')
				$(this).unbind('click')
				left_an()
			})
		$('.tj_item .btn_R').click(function () {
			$(this).addClass('on')
			$(this).unbind('click')
			right_an()
		})
	}
	click_lr()
    
    //index HD
    if($('.hd_wrap').size()>=1){
        $('.hd_wrap').banqh({
            box: "#hdlist",
            pic: "#ban_pic1",
            pnum: "#ban_num1",
            autoplay: true,
            interTime: 5000,
            delayTime: 400,
            pop_delayTime: 400,
            order: 0,
            picdire: true,
            mindire: true,
            min_picnum: 6,
            pop_up: false
        })
    }
    // Gagmes  hd
    if($('.Min1-cont').size()>=1){
        $('.Min1-cont').banqh({
            box: "#hdlist",
            pic: "#ban_pic1",
            pnum: "#ban_num1",
            autoplay: true,
            interTime: 5000,
            delayTime: 400,
            pop_delayTime: 400,
            order: 0,
            picdire: true,
            mindire: false,
            min_picnum: 4,
            pop_up: false
        })
    }

    // TAB
    function  tab_mouser(e,f) {
        e.mouseenter(function () {
            $(this).addClass('on').siblings().removeClass('on');
            if(f){ f.hide().eq($(this).index()).fadeIn();}
        })
    }
    tab_mouser($('.Min1_R .tab_btn span'),$('.Min1_R .tabwrap  .lis')); //新闻切换
    tab_mouser($('.phwrap .item ul li')); //排行榜切换


// 最新汉化
    $('.han_a ul li').mouseenter(function () {
        $(this).addClass('on').siblings().removeClass('on');
    })

    function zq_list() {
        $(".zq_list .warp_post li ").each(function (index) {
            var _font = $(".zq_list .warp_post li ").eq(index).find(".font").text();
            var _bj = $(".zq_list .warp_post li ").eq(index).find(".bg").width();
            var _bb = $(".zq_list .warp_post li ").eq(index).find(".tiao").width();
            var _whdth = $(".zq_list .warp_post li ").eq(index).find(".font").width() * 2;
            var abc = -Math.floor(_font) * _whdth + "px";
            var t = _font / 10;
            t = t * _bb;
            $(".zq_list .warp_post li").eq(index).find(".bg").css("width", t + "px");
            //$(".zq_list .warp_post li").eq(index).find(".font").css("background-positionX", abc);
            if (_font >= 9.8) {
                //$(".zq_list .warp_post li").eq(index).find(".font").css("background-positionX", "-1008px");
				$(".zq_list .warp_post li").eq(index).find(".font").addClass('p_on'+Math.floor(10));
				
            }else{
				$(".zq_list .warp_post li").eq(index).find(".font").addClass('p_on'+Math.floor(_font));
			}
			if (parseInt(_font) == 0) {
                $(".zq_list .warp_post li").eq(index).find(".font").html(' ')
            }
        });
    }

    zq_list()

    //锚点跳转

    //导航条实现的锚点跳转
    $('.Content_L  .pin').on('click', function () {
        var nav_clas = $(this).attr('data-id');
        var nav_num = document.getElementById(nav_clas).offsetTop
        $("body,html").animate({scrollTop: nav_num}, 300);

    })
	

    function HomeScroll(a, b) {
        function g() {
            var g = $(window).scrollLeft(), h = $(window).scrollTop(), i = $(document).height(), j = $(window).height(),
                k = c.height(), l = d.height(), m = k > l ? f : e, n = k > l ? d : c,
                o = k > l ? c.offset().left + c.outerWidth(!0) - g : d.offset().left - c.outerWidth(!0) - g,
                p = k > l ? l : k, q = k > l ? k : l, r = parseInt(q - j) - parseInt(p - j);
            $(a + "," + b).removeAttr("style"), j > i || p > q || m > h || p - j + m >= h ? n.removeAttr("style") : j > p && h - m >= r || p > j && h - m >= q - j ? n.attr("style", "margin-top:" + r + "px;") : n.attr("style", "_margin-top:" + (h - m) + "px;position:fixed;left:" + o + "px;" + (j > p ? "top" : "bottom") + ":0;")
        }

        if ($(a).length > 0 && $(b).length > 0) {
            var c = $(a), d = $(b), e = c.offset().top, f = d.offset().top;
            $(window).resize(g).scroll(g).trigger("resize")
        }
    }

    $(function () {
        HomeScroll(".Content_L", ".Content_R");
        HomeScroll(".cont_L", ".cont_R");
    })

    //点击跳转大图
    $('.Content_L .news_warp_center p > img, .content .ZLmp3 .zl_cent p > img').click(function () {
        var imgurl = $(this).attr('src');
        if($("#abigimg").length == 0){
            var a = document.createElement("a");
            a.setAttribute("id", "abigimg");
            a.setAttribute("href", imgurl);
            a.setAttribute("target", "_blank");
            document.body.appendChild(a);
        }else{
            $("#abigimg").attr("href", imgurl);
        }
        document.getElementById("abigimg").click();
    });
})


 function show_score(){

    $('.data_pf').each(function(){ 
		
        var that = $(this);
        var ypf = that.attr("data-dp"); 
        var a =$(this).find('.scorewrap');
        var	b=a.find('.score');
        var	c=a.find('.processingbar');
        var d=a.find('.txt');

        var w = c.children().first()
        var n = c.children().first().text();

        var h = a.find('.hover');
        //var d = c.find('span');
        var e = d.find('u');

        var i_nuber = e.find('i');

        h.unbind("mousemove")
        h.unbind("mouseleave")
        h.unbind("click")
		
        if(n>=10){n==10}
        var _w = c.width()*2;

        var postion=-Math.floor(n) * _w + "px";

        if(n>= 9.8){
           // c.css("background-positionX", -c.width()*19);
			 c.addClass('p_on10');
        }else{
           // c.css("background-positionX", postion);
			  c.addClass('p_on'+Math.floor(n) );
        }
		if(parseInt(n) == 0){
			w.text('')
		}
        b.children().first().css('width',n*10 +'%')

        if(ypf != undefined && ypf != 0){
            e.text('您的评分为'+ ypf +'分');
        }

        h.mousemove(function(event) { 
            if ( ypf == 0 || ypf == undefined || ypf =="" ) { 
                var x = event.offsetX;
                var f = (x / b.width()) * 10
                f = f.toFixed(1)
                e.html('您的评分为'+ '<i>'+f+'</i>' +'分');
                e.attr("data-sc",f)
                $(this).children().first().css("width", x + "px")

            }
        });
        h.mouseleave(function(event){
            if ( ypf == 0 || ypf == undefined || ypf =="" ) { 
                var x = event.offsetX;
                b.children().first().css("width", n*10 +'%')
                e.text('您还未评分！');
            }
        });
        h.click(function(){
			
            if ( $('.username').size()>=1 && ypf == 0 ){
                var x = event.offsetX;
                var f = (x / b.width()) * 10
                f = f.toFixed(1)
                e.html('您的评分为'+ '<i>'+f+'</i>' +'分');
                e.attr("data-sc",f)
                that.attr("data-dp",f)
				ypf = that.attr("data-dp");
				//layer.msg('评分成功！')
            } else {
               // e.attr("data-sc",f)
			   //layer.msg('您已评分')
            }
        })
    })

}

show_score()

//礼包剩余
$(function () {
    var wp_ =  $('.libao_info_top .number');
    var nm_1 = Number(wp_.find('span').eq(0).html());
    var nm_2 = Number(wp_.find('span').eq(1).html());
    var nm = (nm_1/nm_2)*100;
    $('.libao_info_top .tiao .bj').css('width',nm+'%');
})

//截图轮播
$(function () {
    if ($('.snopshot').length > 0) {
        var sst = $(".snopshot");
        if (sst.length == 1) {
            sst.css({
                "position": "relative",
                "text-align": "center"
            }).find("img").css({
                "max-width": "520",
                "max-height": "320;"
            }).next(".elementOverlay").hide();
            $(".snap-shot-btn").hide();
        } else if (sst.length == 2) {
            sst.css({
                "position": "relative",
                "float": "left"
            }).find("img").css({
                "max-width": "320",
                "margin-right": "10px"
            }).next(".elementOverlay").hide();
            $(".snap-shot-btn").hide();
        } else {
            var img = new Image();
            img.src = $(".snapShotCont li").eq(0).find("img").attr("src");
			img.onload =function() {
				var imgWidth = img.width;
				var imgHeight = img.height;
				if (imgWidth > imgHeight) {
					imgHeight = 320;
					imgWidth = 520;
					sst.css('height',270)
						$('.snap-shot-btn i').css('top','40%')
				} else {
					imgHeight = 564;
					imgWidth = 320;
					sst.css('height',470)
				}
				var snapShotWrap = new posterTvGrid('snapShotWrap', {
					imgHeight: imgHeight,
					//图片宽高，来调整框架样式
					imgWidth: imgWidth,
					imgP: parseInt(imgWidth / 1.2) //小图与大图比例暂定1比1.2
				});	
			}
           
        }
    }
})

//h3
$('.news_warp_center>h3').each(function(){
	$(this).html('<span class="bt">'+$(this).html()+'</span>')
});

//详情页面左右切换;
(function () {
    if($(".dj_chinesemode .pagewrap .keyup_ts_text").size()>=1){
        'use strict';
        if(typeof(Cdetail_total) == "undefined"){
            Cdetail_total = $(".pagewrap .pagination .next").prev().text();
        }
        var gkeyup_k = 0;
        var gkeyup_n = 0;
        var gkeyup_U1 = BeginUrl(location.href);
        $(document).keyup(function(event){
            var isFocus= $("input , textarea").is(":focus"); 
            var e = event || window.event;
            gkeyup_k = e.keycode || e.which;
            if (gkeyup_k == 37 && isFocus==false) {
                //left
                PlusUrl('-');
            }else if (gkeyup_k == 39 && isFocus==false) {
                //right
                PlusUrl('+');
            }
            return;
        }); 
        function BeginUrl(u) {
            var uTmp = '';
            if (u.indexOf('_') > 0) {
                uTmp = u.substring(0,u.indexOf('_'));
                gkeyup_n = u.substring(u.indexOf('_') + 1,u.indexOf('.html'));
            }
            else {
                uTmp = u.substring(0,u.indexOf('.html'));
                gkeyup_n = 1;
            }
            return uTmp;
        }
        function PlusUrl(n1) {
            var nTmp = gkeyup_n;
            if (n1 == '-') {
                nTmp--;
            }else if (n1 == '+') {
                nTmp++;
            }
            if (nTmp <=0) {
                gkeyup_n=1;
                return;
            }else if(nTmp > Cdetail_total){
                gkeyup_n=Cdetail_total;
                return;
            }
            AddUrl(nTmp);
        }
        function AddUrl(u) {
            //跳转...
            if (u === null) {
                return;
            }
            if (u%1 === 0 && u <= Cdetail_total) {
                if (u <= 1) {
                    window.location.assign(gkeyup_U1 + '.html');
                }else {
                    window.location.assign(gkeyup_U1 + '_' + u + '.html');
                }
            }else {
                return;
            }
        }
    }               
})();
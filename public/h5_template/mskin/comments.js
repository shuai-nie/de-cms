var myuserhost = "https://my.3dmgame.com";
var g_loginuserid = 0,g_posting=0,g_repling=0,g_praising=0,is_mobile=0,g_report=0,g_pagereport=0,g_huifucaptcha_obj=null,g_huifucaptcha_id=0;
var g_comment_list = new Array();
var g_user_info = {uid:0,nickname:'',avatarstr:'',gender:1,regionstr:'',title:'',title_level:0};
var g_ct_order = "";
//好友数组
var g_friends_flag = false;
var g_lastconcat_arr=[];
var g_friends_arr=[
    {"name":"a","list":[]},
    {"name":"b","list":[]},
    {"name":"c","list":[]},
    {"name":"d","list":[]},
    {"name":"e","list":[]},
    {"name":"f","list":[]},
    {"name":"j","list":[]},
    {"name":"h","list":[]},
    {"name":"i","list":[]},
    {"name":"j","list":[]},
    {"name":"k","list":[]},
    {"name":"l","list":[]},
    {"name":"m","list":[]},
    {"name":"n","list":[]},
    {"name":"o","list":[]},
    {"name":"p","list":[]},
    {"name":"q","list":[]},
    {"name":"r","list":[]},
    {"name":"s","list":[]},
    {"name":"t","list":[]},
    {"name":"u","list":[]},
    {"name":"v","list":[]},
    {"name":"w","list":[]},
    {"name":"x","list":[]},
    {"name":"y","list":[]},
    {"name":"z","list":[]},
    {"name":"#","list":[]},
];
$(function(){
    init();//初始化
    if($(".Ct_sel_order").length > 0){
        $(".Ct_sel_order a").click(function(){
            $(".Ct_sel_order a").removeClass('on');
            var ct_order = "";
            if($(this).html() == "最早"){
                ct_order = "time";
            }
            if(g_ct_order == ct_order){
                return true;
            }else{
                g_ct_order = ct_order;
                getpostlist(1);
            }
            $(this).addClass('on');
        });
    }
});
//展示回复的隐藏楼层
function showfloor(obj){
    $(obj).siblings('.lis').show();
    $(obj).hide();
	 
	var open_btn = $(obj).parents('.cmt_floor').find('.floor_openall');
	var this_box = $(obj).parents('.cmt_floor')
	open_btn.show();
	open_btn.click(function () {
		$(obj).show();
		var this_ = this_box.find('.lis');
		this_.hide();
		this_.eq(0).show();
		this_.eq(1).show();
		this_.eq(this_.length - 1).show();
		open_btn.hide();
	})
	 
	this_box.find('.lis_txt').each(function(){
		 var this_h = $(this).height();
		//console.log(this_h)
		if(this_h>66 && $(this).find('.openmor').length<=0){
			$(this).addClass('hidemor');
			$(this).append('<div class="openmor">查看全部</div>');
		}
	})

}
//展示隐藏的评论内容
function showdetail(obj){
    $(obj).parent().css('max-height','none');
    $(obj).remove();
	 
}
 

function init(){
    //获取评论列表
    $('#Ct_top_total').text(0);
    initpostlist(0);
    //获取收藏状态
    getcollection();
    //获取文章举报状态
    getpagereport();
    $("body").on("click", function(){
        $(".popFaceBox").hide();//关闭表情框
    });

}
//为防止同一个页面多少调用获取登录信息接口，这里写好评论用户登录信息初始化函数，共获取用户信息成功后调用
function setmyctuserlogin(user){
    g_loginuserid = user.uid;
    is_mobile = user.is_mobile;
    $(".cmt_poswrap").html('<a href="https://my.3dmgame.com/setting"><div class="a_user_img"><img style="width:20px;height: 20px;border-radius: 50%;vertical-align: middle" src="'+user.avatarstr+'"></div><span class="a_user_spilt">|</span><div class="a_user_name">'+user.nickname+'</div></a>');
    $(".cmt-boxtextarea").attr('placeholder', '点评一下...');
    $(".cmt-boxtextarea").attr('onfocusout', 'this.placeholder=\'点评一下...\'');
    $('.no_logged').hide();
    $('.user img').attr('src',user.avatarstr);
    $('.user .name').text(user.nickname);
    $('.user').show();
    if($(".cmt_login .popFace").length < 1){
        $(".cmt_login .cmt_release").after(getCtFaceBox());
        getFriends();
    }
    set_g_userinfo(user);
}
//初始化评论列表
function initpostlist(refresh){
    var page = 1;
    var maxid = 0;
    var total = 0;
    var sid = 0;
    var pagesize = 20;
    var url = myuserhost + "/api/postlist";
    var pageurl = "";
    if(typeof(collect_pageurl) != "undefined"){
        pageurl = collect_pageurl;
    }
    $.ajax({
        url: url,
        //async:false,
        type: "POST",
        data:{maxid:maxid,total:total,page:page,pagesize:pagesize,pageurl:pageurl,isinit:1},
        dataType:'json',
        xhrFields:{withCredentials:true},
        success: function(data){
            if(data.code == 1 && typeof(data.data) != "undefined" && typeof(data.data.list) != "undefined"){
                var totalpage = 1;
                var ct_position = 1;
                g_comment_list = new Array();
                total = data.data.total;
                totalpage = data.data.total>pagesize ? Math.ceil(total/pagesize) : 1;
                if($("#Ct_maxid").length > 0){
                    $("#Ct_maxid").val(data.data.maxid);
                    $("#Ct_totalnum").val(total);
                    $("#Ct_sid").val(data.data.sid);
                    $("#Ct_totalpage").val(totalpage);
                    $("#Ct_nowpage").val(page);
                }else{
                    var str = '<input type="hidden" id="Ct_maxid" value="'+data.data.maxid+'" />';
                    str += '<input type="hidden" id="Ct_totalnum" value="'+total+'" />';
                    str += '<input type="hidden" id="Ct_sid" value="'+data.data.sid+'" />';
                    str += '<input type="hidden" id="Ct_totalpage" value="'+totalpage+'" />';
                    str += '<input type="hidden" id="Ct_nowpage" value="'+page+'"/>';
                    $(".cms_wrap").append(str);//整个评论列表组件
                }
                $('#partpeople').html(data.data.total_uid);//参与人
                $('#commentnum').html(total);//评论数
                if(typeof(data.data.hotlist) != "undefined"  && data.data.hotlist.length>0){
                    var hotstr = "";
                    var len = data.data.hotlist.length;
                    for(var i=0; i<len; i++){
                        hotstr += getCommentsHtml(data.data.hotlist[i],'');
                        g_comment_list[data.data.hotlist[i]['id']] = data.data.hotlist[i];
                    }
                    if(hotstr != ""){
                        $("#Cslis_wrap_hot").html(hotstr);
                        $("#Comments_wrap_div").show();
                        setOfficialgroupHot();
                    }
                }
                if(page == 1){
                    $("#Cslis_wrap").html('');
                }
                if($('.pl .ico_pl2').length > 0){$('#Ct_top_total').text(data.data.total_uid)}
                if(data.data.list.length>0){
                    var str = "";
                    var len = data.data.list.length;
                    for(var i=0; i<len; i++){
                        str += getCommentsHtml(data.data.list[i],'');
                        g_comment_list[data.data.list[i]['id']] = data.data.list[i];
                        ct_position = data.data.list[i].position;
                    }
                    $(".content_null").remove();
                    $("#Cslis_wrap").append(str);                   //评论列表class
                }
                //查看其它页数据
                if(page<totalpage && data.data.list.length>0 && ct_position > 1){
                    if($("#Ct_more").length<1){
                        $("#Comments_wrap").append('<div style="margin: 10px auto !important;width: 100%;height: 50px;line-height: 50px;text-align: center;background: #f1f1f1;color: #7c7c7c;font-size: 14px;" id="Ct_more" onclick="morepost()">查看更多&nbsp;(<span>'+(data.data.total-page*pagesize)+'</span>)</div>');
                    }else{
                        $("#Ct_more").html('查看更多&nbsp;(<span>'+(total-page*pagesize)+'</span>)');
                    }
                }else{
                    $("#Ct_more").remove();
                }
                if(refresh == 1){
                    if($(".Ct_sel_order").length > 0){
                        $(".Ct_sel_order a").removeClass('on');
                        g_ct_order = "";
                        $(".Ct_sel_order").children(":first").addClass('on');
                    }
                    //排除年度评选
                    if(window.location.href.indexOf('https://m.3dmgame.com/zh/nzpx2019') < 0){
                        var cms_top = $('.cms_wrap').offset().top;
                        $("body,html").animate({scrollTop:cms_top},300);
                    }
                }
            }else if(data.code==37){
                $("#Ct_norecord i").css('background','none');
                $("#Ct_norecord i").html(data.msg);
            }
        }
    }).done(function(){
         //评论过长
        $('.comments_wrap .cmt_txt').each(function () {
            var this_h = $(this).height();
            //console.log(this_h)
            if(this_h>72){
                $(this).addClass('hidemor');
                $(this).append('<div class="openmor">查看全部</div>');
            }
        });
        $('.comments_wrap .cmt_txt').on('click','.openmor',function () {
            var this_box =  $(this).parents('.cmt_txt');
            if(this_box.hasClass('hidemor')){
                $(this).html('收起');
                this_box.removeClass('hidemor');
            }else{
                $(this).html('查看全部');
                 this_box.addClass('hidemor');
            }
        })
        //盖楼内容过长
        $('.comments_wrap .lis_txt').each(function () {
            var this_h = $(this).height();
            //console.log(this_h)
            if(this_h>66){
                $(this).addClass('hidemor');
                $(this).append('<div class="openmor">查看全部</div>');
            }
        });
        $('.comments_wrap .lis_txt').on('click','.openmor',function () {
            var this_box =  $(this).parents('.lis_txt');
            if(this_box.hasClass('hidemor')){
                $(this).html('收起');
                this_box.removeClass('hidemor');
            }else{
                $(this).html('查看全部');
                this_box.addClass('hidemor');
            }
        })
         
    })
}

//获取评论列表
function getpostlist(page){
    var maxid = 0;
    var total = 0;
    var sid = 0;
    var pagesize = 20;
    if($("#Ct_maxid").length>0){
        maxid = $("#Ct_maxid").val();
    }
    if($("#Ct_totalnum").length>0){
        total = $("#Ct_totalnum").val();
    }
    if($("#Ct_sid").length>0){
        sid = $("#Ct_sid").val();
    }
    var url = myuserhost + "/api/postlist";
    var pageurl = "";
    if(typeof(collect_pageurl) != "undefined"){
        pageurl = collect_pageurl;
    }
    $.ajax({
        url: url,
        //async:false,
        type: "POST",
        data:{maxid:maxid,total:total,page:page,pagesize:pagesize,pageurl:pageurl,ordertype:g_ct_order},
        dataType:'json',
        xhrFields:{withCredentials:true},
        success: function(data){
            if(data.code == 1 && typeof(data.data) != "undefined" && typeof(data.data.list) != "undefined"){
                var totalpage = 1;
                var ct_position = 1;
                totalpage = $("#Ct_totalpage").val();
                $("#Ct_nowpage").val(page);
                if(page == 1){
                    $("#Cslis_wrap").html('');
                }
                if(data.data.list.length>0){
                    var str = "";
                    var len = data.data.list.length;
                    for(var i=0; i<len; i++){
                        str += getCommentsHtml(data.data.list[i],'');
                        g_comment_list[data.data.list[i]['id']] = data.data.list[i];
                        ct_position = data.data.list[i].position;
                    }
                    $(".content_null").remove();
                    $("#Cslis_wrap").append(str);					//评论列表class
                }
                //查看其它页数据
                if(page<totalpage && data.data.list.length>0 && ct_position > 1){
                    if($("#Ct_more").length<1){
                        $("#Comments_wrap").append('<div style="margin: 10px auto !important;width: 100%;height: 50px;line-height: 50px;text-align: center;background: #f1f1f1;color: #7c7c7c;font-size: 14px;" id="Ct_more" onclick="morepost()">查看更多&nbsp;(<span>'+(data.data.total-page*pagesize)+'</span>)</div>');
                    }else{
                        $("#Ct_more").html('查看更多&nbsp;(<span>'+(total-page*pagesize)+'</span>)');
                    }
                }else{
                    $("#Ct_more").remove();
                }
            }else if(data.code==37){
                $("#Ct_norecord i").css('background','none');
                $("#Ct_norecord i").html(data.msg);
            }
        }
    }).done(function(){
		 //评论过长
		$('.comments_wrap .cmt_txt').each(function () {
			var this_h = $(this).height();
			//console.log(this_h)
			if(this_h>72){
				$(this).addClass('hidemor');
				$(this).append('<div class="openmor">查看全部</div>');
			}
		});
		$('.comments_wrap .cmt_txt').on('click','.openmor',function () {
			var this_box =  $(this).parents('.cmt_txt');
			if(this_box.hasClass('hidemor')){
				$(this).html('收起');
				this_box.removeClass('hidemor');
			}else{
				$(this).html('查看全部');
				 this_box.addClass('hidemor');
			}
		})
		//盖楼内容过长
		$('.comments_wrap .lis_txt').each(function () {
			var this_h = $(this).height();
			//console.log(this_h)
			if(this_h>66){
				$(this).addClass('hidemor');
				$(this).append('<div class="openmor">查看全部</div>');
			}
		});
		$('.comments_wrap .lis_txt').on('click','.openmor',function () {
			var this_box =  $(this).parents('.lis_txt');
			if(this_box.hasClass('hidemor')){
				$(this).html('收起');
				this_box.removeClass('hidemor');
			}else{
				$(this).html('查看全部');
				this_box.addClass('hidemor');
			}
		})
		 
	})
}
//评论分页查看更多
function morepost(){
    if($("#Ct_nowpage").length>0){
        var page = parseInt($("#Ct_nowpage").val());
        page += 1;
        getpostlist(page);
    }else{
        return false;
    }
}

//防止js注入
function htmlEncodeJQ ( str ) {
    return $('<span/>').text( str ).html();
}

//发布评论
function ct_post(){
    //检查是否登录
    if(g_loginuserid == 0){
        //tipsmsg('请先登录');
        openlogin();
        return false;
    }
    if(g_posting){
        tipsmsg('发送中，请稍后。。。');
        return false;
    }
    var url = myuserhost + "/api/post";
    var content = htmlEncodeJQ( trim($(".cmt-boxtextarea").val()) + ' ');//防止最后一个@好友空格去掉
    var len = content.length;
    if(len<1){
        tipsmsg("评论内容太少了");
        return false;
    }
    if(len>1000){
        tipsmsg("评论内容已超出最大长度1000字");
        return false;
    }
    g_posting = 1;
    var sid = 0;
    if($("#Ct_sid").length>0){
        sid = $("#Ct_sid").val();
    }
    var pageurl = "";
    if(typeof(collect_pageurl) != "undefined"){
        pageurl = collect_pageurl;
    }
    $.ajax({
        url:url,
        type: "POST",
        data:{content:content,sid:sid,pageurl:pageurl},
        dataType:'json',
        xhrFields:{withCredentials:true},
        success: function(data){
            if(data.code == 1){
                $(".cmt-boxtextarea").val('');
                if(data.checkflag){
                    tipsmsg('发布成功，需要等待系统审核');
                    return false;
                }
                initpostlist(1);
            }else if(data.code == 9){
                openlogin();
            }else if(data.code == 206 && $("#TencentCaptcha").length > 0){
                $("#TencentCaptcha").attr('data-type', 'post');
                $("#TencentCaptcha").click();
            }else{
                tipsmsg(data.msg);
                if(typeof data.url !== "undefined"){
                    top.location.href = myuserhost + data.url;
                }
            }
        },
        complete: function(){
            g_posting = 0;
        }
    });
}

//直接回复
function ct_reply(obj,id) {
    //检查是否登录
    if(g_loginuserid == 0){
        openlogin();
        //tipsmsg('请先登录');
        return false;
    }
    if(g_repling){
        tipsmsg('发送中，请稍后。。。');
        return false;
    }
    var url = myuserhost + "/api/reply";
    var content = htmlEncodeJQ( $(obj).parent().find('.cmt-boxtextarea').val() );
    var len = content.length;
    if(len<1){
        tipsmsg("回复内容太少了");
        return false;
    }
    if(len>1000){
        tipsmsg("回复内容已超出最大长度1000字");
        return false;
    }
    g_repling = 1;
    var pageurl = "";
    if(typeof(collect_pageurl) != "undefined"){
        pageurl = collect_pageurl;
    }
    g_huifucaptcha_obj = obj;
    g_huifucaptcha_id = id;
    $.ajax({
        url:url,
        type: "POST",
        data:{content:content,id:id,pageurl:pageurl},
        dataType:'json',
        xhrFields:{withCredentials:true},
        success: function(data){
            if(data.code == 1){
                $(obj).parent().parent().prev().find('.reply_btn').click();				//收起发布框
                $(obj).parent().find('.cmt-boxtextarea').empty();                       //清空发布框
                if(data.checkflag){
                    tipsmsg('回复成功，需要等待系统审核');
                    return false;
                }
                initpostlist(1);
            }else if(data.code == 9){
                openlogin();
            }else if(data.code == 207 && $("#TencentCaptcha").length > 0){
                $("#TencentCaptcha").attr('data-type', 'reply');
                $("#TencentCaptcha").click();
            }else{
                tipsmsg(data.msg);
                if(typeof data.url !== "undefined"){
                    top.location.href = myuserhost + data.url;
                }
            }
        },
        complete: function(){
            g_repling = 0;
        }
    });
}
//楼中楼回复
function ct_reply_list(obj,id,listid) {
    //检查是否登录
    if(g_loginuserid == 0){
        openlogin();
        //tipsmsg('请先登录');
        return false;
    }
    if(g_repling){
        tipsmsg('发送中，请稍后。。。');
        return false;
    }
    var url = myuserhost + "/api/reply";
    var content = htmlEncodeJQ( $(obj).parent().find('.cmt-boxtextarea').val() );
    var len = content.length;
    if(len<1){
        tipsmsg("回复内容太少了");
        return false;
    }
    if(len>1000){
        tipsmsg("回复内容已超出最大长度1000字");
        return false;
    }
    g_repling = 1;
    var pageurl = "";
    if(typeof(collect_pageurl) != "undefined"){
        pageurl = collect_pageurl;
    }
    $.ajax({
        url:url,
        type: "POST",
        data:{content:content,id:id,pageurl:pageurl},
        dataType:'json',
        xhrFields:{withCredentials:true},
        success: function(data){
            if(data.code == 1){
                $(obj).parent().parent().prev().find('.reply_btn').click();             //收起发布框
                $(obj).parent().find('.cmt-boxtextarea').empty();                       //清空发布框
                if(data.checkflag){
                    tipsmsg('回复成功，需要等待系统审核');
                    return false;
                }
                initpostlist(1);
            }else if(data.code == 9){
                openlogin();
            }else{
                tipsmsg(data.msg);
                if(typeof data.url !== "undefined"){
                    top.location.href = myuserhost + data.url;
                }
            }
        },
        complete: function(){
            g_repling = 0;
        }
    });
}

//点赞和踩
function praise(obj,ctid,type){
    if(g_praising){
        tipsmsg('发送中，请稍后。。。');
    }
    var url = myuserhost + "/api/praise";
    var pageurl = "";
    if(typeof(collect_pageurl) != "undefined"){
        pageurl = collect_pageurl;
    }
    $.ajax({
        url:url,
        type: "POST",
        data:{id:ctid,type:type,pageurl:pageurl},
        dataType:'json',
        xhrFields:{withCredentials:true},
        success: function(data){
            if(data.code == 1){
                if(data.add==1){
                    $(obj).addClass('on');
                    var numr = Number($(obj).html());
                    $(obj).html(numr+1);
                }else{
                    if(data.type == type){
                        $(obj).addClass('on')
                    }else{
                        $(obj).siblings().addClass('on');		//为兄弟元素添加on属性
                        $(obj).removeClass('on');
                    }
                }
                $(obj).parent().children().removeAttr('onclick');		//把赞和踩都去除onclick
            }else if(data.code == 9){
                openlogin();
            }else{
                tipsmsg(data.msg);
                if(typeof data.url !== "undefined"){
                    top.location.href = myuserhost + data.url;
                }
            }
        },
        complete: function(){
            g_praising = 0;
        }
    });
}

//拼接评论详情
function getCommentsHtml(data,content){
    var str = '<div class="cmt_item'+(data.user.nickname == '3DM官方账号' ? ' Official' : '')+'">';
    str += '<div class="cmt_tx">';
    str += '<a href="'+getUserHome(data.user.uid)+'" class="img"><img src="'+data.user.avatarstr+'"/></a>';
    str += '<p>'+data.position+'</p>';
	if(typeof(data.follow) != "undefined" && data.follow == 1){
		str += '<div class="follow_box on" onclick="follow_personal(this, '+data.user.uid+')">已关注</div>';
	}else if(typeof(data.follow) == "undefined" || data.follow != -1){
		str += '<div class="follow_box" onclick="follow_personal(this, '+data.user.uid+')">关注</div>';
	}
    str += '</div>';
    str += '<div class="info">';
    str += '<a href="'+getUserHome(data.user.uid)+'" ><div class="bt">'+data.user.nickname;
    if(typeof data.user.vip_level != "undefined" && data.user.vip_level > 0){
        str += '<a href="https://m.3dmgame.com/yeyou/vip/index" target="_blank" class="Cotilebt"><i class="ico ico3dmzy"></i>3DM自运营 VIP'+data.user.vip_level+'</a>';
    }else if(typeof data.user.auth_level != "undefined" && data.user.auth_level > 0 && data.user.auth_level != 4){
        if(data.user.auth_level == 1){
            str += '<a href="javascript:void(0);" class="Cotilebt"><i class="ico icobj"></i>'+data.user.auth_title+'</a>';
        }else if(data.user.auth_level == 2){
            str += '<a href="javascript:void(0);" class="Cotilebt"><i class="ico icogamegf"></i>'+data.user.auth_title+'</a>';
        }else if(data.user.auth_level == 3){
            str += '<a href="javascript:void(0);" class="Cotilebt"><i class="ico ico3dmgf"></i>'+data.user.auth_title+'</a>';
        }
    }else{
        if(typeof data.user.user_level != "undefined" && data.user.user_level > 0)
        {
            str += '<a href="https://my.3dmgame.com/integral/" target="_blank" class="Cotileusual">Lv.'+data.user.user_level+'</a>';
        }else{
            str += '<a href="https://my.3dmgame.com/integral/" target="_blank" class="Cotileusual">Lv.1</a>';
        }
    }
    str += '</div></a> ';
    // str += '<div class="as">'+data.user.regionstr+'网友</div>';
    // str += '<div class="time">'+data.time+'</div>';
    str += '<div class="cmt_score">';
    if(typeof(data.praise)!="undefined" && (data.praise==1 || data.praise==2)){
        str += '<div style="cursor: pointer" class="btn_Good'+(data.praise == 1 ? ' on': '')+'">'+data.goodcount+'</div>';
    }else{
        str += '<div style="cursor: pointer" class="btn_Good" onclick="praise(this,'+data.id+',1)">'+data.goodcount+'</div>';
    }
    str += '</div>';

    if(data.replies.length>0){
        str += '<div class="cmt_floor">';
        str += getReplies(data.replies,data.id);
		str += '<div class="floor_openall" onclick="hidetext(this)" >收起楼层</div>'
        str += '</div>';
    }
    str += '<div class="cmt_message">';
    var nowcontent = content!='' ? content: data.content;
    if(nowcontent.length>=200){
        str += '<div class="cmt_txt hidemor" style="max-height: 22px;">'+replaceFaceContent(nowcontent)+'<div class="conttxt-mor" onclick="showdetail(this)">查看全部</div></div>';
    }else{
        str += '<div class="cmt_txt">'+replaceFaceContent(nowcontent)+'</div>';
    }

    str += '<div class="user_information">';
    str += '<div class="time">'+data.time+'</div>';
    str += '<div class="as">'+data.user.regionstr+'网友</div>';
    str += '</div>';
    str += '<div class="reply_btn" data-replayid="'+data.position+'" style="cursor: pointer" onclick="show_reply_box(this)">回复</div>';
    str += '</div>';
    str += '<div class="cmt_reply_box" data-replaybox="'+data.position+'"><form action=""><textarea onfocus="plfocus(this)" class="cmt-boxtextarea"  placeholder="点评一下..." onfocusout="this.placeholder=\'点评一下...\'"></textarea><div class="cmt_release" style="cursor: pointer" onclick="ct_reply(this,'+data.id+')">发布</div>'+getReplyFaceBox()+'</form></div></div></div>';
    return str;
}
//发布评论时，判断
function plfocus(obj){
    if(g_loginuserid == 0){
        //tipsmsg('请先登录');
        $('.cmt-boxtextarea').blur();
        openlogin();
        return false;
    }
    if(is_mobile == 0){
        tipsmsg('请先绑定手机号码');
        $('.cmt-boxtextarea').blur();
        window.open("https://my.3dmgame.com/setting/binding");
        // window.location.href = "https://my.3dmgame.com/setting/binding";
        return false;
    }
    $(obj).attr('placeholder',' ');
    $(".popFaceBox").hide();//关闭表情框
    if($(obj).parent().parent('.cmt_login').length > 0){
        hide_open_reply();
    }
}
//展示回复框
function show_reply_box(obj) {
    $(".popFaceBox").hide();//关闭表情框
    //检查是否登录
    if(g_loginuserid == 0){
        openlogin();
       //tipsmsg('请先登录');
        return false;
    }
    var replayid = $(obj).attr('data-replayid');
    if($(obj).hasClass('on')){
        $(obj).removeClass('on')
        $(obj).html('回复')
        $('.cmt_reply_box[data-replaybox='+replayid+']').stop().slideUp();
    }else{
        hide_open_reply();
        $(obj).addClass('on')
        $(obj).html('取消回复')
        $('.cmt_reply_box[data-replaybox='+replayid+']').slideDown();
    }
}
//楼中楼展示回复框
function show_reply_list(obj) {
    $(".popFaceBox").hide();//关闭表情框
    //检查是否登录
    if(g_loginuserid == 0){
        //tipsmsg('请先登录');
        openlogin();
        return false;
    }
    if($(obj).hasClass('on')){
        $(obj).removeClass('on')
        $(obj).html('回复');
		$(obj).parents(".lis").find(".cmt_reply_lis").stop().slideUp();       
    }else{
        hide_open_reply();
        $(obj).addClass('on')
        $(obj).html('取消回复')      
		$(obj).parents(".lis").find(".cmt_reply_lis").slideDown()
    }
}
//关闭所有已展开的回复
function hide_open_reply(){
    //直接回复
    $(".cmt_reply_box:visible").each(function(){
        var obj = $(this).parent(".lis").find(".reply_btn");
        $(obj).removeClass('on')
        $(obj).html('回复');
        $(this).stop().slideUp();
    });
    //和楼中楼回复
    $(".cmt_reply_lis:visible").each(function(){
        var obj = $(this).parent(".lis").find(".reply_btn");
        $(obj).removeClass('on')
        $(obj).html('回复');
        $(this).stop().slideUp();
    });
}
//展示小回复框
function show_reply_box_sublime(obj) {
    //检查是否登录
    if(g_loginuserid == 0){
        //tipsmsg('请先登录');
        openlogin();
        return false;
    }
    if($(obj).hasClass('on')){
        $(obj).removeClass('on')
        $(obj).html('回复')
        $(obj).parent().next().stop().slideUp();
    }else{
        $(obj).addClass('on')
        $(obj).html('取消回复')
        $(obj).parent().next().slideDown();
    }
}

//楼层太高，隐藏
function getReplies(data,listid){
    var len = data.length;
    var str = '';
    for(var i=len-1; i>=0; i--){
        if(len>3 && i==len-3){
            str += '<div class="floor_tips" onclick="showfloor(this)"><p>楼层太高已隐藏'+(len-3)+'条</p></div>';
        }
        str += '<div class="lis'+(data[i].user.nickname == '3DM官方账号' ? ' Official2' : '')+'"'+(i>0 && i<len-2 ? ' style="display:none;"' : (i==0 ? ' style="border-bottom: none;"' : ''))+'>';
        str += '<div class="lis_head">';
        str += '<div class="bt">' + data[i].user.nickname;
        if(typeof data[i].user.vip_level != "undefined" && data[i].user.vip_level > 0){
            str += '<a href="https://m.3dmgame.com/yeyou/vip/index" target="_blank" class="Cotilebt"><i class="ico ico3dmzy"></i>3DM自运营 VIP'+data[i].user.vip_level+'</a>';
        }else if(typeof data[i].user.auth_level != "undefined" && data[i].user.auth_level > 0 && data[i].user.auth_level != 4){
            if(data[i].user.auth_level == 1){
                str += '<a href="javascript:void(0);" class="Cotilebt"><i class="ico icobj"></i>'+data[i].user.auth_title+'</a>';
            }else if(data[i].user.auth_level == 2){
                str += '<a href="javascript:void(0);" class="Cotilebt"><i class="ico icogamegf"></i>'+data[i].user.auth_title+'</a>';
            }else if(data[i].user.auth_level == 3){
                str += '<a href="javascript:void(0);" class="Cotilebt"><i class="ico ico3dmgf"></i>'+data[i].user.auth_title+'</a>';
            }
        }else{
            if(typeof data[i].user.user_level != "undefined" && data[i].user.user_level > 0)
            {
                str += '<a href="https://my.3dmgame.com/integral/" target="_blank" class="Cotileusual">Lv.'+data[i].user.user_level+'</a>';
            }else{
                str += '<a href="https://my.3dmgame.com/integral/" target="_blank" class="Cotileusual">Lv.1</a>';
            }
        }
        str += '</div>';
        str += '<div class="flo_num">' + data[i].position + '</div>';
        str += '</div>';
        str += '<div class="lis_txt">'+ replaceFaceContent(data[i].content) +'</div>';
		str+='<div class="hf_box">'
		str += '<div class="user_information">';
		str += '<div class="time">'+data[i].time+'</div>';
		str += '<div class="as">'+data[i].user.regionstr+'网友</div>';
	    str += '<div class="cmt_score">';
			if(typeof(data[i].praise)!="undefined" && (data[i].praise==1 || data[i].praise==2)){
				str += '<div style="cursor: pointer" class="btn_Good'+(data[i].praise == 1 ? ' on': '')+'">'+data[i].goodcount+'</div>';
			}else{
				str += '<div style="cursor: pointer" class="btn_Good" onclick="praise(this,'+data[i].id+',1)">'+data[i].goodcount+'</div>';
			}
		str += '</div>';
		str += '<div class="reply_btn" data-replayid="'+data[i].position+'" style="cursor: pointer" onclick="show_reply_list(this)">回复</div>';
		str += '</div>';				
		str += '</div>';
		str += '<div class="cmt_reply_lis" data-replaybox="'+data[i].position+'"><form action=""><textarea onfocus="plfocus(this)" class="cmt-boxtextarea"  placeholder="点评一下..." onfocusout="this.placeholder=\'点评一下...\'"></textarea><div class="cmt_release" style="cursor: pointer" onclick="ct_reply_list(this,'+data[i].id+','+listid+')">发布</div>'+getReplyFaceBox()+'</form></div>';
		str+='</div>'       
    }
    return str;
}
function getUserHome(uid){
    return 'https://my.3dmgame.com/user/'+uid;
}
//获取收藏状态
function getcollection(){
    var url = myuserhost + "/api/getfavorite";
    var pageurl = "";
    if(typeof(collect_pageurl) != "undefined"){
        pageurl = collect_pageurl;
    }
    $.ajax({
        url: url,
        type: "POST",
        data:{pageurl:pageurl},
        dataType:'json',
        xhrFields:{withCredentials:true},
        success: function(data){
            if(data.code == 1){
                if(data.favorite){
                    $(".ction_btn").find('i').addClass('on');
                    $(".ction_btn").find('span').html('取消');
                }
                if(data.sid){
                    $(".ction_btn").append('<input type="hidden" id="Cs_collect_sid" value="'+data.sid+'" />');
                }
            }
        }
    });
}
//设置收藏
function ct_collect(obj){
    var favoriteact = 2;                    //取消
    if($(obj).find('span').html() == '收藏'){
        favoriteact = 1;                    //收藏
    }
    var sid = 0;
    if($("#Cs_collect_sid").length>0){
        sid = $("#Cs_collect_sid").val();
    }
    var url = myuserhost + "/api/setfavorite";
    var ctype = typeof(collect_type) != "undefined" ? collect_type : 1;
    var pageurl = "";
    if(typeof(collect_pageurl) != "undefined"){
        pageurl = collect_pageurl;
    }
    $.ajax({
        url: url,
        //async:false,
        type: "POST",
        data:{sid:sid,favoriteact:favoriteact,type:ctype,pageurl:pageurl},
        dataType:'json',
        xhrFields:{withCredentials:true},
        success: function(data){
            if(data.code == 1){
                if(favoriteact == 1){
                    if($("#Cs_collect_sid").length == 0){
                        $(".ction_btn").append('<input type="hidden" id="Cs_collect_sid" value="'+data.sid+'" />');
                    }
                    $(obj).find('i').addClass('on');
                    $(obj).find('span').text('取消');
                }else{
                    $(obj).find('i').removeClass('on');
                    $(obj).find('span').text('收藏');
                }
            }else{
                tipsmsg(data.msg);
                if(typeof data.url !== "undefined"){
                    top.location.href = myuserhost + data.url;
                }
            }
        }
    });
}
//获取文章举报状态
function getpagereport(){
    var url = myuserhost + "/api/getpagereport";
    var pageurl = "";
    if(typeof(collect_pageurl) != "undefined"){
        pageurl = collect_pageurl;
    }
    $.ajax({
        url: url,
        type: "POST",
        data:{pageurl:pageurl},
        dataType:'json',
        xhrFields:{withCredentials:true},
        success: function(data){
            if(data.code == 1){
                if(data.pagereport){
                    $("#Cs_report_bt").removeAttr("onclick");
                    $("#Cs_report_bt").addClass("on");
                }
            }
        }
    });
}

//点击打开页面举报框；
function Ct_report(){
    if($(".layer_Report").length < 1){
        var Report_html = '';
        Report_html += '<div class="layer_Report">';
        Report_html += '<div class="bt">3DM违法和不良信息举报</div>';
        Report_html += '<div class="inputbox input_list">';
        Report_html += '<div class="bt_name">标题：</div>';
        Report_html += '<div class="input infor"><input id="Report_title" /></div>';
        Report_html += '</div>';
        Report_html += '<div class="textareabox input_list">';
        Report_html += '<div class="bt_name">内容：</div>';
        Report_html += '<div class="textarea infor"><textarea id="Report_textarea"></textarea></div>';
        Report_html += '</div>';
        Report_html += '<div class="Cont_information input_list">';
        Report_html += '<div class="bt_name">联系人：</div>';
        Report_html += '<div class=" infor"><input id="Report_tel" class="number_tel"  placeholder="*留下你的联系方式，如（QQ：123456）" /><div class="inputck"><input type="checkbox"id="Report_inputck" ><label for="inputck">匿名</label></div></div>';
        Report_html += '</div>';
        Report_html += '<div class="text_ input_list">';
        Report_html += '<span class="tis">3DM违法和不良信息举报联系方式：</span>';
        Report_html += '<div class="p_lis"><p>QQ : 2801467619</p><p>E-mail : 21143517@qq.com</p></div></div>';
        Report_html += '<div class="bot_btn">';
        Report_html += '<div class="btn btn1" onclick="page_report(this)">确定</div>';
        Report_html += '<div class="btn btn2" onclick="page_report_cancel(this)">取消</div>';
        Report_html += '</div>';
        Report_html += '</div>';
        $("body").append(Report_html);
    }
    layer_Report = layer.open({
        type: 1,
        id:"layer_Reportbox",
        title: false,
        closeBtn: 2,
        shadeClose: false,
        area: ['95%', 'auto'],
        content: $('.layer_Report')
    });
    $("#Report_inputck").click(function () {
        if($(this).prop("checked")==true){
            $(".layer_Report #Report_tel").val("");
            $(".layer_Report #Report_tel").attr("disabled","disabled");
            $(".layer_Report #Report_tel").css({"background":"#d8d3d3"});
        }else{
            $(".layer_Report #Report_tel").removeAttr("disabled");
            $(".layer_Report #Report_tel").css({"background":"#f6f6f6"});
        }
    });
    //监听发表举报时候的按钮的颜色的变化
    $(document).on("input propertychange","#Report_textarea",function(){
        var val_=$("#Report_textarea").val();
        if(val_.length >4){
            $(".layer_Report .bot_btn .btn1").addClass("on")
        }else{
            $(".layer_Report .bot_btn .btn1").removeClass("on")
        }
    });
}
//提交页面举报
function page_report(obj){
    if(g_pagereport){
        alert('发送中，请稍后。。。');
    }
    var url = myuserhost + "/api/pagereport";
    var title = htmlEncodeJQ(trim($("#Report_title").val()));
    var content = htmlEncodeJQ(trim($("#Report_textarea").val()));
    var tel = htmlEncodeJQ(trim($("#Report_tel").val()));
    var len = content.length;
    if(len < 1){
        layer.msg('请输入举报内容');
        return false;
    }else if(len<4){
        layer.msg('输入的举报内容太短');
        return false;
    }
    if(len>600){
        layer.msg("举报内容已超出最大长度600字");
        return false;
    }
    g_pagereport = 1;
    var sid = 0;
    if($("#Ct_sid").length>0){
        sid = $("#Ct_sid").val();
    }
    var pageurl = "";
    if(typeof(collect_pageurl) != "undefined"){
        pageurl = collect_pageurl;
    }
    $.ajax({
        url:url,
        type: "POST",
        data:{title:title,content:content,tel:tel,sid:sid,pageurl:pageurl},
        dataType:'json',
        xhrFields:{withCredentials:true},
        success: function(data){
            layer.close(layer_Report);
            if(data.code == 1){
                layer.msg('举报成功');
                if($("#Ct_sid").length==0){
                    sidstr = '<input type="hidden" id="Ct_sid" value="'+data.sid+'" />';
                    $("#Comments_wrap").append(sidstr);
                }
                $("#Cs_report_bt").removeAttr("onclick");
                $("#Cs_report_bt").addClass("on");
                $(obj).removeAttr("onclick");
                $('.Cs_report_show').hide();
            }else if(data.code == 9){
                openlogin();
            }else{
                layer.msg(data.msg);
                if(typeof data.url !== "undefined"){
                    top.location.href = myuserhost + data.url;
                }
            }
        },
        complete: function(){
            g_pagereport = 0;
            layer.close(layer_Report);
        }
    });
}
//取消页面举报提交
function page_report_cancel(obj){
    layer.close(layer_Report);
} 

function trim(str){
    return str.replace(/(^\s*)|(\s*$)/g, "");
}
function set_g_userinfo(user){
    if(typeof(user.uid) == "undefined" || !isRealNum(user.uid) || user.uid<1){
        return false;
    }
    g_user_info.uid = user.uid;
    g_user_info.nickname = typeof(user.nickname) == "undefined" ? '' : user.nickname;
    g_user_info.avatarstr = typeof(user.avatarstr) == "undefined" ? '' : user.avatarstr;
    g_user_info.gender = typeof(user.gender) == "undefined" ? '' : user.gender;
    g_user_info.regionstr = typeof(user.regionstr) == "undefined" ? '' : user.regionstr;
    g_user_info.title = typeof(user.title) == "undefined" ? '' : user.title;
    g_user_info.title_level = typeof(user.title_level) == "undefined" ? 0 : user.title_level;
    return true;
}
function set_g_comment_post(pid,content,position){
    g_comment_list[pid] = {id:0,position:position,goodcount:0,badcount:0,content:'',time:'刚刚',user:{},replies:[]};
    g_comment_list[pid].id = pid;
    g_comment_list[pid].content = content;
    g_comment_list[pid].user = g_user_info;
    g_comment_list[pid].replies = new Array();
}
function set_g_comment_reply(pid,content,position,replyid){
    g_comment_list[pid] = {id:0,position:position,goodcount:0,badcount:0,content:'',time:'刚刚',user:{},replies:[]};
    g_comment_list[pid].id = pid;
    g_comment_list[pid].content = content;
    g_comment_list[pid].user = g_user_info;
    if(typeof(g_comment_list[replyid]) == "undefined"){
        g_comment_list[pid].replies = new Array();
    }else{
        var listreplies = g_comment_list[replyid].replies;
        var replies = Array();
        replies[0] = g_comment_list[replyid];
        var len = listreplies.length;
        for(var i=0;i<len;i++){
            replies[i+1] = listreplies[i];
        }
        g_comment_list[pid].replies = replies;
    }
}
function set_g_comment_reply_list(pid,content,position,replyid,listid){
        g_comment_list[pid] = {id:0,position:position,goodcount:0,badcount:0,content:'',time:'刚刚',user:{},replies:[]};
    g_comment_list[pid].id = pid;
    g_comment_list[pid].content = content;
    g_comment_list[pid].user = g_user_info;
    if(typeof(g_comment_list[replyid]) == "undefined"){
        if(typeof(g_comment_list[listid]) == "undefined"){
            g_comment_list[pid].replies = new Array();
        }else{
            var listreplies = g_comment_list[listid].replies;
            var len = listreplies.length;
            var replies = new Array();
            var pos_index = -1;
            for(var i=0;i<len; i++){
                if(listreplies[i].id == replyid){
                    pos_index = i;//找到位置
                }
            }
            if(pos_index>-1){
                var j=0;
                for(var i=pos_index;i<len; i++){
                    replies[j++] = listreplies[i];
                }
            }
            g_comment_list[pid].replies = replies;
        }
    }else{
        var listreplies = g_comment_list[replyid].replies;
        var replies = Array();
        replies[0] = g_comment_list[replyid];
        var len = listreplies.length;
        for(var i=0;i<len;i++){
            replies[i+1] = listreplies[i];
        }
        g_comment_list[pid].replies = replies;
    }
}
function isRealNum(val){
    // isNaN()函数 把空串 空格 以及NUll 按照0来处理 所以先去除
    if(val === "" || val ==null){
        return false;
    }
    if(!isNaN(val)){
        return true;
    }else{
        return false;
    }
}


//文字提示弹窗 自动关闭
window.tipsmsg = function (txt) {
    if($('.tips_wind').size()<=0){
        $('body').append('<div class="tips_wind"><span>'+txt+'</span></div>')
        $('.tips_wind').fadeIn()
        setTimeout(function () {
            $('.tips_wind').fadeOut(function () {
                $('.tips_wind').remove()
            });
        },1000)
    }
}
//获取评论表情框
function getCtFaceBox(){
    var popFace_bt = [
        {"text_":"默认表情","id_":"0"},
         {"text_":"无主之地","id_":"1"}   				
    ];
    var popFace_img=[
        [
            { "name":"微笑" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico1.png" },
            { "name":"爱心" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico2.png"},
            { "name":"委屈" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico3.png" },
            { "name":"害羞" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico4.png" },
            { "name":"闭嘴" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico5.png"},
            { "name":"犯困" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico6.png" },
            { "name":"大哭" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico7.png" },         
            { "name":"尴尬" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico8.png" },
            { "name":"生气" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico9.png"},
            { "name":"可爱" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico10.png" },
            { "name":"赞个" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico11.png" },
            { "name":"怀疑" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico12.png"},
            { "name":"汗" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico13.png" },
            { "name":"鄙视" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico14.png" },        
            { "name":"呆" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico15.png"},
            { "name":"辣" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico16.png"},
            { "name":"坏笑" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico17.png" },
            { "name":"机智" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico18.png" },
            { "name":"晕" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico19.png"},
            { "name":"思考" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico20.png" }             
        ],
       [
			{ "name":"晚安" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico2/ico1.png" },
			{ "name":"拿来" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico2/ico2.png"},
			{ "name":"困" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico2/ico3.png" },
			{ "name":"疑惑" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico2/ico4.png" },
			{ "name":"666" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico2/ico5.png"},
			{ "name":"555" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico2/ico6.png" },
			{ "name":"生气2" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico2/ico7.png" },			
			{ "name":"全要" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico2/ico8.png" },
			{ "name":"吃瓜" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico2/ico9.png"},
			{ "name":"棒" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico2/ico10.png" },
			{ "name":"观察" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico2/ico11.png" },
			{ "name":"我的锅" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico2/ico12.png"},
			{ "name":"无奈" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico2/ico13.png" },
			{ "name":"嘲笑" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico2/ico14.png" },		
			{ "name":"绝望" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico2/ico15.png"},
			{ "name":"约吗" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico2/ico16.png"}
	
		]			
    ];
    var index_id=0;
    var this_;
    var popFace_='<div class="popFace">';
        popFace_ += '<div class="popFace_bt" onclick="openCtFaceBox(this,event)"><i class="ico_bq_bt"></i></div>';
        popFace_ += '<div class="popFaceBox">';
        popFace_ += '<div class="popFaceBox_close" onclick="closeCtFaceBox(this)"></div>';
        popFace_ += '<div class="p_item">';
        for(var i=0; i<=popFace_bt.length-1; i++){
            if(i == 0){
                popFace_ += '<p data-id="'+popFace_bt[i].id_+'" class="on" onclick="changeFaceItem(this,'+i+',event)">'+popFace_bt[i].text_+'</p>';
            }else{
                popFace_ += '<p data-id="'+popFace_bt[i].id_+'" onclick="changeFaceItem(this,'+i+',event)">'+popFace_bt[i].text_+'</p>';
            }
        }
        popFace_ += '</div>';
        popFace_ += '<div class="popFace_lis">';
        for(var i=0; i<=popFace_bt.length-1; i++){
            if(i == 0){
                popFace_ += '<div class="face'+i+' face">';
            }else{
                popFace_ += '<div class="face'+i+'">';
            }
            if(typeof(popFace_img[i]) == "undefined"){
                continue;
            }
            var faceImgLen = popFace_img[i].length;
            for(var j=0; j<faceImgLen; j++){
                popFace_ += '<a href="javascript:void(0);" onclick="addCtFace(this,\''+popFace_img[i][j].name+'\','+i+')"><img src="'+popFace_img[i][j].img_+'" title="'+popFace_img[i][j].name+'"><i>'+popFace_img[i][j].name+'</i></a>';
            }
            popFace_ += '</div>';
        }
        popFace_ += '</div>';
        popFace_ += '</div>';       
        popFace_ += '</div>';
        return popFace_;   
}
//获取回复表情框
function getReplyFaceBox(){
    var popFace_bt = [
        {"text_":"默认表情","id_":"0"},
          {"text_":"无主之地","id_":"1"},    				
    ];
    var popFace_img=[
        [
            { "name":"微笑" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico1.png" },
            { "name":"爱心" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico2.png"},
            { "name":"委屈" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico3.png" },
            { "name":"害羞" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico4.png" },
            { "name":"闭嘴" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico5.png"},
            { "name":"犯困" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico6.png" },
            { "name":"大哭" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico7.png" },         
            { "name":"尴尬" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico8.png" },
            { "name":"生气" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico9.png"},
            { "name":"可爱" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico10.png" },
            { "name":"赞个" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico11.png" },
            { "name":"怀疑" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico12.png"},
            { "name":"汗" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico13.png" },
            { "name":"鄙视" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico14.png" },        
            { "name":"呆" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico15.png"},
            { "name":"辣" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico16.png"},
            { "name":"坏笑" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico17.png" },
            { "name":"机智" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico18.png" },
            { "name":"晕" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico19.png"},
            { "name":"思考" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico/ico20.png" }             
        ],
       [
			{ "name":"晚安" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico2/ico1.png" },
			{ "name":"拿来" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico2/ico2.png"},
			{ "name":"困" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico2/ico3.png" },
			{ "name":"疑惑" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico2/ico4.png" },
			{ "name":"666" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico2/ico5.png"},
			{ "name":"555" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico2/ico6.png" },
			{ "name":"生气2" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico2/ico7.png" },			
			{ "name":"全要" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico2/ico8.png" },
			{ "name":"吃瓜" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico2/ico9.png"},
			{ "name":"棒" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico2/ico10.png" },
			{ "name":"观察" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico2/ico11.png" },
			{ "name":"我的锅" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico2/ico12.png"},
			{ "name":"无奈" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico2/ico13.png" },
			{ "name":"嘲笑" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico2/ico14.png" },		
			{ "name":"绝望" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico2/ico15.png"},
			{ "name":"约吗" ,"img_":"https://my.3dmgame.com/ct/images/bq_ico2/ico16.png"}
	
		]			
    ];
    var index_id=0;
    var this_;
    var popFace_='<div class="popFace">';
        popFace_ += '<div class="popFace_bt" onclick="openReplyFaceBox(this,event)"><i class="ico_bq_bt"></i></div>';
        popFace_ += '<div class="popFaceBox">';
        popFace_ += '<div class="popFaceBox_close" onclick="closeReplyFaceBox(this)"></div>';
        popFace_ += '<div class="p_item">';
        for(var i=0; i<=popFace_bt.length-1; i++){
            if(i == 0){
                popFace_ += '<p data-id="'+popFace_bt[i].id_+'" class="on" onclick="changeFaceItem(this,'+i+',event)">'+popFace_bt[i].text_+'</p>';
            }else{
                popFace_ += '<p data-id="'+popFace_bt[i].id_+'" onclick="changeFaceItem(this,'+i+',event)">'+popFace_bt[i].text_+'</p>';
            }
        }
        popFace_ += '</div>';
        popFace_ += '<div class="popFace_lis">';
        for(var i=0; i<=popFace_bt.length-1; i++){
            if(i == 0){
                popFace_ += '<div class="face'+i+' face">';
            }else{
                popFace_ += '<div class="face'+i+'">';
            }
            if(typeof(popFace_img[i]) == "undefined"){
                continue;
            }
            var faceImgLen = popFace_img[i].length;
            for(var j=0; j<faceImgLen; j++){
                popFace_ += '<a href="javascript:void(0);" onclick="addReplyFace(this,\''+popFace_img[i][j].name+'\','+i+')"><img src="'+popFace_img[i][j].img_+'" title="'+popFace_img[i][j].name+'"><i>'+popFace_img[i][j].name+'</i></a>';
            }
            popFace_ += '</div>';
        }
        popFace_ += '</div>';
        popFace_ += '</div>';       
        popFace_ += '</div>';
        return popFace_;
}
//展示评论表情框
function openCtFaceBox(obj,evt){
    $(".popFaceBox").hide();
    hide_open_reply();
    $(obj).parents(".popFace").find(".popFaceBox").show();
    stopNextEvent(evt);
}
//展示回复表情框
function openReplyFaceBox(obj,evt){
    $(".popFaceBox").hide();
    $(obj).parents(".popFace").find(".popFaceBox").show();
    stopNextEvent(evt);
}
//关闭评论表情框
function closeCtFaceBox(obj){
    $(obj).parents(".popFaceBox").hide();
}
//关闭回复表情框
function closeReplyFaceBox(obj){
    $(obj).parents(".popFaceBox").hide();
}
//切换评论表情的主题
function changeFaceItem(obj,index,evt){
	stopNextEvent(evt);
    $(obj).addClass("on").siblings().removeClass("on");
    var popFace_lis = $(obj).parent().parent().find(".popFace_lis");
    popFace_lis.children().hide();
    popFace_lis.find(".face").removeClass("face");
    popFace_lis.find(".face"+index).addClass("face");
    popFace_lis.find(".face"+index).show();
}
//添加评论表情
function addCtFace(obj, face, type){
    var face_pop = $(obj).parent().parent().parent();
    var faceText = '';
    faceText = '['+face+']';
    $(".cmt_login .cmt-boxtextarea").insertAtCaret(faceText);
    face_pop.hide();
}
//添加回复表情
function addReplyFace(obj, face, type){
    var face_pop = $(obj).parent().parent().parent();
    var face_box = face_pop.parent();
    var reply_content = face_box.parent().parent().find('.cmt-boxtextarea');
    var faceText = '';
    faceText = '['+face+']';
    reply_content.insertAtCaret(faceText);
    face_pop.hide();
}
function replaceFaceContent(content){
    var find_face = ['\\[微笑\\]','\\[爱心\\]','\\[委屈\\]','\\[害羞\\]','\\[闭嘴\\]','\\[犯困\\]','\\[大哭\\]','\\[尴尬\\]','\\[生气\\]','\\[可爱\\]','\\[赞个\\]','\\[怀疑\\]','\\[汗\\]','\\[鄙视\\]','\\[呆\\]','\\[辣\\]','\\[坏笑\\]','\\[机智\\]','\\[晕\\]','\\[思考\\]','\\[晚安\\]','\\[拿来\\]','\\[困\\]','\\[疑惑\\]','\\[666\\]','\\[555\\]','\\[生气2\\]','\\[全要\\]','\\[吃瓜\\]','\\[棒\\]','\\[观察\\]','\\[我的锅\\]','\\[无奈\\]','\\[嘲笑\\]','\\[绝望\\]','\\[约吗\\]'];
    var replace_face = [
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico/ico1.png" />',
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico/ico2.png" />',
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico/ico3.png" />',
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico/ico4.png" />',
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico/ico5.png" />',
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico/ico6.png" />',
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico/ico7.png" />',
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico/ico8.png" />',
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico/ico9.png" />',
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico/ico10.png" />',
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico/ico11.png" />',
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico/ico12.png" />',
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico/ico13.png" />',
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico/ico14.png" />',
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico/ico15.png" />',
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico/ico16.png" />',
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico/ico17.png" />',
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico/ico18.png" />',
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico/ico19.png" />',
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico/ico20.png" />',
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico2/ico1.png" />',
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico2/ico2.png" />',
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico2/ico3.png" />',
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico2/ico4.png" />',
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico2/ico5.png" />',
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico2/ico6.png" />',
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico2/ico7.png" />',
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico2/ico8.png" />',
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico2/ico9.png" />',
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico2/ico10.png" />',
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico2/ico11.png" />',
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico2/ico12.png" />',
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico2/ico13.png" />',
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico2/ico14.png" />',
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico2/ico15.png" />',
                        '<img src="https://my.3dmgame.com/ct/images/bq_ico2/ico16.png" />',
                    ];
    var len_face = find_face.length;
    for (var i=0;i<len_face;i++) {
        content = content.replace(new RegExp(find_face[i],"g"), replace_face[i]);
    }
    return content;
}
(function ($) {
    $.fn.extend({
        insertAtCaret: function (myValue) {
            var $t = $(this)[0];
            if (document.selection) {
                this.focus();
                sel = document.selection.createRange();
                sel.text = myValue;
                this.focus();
            } else if ($t.selectionStart || $t.selectionStart == '0') {
                var startPos = $t.selectionStart;
                var endPos = $t.selectionEnd;
                var scrollTop = $t.scrollTop;
                $t.value = $t.value.substring(0, startPos) + myValue + $t.value.substring(endPos, $t.value.length);
                this.focus();
                $t.selectionStart = startPos + myValue.length;
                $t.selectionEnd = startPos + myValue.length;
                $t.scrollTop = scrollTop;
            } else {
                this.value += myValue;
                this.focus();
            }
        }
    });
})(jQuery);
//阻止事件冒泡
function stopNextEvent(evt){
    var e=(evt)?evt:window.event;
    if (window.event) {
        e.cancelBubble=true;// ie下阻止冒泡
    } else {
        //e.preventDefault();
        e.stopPropagation();// 其它浏览器下阻止冒泡
    }
}

//3DM官方交流群
$(function(){
    setOfficialgroupNew();
});

function setOfficialgroupNew(){
    $(".comments_wrap").children(".cmt_item_head").append('<div class="official_group"><a href="https://jq.qq.com/?_wv=1027&k=4Ms7mxqc" target="_blank">3DM官方交流群</a></div>');
}

function setOfficialgroupHot(){
    $("div.official_group").remove();
    $(".comments_wrap .cmt_item_head:first").append('<div class="official_group"><a href="https://jq.qq.com/?_wv=1027&k=4Ms7mxqc" target="_blank">3DM官方交流群</a></div>');
}
//评论@好友
function getFriends() {
    if(!g_friends_flag){
        var url = myuserhost + "/api/getctfollows";
        $.ajax({
            url:url,
            type: "POST",
            dataType:'json',
            xhrFields:{withCredentials:true},
            success: function(data){
                if(data.code == 1){
                    var actLen = data.data.act.length;
                    if(actLen > 0){
                        for (var i = 0; i < actLen; i++) {
                            var obj1 = new Object();
                            obj1.uid = data.data.act[i].uid;
                            obj1.nickname = data.data.act[i].nickname;
                            g_lastconcat_arr[i] = obj1;
                        }
                    }
                    var followLen = data.data.follow.length;
                    if(followLen > 0){
                        for (var i = 0; i < followLen; i++) {
                            var  obj1 = new Object();
                            obj1.uid = data.data.follow[i].uid;
                            obj1.nickname = data.data.follow[i].nickname;
                            switch(data.data.follow[i].nick_first){
                                case 'A':
                                    g_friends_arr[0]['list'].push(obj1);
                                    break;
                                case 'B':
                                    g_friends_arr[1]['list'].push(obj1);
                                    break;
                                case 'C':
                                    g_friends_arr[2]['list'].push(obj1);
                                    break;
                                case 'D':
                                    g_friends_arr[3]['list'].push(obj1);
                                    break;
                                case 'E':
                                    g_friends_arr[4]['list'].push(obj1);
                                    break;
                                case 'F':
                                    g_friends_arr[5]['list'].push(obj1);
                                    break;
                                case 'G':
                                    g_friends_arr[6]['list'].push(obj1);
                                    break;
                                case 'H':
                                    g_friends_arr[7]['list'].push(obj1);
                                    break;
                                case 'I':
                                    g_friends_arr[8]['list'].push(obj1);
                                    break;
                                case 'J':
                                    g_friends_arr[9]['list'].push(obj1);
                                    break;
                                case 'K':
                                    g_friends_arr[10]['list'].push(obj1);
                                    break;
                                case 'L':
                                    g_friends_arr[11]['list'].push(obj1);
                                    break;
                                case 'M':
                                    g_friends_arr[12]['list'].push(obj1);
                                    break;
                                case 'N':
                                    g_friends_arr[13]['list'].push(obj1);
                                    break;
                                case 'O':
                                    g_friends_arr[14]['list'].push(obj1);
                                    break;
                                case 'P':
                                    g_friends_arr[15]['list'].push(obj1);
                                    break;
                                case 'Q':
                                    g_friends_arr[16]['list'].push(obj1);
                                    break;
                                case 'R':
                                    g_friends_arr[17]['list'].push(obj1);
                                    break;
                                case 'S':
                                    g_friends_arr[18]['list'].push(obj1);
                                    break;
                                case 'T':
                                    g_friends_arr[19]['list'].push(obj1);
                                    break;
                                case 'U':
                                    g_friends_arr[20]['list'].push(obj1);
                                    break;
                                case 'V':
                                    g_friends_arr[21]['list'].push(obj1);
                                    break;
                                case 'W':
                                    g_friends_arr[22]['list'].push(obj1);
                                    break;
                                case 'X':
                                    g_friends_arr[23]['list'].push(obj1);
                                    break;
                                case 'Y':
                                    g_friends_arr[24]['list'].push(obj1);
                                    break;
                                case 'Z':
                                    g_friends_arr[25]['list'].push(obj1);
                                    break;
                                case '#':
                                    g_friends_arr[26]['list'].push(obj1);
                                    break;
                            }
                        }
                    }
                }
            },
            complete: function(){
                g_friends_flag = 0;
                    //好友数组
                var friends_html='';
                friends_html+='<div class="friends_warp"><div class="friends_bt" onclick="friends(this)"></div>';
                friends_html+='<div class="friendsBox"><div class="popup"><span></span></div><div class="box_">';
                friends_html+='<div class="friends_lately"><div class="p"><span class="bt_">最近联系：</span>';
                if(g_lastconcat_arr.length){
                    for (var i = 0; i <= g_lastconcat_arr.length-1; i++) {
                        friends_html += '<a class="a" data-follow-uid='+g_lastconcat_arr[i].uid+' onclick="add_atlastfollow(this,\''+g_lastconcat_arr[i].nickname+'\')">'+g_lastconcat_arr[i].nickname+'</a>';
                    }
                }
                friends_html+='</div></div>';
                friends_html+='<div class="friends_ul">';
                for(var i=0;i<=g_friends_arr.length-1;i++){
                    friends_html+='<a class="a_"  onclick="friendsul_data(this,event)">'+ g_friends_arr[i].name + '</a>';
                }
                friends_html+='</div>';
                friends_html+='<div class="friends_data">';
                for(var i=0;i<=g_friends_arr.length-1;i++){
                    friends_html+='<div class="friends_lis" >';
                    if(i!=0){
                        friends_html+='<div class="bt_">'+ g_friends_arr[i].name + '</div>';
                    }
                    friends_html+='<div class="friends_name_box">';
                    for(var a=0;a<=g_friends_arr[i].list.length-1;a++){
                        friends_html+='<a href="javascript:void(0);" class="a" data-follow-uid='+g_friends_arr[i].list[a].uid+' onclick="add_atfollow(this,\''+g_friends_arr[i].list[a].nickname+'\')"> '+ g_friends_arr[i].list[a].nickname + '</a>';
                    }
                    friends_html+='</div></div>';
                }
                friends_html+='</div><div class="close_btn" onclick="closefriends()">取消</div></div></div></div>';
                $(".popFace").before(friends_html);
            }
        });
    }
}
//  显示好友弹框
function friends(obj,evt){
    $(obj).parents(".friends_warp").find(".friendsBox").show();
    stopNextEvent(evt);
	var box_= $(obj).parents(".friends_warp");
	 var het1=box_.find(".friendsBox .box_ ").height();
	 var het2=box_.find(".friendsBox .friends_lately ").height();
	 var het3=box_.find(".friendsBox .friends_ul ").height();
	 box_.find(".friendsBox .friends_data ").css({"height":het1-het2-het3-120+"px"})
}
//定位好友位置
function friendsul_data(obj,evt){
	var index_=$(obj).index();	
	var box_=$(obj).parents(".friendsBox")
	var scroll_top=box_.find(".friends_data").scrollTop();
	var nav_num=box_.find(".friends_data .friends_lis").eq(index_).position().top;
	 stopNextEvent(evt);
	$(obj).addClass("on").siblings().removeClass("on");
	box_.find(".friends_data").animate({scrollTop:nav_num + scroll_top}, 300);
}	
    //点击好友名
   function closefriends(){   
	   $(".friendsBox").hide();
   }
 
 //关注
function follow_personal(obj, follow_uid){
	var act = 1;
	if($(obj).hasClass("on")){
		act = 2;
	}
	var url = myuserhost + "/api/setfollow";
	$.ajax({
		url: url,
		type: "POST",
		data:{follow_uid:follow_uid, follow_act:act},
		dataType:'json',
		xhrFields:{withCredentials:true},
		success: function(data){
			if(data.code == 1){
				if($(obj).hasClass("on")){
					$(obj).removeClass("on");
				}else{
					$(obj).addClass("on");
				}
			}else if(data.code == 9){
                openlogin();
            }
		}
	});
}
//添加@符号
function add_atfollow(obj, nickname){
    var follow_pop = $(obj).parent().parent().parent().parent().parent().parent();
    var follow_box = follow_pop.parent();
    var reply_content = follow_box.find('textarea');
    var followText = '';
    followText = '@'+nickname+' ';
    reply_content.insertAtCaret(followText);
    $(".friends_warp").hide();
}
//添加@最近联系人
function add_atlastfollow(obj, nickname){
    var follow_pop = $(obj).parent().parent().parent().parent().parent();
    var follow_box = follow_pop.parent();
    var reply_content = follow_box.find('textarea');
    var followText = '';
    followText = '@'+nickname+' ';
    reply_content.insertAtCaret(followText);
    $(".friends_warp").hide();
}

function callback_pingluncaptcha_back(data){
    if(data.code == 1){
        ct_post();
    }else{
        alert(data.msg);
        return false;
    }
    return true;
}

function callback_huifucaptcha_back(data){
    if(data.code == 1){
        ct_reply(g_huifucaptcha_obj, g_huifucaptcha_id);
    }else{
        alert(data.msg);
        return false;
    }
    return true;
}
var myuserhost = "https://my.3dmgame.com";
var g_loginuserid = 0,g_posting=0,g_repling=0,g_praising=0,g_report=0,g_pagereport=0,zhztid=0,placemsg='点评一下...',g_huifucaptcha_obj=null,g_huifucaptcha_id=0;
var g_comment_list = new Array();
var g_user_info = {uid:0,nickname:'',avatarstr:'',gender:1,regionstr:'',title:'',title_level:0};
var g_ct_order = "";
var layer_Report = null;
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
    {"name":"#","list":[]}
];
var g_ct_irefer = false;
if(window.XDomainRequest){
	g_ct_irefer = true;
}
$(function(){
	init();//初始化
	var Cs_W = $('#Comments_wrap').width();
	$(window).resize(function(){
		Cs_W = $('#Comments_wrap').width();
		W_resize();	
	});
	W_resize();
	if($('#zhztid').length > 0){
		zhztid = $('#zhztid').val();
        placemsg = 'E3你怎么看？';
	}
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
	$(obj).parent('.floor_item').siblings('.floor_item').show();
	$(obj).parent('.floor_item').remove();
}
//展示隐藏的评论内容
function showdetail(obj){
	$(obj).parent().css('max-height','none');
	$(obj).remove();
}
function W_resize(){
	var Cs_W = $('#Comments_wrap').width();
	$('.Cslis_wrap .cont_w').css('width',Cs_W - 82 +'px');
	$('.Cslis_wrap .cont-name p').css('width',Cs_W - 195 +'px');
}
function init(){
	//获取评论列表
	$('#Ct_top_total').text(0);
	initpostlist(0);
	//获取收藏状态
	getcollection();
	//获取文章举报状态
	getpagereport();
	//举报关闭框
	$(".Cs_report .Cs_report_show .close").click(function(){
		$('.Cs_report_show').hide()
	});
	$("#Ct_content").focus(function(){
		  $(".friendsBox").hide();//关闭@耗油
		$(".popFaceBox").hide();//关闭表情框
		hide_open_reply();
	});
	$("body").on("click", function(){
		$(".popFaceBox").hide();//关闭表情框
		  $(".friendsBox").hide();//关闭@耗油
	});

	
}
//显示好友弹框
function friends(obj,evt){
	$(".popFaceBox").hide();//关闭表情框
    $(obj).parents(".friends_warp").find(".friendsBox").show();
    stopNextEvent(evt);
}

function setmyctuserlogin(user){
	g_loginuserid = user.uid;
	$("#Ct_login").html('<div class="tx"><img src="'+user.avatarstr+'"/></div><div class="name">'+user.nickname+'</div>');
	$("#Ct_login").addClass('user_tx');
	$("#Ct_content").attr('placeholder', placemsg);
    $("#Ct_content").attr('onfocus', 'this.placeholder=""');
    $("#Ct_content").attr('onfocusout', 'this.placeholder="'+placemsg+'"');
    if($("#Comments_wrap .Cs_postwrap .txtwrap .poswrap .popFace").length < 1){
    	$("#Comments_wrap .Cs_postwrap .txtwrap .poswrap .postbtn").before(getCtFaceBox());
    	getFriends();
    }
	set_g_userinfo(user);
}
function ct_login(){
	var username = trim($("#username").val());
	var passwd = trim($("#passwd").val());
	if(username == ''){
		$("#username").focus();
		return false;
	}
	if(passwd == ''){
		$("#passwd").focus();
		return false;
	}
	posting = 1;
	if(g_ct_irefer){
		var url = myuserhost + "/jsonpapi/login?irefer="+encodeURIComponent(top.location.href);
		$.ajax({
			url:url,
			type: "GET",
			data:{username:username, passwd:passwd},
			dataType:'jsonp',
			jsonp: "jsonpcallback",
			success: function(data){
				ct_login_back(data);
				posting = 0;
			}
		});
	}else{
		var url = myuserhost + "/api/login?irefer="+encodeURIComponent(top.location.href);
		$.ajax({
			url:url,
			type: "POST",
			data:{username:username, passwd:passwd},
			dataType:'json',
			xhrFields:{withCredentials:true},
			success: function(data){
				ct_login_back(data);
				posting = 0;
			}
		});
	}
}
//初始化评论列表
function initpostlist(refresh){
	var page = 1;
	var maxid = 0;
	var total = 0;
	var sid = 0;
	var pagesize = 20;
	var pageurl = "";
	if(typeof(collect_pageurl) != "undefined"){
		pageurl = collect_pageurl;
	}
	if(g_ct_irefer){
		var url = myuserhost + "/jsonpapi/postlist?irefer="+encodeURIComponent(top.location.href);
		$.ajax({
			url: url,
			//async:false,
			type: "GET",
			data:{maxid:maxid,total:total,page:page,pagesize:pagesize,pageurl:pageurl,isinit:1},
			dataType:'jsonp',
			jsonp: "jsonpcallback",
			success: function(data){
				ct_initpostlist_back(data, refresh, page, pagesize);
			}
		});
	}else{
		var url = myuserhost + "/api/postlist?irefer="+encodeURIComponent(top.location.href);
		$.ajax({
			url: url,
			//async:false,
			type: "POST",
			data:{maxid:maxid,total:total,page:page,pagesize:pagesize,pageurl:pageurl,isinit:1},
			dataType:'json',
			xhrFields:{withCredentials:true},
			success: function(data){
				ct_initpostlist_back(data, refresh, page, pagesize);
			}
		});
	}
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
	var pageurl = "";
	if(typeof(collect_pageurl) != "undefined"){
		pageurl = collect_pageurl;
	}
	if(g_ct_irefer){
		var url = myuserhost + "/jsonpapi/postlist?irefer="+encodeURIComponent(top.location.href);
		$.ajax({
			url: url,
			//async:false,
			type: "GET",
			data:{maxid:maxid,total:total,page:page,pagesize:pagesize,pageurl:pageurl,ordertype:g_ct_order},
			dataType:'jsonp',
			jsonp: "jsonpcallback",
			success: function(data){
				ct_getpostlist_back(data, page, pagesize, total);
			}
		});
	}else{
		var url = myuserhost + "/api/postlist?irefer="+encodeURIComponent(top.location.href);
		$.ajax({
			url: url,
			//async:false,
			type: "POST",
			data:{maxid:maxid,total:total,page:page,pagesize:pagesize,pageurl:pageurl,ordertype:g_ct_order},
			dataType:'json',
			xhrFields:{withCredentials:true},
			success: function(data){
				ct_getpostlist_back(data, page, pagesize, total);
			}
		});
	}
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
	if(g_posting){
		alert('发送中，请稍后。。。');
		return false;
	}
	var content = htmlEncodeJQ(trim($("#Ct_content").val())+ ' ');//防止最后一个@好友空格去掉
	var len = content.length;
	if(len<1){
		alert("评论内容太少了");
		return false;
	}
	if(len>1000){
		alert("评论内容已超出最大长度1000字");
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
	if(g_ct_irefer){
		var url = myuserhost + "/jsonpapi/post?irefer="+encodeURIComponent(top.location.href);
		$.ajax({
			url:url,
			type: "GET",
			data:{content:content,sid:sid,pageurl:pageurl},
			dataType:'jsonp',
			jsonp: "jsonpcallback",
			success: function(data){
				return ct_post_back(data);
			},
			complete: function(){
				g_posting = 0;
			}
		});
	}else{
		var url = myuserhost + "/api/post?irefer="+encodeURIComponent(top.location.href);
		$.ajax({
			url:url,
			type: "POST",
			data:{content:content,sid:sid,pageurl:pageurl},
			dataType:'json',
			xhrFields:{withCredentials:true},
			success: function(data){
				return ct_post_back(data);
			},
			complete: function(){
				g_posting = 0;
			}
		});
	}
}
//回复
function show_reply(obj){
	$(".popFaceBox").hide();//关闭表情框
	if(!$(obj).hasClass('replybtn2')){
		hide_open_reply();
		var t_wrap = $(obj).parent('.praise_btn').parent('.cont-address');
		t_wrap.find('.replybtn').toggle();
		t_wrap.next('.reply_wrap').slideDown();
	}else{
		var t_wrap = $(obj).parent('.praise_btn').parent('.cont-address');
		t_wrap.find('.replybtn').toggle();
		t_wrap.next('.reply_wrap').stop().slideUp();
	}
}
//l楼中楼回复
function show_reply_list(obj){	
	$(".popFaceBox").hide();//关闭表情框
	if(!$(obj).hasClass('replybtn2')){
		hide_open_reply();
		var t_wrap = $(obj).parents('.praise_btn').parents('.floor_item');
		t_wrap.find('.replybtn').toggle();
		t_wrap.find('.reply_wrap').slideDown();
	}else{
		var t_wrap = $(obj).parents('.praise_btn').parents('.floor_item');
		t_wrap.find('.replybtn').toggle();
		t_wrap.find('.reply_wrap').stop().slideUp();
	}
}
//关闭所有已展开的回复
function hide_open_reply(){
	//直接回复
	$(".cont-address .replybtn2:visible").each(function(){
		var show_wrap = $(this).parent('.praise_btn').parent('.cont-address');
		show_wrap.find('.replybtn').toggle();
		show_wrap.next('.reply_wrap').stop().slideUp();
	});
	//楼中楼回复
	$(".floor_item .replybtn2:visible").each(function(){
		var show_wrap = $(this).parents('.praise_btn').parents('.floor_item')
		show_wrap.find('.replybtn').toggle();
		show_wrap.find('.reply_wrap').stop().slideUp();
	});
}
//直接回复
function ct_reply(obj,id) {
	if(g_repling){
		alert('发送中，请稍后。。。');
		return false;
	}
	var content = htmlEncodeJQ( $(obj).parent().find('.reply_info').val() );
	var len = content.length;
	if(len<1){
		alert("回复内容太少了");
		return false;
	}
	if(len>1000){
		alert("回复内容已超出最大长度1000字");
		return false;
	}
	g_repling = 1;
	var pageurl = "";
	if(typeof(collect_pageurl) != "undefined"){
		pageurl = collect_pageurl;
	}
	g_huifucaptcha_obj = obj;
	g_huifucaptcha_id = id;
	if(g_ct_irefer){
		var url = myuserhost + "/jsonpapi/reply?irefer="+encodeURIComponent(top.location.href);
		$.ajax({
			url:url,
			type: "GET",
			data:{content:content, id:id, pageurl:pageurl},
			dataType:'jsonp',
			jsonp: "jsonpcallback",
			success: function(data){
				ct_reply_back(data, obj);
			},
			complete: function(){
				g_repling = 0;
			}
		});
	}else{
		var url = myuserhost + "/api/reply?irefer="+encodeURIComponent(top.location.href);
		$.ajax({
			url:url,
			type: "POST",
			data:{content:content, id:id, pageurl:pageurl},
			dataType:'json',
			xhrFields:{withCredentials:true},
			success: function(data){
				ct_reply_back(data, obj);
			},
			complete: function(){
				g_repling = 0;
			}
		});
	}
}
//楼中楼回复
function ct_reply_list(obj,id,listid) {
	if(g_repling){
		alert('发送中，请稍后。。。');
		return false;
	}
	var content = htmlEncodeJQ( $(obj).parent().find('.reply_info').val() );
	var len = content.length;
	if(len<1){
		alert("回复内容太少了");
		return false;
	}
	if(len>1000){
		alert("回复内容已超出最大长度1000字");
		return false;
	}
	g_repling = 1;
	var pageurl = "";
	if(typeof(collect_pageurl) != "undefined"){
		pageurl = collect_pageurl;
	}
	if(g_ct_irefer){
		var url = myuserhost + "/jsonpapi/reply?irefer="+encodeURIComponent(top.location.href);
		$.ajax({
			url:url,
			type: "GET",
			data:{content:content, id:id, pageurl:pageurl},
			dataType:'jsonp',
			jsonp: "jsonpcallback",
			success: function(data){
				ct_reply_list_back(data, obj);
			},
			complete: function(){
				g_repling = 0;
			}
		});
	}else{
		var url = myuserhost + "/api/reply?irefer="+encodeURIComponent(top.location.href);
		$.ajax({
			url:url,
			type: "POST",
			data:{content:content, id:id, pageurl:pageurl},
			dataType:'json',
			xhrFields:{withCredentials:true},
			success: function(data){
				ct_reply_list_back(data, obj);
			},
			complete: function(){
				g_repling = 0;
			}
		});
	}
}
//点赞
function praise(obj,ctid,type){
	if(g_praising){
		alert('发送中，请稍后。。。');
	}
	var pageurl = "";
	if(typeof(collect_pageurl) != "undefined"){
		pageurl = collect_pageurl;
	}
	if(g_ct_irefer){
		var url = myuserhost + "/jsonpapi/praise?irefer="+encodeURIComponent(top.location.href);
		$.ajax({
			url:url,
			type: "GET",
			data:{id:ctid, type:type, pageurl:pageurl},
			dataType:'jsonp',
			jsonp: "jsonpcallback",
			success: function(data){
				ct_praise_back(data, obj, type);
			},
			complete: function(){
				g_praising = 0;
			}
		});
	}else{
		var url = myuserhost + "/api/praise?irefer="+encodeURIComponent(top.location.href);
		$.ajax({
			url:url,
			type: "POST",
			data:{id:ctid, type:type, pageurl:pageurl},
			dataType:'json',
			xhrFields:{withCredentials:true},
			success: function(data){
				ct_praise_back(data, obj, type);
			},
			complete: function(){
				g_praising = 0;
			}
		});
	}
}
function getCommentsHtml(data,content){
	var str = '<div class="Cslis_item'+(data.user.nickname == '3DM官方账号' ? ' Official' : '')+'">';
		str += '<div class="cont-head">';
		str += '<a href="'+getUserHome(data.user.uid)+'" class="tx" target="_blank"><img src="'+data.user.avatarstr+'"/></a>';
		str += '<div class="cont-floor">第<span>'+data.position+'</span>楼</div>';
		if(typeof(data.follow) != "undefined" && data.follow == 1){
			str += '<div class="follow_box on" onclick="follow_personal(this, '+data.user.uid+')"></div>';
		}else if(typeof(data.follow) == "undefined" || data.follow != -1){
			str += '<div class="follow_box" onclick="follow_personal(this, '+data.user.uid+')"></div>';
		}
		str += '<div class="user_label user_uid_'+data.user.uid+'">';
		str += '<div class="user_top">';
        str += '<a href="'+getUserHome(data.user.uid)+'" class="img"><img  src="'+data.user.avatarstr+'"></a>';
        str += '<div class="infor">';
        str += '<div class="namebt">';
        str += '<a class="name_">'+data.user.nickname+'</a>';
        str += '<span class="bq">lv.'+(typeof(data.user.user_level) != "undefined" ? data.user.user_level : 1)+'</span>';
        str += '</div>';
        str += '<div class="name_text">'+(typeof(data.user.auth_title) != "undefined" ? data.user.auth_title : '')+'</div>';
        str += '</div>';
        str += '</div>';
        str += '<div class="lis_text">';
        str += '<div class="lis"><p class="bt">关注</p><p class="number">'+(typeof(data.user.follows) != "undefined" ? data.user.follows : '')+'</p></div>';
        str += '<div class="lis"><p class="bt">粉丝</p><p class="number">'+(typeof(data.user.fans) != "undefined" ? data.user.fans : '')+'</p></div>';
        str += '<div class="lis"><p class="bt">帖子</p><p class="number">'+(typeof(data.user.threads) != "undefined" ? data.user.threads : '')+'</p></div>';
        str += '</div>';
        if(typeof(data.follow) != "undefined" && data.follow == 1){
        	str += '<div class="btn_follow on" onclick="follow_personal(this, '+data.user.uid+')"> 已关注</div>';
        }else if(typeof(data.follow) == "undefined" || data.follow != -1){
        	str += '<div class="btn_follow" onclick="follow_personal(this, '+data.user.uid+')"> 关注ta</div>';
        }
        str += '</div>';
		str += '</div>';
		str += '<div class="cont-name cont_w">';
		str += '<p>';
		str += '<a href="'+getUserHome(data.user.uid)+'" target="_blank"><span>'+data.user.nickname+'</span></a>';
		if(typeof data.user.vip_level != "undefined" && data.user.vip_level > 0){
			str += '<a href="https://yeyou.3dmgame.com/vip/index" target="_blank" class="Cotilebt"><i class="ico ico3dmzy"></i>3DM自运营 VIP'+data.user.vip_level+'</a>';
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
		str += '</p>';
		str += '<div class="cont-time">'+data.time+'</div>';
		str += '</div>';
		if(data.replies.length>0){
			str += '<div class="floor_wrap cont_w">';
			str += getReplies(data.replies,data.id);
			str += '</div>';
		}
		str += '<div class="cont-message cont_w">';
		var nowcontent = content!='' ? content: data.content;
		if(nowcontent.length>=200){
			str += '<div class="cont-txt" style="max-height: 72px;">'+replaceFaceContent(nowcontent)+'<span class="conttxt-mor" onclick="showdetail(this)">...<span>查看更多</span></span></div>';
		}else{
			str += '<div class="cont-txt">'+replaceFaceContent(nowcontent)+'</div>';
		}
		str += '</div>';
		str += '<div class="cont-address cont_w">';
		str += '<p>'+data.user.regionstr+'网友</p>';
		str += '<div class="praise_btn">';
		if(typeof(data.praise)!="undefined" && (data.praise==1 || data.praise==2)){
			str += '<p class="zan_cai'+(data.praise == 1 ? ' on': '')+'"><i class="zan"></i><u>'+data.goodcount+'</u></p>';
		}else{
			str += '<p class="zan_cai" onclick="praise(this,'+data.id+',1)"><i class="zan"></i><u>'+data.goodcount+'</u></p>';
		}
		if(typeof(data.report)!="undefined" && data.report==1){
			str += '<p class="jingao on"><i class="ico_jingao" ></i><u>举报</u></p>';
		}else{
			str += '<p class="jingao"  onclick="jingao(this,'+data.id+')"><i class="ico_jingao" ></i><u>举报</u></p>';
		}
		str += '<div class="replybtn" onclick="show_reply(this)">回复</div>';
		str += '<div class="replybtn replybtn2" onclick="show_reply(this)">取消回复</div>';
		str += '</div>';
		str += '</div>';
		str += '<div class="reply_wrap cont_w">';
		str += '<input type="text" class="reply_info" value="" onfocus="this.placeholder=\'\'" onfocusout="this.placeholder=\'回复:\'" placeholder="回复:" />';	
		str += getReplyFaceBox();
		str += '<button class="repl_btn" onclick="ct_reply(this,'+data.id+')">回复</button>';
		str += '</div>';
		str += '</div>';
	return str;
}

function getReplies(data,listid){
	var len = data.length;
	var str = '';
	for(var i=len-1; i>=0; i--){
		if(len>3 && i==len-3){
			str += '<div class="floor_item'+(data[i].user.nickname == '3DM官方账号' ? ' Official2' : '')+'"><div class="mor_floor" onclick="showfloor(this)">重复楼层已隐藏'+(len-3)+'条</div></div>'
		}
		str += '<div class="floor_item'+(data[i].user.nickname == '3DM官方账号' ? ' Official2' : '')+'"'+(i>0 && i<len-2 ? ' style="display:none;"' : (i==0 ? ' style="border-bottom: none;"' : ''))+'>';
		str += '<p>';
		str += '<a href="'+getUserHome(data[i].user.uid)+'" target="_blank" class="name">'+data[i].user.nickname+'</a>';
		if(typeof data[i].user.vip_level != "undefined" && data[i].user.vip_level > 0){
			str += '<a href="https://yeyou.3dmgame.com/vip/index" target="_blank" class="Cotilebt"><i class="ico ico3dmzy"></i>3DM自运营 VIP'+data[i].user.vip_level+'</a>';
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
		str += data[i].user.regionstr+'<span>'+data[i].time+'</span></p>';
		str += '<div class="repl_info">'+replaceFaceContent(data[i].content)+'</div>';
		str += '<div class="praise_btn">';
		if(typeof(data[i].praise)!="undefined" && (data[i].praise==1 || data[i].praise==2)){
			str += '<p class="zan_cai'+(data[i].praise == 1 ? ' on': '')+'"><i class="zan"></i><u>'+data[i].goodcount+'</u></p>';
		}else{
			str += '<p class="zan_cai" onclick="praise(this,'+data[i].id+',1)"><i class="zan"></i><u>'+data[i].goodcount+'</u></p>';
		}
		if(typeof(data[i].report)!="undefined" && data[i].report==1){
			str += '<p class="jingao on"><i class="ico_jingao" ></i><u>举报</u></p>';
		}else{
			str += '<p class="jingao"  onclick="jingao(this,'+data[i].id+')"><i class="ico_jingao" ></i><u>举报</u></p>';
		}
		str += '<div class="replybtn" onclick="show_reply_list(this)">回复</div>';
		str += '<div class="replybtn replybtn2" onclick="show_reply_list(this)">取消回复</div>';
		str += '</div>';
		str += '<div class="reply_wrap">';
		str += '<input type="text" class="reply_info" value="" onfocus="this.placeholder=\'\'" onfocusout="this.placeholder=\'回复:\'" placeholder="回复:" />';
		str += getReplyFaceBox();
		str += '<button class="repl_btn" onclick="ct_reply_list(this,'+data[i].id+','+listid+')">回复</button>';
		str += '</div>';
		str += '</div>';
	}
	return str;
}
function getUserHome(uid){
	return myuserhost+'/user/'+uid;
}
//获取收藏状态
function getcollection(){
	var pageurl = "";
	if(typeof(collect_pageurl) != "undefined"){
		pageurl = collect_pageurl;
	}
	if(g_ct_irefer){
		var url = myuserhost + "/jsonpapi/getfavorite?irefer="+encodeURIComponent(top.location.href);
		$.ajax({
			url: url,
			type: "GET",
			data:{pageurl:pageurl},
			dataType:'jsonp',
			jsonp: "jsonpcallback",
			success: function(data){
				ct_getcollection_back(data);
			}
		});
	}else{
		var url = myuserhost + "/api/getfavorite?irefer="+encodeURIComponent(top.location.href);
		$.ajax({
			url: url,
			type: "POST",
			data:{pageurl:pageurl},
			dataType:'json',
			xhrFields:{withCredentials:true},
			success: function(data){
				ct_getcollection_back(data);
			}
		});
	}
}
//设置收藏
function ct_collect(obj){
	var favoriteact = 2;
	if($(obj).find('span').html() == '收藏'){
		favoriteact = 1;
	}
	var sid = 0;
	if($("#Cs_collect_sid").length>0){
		sid = $("#Cs_collect_sid").val();
	}
	var ctype = typeof(collect_type) != "undefined" ? collect_type : 1;
	var pageurl = "";
	if(typeof(collect_pageurl) != "undefined"){
		pageurl = collect_pageurl;
	}
	if(g_ct_irefer){
		var url = myuserhost + "/jsonpapi/setfavorite?irefer="+encodeURIComponent(top.location.href);
		$.ajax({
			url: url,
			//async:false,
			type: "GET",
			data:{sid:sid,favoriteact:favoriteact,type:ctype,pageurl:pageurl},
			dataType:'jsonp',
			jsonp: "jsonpcallback",
			success: function(data){
				ct_collect_back(data, favoriteact, obj);
			}
		});
	}else{
		var url = myuserhost + "/api/setfavorite?irefer="+encodeURIComponent(top.location.href);
		$.ajax({
			url: url,
			//async:false,
			type: "POST",
			data:{sid:sid,favoriteact:favoriteact,type:ctype,pageurl:pageurl},
			dataType:'json',
			xhrFields:{withCredentials:true},
			success: function(data){
				ct_collect_back(data, favoriteact, obj);
			}
		});
	}
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
function trim(str){
	return str.replace(/(^\s*)|(\s*$)/g, "");
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
	    area: ['auto', 'auto'],
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
	if(g_ct_irefer){
		var url = myuserhost + "/jsonpapi/pagereport?irefer="+encodeURIComponent(top.location.href);
		$.ajax({
			url:url,
			type: "GET",
			data:{title:title,content:content,tel:tel,sid:sid,pageurl:pageurl},
			dataType:'jsonp',
			jsonp: "jsonpcallback",
			success: function(data){
				ct_page_report_back(data, obj);
			},
			complete: function(){
				g_pagereport = 0;
				layer.close(layer_Report);
			}
		});
	}else{
		var url = myuserhost + "/api/pagereport?irefer="+encodeURIComponent(top.location.href);
		$.ajax({
			url:url,
			type: "POST",
			data:{title:title,content:content,tel:tel,sid:sid,pageurl:pageurl},
			dataType:'json',
			xhrFields:{withCredentials:true},
			success: function(data){
				ct_page_report_back(data, obj);
			},
			complete: function(){
				g_pagereport = 0;
				layer.close(layer_Report);
			}
		});
	}
}
//取消页面举报提交
function page_report_cancel(obj){
	layer.close(layer_Report);
}    
//点击评论下的举报
function jingao(obj,ctid){
	if(g_report){
		alert('发送中，请稍后。。。');
	}
	if(g_ct_irefer){
		var url = myuserhost + "/jsonpapi/report?irefer="+encodeURIComponent(top.location.href);
		$.ajax({
			url:url,
			type: "GET",
			data:{id:ctid},
			dataType:'jsonp',
			jsonp: "jsonpcallback",
			success: function(data){
				ct_jingao_back(data, obj);
			},
			complete: function(){
				g_report = 0;
			}
		});
	}else{
		var url = myuserhost + "/api/report?irefer="+encodeURIComponent(top.location.href);
		$.ajax({
			url:url,
			type: "POST",
			data:{id:ctid},
			dataType:'json',
			xhrFields:{withCredentials:true},
			success: function(data){
				ct_jingao_back(data, obj);
			},
			complete: function(){
				g_report = 0;
			}
		});
	}
}
//获取文章举报状态
function getpagereport(){
	var pageurl = "";
	if(typeof(collect_pageurl) != "undefined"){
		pageurl = collect_pageurl;
	}
	if(g_ct_irefer){
		var url = myuserhost + "/jsonpapi/getpagereport?irefer="+encodeURIComponent(top.location.href);
		$.ajax({
			url: url,
			type: "GET",
			data:{pageurl:pageurl},
			dataType:'jsonp',
			jsonp: "jsonpcallback",
			success: function(data){
				ct_getpagereport_back(data);
			}
		});
	}else{
		var url = myuserhost + "/api/getpagereport?irefer="+encodeURIComponent(top.location.href);
		$.ajax({
			url: url,
			type: "POST",
			data:{pageurl:pageurl},
			dataType:'json',
			xhrFields:{withCredentials:true},
			success: function(data){
				ct_getpagereport_back(data);
			}
		});
	}
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
		popFace_ += '<div class="popFace_bt" onclick="openCtFaceBox(this,event)"><i class="ico_bq_bt"></i>添加表情</div>';
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
		popFace_ += '<div class="popFace_bt" onclick="openReplyFaceBox(this,event)"><i class="ico_bq_bt"></i>添加表情</div>';
		popFace_ += '<div class="popFaceBox">';
		popFace_ += '<div class="popFaceBox_close" onclick="closeReplyFaceBox(this)"></div>';
		popFace_ += '<div class="p_item">';
		for(var i=0; i<=popFace_bt.length-1; i++){
			if(i == 0){
				popFace_ += '<p data-id="'+popFace_bt[i].id_+'" class="on" onclick="changeFaceItem(this,'+i+',event)">'+popFace_bt[i].text_+'</p>';
			}else{
				popFace_ += '<p data-id="'+popFace_bt[i].id_+'" onclick="changeFaceItem(this,'+i+' ,event)">'+popFace_bt[i].text_+'</p>';
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
	$(".friendsBox").hide();
	hide_open_reply();
	$(obj).parents(".popFace").find(".popFaceBox").show();
	stopNextEvent(evt);
}
//展示回复表情框
function openReplyFaceBox(obj,evt){
	$(".popFaceBox").hide();
	$(".friendsBox").hide();
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
	$("#Ct_content").insertAtCaret(faceText);
	face_pop.hide();
}
//添加回复表情
function addReplyFace(obj, face, type){
	var face_pop = $(obj).parent().parent().parent();
	var face_box = face_pop.parent();
	var reply_content = face_box.prev();
	var faceText = '';
	faceText = '['+face+']';
	reply_content.insertAtCaret(faceText);
	face_pop.hide();
}
function replaceFaceContent(content){
	var find_face = ['\\[微笑\\]','\\[爱心\\]','\\[委屈\\]','\\[害羞\\]','\\[闭嘴\\]','\\[犯困\\]','\\[大哭\\]','\\[尴尬\\]','\\[生气\\]','\\[可爱\\]','\\[赞个\\]','\\[怀疑\\]','\\[汗\\]','\\[鄙视\\]','\\[呆\\]','\\[辣\\]','\\[坏笑\\]','\\[机智\\]','\\[晕\\]','\\[思考\\]','\\[晚安\\]','\\[拿来\\]','\\[困\\]','\\[疑惑\\]','\\[666\\]','\\[555\\]','\\[生气2\\]','\\[全要\\]','\\[吃瓜\\]','\\[棒\\]','\\[观察\\]','\\[我的锅\\]','\\[无奈\\]','\\[嘲笑\\]','\\[绝望\\]','\\[约吗\\]'];
	var replace_face = [
						'<img src="https://my.3dmgame.com/ct/images/bq_ico/ico1.png" title="微笑" />',
						'<img src="https://my.3dmgame.com/ct/images/bq_ico/ico2.png" title="爱心" />',
						'<img src="https://my.3dmgame.com/ct/images/bq_ico/ico3.png" title="委屈" />',
						'<img src="https://my.3dmgame.com/ct/images/bq_ico/ico4.png" title="害羞" />',
						'<img src="https://my.3dmgame.com/ct/images/bq_ico/ico5.png" title="闭嘴" />',
						'<img src="https://my.3dmgame.com/ct/images/bq_ico/ico6.png" title="犯困" />',
						'<img src="https://my.3dmgame.com/ct/images/bq_ico/ico7.png" title="大哭" />',
						'<img src="https://my.3dmgame.com/ct/images/bq_ico/ico8.png" title="尴尬" />',
						'<img src="https://my.3dmgame.com/ct/images/bq_ico/ico9.png" title="生气" />',
						'<img src="https://my.3dmgame.com/ct/images/bq_ico/ico10.png" title="可爱" />',
						'<img src="https://my.3dmgame.com/ct/images/bq_ico/ico11.png" title="赞个" />',
						'<img src="https://my.3dmgame.com/ct/images/bq_ico/ico12.png" title="怀疑" />',
						'<img src="https://my.3dmgame.com/ct/images/bq_ico/ico13.png" title="汗" />',
						'<img src="https://my.3dmgame.com/ct/images/bq_ico/ico14.png" title="鄙视" />',
						'<img src="https://my.3dmgame.com/ct/images/bq_ico/ico15.png" title="呆" />',
						'<img src="https://my.3dmgame.com/ct/images/bq_ico/ico16.png" title="辣" />',
						'<img src="https://my.3dmgame.com/ct/images/bq_ico/ico17.png" title="坏笑" />',
						'<img src="https://my.3dmgame.com/ct/images/bq_ico/ico18.png" title="机智" />',
						'<img src="https://my.3dmgame.com/ct/images/bq_ico/ico19.png" title="晕" />',
						'<img src="https://my.3dmgame.com/ct/images/bq_ico/ico20.png" title="思考" />',
						'<img src="https://my.3dmgame.com/ct/images/bq_ico2/ico1.png" title="晚安" />',
						'<img src="https://my.3dmgame.com/ct/images/bq_ico2/ico2.png" title="拿来" />',
						'<img src="https://my.3dmgame.com/ct/images/bq_ico2/ico3.png" title="困" />',
						'<img src="https://my.3dmgame.com/ct/images/bq_ico2/ico4.png" title="疑惑" />',
						'<img src="https://my.3dmgame.com/ct/images/bq_ico2/ico5.png" title="666" />',
						'<img src="https://my.3dmgame.com/ct/images/bq_ico2/ico6.png" title="555" />',
						'<img src="https://my.3dmgame.com/ct/images/bq_ico2/ico7.png" title="生气" />',
						'<img src="https://my.3dmgame.com/ct/images/bq_ico2/ico8.png" title="全要" />',
						'<img src="https://my.3dmgame.com/ct/images/bq_ico2/ico9.png" title="吃瓜" />',
						'<img src="https://my.3dmgame.com/ct/images/bq_ico2/ico10.png" title="棒" />',
						'<img src="https://my.3dmgame.com/ct/images/bq_ico2/ico11.png" title="观察" />',
						'<img src="https://my.3dmgame.com/ct/images/bq_ico2/ico12.png" title="我的锅" />',
						'<img src="https://my.3dmgame.com/ct/images/bq_ico2/ico13.png" title="无奈" />',
						'<img src="https://my.3dmgame.com/ct/images/bq_ico2/ico14.png" title="嘲笑" />',
						'<img src="https://my.3dmgame.com/ct/images/bq_ico2/ico15.png" title="绝望" />',
						'<img src="https://my.3dmgame.com/ct/images/bq_ico2/ico16.png" title="约吗" />'
					];
	var len_face = find_face.length;
	for (var i=0;i<len_face;i++) {
		content = content.replace(new RegExp(find_face[i],"g"), replace_face[i]);
	}
	return content;
}
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
	var official_group = '<div class="official_group" onclick="show_official_group(this)"><a href="javascript:void(0);">3DM官方交流群</a>';
    official_group += '<div class="official_groupshow">';
    official_group += '<div class="li"><a href="//shang.qq.com/wpa/qunwpa?idkey=638860b2cee2dbf42e65718617326c842c5fd45166b8308ce33bf2f412f414c3" target="_blank" class="a">3DM游戏网官方交流群7<span class="new">＋</span></a></div>';
    official_group += '<div class="li"><a href="//shang.qq.com/wpa/qunwpa?idkey=6d928d6f231880da89a695f73b1a20b92ea39b28ee7abfd08084eb4a4ae43635" target="_blank" class="a">3DM游戏网官方交流群6<span class="new">＋</span></a></div>';
    official_group += '<div class="li"><a href="//shang.qq.com/wpa/qunwpa?idkey=c1afa0e4a313510e590624f1e762dcb847128a29fcfcf345778cc3e67f4d6f9b" target="_blank" class="a">3DM游戏网官方交流群5<span class="new">＋</span></a></div>';
    official_group += '<div class="li"><a href="//shang.qq.com/wpa/qunwpa?idkey=7db343210bef6d5c89e02f0583dc03dac75bc92f40cad964b459726ac6000e71" target="_blank" class="a">3DM游戏网官方交流群4<span class="new">＋</span></a></div>';
    official_group += '<div class="li"><a href="//shang.qq.com/wpa/qunwpa?idkey=4309709e20e7d39268544d6b781c4a54c9f9289231e2b3efcc99b2e257abc5c4" target="_blank" class="a">3DM游戏网官方交流群3<span class="new">＋</span></a></div>';
    official_group += '<div class="li"><a href="//shang.qq.com/wpa/qunwpa?idkey=a40d6e27e414b3d8ddae7803dd38973547b0bfcf8c8ae0d4a6f47f2f6c82faf1" target="_blank" class="a">3DM游戏网官方交流群2<span class="new">＋</span></a></div>';
    official_group += '<div class="li"><a href="//shang.qq.com/wpa/qunwpa?idkey=bbfaf5cb92d4ebed6f17b856222c3b4d48e2fe24f4c19de4fe34f0282fd7f4ec" target="_blank" class="a">3DM游戏网官方交流群1<span class="new">＋</span></a></div>';
    official_group += '</div></div>';
	$("#Comments_wrap .Cs_head p.Cs_titile").after(official_group);
});
function show_official_group(obj){
	if($(obj).find(".official_groupshow").hasClass("on")){
        $(obj).find(".official_groupshow").hide();
        $(obj).find(".official_groupshow").removeClass("on")
    }else{
        $(obj).find(".official_groupshow").show();
        $(obj).find(".official_groupshow").addClass("on")
    }
}
//评论@好友
function getFriends(){
	if(!g_friends_flag){
		if(g_ct_irefer){
			var url = myuserhost + "/jsonpapi/getctfollows?irefer="+encodeURIComponent(top.location.href);
			$.ajax({
				url:url,
				type: "GET",
				dataType:'jsonp',
				jsonp: "jsonpcallback",
				success: function(data){
					ct_getFriends_back(data);
				},
				complete: function(){
					ct_getFriends_complete();
				}
			});
		}else{
			var url = myuserhost + "/api/getctfollows?irefer="+encodeURIComponent(top.location.href);
			$.ajax({
				url:url,
				type: "POST",
				dataType:'json',
				xhrFields:{withCredentials:true},
				success: function(data){
					ct_getFriends_back(data);
				},
				complete: function(){
					ct_getFriends_complete();
				}
			});
		}
	}
}

//定位好友位置
function friendsul_data(obj,evt){
	stopNextEvent(evt);
	 var nav_clas = $(obj).attr("data-id");
     var index=$(obj).index();
    var  scroll_top=$(obj).parents(".friends_warp").find(".friends_data").scrollTop();
	var nav_num =$(obj).parents(".friends_warp").find(".friends_lis").eq(index).position().top;	
	$(obj).addClass("on").siblings().removeClass("on");
	$(obj).parents(".friends_warp").find(".friends_data").animate({scrollTop: nav_num+scroll_top}, 300);
}

//关注
function follow_personal(obj, follow_uid){
	var act = 1;
	if($(obj).hasClass("on")){
		act = 2;
	}
	if(g_ct_irefer){
		var url = myuserhost + "/jsonpapi/setfollow?irefer="+encodeURIComponent(top.location.href);
		$.ajax({
			url: url,
			type: "GET",
			data:{follow_uid:follow_uid, follow_act:act},
			dataType:'jsonp',
			jsonp: "jsonpcallback",
			success: function(data){
				ct_follow_personal_back(data, obj);
			}
		});
	}else{
		var url = myuserhost + "/api/setfollow?irefer="+encodeURIComponent(top.location.href);
		$.ajax({
			url: url,
			type: "POST",
			data:{follow_uid:follow_uid, follow_act:act},
			dataType:'json',
			xhrFields:{withCredentials:true},
			success: function(data){
				ct_follow_personal_back(data, obj);
			}
		});
	}
}

//添加@符号
function add_atfollow(obj, nickname){
	var follow_pop = $(obj).parent().parent().parent().parent().parent().parent().parent();
	var follow_box = follow_pop.parent();
	if(follow_box.hasClass('poswrap')){
		//评论
		var reply_content = follow_box.prev().find('textarea');
	}else{
		//回复
		var reply_content = follow_box.find(".reply_info");
	}
	var followText = '';
	followText = '@'+nickname+' ';
	reply_content.insertAtCaret(followText);
}
//添加@最近联系人
function add_atlastfollow(obj, nickname){
	var follow_pop = $(obj).parent().parent().parent().parent().parent();
	var follow_box = follow_pop.parent();
	if(follow_box.hasClass('poswrap')){
		//评论
		var reply_content = follow_box.prev().find('textarea');
	}else{
		//回复
		var reply_content = follow_box.find(".reply_info");
	}
	var followText = '';
	followText = '@'+nickname+' ';
	reply_content.insertAtCaret(followText);
}

/*ajax*/
function ct_login_back(data){
	if(data.code == 1){
		$("#Ct_login").html('<div class="tx"><img src="'+data.user.avatarstr+'"/></div><div class="name">'+data.user.nickname+'</div>');
		$("#Ct_login").addClass('user_tx');
        $("#Ct_content").attr('placeholder', placemsg);
        $("#Ct_content").attr('onfocus', 'this.placeholder=""');
        $("#Ct_content").attr('onfocusout', 'this.placeholder="'+placemsg+'"');
        if($("#my_user_top").length>0){
            var userinfo = '<img src="'+data.user.avatarstr+'">';
            userinfo += '<span class="username">'+data.user.nickname+'</span>';
			userinfo += '<div class="exitwrap">';
			userinfo += '<div class="txwrap"><a href="'+myuserhost+'/setting/binding" target="_blank"><img src="'+data.user.avatarstr+'"/></a></div>';
			userinfo += '<div class="usname"><a href="'+myuserhost+'/setting/binding" target="_blank">'+data.user.nickname+'</a></div>';
			userinfo += '<div class="signature">'+data.user.personalized+'</div>';
			userinfo += '<div class="exitbtn">';
			userinfo += '<div class="list">';
			userinfo += '<a href="'+myuserhost+'/setting/binding" target="_blank" class="phone'+(typeof(data.user.is_mobile) != "undefined" && data.user.is_mobile == 1 ? ' on' : '')+'"></a>';
			userinfo += '<a href="'+myuserhost+'/setting/binding" target="_blank" class="weixin'+(typeof(data.user.is_wechat) != "undefined" && data.user.is_wechat == 1 ? ' on' : '')+'"></a>';
			userinfo += '<a href="'+myuserhost+'/setting/binding" target="_blank" class="qq'+(typeof(data.user.is_qq) != "undefined" && data.user.is_qq == 1 ? ' on' : '')+'"></a>';
			userinfo += '<a href="'+myuserhost+'/setting/binding" target="_blank" class="weibo'+(typeof(data.user.is_sina) != "undefined" && data.user.is_sina == 1 ? ' on' : '')+'"></a>';
			userinfo += '</div>';
			userinfo += '<a href="javascript:void(0);" class="exit" onclick="logout_submit()">[退出]</a>';
			userinfo += '</div>';
			userinfo += '</div>';
			$("#my_user_top").html(userinfo);
		}
        if($("#Comments_wrap .Cs_postwrap .txtwrap .poswrap .popFace").length < 1){
			$("#Comments_wrap .Cs_postwrap .txtwrap .poswrap .postbtn").before(getCtFaceBox());
			getFriends();
		}
        set_g_userinfo(data.user);
        //唤起需要刷新登录的
        try {
	        if(typeof calluserlogin === "function") {
	            calluserlogin(data.user.uid, data.user.nickname);
	        }
        }catch(e){  }
        //唤起登录状态
	    try{
	        if(typeof calluserthird === "function") {
		        calluserthird(data.user.uid, data.user.nickname, data.data.ticket);
		    }
	    }catch(e){ }
	    //唤起需要更新的
	    try {
	    	if(typeof callshopuserinfo === "function") {
		        callshopuserinfo();
		    }
	    }catch(e){ }
	}else{
		alert(data.msg);
	}
}

function ct_initpostlist_back(data, refresh, page, pagesize){
	if(data.code == 1 && typeof(data.data) != "undefined" && typeof(data.data.list) != "undefined"){
		var totalpage = 1;
		var ct_position = 1;
		g_comment_list = new Array();
		var total = parseInt(data.data.total);
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
			$("#Comments_wrap").append(str);
		}
		$("#Ct_total").html('（<i>'+data.data.total_uid+'</i>人参与，<i>'+total+'</i>条评论）');
		if($("#Ct_top_total").length>0){
			$("#Ct_top_total").html(data.data.total_uid);
		}
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
            }
        }
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
			$("#Cslis_wrap").append(str);
			$("#Ct_norecord").remove();
		}
		if(page<totalpage && data.data.list.length>0 && ct_position > 1){
			if($("#Ct_more").length<1){
				$("#Comments_wrap").append('<div id="Ct_more" onclick="morepost()">查看更多&nbsp;(<span>'+(data.data.total-page*pagesize)+'</span>)</div>');
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
			var nav_num = document.getElementById('Comments_wrap').offsetTop;
			$("body,html").animate({scrollTop:nav_num - 100},400);
		}
	}else if(data.code==37){
		$("#Ct_norecord i").css('background','none');
		$("#Ct_norecord i").html(data.msg);
	}
}

function ct_getpostlist_back(data, page, pagesize, total){
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
			$("#Cslis_wrap").append(str);
			$("#Ct_norecord").remove();
		}
		if(page<totalpage && data.data.list.length>0 && ct_position > 1){
			if($("#Ct_more").length<1){
				$("#Comments_wrap").append('<div id="Ct_more" onclick="morepost()">查看更多&nbsp;(<span>'+(data.data.total-page*pagesize)+'</span>)</div>');
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

function ct_post_back(data){
	if(data.code == 1){
		$("#Ct_content").val('');
		if(data.checkflag){
			alert('发布成功，需要等待系统审核');
			return false;
		}
		initpostlist(1);
	}else if(data.code == 9){
		openlogin();
	}else if(data.code == 206 && $("#TencentCaptcha").length > 0){
		$("#TencentCaptcha").attr('data-type', 'post');
		$("#TencentCaptcha").click();
	}else{
		alert(data.msg);
		if(typeof data.url !== "undefined"){
			top.location.href = myuserhost + data.url;
		}
	}
	return true;
}

function ct_reply_back(data, obj){
	if(data.code == 1){
		show_reply($(obj).parent().parent().find('.replybtn2'));
		if(data.checkflag){
			alert('回复成功，需要等待系统审核');
			return false;
		}
		initpostlist(1);
	}else if(data.code == 9){
		openlogin();
	}else if(data.code == 207 && $("#TencentCaptcha").length > 0){
		$("#TencentCaptcha").attr('data-type', 'reply');
		$("#TencentCaptcha").click();
	}else{
		alert(data.msg);
		if(typeof data.url !== "undefined"){
			top.location.href = myuserhost + data.url;
		}
	}
}

function ct_reply_list_back(data, obj){
	if(data.code == 1){
		show_reply_list($(obj).parent().parent().find('.replybtn2'));
		if(data.checkflag){
			alert('回复成功，需要等待系统审核');
			return false;
		}
		initpostlist(1);
	}else if(data.code == 9){
		openlogin();
	}else{
		alert(data.msg);
		if(typeof data.url !== "undefined"){
			top.location.href = myuserhost + data.url;
		}
	}
	return true;
}

function ct_praise_back(data, obj, type){
	if(data.code == 1){
		if(data.add==1){
			$(obj).addClass('on');
			var nm = $(obj).find('u').html();
			$(obj).find('u').html(parseInt(nm)+1);
		}else{
			if(data.type == type){
				$(obj).addClass('on')
			}else{
				$(obj).parent().find('.zan_cai').addClass('on');
				$(obj).removeClass('on');
			}
		}
		$(obj).parent().find('.zan_cai').removeAttr('onclick');
	}else if(data.code == 9){
		openlogin();
	}else{
		alert(data.msg);
		if(typeof data.url !== "undefined"){
			top.location.href = myuserhost + data.url;
		}
	}
}

function ct_getcollection_back(data){
	if(data.code == 1){
		if(data.favorite){
			$("#Cs_collection").find('i').addClass('on');
			$("#Cs_collection").find('span').html('取消');
		}
		if(data.sid){
			$("#Cs_collection").append('<input type="hidden" id="Cs_collect_sid" value="'+data.sid+'" />');
		}
	}
}

function ct_collect_back(data, favoriteact, obj){
	if(data.code == 1){
		if(favoriteact == 1){
			if($("#Cs_collect_sid").length == 0){
				$("#Cs_collection").append('<input type="hidden" id="Cs_collect_sid" value="'+data.sid+'" />');
			}
			$(obj).find('i').addClass('on');
			$(obj).find('span').html('取消');
		}else{
			$(obj).find('i').removeClass('on');
			$(obj).find('span').html('收藏');
		}
	}else if(data.code == 9){
		openlogin();
	}else{
		alert(data.msg);
		if(typeof data.url !== "undefined"){
			top.location.href = myuserhost + data.url;
		}
	}
}

function ct_page_report_back(data, obj){
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
}

function ct_jingao_back(data, obj){
	if(data.code == 1){
		alert('举报成功');
		$(obj).addClass("on");
		$(obj).removeAttr("onclick");
	}else if(data.code == 9){
		openlogin();
	}else{
		alert(data.msg);
		if(typeof data.url !== "undefined"){
			top.location.href = myuserhost + data.url;
		}
	}
}

function ct_getpagereport_back(data){
	if(data.code == 1){
		if(data.pagereport){
			$("#Cs_report_bt").removeAttr("onclick");
			$("#Cs_report_bt").addClass("on");
		}
	}
}

function ct_getFriends_back(data){
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
}

function ct_getFriends_complete(){
	g_friends_flag = 0;
	var friends_html='';
	friends_html+='<div class="friends_warp"><div class="friends_bt" onclick="friends(this)">滴好友</div>';
	friends_html+='<div class="friendsBox" ><div class="popup"><span></span></div><div class="box_">';
	friends_html+='<div class="friends_lately"><span class="bt">最近联系：</span>';
	if(g_lastconcat_arr.length){
		for (var i = 0; i <= g_lastconcat_arr.length-1; i++) {
			friends_html += '<a href="javascript:void(0);" class="a" data-follow-uid='+g_lastconcat_arr[i].uid+' onclick="add_atlastfollow(this,\''+g_lastconcat_arr[i].nickname+'\')">'+g_lastconcat_arr[i].nickname+'</a>';
		}
	}
	friends_html+='</div>';
	friends_html+='<div class="friends_ul">';
	for(var i=0;i<=g_friends_arr.length-1;i++){
		friends_html+='<a class="a_" data-id="friends_li'+ i +'"  onclick="friendsul_data(this,event)">'+ g_friends_arr[i].name + '</a>';
	}
	friends_html+='</div>';
	friends_html+='<div class="friends_data">';
	for(var i=0;i<=g_friends_arr.length-1;i++){
		friends_html+='<div class="friends_lis" data-lis="friends_li'+ i +'">';
		if(i!=0){
			friends_html+='<div class="bt">'+ g_friends_arr[i].name + '</div>';
		}
		friends_html+='<div class="friends_name_box">';
		for(var a=0;a<=g_friends_arr[i].list.length-1;a++){
			friends_html+='<a href="javascript:void(0);" class="a" data-follow-uid='+g_friends_arr[i].list[a].uid+' onclick="add_atfollow(this,\''+g_friends_arr[i].list[a].nickname+'\')"> '+ g_friends_arr[i].list[a].nickname + '</a>';
		}
		friends_html+='</div></div>';
	}
	friends_html+='</div></div></div></div>';
	$(".popFace").append(friends_html);
}

function ct_follow_personal_back(data, obj){
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

//增加iframe  替换图片
$("#Comments_wrap .Cs_postwrap ").next("a").hide();
$("#Comments_wrap .Cs_postwrap ").after('<iframe style="width:100%;padding:0px;height:90px ;margin:10px 0; " vspace="0px" hspace="0px" scrolling="no" frameborder="0" src="https://yeyou.3dmgame.com/tools/gamead"></iframe>')


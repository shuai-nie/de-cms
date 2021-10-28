$(function(){

    if($('.GmL_4').size()>=1){
    	$(".GmL_4").thumbnailImg({
	
	        large_elem: ".large_box",
	
	        small_elem: ".small_list",
	
	        left_btn: ".left_btn",
	
	        right_btn: ".right_btn"
	
	    });
    }
    
    $('.GmL_8 .tabtop p').mouseenter(function(){
    	$(this).addClass('on').siblings().removeClass('on')
    	$('.GmL_8 ul').hide().eq($(this).index()).show();
    });

	$('.downl22').click(function () {
		var nav_num = $(".downlwrap").offset().top;
		$("body,html").animate({scrollTop:nav_num-200},400);
	});

	if($(".newqkdown2").length > 0){
		var downi = 1;
		var downimatch = window.location.pathname.match(/\/(\w+)\/(\d+).html/);
		var downitype = downimatch[1] == "patch" ? 2 : 1;
		var downiaid = downimatch[2];
		if($("#aid").length>0){
			var downiaid = $("#aid").val();;
		}
		var downititle = $("div.name h1").html();
		var downititle2 = downititle.replace('/','');
		downititle2 = downititle2.replace(' ', '');
		downititle2 = downititle2.replace(',', '');
		var donwiurl = 'https://id.hndazhan.com/az/'+encodeURI(downititle2)+'_id371@371_'+downitype+downiaid;
		$(".newqkdown2").attr('href', donwiurl);
		if($(".newqkdown3").length>0){
			donwiurl = 'https://id.hndazhan.com/az/'+encodeURI(downititle2)+'_id372@372_'+downitype+downiaid;
			$(".newqkdown3").each(function(){
				$(this).attr('href', donwiurl);
			});
		}
	}

});
$(function () {
	var tab_a = $('.GmL_2 table').eq(0).height();
	var tab_b = $('.GmL_2 table').eq(1).height();
	var tab1,tab2
	if(tab_a>tab_b){
		tab1 = $('.GmL_2 table').eq(0)
		tab2 =  $('.GmL_2 table').eq(1)
	}else {
		tab1 = $('.GmL_2 table').eq(1)
		tab2 =  $('.GmL_2 table').eq(0)
	}
   for(i=0;i<tab1.find('tr').length;i++){
	   var taba1_h = tab1.find('tr').eq(i).height();
	   var tabb1_h = tab2.find('tr').eq(i).height();
	   if(taba1_h != tabb1_h){  tab2.find('tr').eq(i).find('td').height(taba1_h-26);}
   }
})

//h3
$('.GmL_1>h3').each(function(){	$(this).html('<span class="bt">'+$(this).html()+'</span>')});


function xiazaicount(type){
	if($("#aid").length>0){
		var downiaid = $("#aid").val();;
		downcount('xiazai',downiaid);
	}
}

function opendownpublic(obj, id){
    downcount('patch',id)
    var href=$(obj).attr("data-href");
    var title=$(".patchtop .name h1").text();
    console.log(href,title);
    $(".public .btn").attr("href",href);
    $(".public .text p a").text(title)
    var public = layer.open({
        type: 1,
        title: false,
        shadeClose:true,
        closeBtn: 0,
        area: ['auto', 'auto'],
        content: $('.public')
    });
    $(".public .btn").click(function(){
        layer.closeAll();
    })
}
$(".public .btn").click(function(){
    layer.closeAll();
})
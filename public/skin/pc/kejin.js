// JavaScript Document
$(function(){
	$(".indexCon .content .theService .theList li").hover(function(){
		$(".theBox .theList li").removeClass("now");
		$(this).addClass("now");
		$(".theBox .theList li div").removeClass("h");
		$(".theBox .theList li div").addClass("theIco");
		$(this).find(".theIco").addClass("h");
		$(this).find(".theIco").removeClass("theIco");
		
	})
})

$(function () {
   $.fn.swBanner=function(options){
     var defaults={
	     animateTime:300,
		 delayTime:3000
	 }
   var setting=$.extend({},defaults,options);
   
   return this.each(function(){
      $obj=$(this);
	  var o=setting.animateTime;
	  var d=setting.delayTime;
	  var $oban=$obj.find(".banList li");
	  var _len=$oban.length;
	  var $nav=$obj.find(".fomW a");
	  var _index=0;
	  var timer;
	  //图片轮换
	  function showImg(n){
	     $oban.eq(n).addClass("active").siblings().removeClass("active");
		 $nav.eq(n).addClass("current").siblings().removeClass("current");
	  }
	  //自动播放
	  function player(){
	    timer=setInterval(function(){
		   var _index=$obj.find(".fomW").find("a.current").index();
		   showImg((_index+1)%_len);
		},d)
	  }
	  //
	  $nav.click(function(){
	     if(!($oban.is(":animated"))){
	     _index=$(this).index();
		 showImg(_index);
		 }
	  });
	  //
	  $oban.hover(function(){
	    clearInterval(timer);
	  
	  },function(){
	    player();
	  
	  });
	   player();
   });
   
   }
})
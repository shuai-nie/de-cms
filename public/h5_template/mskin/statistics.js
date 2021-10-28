$(function(){
	var url = window.location.href;
	var referer = document.referrer;
	var sendData={'url':url, 'referer':referer};
	$.ajax({
		url:"https://work.3dmgame.com/statistics",
		type:"post",
		dataType:"json",
		data:sendData,
		success:function(data){}
	})
});
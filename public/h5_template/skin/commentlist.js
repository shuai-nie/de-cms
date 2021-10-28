var myuserhost = "https://my.3dmgame.com";
$(function(){
	$(".selectarcnum").html(0);
	initlist();//初始化
});
function initlist(){
	var urls = new Array();
	var i = 0;
	$(".selectpost").each(function(){
		urls[i++] = $(this).find(".selectarcpost").attr("href");
	});
	if(urls.length>0){
		if(window.XDomainRequest){
			var url = myuserhost + "/jsonpapi/arclistpost?irefer="+encodeURIComponent(top.location.href);
			$.ajax({
				url: url,
				//async:false,
				type: "GET",
				data:{arcurls:urls},
				dataType:'jsonp',
				jsonp: "jsonpcallback",
				success: function(data){
					ct_initlist_back(data);
				}
			});
		}else{
			var url = myuserhost + "/api/arclistpost?irefer="+encodeURIComponent(top.location.href);
			$.ajax({
				url: url,
				//async:false,
				type: "POST",
				data:{arcurls:urls},
				dataType:'json',
				xhrFields:{withCredentials:true},
				success: function(data){
					ct_initlist_back(data);
				}
			});
		}
	}
}
function ct_initlist_back(data){
	if(data.code == 1 && typeof(data.data) != "undefined"){
		if(data.data.length>0){
			var len = data.data.length;
			$(".selectpost").each(function(){
				var url = $(this).find(".selectarcpost").attr("href");
				for(var i=0; i<len; i++){
					if(data.data[i].arcurl == url){
						var total = parseInt(data.data[i].total);
						$(this).find(".selectarcnum").html(total);
						break;
					}
				}
			});
			$(".selectpost").removeClass("selectpost");
		}
	}
}
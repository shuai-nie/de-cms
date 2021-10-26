//<![CDATA[
$(function(){
	(function(){
		var curr = 0;
		$("#jsNav .trigger").each(function(i){
			$(this).click(function(){
				curr = i;
				$("#js img").eq(i).fadeIn("slow").siblings("img").hide();
				$(this).siblings(".trigger").removeClass("imgSelected").end().addClass("imgSelected");
				return false;
			});
		});

		var pg = function(flag){
			//flag:true表示前翻， false表示后翻
			if (flag) {
				if (curr == 0) {
					todo = 2;
				} else {
					todo = (curr - 1) % 3;
				}
			} else {
				todo = (curr + 1) % 3;
			}
			$("#jsNav .trigger").eq(todo).click();
		};

		//前翻
		$("#prev").click(function(){
			pg(true);
			return false;
		});

		//后翻
		$("#next").click(function(){
			pg(false);
			return false;
		});

		//自动翻
		var timer = setInterval(function(){
			todo = (curr + 1) % 3;
			$("#jsNav .trigger").eq(todo).click();
		},4000);

		//鼠标悬停在触发器上时停止自动翻
		$("#jsNav a").hover(function(){
				clearInterval(timer);
			},
			function(){
				timer = setInterval(function(){
					todo = (curr + 1) % 3;
					$("#jsNav .trigger").eq(todo).click();
				},3500);
			}
		);
	})();
});

	$(function(){
		$("#aFloatTools_Show").click(function(){
			$('#divFloatToolsView').animate({width:'show',opacity:'show'},100,function(){$('#divFloatToolsView').show();});
			$('#aFloatTools_Show').hide();
			$('#aFloatTools_Hide').show();
		});
		$("#aFloatTools_Hide").click(function(){
			$('#divFloatToolsView').animate({width:'hide', opacity:'hide'},100,function(){$('#divFloatToolsView').hide();});
			$('#aFloatTools_Show').show();
			$('#aFloatTools_Hide').hide();
		});
	});

//]]>

//懒人图库 www.lanrentuku.com
(function(a){
    a.fn.webwidget_menu_glide=function(p){
        var p=p||{};

        var f=p&&p.menu_text_size?p.menu_text_size:"12px";
        var g=p&&p.menu_text_color?p.menu_text_color:"blue";
        var h=p&&p.menu_margin?p.menu_margin:"10px";
        var i=p&&p.menu_width?p.menu_width:"100px";
        var j=p&&p.menu_height?p.menu_height:"20px";
        var k=p&&p.menu_sprite_color?p.menu_sprite_color:"red";
        var l=p&&p.menu_background_color?p.menu_background_color:"black";
        var m=p&&p.sprite_speed?p.sprite_speed:"fast";
        f += "px";
        h += "px";
        i += "px";
        j += "px";
        var n=a(this);
        if(n.children("ul").length==0||n.find("li").length==0){
            n.append("Require menu content");
            return null
            }
            s_m(n.children("ul").children("li"),h,i,j);
        s_m_t_c(n.find("a"),g,j,f);
       // n.css("background-color",l);
        if(n.children("ul").children("li").is(".current")){
            var o=n.children("ul").children("li").filter(".current").offset()
            }else{
            var o=n.children("ul").children("li:first").offset()
            }
            var q=o.left+'px';
        s_m_s_c(n.find(".webwidget_menu_glide_sprite"),k,i,j,q);
        n.children("ul").children("li").hover(function(){
            var b=$(this).offset();
            var c=b.left+'px';
            n.find(".webwidget_menu_glide_sprite").stop().animate({
                left:c
            },m)
            },function(){
            n.find(".webwidget_menu_glide_sprite").stop().animate({
                left:q
            },m)
            });
        n.children("ul").children("li").children("ul").children("li").hover(function(){},function(){});
        function s_m_t_c(a,b,c,d){
            a.css("color",b);
            a.css("line-height",c);
            a.css("font-size",d)
            }
            function s_m(a,b,c,d){
            style="margin-right:"+b+"; width: "+c+"; height: "+d+";";
            a.attr("style",style)
            }
            function s_m_s_c(a,b,c,d,e){
            a.css("background-color",b);
            a.css("width",c);
            a.css("height",d);
            a.css("left",e)
            }
        }
})(jQuery);

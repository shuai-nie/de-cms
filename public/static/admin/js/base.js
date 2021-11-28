
layui.use(['element', 'layer', 'util', 'table'], function(){
    var element = layui.element,
    layer = layui.layer,
    util = layui.util,
    $ = layui.$,
    table = layui.table;

    //头部事件
    util.event('lay-header-event', {
        //左侧菜单事件
        menuLeft: function(othis){
            layer.msg('展开左侧菜单的操作', {icon: 0});
        }
        ,menuRight: function(){
            layer.open({
                type: 1
                ,content: '<div style="padding: 15px;">处理右侧面板的操作</div>'
                ,area: ['260px', '100%']
                ,offset: 'rt' //右上角
                ,anim: 5
                ,shadeClose: true
            });
        }
    });


    table.init('demoSysInfoIndex', {
        // // height: 315
        limit: 100
    });

    table.init('demoSysAdminUser', {
        limit: 100
    });

    table.init('demoSysAdminUser2', {
        limit: 100
    });

    table.init('demoFriendlinkMain', {
        limit:100
    });

    table.init('demoTagsMainIndex', {
        limit:100
    })

    table.init('demoMyChannelMain', {
        limit:10
    })

    table.init('demoCatalogMain', {
        limit:10
    })

    var layid = location.hash.replace(/^#test1=/, '');
    /***********************************/
    element.on('tab(mychannel_edit)', function(data){
        console.log(this, data);
        console.log(data.index);
    });
    element.tabChange('mychannel_edit', layid);
    element.on('tab(mychannel_edit)', function(){
        location.hash = 'test1='+ this.getAttribute('lay-id');
    });
    /***********************************/
    element.on('tab(CatalogMain_catalog_add)', function(data){
        console.log(this, data);
        console.log(data.index);
    });
    element.tabChange('CatalogMain_catalog_add', layid);
    element.on('tab(CatalogMain_catalog_add)', function(){
        location.hash = 'test1='+ this.getAttribute('lay-id');
    });
    /***********************************/






});


function $Nav()
{
    if(window.navigator.userAgent.indexOf("MSIE")>=1) return 'IE';
    else if(window.navigator.userAgent.indexOf("Firefox")>=1) return 'FF';
    else return "OT";
}

function $Obj(objname)
{
    return document.getElementById(objname);
}


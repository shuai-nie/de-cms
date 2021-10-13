
layui.use(['element', 'layer', 'util', 'table'], function(){
    element = layui.element;
    layer = layui.layer;
    util = layui.util;
    $ = layui.$;
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

    table.init('demoFriendlinkMain', {
        limit:100
    });




});

<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use think\Request;
use think\facade\View;
use app\admin\model\Channeltype as ChanneltypeModel;
use app\admin\model\Arctype as ArctypeModel;
/**
 * [网站栏目管理]
 * Class CatalogMain
 * @package app\Admin\controller
 */
class CatalogMain extends Base
{
    public function initialize()
    {
        parent::initialize();
        View::assign('nav', array(
            array('title'=>'核心', 'url'=>''),
            array('title'=>'网站栏目管理', 'url'=>''),
        ));
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $data = ArctypeModel::where(array())->select()->toArray();
        View::assign('_data', $data);
        return View::fetch();
    }

    /**
     * [增加子类]
     * @author Dave 178698695@qq.com
     */
    public function catalog_add()
    {
        return View::fetch();
    }



    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $channel = ChanneltypeModel::where("id<>-1 AND isshow=1 ")->order('id asc')->select();
        View::assign('_channel', $channel);
        return View::fetch();
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}

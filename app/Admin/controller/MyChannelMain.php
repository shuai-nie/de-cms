<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use think\Request;
use think\facade\View;
use app\admin\model\Channeltype as ChanneltypeModel;
use app\admin\model\Arcrank as ArcrankModel;
use app\admin\model\MemberModel ;

/**
 * [内容模型管理]
 * Class MyChannelMain
 * @package app\Admin\controller
 */
class MyChannelMain extends Base
{
    public function initialize()
    {
        parent::initialize();
        View::assign('nav', array(
            array('title'=>'核心', 'url'=>''),
            array('title'=>'内容模型管理', 'url'=>''),
        ));
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $data = ChanneltypeModel::where(array())->select()->toArray();
        View::assign('_data', $data);
        return View::fetch();
    }


    public function mychannel_edit()
    {
        $request = $this->request;
        $id = $request->param('id');
        $dopost = $request->param('dopost');
        $data = ChanneltypeModel::where(array('id'=>$id))->find();
        View::assign('data', $data);
        $ArcrankAll = ArcrankModel::where("rank>=10")->select()->toArray();
        View::assign('ArcrankAll', $ArcrankAll);
        $MemberModelAll = MemberModel::where('')->select()->toArray();
        View::assign('MemberModelAll', $MemberModelAll);
        View::assign('id', $id);
        return View::fetch();
    }
}

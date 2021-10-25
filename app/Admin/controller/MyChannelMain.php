<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use think\facade\Config;
use think\facade\Request;
use think\facade\Session;
use think\facade\View;
use app\Admin\model\Channeltype as ChanneltypeModel;
use app\Admin\model\Arcrank as ArcrankModel;
use app\Admin\model\MemberModel ;

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
        View::assign('_nav_this', 'MyChannelMain_index');
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

//        $fieldset = $data['fieldset'];
//        var_dump($fieldset);exit();






        return View::fetch();
    }

    public function mychannel_field_add()
    {
        $id = Request::param('id');
        View::assign('id', $id);
        return View::fetch();
    }

    public function mychannel_add()
    {
        $row = ChanneltypeModel::where(array())->find();
        $newid = $row['id'] + 1;
        if($newid < 10) $newid = $newid+10;
        View::assign('cfg_dbprefix', Config::get('database.connections')['mysql']['prefix'] );
        View::assign('newid', $newid);
        $usertype = Session::get('user')->usertype;

        $ArcrankAll = ArcrankModel::where("adminrank<='$usertype' And rank>=10")->select();
        View::assign('ArcrankAll', $ArcrankAll);
        $MemberModelAll = MemberModel::where("")->select();
        View::assign('MemberModelAll', $MemberModelAll);
        return View::fetch();
    }

}

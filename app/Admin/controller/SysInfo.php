<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use think\App;
use think\facade\Request;
use think\facade\View;
use app\Admin\model\Sysconfig as  SysconfigModel;
use think\Db;


/**
 * [系统基本参数]
 * Class SysInfo
 * @package app\Admin\controller
 */
class SysInfo extends Base
{

    public function initialize()
    {

        parent::initialize();
        View::assign('nav', array(
            array('title'=>'系统', 'url'=>''),
            array('title'=>'系统基本参数', 'url'=>''),
        ));
        View::assign('_nav_itemed', 'xitong');
    }

    /**
     * 显示资源列表
     * @return \think\Response
     */
    public function index()
    {
        if(Request::isPost()){
            $data = SysconfigModel::where(['groupid'=>1])->select();
            $count = SysconfigModel::where(['groupid'=>1])->count();
            return json(['code'=>0, 'count'=>$count, 'data'=>$data]);
        }
        return View::fetch('index');
    }

    public function detail()
    {
        $id = Request::param('aid');
        $info = SysconfigModel::where(['aid' => $id])->find();
        return view('', ['info' => $info]);
    }

    public function edit()
    {
        $id = Request::param('aid');
        if(Request::isPost()){
            $state = SysconfigModel::update([], []);
            if($state !== false){
                return success();
            }
            return error();
        }
        $info = SysconfigModel::where(['id' => $id])->find();
        return view('', ['info'=>$info]);
    }


}

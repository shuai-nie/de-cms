<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use think\App;
use think\facade\Request;
use think\facade\View;
use app\Admin\model\Sysconfig as  SysconfigModel;


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
    }

    /**
     * 显示资源列表
     * @return \think\Response
     */
    public function index()
    {
        if(Request::isPost()){
            $param = Request::param();

            foreach ($param as $k=>$v){

                SysconfigModel::update(array(
                    'value'=>$v
                ), array(
                    'varname' => $k
                ));
            }

            return $this->success('提交成功', (string)url('index'));
        }
        $data = SysconfigModel::where(['groupid'=>1])->select();
        View::assign('_sysconfig', $data);
        return View::fetch('index');
    }




}

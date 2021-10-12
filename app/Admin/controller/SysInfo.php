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
//    public function __construct(App $app)
//    {
//        // 控制器初始化
//        $this->initialize();
//    }
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $data = SysconfigModel::where(['groupid'=>1])->select()->toArray();

        View::assign('_sysconfig', $data);
        return View::fetch('index');
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
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

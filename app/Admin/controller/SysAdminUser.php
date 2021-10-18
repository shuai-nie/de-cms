<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use think\facade\Request;
use think\facade\View;
use app\Admin\model\Admin as AdminModel;

/**
 * [系统用户管理]
 * Class SysAdminUser
 * @package app\Admin\controller
 */
class SysAdminUser extends Base
{
    public function initialize()
    {
        parent::initialize();
        View::assign('nav', array(
            array('title'=>'系统', 'url'=>''),
            array('title'=>'系统用户管理', 'url'=>''),
        ));
    }

    public function index()
    {
        $data = AdminModel::hasWhere('profile')->field('Admintype.typename')->select()->toArray();
        View::assign('_user', $data);
        return View::fetch();
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

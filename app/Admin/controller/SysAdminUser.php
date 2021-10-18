<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use think\facade\Request;
use think\facade\View;
use app\Admin\model\Admin as AdminModel;
use app\admin\model\Arctype as ArctypeModel;
use app\Admin\model\Admintype as AdmintypeModel;
use think\facade\Config;

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
        $request = $this->request;
        $rank = $request->param('rank');
        $data = AdminModel::hasWhere('profile2', '', 'Admin.*,Arctype.typename', 'left')->select()->toArray();
//        echo (new AdminModel())->getLastSql();
//        var_dump($data);exit();

        //$data = AdminModel::where(array())->select()->toArray();

        View::assign('_user', $data);
        return View::fetch();
    }

   public function sys_admin_user_edit()
   {
       $request = $this->request;
       $id = $request->param('id');
       $dopost = $request->param('dopost');
       if($dopost == 'edit'){
           $adminInfo = AdminModel::where(['id'=>$id])->find()->toArray();
           $arctypeAll = ArctypeModel::where("reid=0 AND (ispart=0 OR ispart=1)")->field('id,typename')->select()->toArray();
           $AdmintypeAll = AdmintypeModel::where(array())->order('rank asc')->select()->toArray();
           View::assign('adminInfo', $adminInfo);
           View::assign('arctypeAll', $arctypeAll);
           View::assign('AdmintypeAll', $AdmintypeAll);
           $randcode = mt_rand(10000,99999);
           $cfg_cookie_encode = Config::get('app.cfg_cookie_encode');
           $safecode = substr(md5($cfg_cookie_encode.$randcode),0,24);
           View::assign('randcode', $randcode);
           View::assign('safecode', $safecode);


       }

       View::assign('nav', array(
           array('title'=>'系统', 'url'=>''),
           array('title'=>'系统用户管理', 'url'=>''),
           array('title'=>'编辑用户管理', 'url'=>''),
       ));
       return View::fetch();
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

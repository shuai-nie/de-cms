<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use app\Admin\model\Admin;

use think\facade\Request;
use think\facade\View;
use think\facade\Config;
use app\Admin\model\Admin as AdminModel;
use app\Admin\model\Arctype as ArctypeModel;
use app\Admin\model\Admintype as AdmintypeModel;
use app\Admin\model\Arctype;
use app\Admin\model\MemberModel;
use app\Admin\model\Member;


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
        View::assign('_nav_this', 'SysAdminUser_index');
        View::assign('_nav_itemed', 'xitong');
    }

    public function index()
    {
        $rank = Request::param('rank');
        if(\request()->isPost()) {
            $where = "";
            if(!empty($rank)){
                $where .= "concat(usertype)=".$rank;
            }
            $data =  Admin::where($where)->select();
            $count =  Admin::where($where)->count();
            return json(['code' => 0, 'data' => ['count' => $count, 'list' => $data], 'msg' => ''], 200);
        }
        return View::fetch();
    }

    public function sys_admin_user_add()
    {
        if(Request::isPost()){
            $param = Request::post('');
            $pwd = Request::post('pwd');
            $usertype = Request::post('usertype');
            $userid = Request::post('userid');
            $uname = Request::post('uname');
            $tname = Request::post('tname');
            $email = Request::post('email');
            $mpwd = md5($pwd);
            $pwd = substr(md5($pwd), 5, 20);
            $time = time();
            $ip = \request()->ip();
            $mid = Member::insert(array(
                'mtype' => '个人',
                'userid' => $userid,
                'pwd' => $mpwd,
                'uname' => $uname,
                'sex' => '男',
                'rank' => '100',
                'money' => '0',
                'email' => $email,
                'scores' => '1000',
                'matt' => 10,
                'face' => '',
                'safequestion' => 0,
                'safeanswer' => '',
                'jointime' => $time,
                'joinip' => $ip,
                'logintime' => $time,
                'loginip' => $ip,
            ), true);

            $typeid = join(',', $param['typeids']);

            $adminId = AdminModel::insert(array(
               'id'=>$mid,
               'usertype' => $usertype,
               'userid' => $userid,
               'pwd' => $pwd,
               'uname' => $uname,
               'typeid' => $typeid,
               'tname' => $tname,
               'email' => $email,
           ), true);
           return success('成功');
        }

        $ut = AdmintypeModel::where("")->order('rank asc')->select();
        View::assign('_ut', $ut);
        $randcode = mt_rand(10000, 99999);
        $cfg_cookie_encode = Config::get('app.cfg_cookie_encode');
         $safecode = substr(md5($cfg_cookie_encode.$randcode), 0, 24);
        View::assign('_randcode', $randcode);
        View::assign('_safecode', $safecode);
        $this->typeOptions();
        View::assign('nav', array(
            array('title'=>'系统', 'url'=>''),
            array('title'=>'系统用户管理', 'url'=>'index'),
            array('title'=>'新增账号', 'url'=>''),
        ));
        return View::fetch();
    }

    public function sys_admin_user_edit()
    {
        if(Request::isPost()){
            $param = Request::param('');

            if(empty($param['typeids']))
            {
                $typeid = '';
            } else {
                $typeid = join(',', $param['typeids']);
                if($typeid=='0') $typeid = '';
            }

            $set = array(
                'uname' => $param['uname'],
                'tname' => $param['tname'],
                'email' => $param['email'],
            );
            if($param['id'] != 1){
                $set['typeid'] = $typeid;
            }
            $member = array(
                'uname' => $param['uname'],
            );
            if($param['pwd']!= ''){
                $set['pwd'] = substr(md5($param['pwd']), 5, 20);
                $member['pwd'] = substr(md5($param['pwd']), 5, 20);
            }
            $state = Admin::update($set, array('id'=>$param['id']));
            $state2 = Member::update($member, array('mid'=>$param['id']));

            return success('修改成功');
        }

        $request = $this->request;
        $id = $request->param('id');
        $dopost = $request->param('dopost');

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


        View::assign('nav', array(
            array('title'=>'系统', 'url'=>''),
            array('title'=>'系统用户管理', 'url'=>'index'),
            array('title'=>'编辑用户管理', 'url'=>''),
        ));
        return View::fetch();
    }

   protected function typeOptions()
   {
       $typeOptions = "";
       $data = ArctypeModel::where("reid=0 AND (ispart=0 or ispart=1)")->select();
       foreach ($data as $k => $v){
           $topc = $v->id;
           $typeOptions .= "<option value='{$v->id}' class='btype'>{$v->typename}</option>\r\n";
           $s = Arctype::where("reid={$v->id} and (ispart=0 or ispart=1)")->select();
           foreach ($s as $k => $vo){
               $typeOptions .= "<option value='{$vo->id}' class='stype'>—{$vo->typename}</option>\r\n";
           }
       }
       View::assign('_typeOptions', $typeOptions);
   }

   public function sys_admin_user_delete()
   {
       if(Request::isGet()){
           $param = Request::param('');
           $state = Admin::where("id=".$param['id'])->delete();
           return $this->success('删除成功', (string)url('index'));
       }
   }



}

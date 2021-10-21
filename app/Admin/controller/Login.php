<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use think\facade\View;
use think\facade\Request;
use app\Admin\model\Admin as AdminModel;
use app\Admin\model\Member as MemberModel;
use think\facade\Session;

class Login
{
    public function index()
    {
        if(Request::post()){
            $username = Request::param('loginUsername');
            $userPwd = Request::param('loginPassword');
            $state = $this->checkUser($username, $userPwd);
            if($state != false){
                return json(['msg'=>'成功','code'=>0], 0);
            }
            return json(['msg'=>'成功','code'=>1], 0);
        }
        return View::fetch();
    }

    protected function checkUser($username, $userPwd)
    {
        $pwd = substr(md5($userPwd), 5, 20);
        $loginip = GetIP();
        $time = time();
        $state = AdminModel::where(array('userid'=>$username, 'pwd'=>$pwd))->find();
        if($state != false){
            AdminModel::update(array('loginip' => $loginip, 'logintime' => $time ), array( 'id' => $state['id'] ));
            MemberModel::update(array('logintime'=>$time, 'loginip'=>$loginip), array('mid'=>$state['id']));
            Session::set('user', $state);
            return true;
        }
        return false;
    }



}

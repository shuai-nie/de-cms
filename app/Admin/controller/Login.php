<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use think\facade\View;
use think\Request;
use app\Admin\model\Admin as AdminModel;
use app\Admin\model\Member as MemberModel;

class Login
{
    public function index()
    {
        View::fetch();
    }

    protected function checkUser($username, $userpwd)
    {
        $pwd = substr(md5($this->userPwd), 5, 20);
        $loginip = GetIP();
        $time = time();
        $state = AdminModel::where()->find();
        if($state != false){
            AdminModel::update(array('loginip' => $loginip, 'logintime' => $time ), array( 'id' => $state['id'] ));
            MemberModel::update(array('logintime'=>$time, 'loginip'=>$loginip), array('mid'=>$state['id']));
            return true;
        }
        return false;
    }


}

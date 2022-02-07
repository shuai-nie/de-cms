<?php
declare (strict_types = 1);

namespace app\Home\controller;

use app\Admin\model\MemberGuestbook;
use think\App;
use app\BaseController;
use think\facade\View;
use think\facade\Request;
use think\facade\Session;
use app\Admin\model\Arctiny;
use app\Admin\model\Channeltype;
use app\Admin\model\Feedback;
use app\Admin\model\Member;

class Index extends BaseController
{
    public function index()
    {
        if(Request::isPost()){
            $MemberGuestbook = new MemberGuestbook();
            $data = Request::param('');
            $state = $MemberGuestbook->save($data);
            echo json(['code'=>200, 'msg'=>'提交成功']);
        }
        return view();
    }
}
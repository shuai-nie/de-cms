<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use app\Admin\model\MemberGuestbook;
use think\facade\View;

class Guestbook extends Base {

    public function initialize()
    {
        parent::initialize();
        View::assign('nav', array(
            array('title'=>'留言板', 'url'=>''),
            array('title'=>'会员留言板', 'url'=>''),
        ));
        View::assign('_nav_itemed', 'Guestbook');
        View::assign('_nav_this', 'Guestbook_index');

    }

    public function index()
    {
        if(request()->isPost()){
            return json([
                'code' => 0,
                'msg' => '',
                'data' => MemberGuestbook::guestbook_member(),
            ]);
        }
        return View::fetch('');
    }


}
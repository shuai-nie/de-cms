<?php
declare (strict_types = 1);

namespace app\controller;

use app\Admin\controller\Base;
use think\facade\View;
use think\facade\Request;

class Index extends Base
{
    public function initialize()
    {
        parent::initialize();
        View::assign('nav', array(
            array('title' => '首页','url'=>''),
            array('title'=>'系统用户管理', 'url'=>''),
        ));

    }

    public function index()
    {
        return View::fetch();
    }


}
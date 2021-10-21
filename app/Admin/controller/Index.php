<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use think\App;
use think\facade\View;
use think\facade\Request;
use think\facade\Session;

class Index extends Base
{
    public function __construct(App $app)
    {
        // 控制器初始化
        $this->initialize();
    }
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        return View::fetch('index');
    }



}

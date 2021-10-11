<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use app\BaseController;
use think\App;
use think\Request;
use think\facade\View;

class Base extends BaseController
{
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;

        View::display('public/base');

        // 控制器初始化
        $this->initialize();
    }


}

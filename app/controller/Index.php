<?php
declare (strict_types = 1);

namespace app\controller;

use think\facade\View;
use think\facade\Request;

class Index
{
    public function initialize()
    {

        parent::initialize();


    }

    public function index()
    {

        return View::fetch();
    }


}
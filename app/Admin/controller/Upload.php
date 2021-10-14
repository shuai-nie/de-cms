<?php
declare (strict_types = 1);

namespace app\admin\controller;

use think\Request;
use think\facade\Filesystem;
use think\facade\View;

class Upload
{

    public function upload()
    {
        $file = \request()->file('image');
        $savename = Filesystem::putFile('image', $file);
        var_dump($savename);
        exit();
    }

    public function index()
    {
        return View::fetch();
    }

}

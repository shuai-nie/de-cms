<?php
declare (strict_types = 1);

namespace app\admin\controller;

use think\facade\Config;
use think\facade\Request;
use think\facade\Filesystem;
use think\facade\View;

class Upload
{

    public function upload()
    {
        $host = Config::get('app.app_host');
        $file = Request::file('file');
        $path = Filesystem::disk('public')->putFile('image', $file, 'hashNameTime');
        $picSrc = Filesystem::getDiskConfig('public', 'url').'/'.str_replace('\\', '/', $path);
        return json(['code'=>0, 'msg'=>'成功','data'=>['src'=>$picSrc]]);
    }

    public function index()
    {
        return View::fetch();
    }

}

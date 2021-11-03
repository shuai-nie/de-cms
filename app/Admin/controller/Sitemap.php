<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use think\facade\Request;
use think\facade\View;
use sitemap\Sitemap as SitemapClass;

class Sitemap extends Base{

    public function initialize()
    {
        parent::initialize();
        View::assign('nav', array(
            array('title'=>'模块', 'url'=>''),
            array('title'=>'生成网站地图', 'url'=>''),
        ));
    }

    public function index()
    {
        $sitemap = new SitemapClass();
        if(Request::isPost()){
            $sitemap->AddItem('http://baidu.com',1);
            $sitemap->SaveToFile('sitemap.xml');
            $this->success('生成成功', (string)url('index'));
        }
        return View::fetch();
    }

}
<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use app\Admin\model\Arctype;
use app\Admin\model\Channeltype;
use think\facade\Config;
use think\facade\View;
use think\facade\Request;

class Generate extends Base
{
    public function initialize()
    {
        parent::initialize();

        View::assign('nav', array(
            array('title'=>'核心', 'url'=>''),
            array('title'=>'网站栏目管理', 'url'=>''),
        ));
        View::assign('_nav_itemed', 'generateHtml');
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        if(Request::isPost()){
            $this->success('首页生成完成', (string)url('index'));
        }
        View::assign('_nav_this', 'Generate_index');
        return View::fetch('');
    }

    public function list()
    {
        $ArctypeAll = Arctype::alias('A')->leftjoin(Channeltype::getTable()." B", 'B.id=A.channeltype')->field('A.*,B.typename as ctypename,B.addtable,B.issystem')->select();
        $cfg_remote_site = Config::get('app.cfg_remote_site');
        View::assign('cfg_remote_site', $cfg_remote_site);
        View::assign('ArctypeAll', $ArctypeAll);
        return View::fetch('catalog_main:makehtml_list');
    }

    public function article()
    {
        $ArctypeAll = Arctype::alias('A')->leftjoin(Channeltype::getTable()." B", 'B.id=A.channeltype')->field('A.*,B.typename as ctypename,B.addtable,B.issystem')->select();
        $cfg_remote_site = Config::get('app.cfg_remote_site');
        View::assign('cfg_remote_site', $cfg_remote_site);
        View::assign('ArctypeAll', $ArctypeAll);

        return View::fetch('catalog_main:makehtml_archives');
    }

}

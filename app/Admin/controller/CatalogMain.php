<?php
declare (strict_types = 1);

namespace app\Admin\controller;


use think\facade\Request;
use think\facade\View;
use think\facade\Config;
use app\Admin\model\Channeltype as ChanneltypeModel;
use app\Admin\model\Arctype as ArctypeModel;
use app\Admin\model\Arcrank;
use app\Admin\model\Arctype;
use app\Admin\model\Channeltype;
use app\Admin\model\SysEnum;


/**
 * [网站栏目管理]
 * Class CatalogMain
 * @package app\Admin\controller
 */
class CatalogMain extends Base
{
    public function initialize()
    {
        parent::initialize();
        View::assign('_nav_this', 'CatalogMain_index');
        View::assign('nav', array(
            array('title'=>'核心', 'url'=>''),
            array('title'=>'网站栏目管理', 'url'=>''),
        ));
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $data = ArctypeModel::where(array())->select()->toArray();
        View::assign('_data', $data);
        return View::fetch();
    }

    /**
     * [增加子类]
     * @author Dave 178698695@qq.com
     */
    public function catalog_add()
    {
        if(Request::isPost()){
            $param = Request::param('');

            var_dump($param);

            exit();

        }

        $id = Request::param('id', 0);
        if(empty($dopost)) $dopost = '';
        $nid = 'article';
        $channelid = 1;
        $issend = 1;
        $corank = 0;
        $reid = 0;
        $topid = 0;
        $typedir = '';
        $moresite = 0;
        View::assign('issend', $issend);
        View::assign('channelid', $channelid);
        if($id>0)
        {
            $myrow = ArctypeModel::hasWhere('profile', 'Arctype.id='.$id, 'Arctype.*,Channeltype.typename as ctypename', 'left')->find();
            View::assign('channelid', $myrow['channeltype']);
            View::assign('issennd', $myrow['issend']);
            View::assign('corank', $myrow['corank']);
            View::assign('topid', $myrow['topid']);
            View::assign('typedir', $myrow['typedir']);
        }

        //父栏目是否为二级站点
        $moresite = empty($myrow['moresite']) ? 0 : $myrow['moresite'];
        View::assign('moresite', $moresite);
        $row = ChanneltypeModel::where("id<>-1 AND isshow=1")->order("id asc")->select();
        $channelArray = array();
        foreach ($row as $k => $v){
            $channelArray[$v->id]['typename'] = $v->typename;
            $channelArray[$v->id]['nid'] = $v->nid;
            if($v->id == $channelid)
            {
                $nid = $v->nid;
            }
        }



        //Select * from `#@__arcrank` where rank >= 0
        $ArcrankAll = Arcrank::where("rank >= 0")->select();
        $SysEnumAll = SysEnum::where('')->order('disorder ASC, id DESC')->select();
        $cfg_templets_dir = '/templets';

        //文档的默认命名规则
        $art_shortname = $cfg_df_ext = '.html';
        $cfg_df_namerule = '{typedir}/{Y}/{M}{D}/{aid}'.$cfg_df_ext;
        View::assign('cfg_templets_dir', $cfg_templets_dir);
        View::assign('cfg_df_namerule', $cfg_df_namerule);
        View::assign('topid', $topid);
        View::assign('SysEnumAll', $SysEnumAll);
        View::assign('id', $id);
        View::assign('nid', $nid);
        View::assign('channelArray', $channelArray);
        View::assign('ArcrankAll', $ArcrankAll);
        return View::fetch();
    }



    /**
     * 显示编辑资源表单页.
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $channel = ChanneltypeModel::where("id<>-1 AND isshow=1 ")->order('id asc')->select();
        View::assign('_channel', $channel);
        return View::fetch();
    }

    public function catalog_edit()
    {
        $id = Request::param('id');
        $channelid = 1;

        $ArcrankAll = Arcrank::where("rank >= 0")->select();
        View::assign('ArcrankAll', $ArcrankAll);

        $row = ChanneltypeModel::where("id<>-1 AND isshow=1")->order("id asc")->select();
        $channelArray = array();
        foreach ($row as $k => $v){
            $channelArray[$v->id]['typename'] = $v->typename;
            $channelArray[$v->id]['nid'] = $v->nid;
            if($v->id == $channelid)
            {
                $nid = $v->nid;
            }
        }

        $SysEnumAll = SysEnum::where('')->order('disorder ASC, id DESC')->select();

        $myrow = ArctypeModel::hasWhere('profile', 'Arctype.id='.$id, 'Arctype.*,Channeltype.typename as ctypename', 'left')->find();
        View::assign('myrow', $myrow);
        View::assign('channelid', $channelid);
        $cfg_templets_dir = '/templets';
        View::assign('cfg_templets_dir', $cfg_templets_dir);
        View::assign('SysEnumAll', $SysEnumAll);
        View::assign('channelArray', $channelArray);
        View::assign('nav', array(
            array('title'=>'核心', 'url'=>''),
            array('title'=>'网站栏目管理', 'url'=>''),
            array('title'=>'修改栏目', 'url'=>''),
        ));
        return View::fetch();
    }

    /**
     * [更新栏目组缓存]
     * @author Dave 178698695@qq.com
     */
    public function catalog_do()
    {

    }

    public function makehtml_list()
    {
        $ArctypeAll = Arctype::alias('A')->leftjoin(Channeltype::getTable()." B", 'B.id=A.channeltype')->field('A.*,B.typename as ctypename,B.addtable,B.issystem')->select();
        $cfg_remote_site = Config::get('app.cfg_remote_site');
        View::assign('cfg_remote_site', $cfg_remote_site);

        View::assign('ArctypeAll', $ArctypeAll);


        View::assign('nav', array(
            array('title'=>'核心', 'url'=>''),
            array('title'=>'网站栏目管理', 'url'=>''),
            array('title'=>'更新栏目HTML', 'url'=>''),
        ));
        return View::fetch();
    }


    public function makehtml_archives()
    {

        $ArctypeAll = Arctype::alias('A')->leftjoin(Channeltype::getTable()." B", 'B.id=A.channeltype')->field('A.*,B.typename as ctypename,B.addtable,B.issystem')->select();
        $cfg_remote_site = Config::get('app.cfg_remote_site');
        View::assign('cfg_remote_site', $cfg_remote_site);

        View::assign('ArctypeAll', $ArctypeAll);

        View::assign('nav', array(
            array('title'=>'核心', 'url'=>''),
            array('title'=>'网站栏目管理', 'url'=>''),
            array('title'=>'更新文档HTML', 'url'=>''),
        ));
        return View::fetch();
    }






}

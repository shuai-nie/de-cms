<?php
declare (strict_types = 1);

namespace app\Admin\controller;


use think\facade\Config;
use think\facade\Db;
use think\facade\Request;
use think\facade\Session;
use think\facade\View;
use app\Admin\model\Archives as ArchivesModel;
use app\Admin\model\Arcatt;
use app\Admin\model\Archives;
use app\Admin\model\Arcrank;
use app\Admin\model\Channeltype;
use app\Admin\model\Uploads;

/**
 * [所有档案列表]
 * Class ContentList
 * @package app\Admin\controller
 */
class ContentList extends Base
{
    public function initialize()
    {
        parent::initialize();
        View::assign('_nav_this', 'ContentList_index');
        View::assign('nav', array(
            array('title'=>'核心', 'url'=>''),
            array('title'=>'所有档案列表', 'url'=>''),
        ));
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $length = 20;
        $map = array();
        $arcrank = Request::param('arcrank');
        if(!empty($arcrank)){
            View::assign('nav', array(
                array('title'=>'核心', 'url'=>''),
                array('title'=>'等审核的文档', 'url'=>''),
            ));
            View::assign('_nav_this', 'ContentList_index2');
            $map['arcrank'] = $arcrank;
        }
        $data = ArchivesModel::where($map)->order('id desc')->paginate($length);
        View::assign('_data', $data);
        return View::fetch();
    }

    public function article_add()
    {
        $channelid = Request::param('channelid');
        $cid = Request::param('cid');


        $channelid = empty($channelid) ? 0 : intval($channelid);
        $cid = empty($cid) ? 0 : intval($cid);
        View::assign('channelid', $channelid);
        View::assign('cid', $cid);
        $geturl = Request::param('geturl');
        if(empty($geturl)) $geturl = '';
        View::assign('geturl', $geturl);
        View::assign('title', '');
        $ArcattAll = Arcatt::where("")->order('sortid asc')->select();
        View::assign('ArcattAll', $ArcattAll);
         $ArchivesCount = Archives::where("")->count();
        $cfg_need_typeid2 = Config::get('app.cfg_need_typeid2');
        $cfg_remote_site = Config::get('app.cfg_remote_site');
        $cfg_arc_autokeyword = Config::get('app.cfg_arc_autokeyword');
        $cfg_rm_remote = Config::get('app.cfg_rm_remote');
        $cfg_arc_dellink = Config::get('app.cfg_arc_dellink');
        $cfg_arc_autopic = Config::get('app.cfg_arc_autopic');
        $photo_markup = Config::get('app.photo_markup');
        $cfg_arcautosp = Config::get('app.cfg_arcautosp');
        $cfg_arcautosp_size = Config::get('app.cfg_arcautosp_size');
        $cfg_feedback_forbid = Config::get('app.cfg_feedback_forbid');
        $cfg_arc_click = Config::get('app.cfg_arc_click');
        $ArcrankAll = Arcrank::where("")->select();
        View::assign('nowtime', time());
        View::assign('ArcrankAll', $ArcrankAll);
        View::assign('cfg_arc_click', $cfg_arc_click);
        View::assign('cfg_feedback_forbid', $cfg_feedback_forbid);
        View::assign('cfg_arcautosp_size', $cfg_arcautosp_size);
        View::assign('cfg_arcautosp', $cfg_arcautosp);
        View::assign('photo_markup', $photo_markup);
        View::assign('ArchivesCount', $ArchivesCount);
        View::assign('source', '');
        View::assign('writer', '');
        View::assign('cfg_need_typeid2', $cfg_need_typeid2);
        View::assign('cfg_remote_site', $cfg_remote_site);
        View::assign('cfg_arc_autokeyword', $cfg_arc_autokeyword);
        View::assign('cfg_rm_remote', $cfg_rm_remote);
        View::assign('cfg_arc_dellink', $cfg_arc_dellink);
        View::assign('cfg_arc_autopic', $cfg_arc_autopic);
        View::assign('keywords', '');
        View::assign('description', '');

        return View::fetch();
    }

    public function article_edit()
    {

        $channelid = Request::param('channelid');
        $cid = Request::param('cid');
        $channelid = empty($channelid) ? 0 : intval($channelid);
        $cid = empty($cid) ? 0 : intval($cid);

        $geturl = Request::param('geturl');
        if(empty($geturl)) $geturl = '';

        $ArcattAll = Arcatt::where("")->order('sortid asc')->select();

        $ArchivesCount = Archives::where("")->count();


        $aid = Request::param('aid');
        $arcRow = Archives::alias('A')
                ->leftJoin(Channeltype::getTable()." B", "B.id=A.channel")
                ->leftJoin(Arcrank::getTable()." C", "C.rank=A.arcrank")
                ->field('B.typename AS channelname,C.membername AS rankname,A.*')
                ->where("A.id=$aid")->find();


        $cInfos = Channeltype::where("id=".$arcRow['channel'])->find();
        $addtable = $cInfos['addtable'];
        $addRow = Db::query("SELECT * FROM `$addtable` WHERE aid='$aid'");

        $trow = Uploads::where("arcid =".$addRow[0]['aid'])->select();
        $tags = GetTags($aid);
        $user = Session::get('user');
        $cfg_remote_site = Config::get('app.cfg_remote_site');
        $cfg_need_typeid2 = Config::get('app.cfg_need_typeid2');
        $ArcrankAll = Arcrank::where("")->select();
        View::assign('arcRow', $arcRow);
        View::assign('addRow', $addRow[0]);
        View::assign('cfg_remote_site', $cfg_remote_site);
        View::assign('cfg_need_typeid2', $cfg_need_typeid2);
        View::assign('ArcattAll', $ArcattAll);
        View::assign('nowtime', time());
        View::assign('tags', $tags);
        View::assign('trow', $trow);
        View::assign('aid', $aid);
        View::assign('channelid', $channelid);
        View::assign('cid', $cid);
        View::assign('geturl', $geturl);
        View::assign('title', '');
        View::assign('ArcattAll', $ArcattAll);
        View::assign('ArchivesCount', $ArchivesCount);

        View::assign('nav', array(
            array('title'=>'核心', 'url'=>''),
            array('title'=>'所有档案列表', 'url'=>''),
            array('title'=>'更改文章', 'url'=>''),
        ));
        return View::fetch();
    }





}

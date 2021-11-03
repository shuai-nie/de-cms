<?php
declare (strict_types = 1);

namespace app\Admin\controller;


use app\Admin\model\Admin;
use app\Admin\model\Arctype;
use think\facade\Config;
use think\facade\Db;
use think\facade\Request;
use think\facade\Session;
use think\facade\View;
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
        $data = Archives::alias('A')
            ->leftjoin(Arctype::getTable()." B ", "B.id=A.typeid")
            ->leftjoin(Admin::getTable()." C ", "C.id=A.mid")
            ->field('A.*,B.typename as ctypename,C.userid')
            ->order('id desc')->paginate($length);
        View::assign('_data', $data);
        View::assign('arcrank', $arcrank);
        return View::fetch();
    }

    public function article_add()
    {
        if(Request::isPost()){
            $param    = Request::param('');
            $pubdate  = GetMkTime($param['pubdate']);
            $senddate = time();
            $sortrank = AddDay($pubdate, $param['sortup']);
            $senddate = time();
            $arcID    = GetIndexKey($param['arcrank'], $param['typeid'], $sortrank, $param['channelid'], $senddate, 1);


            $state = Archives::insert(array(
                'id'          => $arcID,
                'typeid'      => $param['typeid'],
                'typeid2'     => $param['typeid2'],
                'sortrank'    => $param['sortrank'],
                'flag'        => $param['flag'],
                'ismake'      => $param['ismake'],
                'channel'     => $param['channelid'],
                'arcrank'     => $param['arcrank'],
                'click'       => $param['click'],
                'money'       => $param['money'],
                'title'       => $param['title'],
                'shorttitle'  => $param['shorttitle'],
                'color'       => $param['color'],
                'writer'      => $param['writer'],
                'source'      => $param['source'],
                'mid'         => 1,
                'voteid'      => $param['voteid'],
                'notpost'     => $param['notpost'],
                'description' => $param['description'],
                'keywords'    => $param['keywords'],
                'filename'    => $param['filename'],
                'dutyadmin'   => $param['dutyadmin'],
                'weight'      => $param['weight'],

            ));

            var_dump($param);


            exit();
        }
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
        $ArctypeAll = Arctype::where("")->select();
        View::assign('nowtime', time());
        View::assign('ArctypeAll', $ArctypeAll);

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
        if(Request::isPost()){
            $param = Request::param('');
            if(isset($param['typeid2'])){
                $typeid2 = $param['typeid2'];
            }else{
                $typeid2 = 0;
            }
            $pubdate = GetMkTime($param['pubdate']);
            $sortrank = AddDay($pubdate, $param['sortup']);
            $flag = isset($flags) ? join(',',$flags) : '';

            if(empty($param['ddisremote']))
            {
                $ddisremote = 0;
            }else{
                $ddisremote = 1;
            }

            // 处理缩略图
            $litpic = GetDDImage('none', $param['picname'], $ddisremote);
            //处理图片文档的自定义属性
            if($litpic != '' && !preg_match("#p#", $flag))
            {
                $flag = ($flag=='' ? 'p' : $flag.',p');
            }
            if($param['redirecturl'] != '' && !preg_match("#j#", $flag))
            {
                $flag = ($flag=='' ? 'j' : $flag.',j');
            }
            $param['ismake'] = $param['ismake'] == 0? -1 : 0;
            if(preg_match("#j#", $flag)){$ismake = -1;}
            $arcrank = 0;


            $AdminUser = Session::has('AdminUser');
            $adminid = $AdminUser->uid;
            $state = Archives::update(array(
                'typeid' => $param['typeid'] , 'typeid2' => $typeid2, 'sortrank' => $sortrank, 'flag' => $flag, 'click' => $param['click'],
                'ismake' => $ismake, 'arcrank' => $arcrank, 'money' => $param['money'], 'title' => $param['title'], 'color' => $param['color'],
                'writer' => $param['writer'], 'source' => $param['source'], 'litpic' => $litpic, 'pubdate' => $param['pubdate'], 'voteid' => $param['voteid'],
                'notpost' => $param['notpost'], 'description' => $param['description'],'keywords' => $param['keywords'],
                'shorttitle' => $param['shorttitle'], 'filename' => $param['filename'], 'dutyadmin' => $adminid, 'weight' => $param['weight'],
            ), array('id'=>$param['id'] ));

            $cts = Channeltype::where("id", '=', $param['channelid'])->find();
            $addtable = trim($cts['addtable']);
            if($addtable != '')
            {
                $useip = Request::ip();
                $templet = empty($param['templet']) ? '' : $param['templet'];
                $iquery = "UPDATE `$addtable` SET typeid='{$param['typeid']}',body='{$param['body']}',redirecturl='{$param['redirecturl']}',templet='$templet',userip='$useip' WHERE aid='$id'";

            }

//
            var_dump($param);

            exit();
            //$this->success('修改成功', (string)url('article_edit') );
        }

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
        $ArcrankAll = Arcrank::where("")->select()->toArray();

        $ArctypeAll = Arctype::where("")->select();
        $channelid = $arcRow['channel'];

        View::assign('ArctypeAll', $ArctypeAll);
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

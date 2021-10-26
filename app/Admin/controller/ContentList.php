<?php
declare (strict_types = 1);

namespace app\Admin\controller;


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
         View::assign('ArchivesCount', $ArchivesCount);
         View::assign('source', '');
         View::assign('writer', '');

        return View::fetch();
    }

    public function article_edit()
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
        View::assign('ArchivesCount', $ArchivesCount);

        $aid = Request::param('aid');
        $arcRow = Archives::alias('A')
                ->leftJoin(Channeltype::getTable()." B", "B.id=A.channel")
                ->leftJoin(Arcrank::getTable()." C", "C.rank=A.arcrank")
                ->field('B.typename AS channelname,C.membername AS rankname,A.*')
                ->where("A.id=$aid")->find();
        View::assign('arcRow', $arcRow);

        $cInfos = Channeltype::where("id=".$arcRow['channel'])->find();
        $addtable = $cInfos['addtable'];
        //$addRow = $dsql->GetOne("");

        $addRow = Db::query("SELECT * FROM `$addtable` WHERE aid='$aid'");
        View::assign('addRow', $addRow[0]);
        $trow = Uploads::where("arcid =".$addRow[0]['aid'])->select();
        View::assign('trow', $trow);
        View::assign('aid', $aid);
        $tags = GetTags($aid);
        View::assign('tags', $tags);
        $user = Session::get('user');
        $typeOptions = GetOptionList($arcRow['typeid'], $user->usertype, $channelid);
        var_dump($typeOptions);exit();
        return View::fetch();
    }





}

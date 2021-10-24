<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use app\admin\model\Arcatt;
use app\admin\model\Archives;
use think\facade\Request;
use think\facade\View;
use app\admin\model\Archives as ArchivesModel;

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
        $data = ArchivesModel::where($map)->paginate($length);
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





}

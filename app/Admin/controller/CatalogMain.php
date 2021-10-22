<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use app\admin\model\Arcrank;
use app\Admin\model\SysEnum;
use think\facade\Request;
use think\facade\View;
use app\admin\model\Channeltype as ChanneltypeModel;
use app\admin\model\Arctype as ArctypeModel;
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
            //$myrow = $dsql->GetOne(" SELECT tp.*,ch.typename AS ctypename FROM `#@__arctype` tp LEFT JOIN `#@__channeltype` ch ON ch.id=tp.channeltype WHERE tp.id=$id ");
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
        View::assign('channelArray', $channelArray);
        //Select * from `#@__arcrank` where rank >= 0
        $ArcrankAll = Arcrank::where("rank >= 0")->select();
        View::assign('ArcrankAll', $ArcrankAll);

        //SELECT * FROM `#@__sys_enum` WHERE egroup LIKE 'infotype' ORDER BY disorder ASC, id DESC
        $SysEnumAll = SysEnum::where('')->order('disorder ASC, id DESC')->select();
        View::assign('SysEnumAll', $SysEnumAll);
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

    /**
     * [更新栏目组缓存]
     * @author Dave 178698695@qq.com
     */
    public function catalog_do()
    {

    }

    public function makehtml_list()
    {

    }






}

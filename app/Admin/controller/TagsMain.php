<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use app\Admin\model\Archives;
use app\Admin\model\Tagindex;
use app\Admin\model\Taglist;
use think\facade\Db;
use think\Request;
use think\facade\View;
use app\Admin\model\Flink as FlinkModel;

/**
 * [TAG 标签管理]
 * Class TagsMain
 * @package app\Admin\controller
 */
class TagsMain extends Base
{

    public function initialize()
    {
        parent::initialize();
        View::assign('nav', array(
            array('title'=>'核心', 'url'=>''),
            array('title'=>'TAG 标签管理', 'url'=>''),
        ));
    }

    /**
     * 显示资源列表
     * @return \think\Response
     */
    public function index()
    {

        $offset = 0;
        $length = 20;
        $data = Tagindex::where("")->order('id desc')->paginate($length);
        View::assign('_data', $data);
        return View::fetch();
    }

    public function tags_main_fetch()
    {
        $timestamp = time();
        $wheresql = '';
        $start = isset($start) && is_numeric($start) ? $start : 0;
        $where = array();
        if(isset($startaid) && is_numeric($startaid) && $startaid > 0){
            $where[] = " id>$startaid ";
        }else{
            $startaid = 0;
        }

        if(isset($endaid) && is_numeric($endaid) && $endaid > 0){
            $where[] = " id<$endaid ";
        }else{
            $endaid = 0;
        }
        if(!empty($where))
        {
            $wheresql = " arcrank>-1 AND ".implode(' AND ', $where);
        }
//        $query = "SELECT id as aid,arcrank,typeid,keywords FROM `#@__archives` $wheresql LIMIT $start, 100";
//        $dsql->SetQuery($query);
//        $dsql->Execute();
//        $complete = true;
//        while($row = $dsql->GetArray())
        $row = Archives::where($wheresql)->limit($start, 100)->select();
        foreach($row as $k => $vo){
            $aid = $vo['aid'];
            $typeid = $vo['typeid'];
            $arcrank = $vo['arcrank'];
            $vo['keywords'] = trim($vo['keywords']);
            if($vo['keywords']!='' && !preg_match("#,#", $vo['keywords'])){
                $keyarr = explode(' ', $vo['keywords']);
            }else{
                $keyarr = explode(',', $vo['keywords']);
            }

            foreach($keyarr as $keyword)
            {
                $keyword = trim($keyword);
                if($keyword != '' && strlen($keyword)<13 )
                {
                    $keyword = addslashes($keyword);
                    //$row = $dsql->GetOne("SELECT id FROM `#@__tagindex` WHERE tag LIKE '$keyword'");
                    $row = Tagindex::where('tag', 'like', '%'.$keyword.'%')->find();
                    if(is_array($row))
                    {
                        $tid = $row['id'];
//                        $query = "UPDATE `#@__tagindex` SET `total`=`total`+1 WHERE id='$tid' ";
                        Tagindex::where("id=".$tid)->inc('total')->update();
//                        $dsql->ExecuteNoneQuery($query);
                    }
                    else
                    {
//                        $query = " INSERT INTO `#@__tagindex`(`tag`,`count`,`total`,`weekcc`,`monthcc`,`weekup`,`monthup`,`addtime`) VALUES('$keyword','0','1','0','0','$timestamp','$timestamp','$timestamp');";
//                        $dsql->ExecuteNoneQuery($query);
                        $tid = Tagindex::Insert(array(
                            'tag'     => $keyword,
                            'count'   => 0,
                            'total'   => 1,
                            'weekcc'  => 0,
                            'monthcc' => 0,
                            'weekup'  => $timestamp,
                            'monthup' => $timestamp,
                            'addtime' => $timestamp,
                        ), true);
//                        $tid = $dsql->GetLastID();
                    }
//                    $query = "REPLACE INTO `#@__taglist`(`tid`,`aid`,`typeid`,`arcrank`,`tag`) VALUES ('$tid', '$aid', '$typeid','$arcrank','$keyword'); ";
//                    $dsql->ExecuteNoneQuery($query);
                    Taglist::replace()->insert(array(
                        'tid'     => $tid,
                        'aid'     => $aid,
                        'typeid'  => $typeid,
                        'arcrank' => $arcrank,
                        'tag'     => $keyword,
                    ), true);

                }
            }
            $complete = FALSE;
        }
        if($complete)
        {
            return json(['code'=>0, 'msg'=>'成功']);
        }
        return json(['code'=>1, 'msg'=>'失败']);

    }












}

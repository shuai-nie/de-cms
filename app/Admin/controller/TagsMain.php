<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use app\Admin\model\Archives;
use app\Admin\model\Tagindex;
use app\Admin\model\Taglist;
use think\facade\Db;
use think\facade\Request;
use think\facade\View;

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
        View::assign('_nav_itemed', 'hexing');
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
        if(!empty($where)){
            $wheresql = " arcrank>-1 AND ".implode(' AND ', $where);
        }

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
                        Tagindex::where("id=".$tid)->inc('total')->update();

                    }else{
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
                    }

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

    public function tags_delete()
    {
        if($ids = Request::param('ids')){
            $state = Tagindex::where("id=".$ids)->delete();
            if($state !== false){
                return $this->success("删除成功", (string)url('index'));
            }
            return $this->error("删除失败");
            exit();
        }
    }

    public function tags_main()
    {
        $start = Request::param('start', 0);
        $startaid = Request::param('startaid', 0);
        //$start = isset($start) && is_numeric($start) ? $start : 0;
        $timestamp = time();
        $map = array();
        $where = array();
        if( $startaid > 0){
            $where[] = " id>$startaid ";
        }else{
            $startaid = 0;
        }

        if(isset($endaid) && is_numeric($endaid) && $endaid > 0){
            $where[] = " id<$endaid ";
        }else{
            $endaid = 0;
        }
        if(!empty($where)){
            $wheresql = " WHERE arcrank>-1 AND ".implode(' AND ', $where);
        }
        $data = Archives::where($map)->field('id as aid,arcrank,typeid,keywords')->limit($start, 100)->select();
        foreach ($data as $k => $row){
            $aid = $row['aid'];
            $typeid = $row['typeid'];
            $arcrank = $row['arcrank'];
            $row['keywords'] = trim($row['keywords']);
            if($row['keywords']!='' && !preg_match("#,#", $row['keywords'])){
                $keyarr = explode(' ', $row['keywords']);
            }else{
                $keyarr = explode(',', $row['keywords']);
            }
            foreach($keyarr as $keyword){
                $keyword = trim($keyword);
                if($keyword != '' && strlen($keyword)<13 )
                {
                    $keyword = addslashes($keyword);
                    //$row = $dsql->GetOne("SELECT id FROM `#@__tagindex` WHERE tag LIKE '$keyword'");
                    $row = Tagindex::where(" tag like '".$keyword."'")->find();
                    if(is_array($row)){
                        $tid = $row['id'];
                        //Tagindex::where("id=".$tid)->inc('total')->update();
                        //Db::name('')->where('')->inc('total')->update();
                        Tagindex::TagindexInc("id=".$tid, 'total', 1);
                        //$query = "UPDATE `#@__tagindex` SET `total`=`total`+1 WHERE id='$tid' ";
                        //$dsql->ExecuteNoneQuery($query);
                    }else{
                        $data = [
                            'tag'     => $keyword,
                            'count'   => 0,
                            'total'   => 1,
                            'weekcc'  => 0,
                            'monthcc' => 0,
                            'weekup'  => $timestamp,
                            'monthup' => $timestamp,
                            'addtime' => $timestamp,
                        ];
                        $tid = Tagindex::replace()->insert($data, true);
                        //$query = " INSERT INTO `#@__tagindex`(`tag`,`count`,`total`,`weekcc`,`monthcc`,`weekup`,`monthup`,`addtime`) VALUES('$keyword','0','1','0','0','$timestamp','$timestamp','$timestamp');";
                        //$dsql->ExecuteNoneQuery($query);
                        //$tid = $dsql->GetLastID();
                    }
                    //$query = "REPLACE INTO `#@__taglist`(`tid`,`aid`,`typeid`,`arcrank`,`tag`) VALUES ('$tid', '$aid', '$typeid','$arcrank','$keyword'); ";
                    //$dsql->ExecuteNoneQuery($query);
                    $data2 = array(
                        'tid'     => $tid,
                        'aid'     => $aid,
                        'typeid'  => $typeid,
                        'arcrank' => $arcrank,
                        'tag'     => $keyword,
                    );
                    Tagindex::REPLACE()->insert($data2);
                }
            }

        }
        return $this->success("拉取成功", (string)url('index'));

    }












}

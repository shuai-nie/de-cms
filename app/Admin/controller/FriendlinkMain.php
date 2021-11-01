<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use app\Admin\model\Flink;
use app\Admin\model\Flinktype;
use think\facade\Request;
use think\facade\View;
use app\Admin\model\Flink as FlinkModel;

/**
 * [有情链接]
 * Class FriendlikeMain
 * @package app\Admin\controller
 */
class FriendlinkMain extends Base
{
    public function initialize()
    {
        parent::initialize();
        View::assign('_nav_this', 'FriendlinkMain_index');
        View::assign('nav', array(
            array('title'=>'模块', 'url'=>''),
            array('title'=>'友情链接', 'url'=>''),
        ));
    }

    /**
     * 显示资源列表
     * @return \think\Response
     */
    public function index()
    {
        $data = FlinkModel::where([])->select();
        View::assign('_data', $data);
        return View::fetch();
    }

    public function friendlink_add()
    {
        if(Request::isPost()){
            $param = Request::param('');
            $dtime = time();

            $state = Flink::insert(array(
                'sortrank' => $param['sortrank'],
                'url' => $param['url'],
                'webname' => $param['webname'],
                'logo' => $param['logo'],
                'msg' => $param['msg'],
                'email' => $param['email'],
                'typeid' => $param['typeid'],
                'dtime' => $dtime,
                'ischeck' => $param['ischeck'],
            ));
            if($state !== false){
                return $this->success('提交成功', (string)url('index'));
            }else{
                return $this->error('提交失败');
            }

        }

        View::assign('nav', array(
            array('title'=>'模块', 'url'=>''),
            array('title'=>'友情链接', 'url'=>''),
            array('title'=>'添加链接', 'url'=>''),
        ));

        $row = Flinktype::where("")->select();
        View::assign('row', $row);
        return View::fetch();
    }

    public function friendlink_type()
    {
        View::assign('nav', array(
            array('title'=>'模块', 'url'=>''),
            array('title'=>'友情链接', 'url'=>''),
            array('title'=>'网站类型管理', 'url'=>''),
        ));

        $row = Flinktype::where("")->select();
        View::assign('row', $row);
        return View::fetch();
    }

    public function friendlink_edit()
    {
        if(Request::isPost()){
            $param = Request::param('');

            $state = Flink::update(array(
                'sortrank' => $param['sortrank'],
                'url' => $param['url'],
                'webname' => $param['webname'],
                'logo' => $param['logo'],
                'msg' => $param['msg'],
                'email' => $param['email'],
                'typeid' => $param['typeid'],
                'ischeck' => $param['ischeck']
            ), array(
                'id' => $param['id']
            ));

            if($state !== false){
                return $this->success('提交成功', (string)url('index'));
            }else{
                return $this->error('提交失败');
            }
        }


        View::assign('nav', array(
            array('title'=>'模块', 'url'=>''),
            array('title'=>'友情链接', 'url'=>''),
            array('title'=>'编辑链接', 'url'=>''),
        ));

        $id = Request::param('id');

        $myLink = Flink::alias('A')->leftjoin(Flinktype::getTable().' B', ' A.typeid=B.id ')->field('A.*,B.typename')->where("A.id=$id")->find();
        $row = Flinktype::where("id<>".$myLink['typeid'])->select();
        View::assign('myLink', $myLink);
        View::assign('row', $row);
        return View::fetch();
    }



}

<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use think\facade\Request;
use think\facade\View;
use app\Admin\model\Flink;
use app\Admin\model\Flinktype;
use think\Image;

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
        View::assign('_nav_itemed', 'mokuai');
    }

    /**
     * 显示资源列表
     * @return \think\Response
     */
    public function index()
    {
        $data = Flink::where([])->select();
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
                'url'      => $param['url'],
                'webname'  => $param['webname'],
                'logo'     => $param['logo'],
                'msg'      => $param['msg'],
                'email'    => $param['email'],
                'typeid'   => $param['typeid'],
                'dtime'    => $dtime,
                'ischeck'  => $param['ischeck'],
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
            var_dump($param);exit();

            $state = Flink::update(array(
                'sortrank' => $param['sortrank'],
                'url'      => $param['url'],
                'webname'  => $param['webname'],
                'logo'     => $param['logo'],
                'msg'      => $param['msg'],
                'email'    => $param['email'],
                'typeid'   => $param['typeid'],
                'ischeck'  => $param['ischeck'],
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

    /**
     * [水印案例]
     * @author Dave 178698695@qq.com
     */
    public function src()
    {
        $root = root_path('public\storage');
        $image = Image::open($root.'image/20211104/a953b733224457e06c44da0b0b765bb8.jpg');
        $width = $image->width();
        $height = $image->height();
        $type = $image->type();
        $mime = $image->mime();
        $size = $image->size();

        // 后台剪裁
        //$image->crop(100, 100, 50, 20)->save($root.time().'.png');

        // 生成缩略图
        //$image->thumb(50, 50, Image::THUMB_CENTER)->save($root.time().'_ss'.'.png');

        // 图片水印
        //$image->water($root.'pview.gif', Image::FLIP_X, 50)->save($root.time().'_water_img.png');

        // 文字水印
        $image->text(time().'十年磨一剑', $root.'msyhl.ttc', 20, '#f44336', Image::WATER_CENTER, 00, 50)->save($root.time().'text_image.png');




    }





}

<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use think\Config;
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
        $length = 10;
        $data = Flink::where([])->order('sortrank desc')->paginate($length);
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
            array('title'=>'模块'),
            array('title'=>'友情链接', 'url'=>(string)url('FriendlinkMain/index')),
            array('title'=>'添加链接'),
        ));

        $row = Flinktype::where("")->select();
        View::assign('row', $row);
        return View::fetch();
    }

    public function friendlink_type()
    {
        View::assign('nav', array(
            array('title'=>'模块'),
            array('title'=>'友情链接', 'url'=>(string)url('FriendlinkMain/index')),
            array('title'=>'网站类型管理'),
        ));

        $row = Flinktype::where("")->select();
        View::assign('row', $row);
        return View::fetch();
    }

    /**
     * [编辑]
     * @author Dave 178698695@qq.com
     * @return string|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function friendlink_edit()
    {
        if(Request::isPost()){
            $param = Request::param('');

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
                return $this->success('编辑成功', (string)url('index'));
            }else{
                return $this->error('编辑失败');
            }
        }


        View::assign('nav', array(
            array('title'=>'模块', 'url'=>''),
            array('title'=>'友情链接', 'url'=>(string)url('FriendlinkMain/index')),
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
     * [删除]
     * @author Dave 178698695@qq.com
     */
    public function friendlink_delete()
    {
        $id = Request::param('id');
        $dopost = Request::param('dopost');
        if($dopost == 'delete'){
            $state = Flink::where("id=".$id)->delete();
            if($state !== false){
                return $this->success('删除成功', (string)url('index'));
            }else{
                return $this->error('删除失败');
            }

        }

    }

    /**
     * [添加网站类型]
     * User: yesheng35@126.com
     * DateTime 2021/11/23 22:55
     */
    public function friendlink_type_add()
    {
        if(Request::isPost()){
            $param = Request::param('');
            $state = Flinktype::insert($param);
            if($state !== false){
                return json(array(
                    'code' => 0,
                    'msg' => '新建成功'
                ));
            }
            return json(array(
               'code' => 1,
               'msg' => '新建失败',
            ));

            exit();

        }
        View::assign('nav', array(
            array('title'=>'模块', 'url'=>''),
            array('title'=>'友情链接', 'url'=>(string)url('FriendlinkMain/index')),
            array('title'=>'网站类型管理', 'url'=>(string)url('FriendlinkMain/friendlink_type') ),
            array('title'=>'新建-网站类型管理', 'url'=>''),
        ));
        View::assign('data', array('id'=>'','typename'=>''));
        return View::fetch('friendlink_type_add');
    }

    /**
     * [编辑网站类型]
     * User: yesheng35@126.com
     * DateTime 2021/11/23 22:56
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function friendlink_type_edit()
    {
        $id = Request::param('id');
        if(Request::isPost()){
            $typename = Request::param('typename');

            $state = Flinktype::update(['typename'=>$typename], ['id'=>$id]);
            if($state !== false){
                return json(array(
                    'code' => 0,
                    'msg' => '编辑成功'
                ));
            }
            return json(array(
                'code' => 1,
                'msg' => '编辑失败',
            ));

            exit();

        }

        $data = Flinktype::where(['id'=>$id])->find();
        View::assign('data', $data);
        View::assign('nav', array(
            array('title'=>'模块'),
            array('title'=>'友情链接', 'url'=>(string)url('FriendlinkMain/index')),
            array('title'=>'网站类型管理', 'url'=>(string)url('FriendlinkMain/friendlink_type') ),
            array('title'=>'编辑-网站类型管理'),
        ));
        return View::fetch('friendlink_type_add');

    }

    /**
     * [删除网站类型]
     * User: yesheng35@126.com
     * DateTime 2021/11/23 22:56
     */
    public function friendlink_type_delete()
    {

        $id = Request::param('id');
        $state = Flinktype::where(['id'=>$id])->delete();
        if($state !== false){
            return $this->success("删除成功", (string)url('friendlink_type'));
        }
        return $this->error("删除失败");

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

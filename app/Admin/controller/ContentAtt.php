<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use think\facade\Request;
use think\facade\View;
use app\Admin\model\Arcatt as ArcattModel;

/**
 * [自定义文档属性]
 * Class ContentAtt
 * @package app\Admin\controller
 */
class ContentAtt extends Base
{
    public function initialize()
    {
        parent::initialize();
        View::assign('nav', array(
            array('title'=>'系统', 'url'=>''),
            array('title'=>'自定义文档属性', 'url'=>''),
        ));
        View::assign('_nav_itemed', 'xitong');
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        if(Request::isPost()){
            $map = array();
            $length = 20;
            $data = ArcattModel::where($map)->order('sortid asc')->select();
            return json(['code'=>0, 'count'=>$length, 'data'=>$data]);
        }
        return View::fetch();
    }











}

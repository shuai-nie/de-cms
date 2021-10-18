<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use think\Request;
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
        $data = FlinkModel::where([])->select()->toArray();
        View::assign('_data', $data);
        return View::fetch();
    }



}

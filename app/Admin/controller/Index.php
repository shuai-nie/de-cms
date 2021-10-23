<?php
declare (strict_types = 1);

namespace app\Admin\controller;


use think\App;
use think\facade\View;
use think\facade\Request;
use think\facade\Session;
use app\admin\model\Arctiny;
use app\admin\model\Channeltype;
use app\admin\model\Feedback;
use app\admin\model\Member;

class Index extends Base
{
    public function initialize()
    {
        parent::initialize();
        View::assign('nav', array(
            array('title'=>'首页', 'url'=>''),
        ));
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $memberCount = Member::where("")->count();
        $feedbackCount = Feedback::where("")->count();
        $row = Channeltype::where("")->select();
        $chArrNames = array();
        foreach ($row as $k => $v){
            $chArrNames[$v->id] = $v->typename;
        }
        $e = Arctiny::where("")->field("count(channel) as dd,channel")->order("channel asc")->select();
        $allArr = 0;
        $chArr = array();
        foreach ($e as $k => $v){
            $allArr += $v['dd'];
            $v->typename = $chArrNames[$v->channel];
            $chArr[] = $v;
        }

        View::assign('_memberCount', $memberCount);
        View::assign('_allArr', $allArr);
        View::assign('chArr', $chArr);
        View::assign('_feedbackCount', $feedbackCount);
        return View::fetch();
    }



}

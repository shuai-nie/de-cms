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
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        if(Request::isPost()){
            $param = Request::param('');

            $startID = 1;
            $endID = $param['idend'];
            for(; $startID<=$endID; $startID++)
            {
                $att = $param['att_'.$startID];
                $attname = $param['attname_'.$startID];
                $sortid = $param['sortid_'.$startID];
                ArcattModel::update(array(
                    'attname' => $attname,
                    'sortid' => $sortid,
                ), array(
                    'att' => $att
                ));
            }

            return $this->success("修改成功", (string)url('index'));

        }
        $length = 20;
        $map = array();
        $data = ArcattModel::where($map)->order('sortid asc')->paginate($length);
        View::assign('_data', $data);
        return View::fetch();
    }











}

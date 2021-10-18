<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use think\facade\View;
use think\Request;
use app\Admin\model\Admintype as AdmintypeModel;
use app\Admin\model\Admin as AdminModel;
use app\admin\model\Arctype as ArctypeModel;

/**
 * [用户组设定]
 * Class SysGroup
 * @package app\Admin\controller
 */
class SysGroup extends Base
{
    public function initialize()
    {
        parent::initialize();
        View::assign('nav', array(
            array('title'=>'系统', 'url'=>''),
            array('title'=>'用户组设定', 'url'=>''),
        ));
    }


    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $data = AdmintypeModel::where(array())->field('rank,typename,system')->select()->toArray();
        View::assign('_data', $data);
        return View::fetch('index');
    }

    public function sys_group_edit($rank)
    {
        $gouplists = file('inc/grouplist.txt');
        $groupSet = AdmintypeModel::where(['rank'=>$rank])->find()->toArray();
        View::assign('_groupSet', $groupSet);
        View::assign('_gouplists', $gouplists);
        return View::fetch();

    }

    public function sys_admin_user($rank)
    {
        $data = AdminModel::where(['usertype'=>$rank])->select()->toArray();
        foreach ($data as $k=>$v){
            if(!empty($v['typeid'])){
                $ArctypeInfo = ArctypeModel::where(['id'=>$v['typeid']])->find()->toArray();
                $v['typename'] = $ArctypeInfo['typename'];
            }else{
                $v['typename'] = "";
            }
            $data[$k] = $v;
        }


        View::assign('_data', $data);
        return View::fetch();
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}

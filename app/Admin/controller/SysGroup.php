<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use app\Admin\model\Admintype;
use app\Admin\model\Plus;
use think\facade\Log;
use think\facade\View;
use think\facade\Request;
use app\Admin\model\Admintype as AdmintypeModel;
use app\Admin\model\Admin as AdminModel;
use app\Admin\model\Arctype as ArctypeModel;

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
        View::assign('_nav_this', 'SysGroup_index');
        View::assign('nav', array(
            array('title'=>'系统', 'url'=>''),
            array('title'=>'用户组设定', 'url'=>''),
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
        if(\request()->isPost()) {
            $page = \request()->post('page', 1);
            $limit = (int)\request()->post('limit', 10);
            $offset = ($page - 1) * $limit;
            $map = [];
            $data = AdmintypeModel::where($map)->field('rank,typename,system')->limit($offset, $limit)->select();
            $count = AdmintypeModel::where($map)->count();
            return json(['code' => 0, 'count' => $count, 'data' => $data], 200);
        }
        $data = AdmintypeModel::where(array())->field('rank,typename,system')->select()->toArray();
        View::assign('_data', $data);

        return View::fetch('index');
    }

    public function sys_group_edit($rank)
    {
        if(Request::isPost()){
            $param = Request::param('');

            $purview = "";
            if(is_array($param['purviews']))
            {
                foreach($param['purviews'] as $p)
                {
                    $purview .= "$p ";
                }
                $purview = trim($purview);
            }

            Admintype::update(array(
                'typename' => $param['typename'],
                'purviews' => $purview,
            ), array(
                'rank' => $param['rank']
            ));

            return $this->success('提交成功', (string)url('index'));
        }
        $gouplists = file('inc/grouplist.txt');
        $groupSet = AdmintypeModel::where(['rank'=>$rank])->find()->toArray();
        $PlusAll = Plus::where("")->select();
        View::assign('_groupSet', $groupSet);
        View::assign('_gouplists', $gouplists);
        View::assign('_PlusAll', $PlusAll);
        View::assign('nav', array(
            array('title'=>'系统', 'url'=>''),
            array('title'=>'用户组设定', 'url'=>''),
            array('title'=>'更改用户组', 'url'=>''),
        ));
        View::assign('rank', $rank);
        return View::fetch();
    }

    public function sys_admin_user()
    {
        $rank = \request()->param('rank');
        if(\request()->isPost()) {
            $data = AdminModel::where(['usertype'=>$rank])->select()->toArray();
            $count = AdminModel::where(['usertype'=>$rank])->count();
            foreach ($data as $k=>$v){
                if(!empty($v['typeid'])){
                    $ArctypeInfo = ArctypeModel::where(['id'=>$v['typeid']])->find()->toArray();
                    $v['typename'] = $ArctypeInfo['typename'];
                }else{
                    $v['typename'] = "";
                }
                $data[$k] = $v;
            }
            return json(['code' => 0, 'count'=>$count, 'data'=> $data]);

        }
        return view('', [
            'rank' => $rank
        ]);
    }

    public function sys_group_add()
    {
        if(Request::isPost()){
            $param = Request::param('');

            $AllPurviews = '';
            if(is_array($param['purviews']))
            {
                foreach($param['purviews'] as $pur)
                {
                    $AllPurviews = $pur.' ';
                }
                $AllPurviews = trim($AllPurviews);
            }

            Admintype::insert(array(
                'rank'     => $param['rankid'],
                'typename' => $param['groupname'],
                'system'   => 0,
                'purviews' => $AllPurviews,
            ));

            return $this->success('提交成功', (string)url('index'));
        }

        $gouplists = file('inc/grouplist.txt');
        View::assign('_gouplists', $gouplists);
        $row = AdmintypeModel::where("")->select();
        View::assign('_row', $row);
        $PlusAll = Plus::where("")->select();
        View::assign('_PlusAll', $PlusAll);

        View::assign('nav', array(
            array('title'=>'系统', 'url'=>''),
            array('title'=>'用户组设定', 'url'=>''),
            array('title'=>'增加用户组', 'url'=>''),
        ));
        return View::fetch();
    }

    public function sys_group_delete()
    {
        if(Request::isGet()){
            $param = Request::param('');

            $state = Admintype::where("rank=".$param['rank'])->delete();
            return $this->success('删除成功', (string)url('index'));
        }

    }



}

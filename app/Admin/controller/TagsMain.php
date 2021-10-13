<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use think\Request;
use think\facade\View;
use app\Admin\model\Flink as FlinkModel;

/**
 * [TAG 标签管理]
 * Class TagsMain
 * @package app\Admin\controller
 */
class TagsMain extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $data = FlinkModel::where(array())->select()->toArray();
        foreach ($data as $k=>$v){
            if(!empty($v['logo'])){
                $v['logo'] = '<img src="'.$v['logo'].'" width="40" height="40" />';
            }else{
                $v['logo'] = '无图标';
            }
            $data[$k] = $v;
        }
        View::assign('_data', $data);
        return View::fetch();

    }


    public function create()
    {

    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
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

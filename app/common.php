<?php
// 应用公共文件
use app\Admin\model\Admintype as AdmintypeModel;

function GetUserType($trank)
{
    $adminTypeAll =  AdmintypeModel::where(array())->select();
    $adminRanks = array();
    foreach ($adminTypeAll as $k => $v){
        $adminRanks[$v->rank] = $v->typename;
    }
    if(isset($adminRanks[$trank])) return $adminRanks[$trank];
    else return "错误类型";
}
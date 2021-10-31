<?php
declare (strict_types = 1);

namespace app\Admin\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Sysconfig extends Model
{

    public static function sele()
    {
        $data = self::where("")->select();
        $DataAll = array();
        foreach ($data as $vo){
            $DataAll[$vo['varname']] = $vo['value'];
        }
        return $DataAll;
    }
}

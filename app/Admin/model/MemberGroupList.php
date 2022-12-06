<?php
namespace app\Admin\model;

use think\Model;

class MemberGroupList extends Model{

    public static function tree()
    {
        $data = self::where(['status'=>0, 'pid'=>0])->select();
        foreach ($data as $key => $value){
            $data[$key]['child'] = self::where(['status'=>0,'pid'=>$value['id']])->select();
        }
        return $data;
    }

}
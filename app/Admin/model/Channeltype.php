<?php
declare (strict_types = 1);

namespace app\admin\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Channeltype extends Model
{

    public static function tableName(){
        return self::getTable();
    }
}

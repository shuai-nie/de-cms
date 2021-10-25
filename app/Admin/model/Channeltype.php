<?php
declare (strict_types = 1);

namespace app\Admin\model;

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

<?php
declare (strict_types = 1);

namespace app\Admin\model;

use think\facade\Db;
use think\Model;

/**
 * @mixin \think\Model
 */
class Tagindex extends Model
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'addtime';

    // 字段加1
    public static function TagindexInc($map, $field, $step=1){
        return Db::name((new Tagindex())->getName())->where($map)->inc($field, $step)->update();
    }

    // 字段减1
    public static function TagindexDec($map, $field, $step=1){
        return Db::name((new Tagindex())->getName())->where($map)->dec($field, $step)->update();
    }

}

<?php
declare (strict_types = 1);

namespace app\Admin\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Flink extends Model
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'dtime';
}

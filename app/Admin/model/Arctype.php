<?php
declare (strict_types = 1);

namespace app\admin\model;

use think\Model;
use app\admin\model\Channeltype;



/**
 * @mixin \think\Model
 */
class Arctype extends Model
{
    public function profile()
    {
        return $this->hasMany(Channeltype::class, 'id', 'channeltype' );
    }

}

<?php
declare (strict_types = 1);

namespace app\Admin\model;

use think\Model;
use app\Admin\model\Channeltype;



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

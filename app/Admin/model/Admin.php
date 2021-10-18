<?php
declare (strict_types = 1);

namespace app\Admin\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Admin extends Model
{
    public function profile()
    {
        return $this->has(Admintype::class, 'rank', 'usertype');
    }

    public function profile2()
    {
        return $this->hasMany(Arctype::class, 'id', 'typeid');
    }
}

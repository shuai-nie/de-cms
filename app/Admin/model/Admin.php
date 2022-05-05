<?php
declare (strict_types = 1);

namespace app\Admin\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Admin extends Model
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'logintime';

    public static function onBeforeInsert($data)
    {
        $data['login'] = request()->ip();
    }

    public static function onBeforeUpdate($data)
    {
        $data['login'] = request()->ip();
    }

    public function profile()
    {
        return $this->has(Admintype::class, 'rank', 'usertype');
    }

    public function profile2()
    {
        return $this->hasMany(Arctype::class, 'id', 'typeid');
    }
}

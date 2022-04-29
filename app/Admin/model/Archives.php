<?php
declare (strict_types = 1);

namespace app\Admin\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Archives extends Model
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'senddate';

    // $query = "SELECT ch.typename AS channelname,ar.membername AS rankname,arc.*
    //            FROM `#@__archives` arc
    //            LEFT JOIN `#@__channeltype` ch ON ch.id=arc.channel
    //            LEFT JOIN `#@__arcrank` ar ON ar.rank=arc.arcrank WHERE arc.id='$aid' ";

    public function profile()
    {
        return $this->hasMany(Channeltype::class, 'id', 'channel' )->hasMany(Arcrank::class, 'rank', 'arcrank');
    }
}

<?php
declare (strict_types = 1);

namespace app\Admin\model;


use think\facade\Db;
use think\Model;

class MemberGuestbook extends Model{

    public static function guestbook_member()
    {

        $data =  Db::name((new self())->getName())->alias('g') ->leftJoin(Member::getTable()." m ", "g.mid=m.mid ")->field("g.*,m.userid")->order('g.aid desc')->paginate();
        return $data;

    }


}
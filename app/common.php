<?php
// 应用公共文件
use app\Admin\model\Admintype as AdmintypeModel;

function GetUserType($trank)
{
    $adminTypeAll =  AdmintypeModel::where(array())->select();
    $adminRanks = array();
    foreach ($adminTypeAll as $k => $v){
        $adminRanks[$v->rank] = $v->typename;
    }
    if(isset($adminRanks[$trank])) return $adminRanks[$trank];
    else return "错误类型";
}

function GetChannel($c)
{
    if($c==""||$c==0) return "所有频道";
    else return $c;
}

function GetSta($sta,$id)
{
    if($sta==1)
    {
        return ($id!=-1 ? "启用  &gt; <a href='mychannel_edit.php?dopost=hide&id=$id'><u>禁用</u></a>" : "固定项目");
    }
    else
    {
        return "禁用 &gt; <a href='mychannel_edit.php?dopost=show&id=$id'><u>启用</u></a>";
    }
}

function IsSystem($s)
{
    return $s==1 ? "系统" : "自动";
}


function CRank($n, $rank){
    $groupRanks = array();
//    $n = "plus_".$n;
    $groupSet = AdmintypeModel::whereRaw("CONCAT(`rank`) = $rank")->find();
    $groupRanks = explode(' ', $groupSet['purviews']);
    return in_array($n, $groupRanks) ? 'checked' : '';
}

/**
 *  获取选项列表
 *
 * @access    public
 * @param     string  $selid  选择ID
 * @param     string  $userCatalog  用户类目
 * @param     string  $channeltype  频道类型
 * @return    string
 */
function GetOptionList($selid=0, $userCatalog=0, $channeltype=0)
{
    global $OptionArrayList, $channels, $dsql, $cfg_admin_channel, $admin_catalogs;

//    $dsql->SetQuery("SELECT id,typename FROM `#@__channeltype` ");
//    $dsql->Execute('dd');
//    $channels = Array();
//    while($row = $dsql->GetObject('dd'))

    $ChanneltypeAll = \app\admin\model\Channeltype::where("")->select();
    $channels = Array();
    foreach ($ChanneltypeAll as $k => $v){
        $channels[$v->id] = $v->typename;
    }

    $OptionArrayList = '';

    //当前选中的栏目
    if($selid > 0)
    {
        //$row = $dsql->GetOne("SELECT id,typename,ispart,channeltype FROM `#@__arctype` WHERE id='$selid'");
        $row  = \app\admin\model\Arctype::where(['id'=>$selid])->find();
        if($row['ispart']==1) $OptionArrayList .= "<option value='".$row['id']."' class='option1' selected='selected'>".$row['typename']."(封面频道)</option>\r\n";
        else $OptionArrayList .= "<option value='".$row['id']."' selected='selected'>".$row['typename']."</option>\r\n";
    }

    //是否限定用户管理的栏目
    if( $cfg_admin_channel=='array' )
    {
        if(count($admin_catalogs)==0){
            $query = "SELECT id,typename,ispart,channeltype FROM `#@__arctype` WHERE 1=2 ";
        }else{
            $admin_catalog = join(',', $admin_catalogs);
//            $dsql->SetQuery("SELECT reid FROM `#@__arctype` WHERE id IN($admin_catalog) GROUP BY reid ");
//            $dsql->Execute('qq');
            $qq = \app\admin\model\Arctype::where(" id IN($admin_catalog) ")->order('reid')->select();
            $topidstr = '';
//            while($row = $dsql->GetObject('qq'))
//            {
//                if($row->reid==0) continue;
//                $topidstr .= ($topidstr=='' ? $row->reid : ','.$row->reid);
//            }
            foreach ($qq as $k=> $v){
                if($v->reid==0) continue;
                $topidstr .= ($topidstr=='' ? $v->reid : ','.$v->reid);
            }

            $admin_catalog .= ','.$topidstr;
            $admin_catalogs = explode(',', $admin_catalog);
            $admin_catalogs = array_unique($admin_catalogs);
            $admin_catalog = join(',', $admin_catalogs);
            $admin_catalog = preg_replace("#,$#", '', $admin_catalog);
            $query = "SELECT id,typename,ispart,channeltype FROM `#@__arctype` WHERE id IN($admin_catalog) AND reid=0 AND ispart<>2 ";
        }
    }else{
        $query = "SELECT id,typename,ispart,channeltype FROM `#@__arctype` WHERE ispart<>2 AND reid=0 ORDER BY sortrank ASC ";
    }

    $dsql->SetQuery($query);
    $dsql->Execute('cc');

    while($row=$dsql->GetObject('cc'))
    {
        $sonCats = '';
        LogicGetOptionArray($row->id, '─', $channeltype, $dsql, $sonCats);
        if($sonCats != '')
        {
            if($row->ispart==1) $OptionArrayList .= "<option value='".$row->id."' class='option1'>".$row->typename."(封面频道)</option>\r\n";
            else if($row->ispart==2) $OptionArrayList .= '';
            else if( empty($channeltype) && $row->ispart != 0 ) $OptionArrayList .= "<option value='".$row->id."' class='option2'>".$row->typename."(".$channels[$row->channeltype].")</option>\r\n";
            else $OptionArrayList .= "<option value='".$row->id."' class='option3'>".$row->typename."</option>\r\n";
            $OptionArrayList .= $sonCats;
        }
        else
        {
            if($row->ispart==0 && (!empty($channeltype) && $row->channeltype == $channeltype) )
            {
                $OptionArrayList .= "<option value='".$row->id."' class='option3'>".$row->typename."</option>\r\n";
            } else if($row->ispart==0 && empty($channeltype) )
            {
                // 专题
                $OptionArrayList .= "<option value='".$row->id."' class='option3'>".$row->typename."</option>\r\n";
            }
        }
    }
    return $OptionArrayList;

}
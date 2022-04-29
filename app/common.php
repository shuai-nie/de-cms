<?php
// 应用公共文件
use app\Admin\model\Admintype as AdmintypeModel;
use app\Admin\model\Arctiny;
use think\facade\Session;

function getUser()
{
    return Session::get(config('app.uid_key'));
}

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
        return ($id!=-1 ? "启用  &gt; <a href='".url('mychannel_edit_hide', ['id'=>$id])."'><u>禁用</u></a>" : "固定项目");
    }
    else
    {
        return "禁用 &gt; <a href='".url('mychannel_edit_show', ['id'=>$id])."'><u>启用</u></a>";
    }
}

function IsSystem($s)
{
    return $s==1 ? "系统" : "自动";
}


function CRank($n, $rank){
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

    $ChanneltypeAll = \app\Admin\model\Channeltype::where("")->select();
    $channels = Array();
    foreach ($ChanneltypeAll as $k => $v){
        $channels[$v->id] = $v->typename;
    }

    $OptionArrayList = '';

    //当前选中的栏目
    if($selid > 0)
    {
        //$row = $dsql->GetOne("SELECT id,typename,ispart,channeltype FROM `#@__arctype` WHERE id='$selid'");
        $row  = \app\Admin\model\Arctype::where(['id'=>$selid])->find();
        if($row['ispart']==1) $OptionArrayList .= "<option value='".$row['id']."' class='option1' selected='selected'>".$row['typename']."(封面频道)</option>\r\n";
        else $OptionArrayList .= "<option value='".$row['id']."' selected='selected'>".$row['typename']."</option>\r\n";
    }
    $ArctypeGetName = \app\Admin\model\Arctype::getTable();

    //是否限定用户管理的栏目
    if( $cfg_admin_channel=='array' )
    {
        if(count($admin_catalogs)==0){
            $query = "SELECT id,typename,ispart,channeltype FROM $ArctypeGetName WHERE 1=2 ";
        }else{
            $admin_catalog = join(',', $admin_catalogs);
//            $dsql->SetQuery("SELECT reid FROM `#@__arctype` WHERE id IN($admin_catalog) GROUP BY reid ");
//            $dsql->Execute('qq');
            $qq = \app\Admin\model\Arctype::where(" id IN($admin_catalog) ")->order('reid')->select();
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

            $query = "SELECT id,typename,ispart,channeltype FROM $ArctypeGetName WHERE id IN($admin_catalog) AND reid=0 AND ispart<>2 ";

        }
    }else{
        $query = "SELECT id,typename,ispart,channeltype FROM $ArctypeGetName WHERE ispart<>2 AND reid=0 ORDER BY sortrank ASC ";
    }

    $row = \think\facade\Db::query($query);
    foreach ($row as $k => $v){
        $sonCats = '';
        LogicGetOptionArray($v->id, '─', $channeltype, $dsql, $sonCats);
        if($sonCats != '')
        {
            if($v->ispart==1) $OptionArrayList .= "<option value='".$v->id."' class='option1'>".$v->typename."(封面频道)</option>\r\n";
            else if($v->ispart==2) $OptionArrayList .= '';
            else if( empty($channeltype) && $v->ispart != 0 ) $OptionArrayList .= "<option value='".$v->id."' class='option2'>".$v->typename."(".$channels[$v->channeltype].")</option>\r\n";
            else $OptionArrayList .= "<option value='".$v->id."' class='option3'>".$v->typename."</option>\r\n";
            $OptionArrayList .= $sonCats;
        }else{
            if($v->ispart==0 && (!empty($channeltype) && $v->channeltype == $channeltype) ){
                $OptionArrayList .= "<option value='".$v->id."' class='option3'>".$v->typename."</option>\r\n";
            } else if($v->ispart==0 && empty($channeltype) ){
                // 专题
                $OptionArrayList .= "<option value='".$v->id."' class='option3'>".$v->typename."</option>\r\n";
            }
        }
    }
    return $OptionArrayList;
}

function LogicGetOptionArray($id,$step,$channeltype, &$dsql, &$sonCats)
{
    global $OptionArrayList, $channels, $cfg_admin_channel, $admin_catalogs;
    //$dsql->SetQuery("Select id,typename,ispart,channeltype From `#@__arctype` where reid='".$id."' And ispart<>2 order by sortrank asc");
    $row = \app\Admin\model\Arctype::where("reid='".$id."' And ispart<>2 ")->order("sortrank asc")->select();

    foreach ($row as $k => $v){
        if($cfg_admin_channel != 'all' && !in_array($v->id, $admin_catalogs))
        {
            continue;
        }
        if($v->channeltype==$channeltype && $v->ispart==1)
        {
            $sonCats .= "<option value='".$v->id."' class='option1'>$step".$v->typename."</option>\r\n";
        }
        else if( ($v->channeltype==$channeltype && $v->ispart==0) || empty($channeltype) )
        {
            $sonCats .= "<option value='".$v->id."' class='option3'>$step".$v->typename."</option>\r\n";
        }
        LogicGetOptionArray($v->id,$step.'─',$channeltype,$dsql, $sonCats);

    }
}


/**
 *  获得某文档的所有tag
 *
 * @param     int     $aid  文档id
 * @return    string
 */
if ( ! function_exists('GetTags'))
{
    function GetTags($aid)
    {
        global $dsql;
        $tags = '';
        $query = "SELECT tag FROM `#@__taglist` WHERE aid='$aid' ";
        $row = \app\Admin\model\Taglist::where("aid=".$aid)->select();

        foreach ($row as $k=>$v){
            $tags .= ($tags=='' ? $v['tag'] : ','.$v['tag']);
        }
        return $tags;
    }
}

/**
 * 从普通时间转换为Linux时间截
 *
 * @param     string   $dtime  普通时间
 * @return    string
 */
if ( ! function_exists('GetMkTime'))
{
    function GetMkTime($dtime)
    {
        if(!preg_match("/[^0-9]/", $dtime)){
            return $dtime;
        }
        $dtime = trim($dtime);
        $dt = Array(1970, 1, 1, 0, 0, 0);
        $dtime = preg_replace("/[\r\n\t]|日|秒/", " ", $dtime);
        $dtime = str_replace("年", "-", $dtime);
        $dtime = str_replace("月", "-", $dtime);
        $dtime = str_replace("时", ":", $dtime);
        $dtime = str_replace("分", ":", $dtime);
        $dtime = trim(preg_replace("/[ ]{1,}/", " ", $dtime));
        $ds = explode(" ", $dtime);
        $ymd = explode("-", $ds[0]);
        if(!isset($ymd[1])){
            $ymd = explode(".", $ds[0]);
        }
        if(isset($ymd[0])){
            $dt[0] = $ymd[0];
        }
        if(isset($ymd[1])) $dt[1] = $ymd[1];
        if(isset($ymd[2])) $dt[2] = $ymd[2];
        if(strlen($dt[0])==2) $dt[0] = '20'.$dt[0];
        if(isset($ds[1])){
            $hms = explode(":", $ds[1]);
            if(isset($hms[0])) $dt[3] = $hms[0];
            if(isset($hms[1])) $dt[4] = $hms[1];
            if(isset($hms[2])) $dt[5] = $hms[2];
        }
        foreach($dt as $k=>$v){
            $v = preg_replace("/^0{1,}/", '', trim($v));
            if($v==''){
                $dt[$k] = 0;
            }
        }
        $mt = mktime($dt[3], $dt[4], $dt[5], $dt[1], $dt[2], $dt[0]);
        if(!empty($mt)){
            return $mt;
        }else{
            return time();
        }
    }
}

/**
 *  增加天数
 *
 * @param     int  $ntime  当前时间
 * @param     int  $aday   增加天数
 * @return    int
 */
if ( ! function_exists('AddDay'))
{
    function AddDay($ntime, $aday){
        $dayst = 3600 * 24;
        $oktime = $ntime + ($aday * $dayst);
        return $oktime;
    }
}


function GetSta3($sta)
{
    if($sta==1) return '内页';
    if($sta==2) return '首页';
    else return '未审核';
}

/**
 *  获取一个微表的索引键
 *
 * @access    public
 * @param     string  $arcrank  权限值
 * @param     int  $typeid  栏目ID
 * @param     int  $sortrank  排序ID
 * @param     int  $channelid  模型ID
 * @param     int  $senddate  发布日期
 * @param     int  $mid  会员ID
 * @return    int
 */
if ( ! function_exists('GetIndexKey'))
{
    function GetIndexKey($arcrank, $typeid, $sortrank=0, $channelid=1, $senddate=0, $mid=1)
    {
        if(empty($typeid2)) $typeid2 = 0;
        if(empty($senddate)) $senddate = time();
        if(empty($sortrank)) $sortrank = $senddate;
        $typeid2 = intval($typeid2);
        $senddate = intval($senddate);
        $aid = Arctiny::insert(array(
            'arcrank' => $arcrank,
            'typeid' => $typeid,
            'typeid2' => $typeid2,
            'channel' => $channelid,
            'senddate' => $senddate,
            //'sortrant' => $sortrank,
            'mid' => $mid
        ), true);
        return $aid;
    }
}

/**
 * 获得字段创建信息
 *
 * @access    public
 * @param     string  $dtype  字段类型
 * @param     string  $fieldname  字段名称
 * @param     string  $dfvalue  默认值
 * @param     string  $mxlen  最大字符长度
 * @return    array
 */
function GetFieldMake($dtype, $fieldname, $dfvalue, $mxlen)
{
    $fields = array();
    if($dtype == "int" || $dtype == "datetime"){
        if($dfvalue == "" || preg_match("#[^0-9-]#", $dfvalue))
        {
            $dfvalue = 0;
        }
        $fields[0] = " `$fieldname` int(11) NOT NULL default '$dfvalue';";
        $fields[1] = "int(11)";
    }else if($dtype == "stepselect"){
        if($dfvalue == "" || preg_match("#[^0-9\.-]#", $dfvalue)){
            $dfvalue = 0;
        }
        $fields[0] = " `$fieldname` char(20) NOT NULL default '$dfvalue';";
        $fields[1] = "char(20)";
    }else if($dtype == "float"){
        if($dfvalue == "" || preg_match("#[^0-9\.-]#", $dfvalue)){
            $dfvalue = 0;
        }
        $fields[0] = " `$fieldname` float NOT NULL default '$dfvalue';";
        $fields[1] = "float";
    }else if($dtype == "img" || $dtype == "media" || $dtype == "addon" || $dtype == "imgfile"){
        if(empty($dfvalue)) $dfvalue = '';
        if($mxlen=="") $mxlen = 200;
        if($mxlen > 255) $mxlen = 100;

        $fields[0] = " `$fieldname` varchar($mxlen) NOT NULL default '$dfvalue';";
        $fields[1] = "varchar($mxlen)";
    }else if($dtype == "multitext" || $dtype == "htmltext"){
        $fields[0] = " `$fieldname` mediumtext;";
        $fields[1] = "mediumtext";
    }else if($dtype=="textdata"){
        if(empty($dfvalue)) $dfvalue = '';

        $fields[0] = " `$fieldname` varchar(100) NOT NULL default '';";
        $fields[1] = "varchar(100)";
    }else if($dtype=="textchar"){
        if(empty($dfvalue)) $dfvalue = '';

        $fields[0] = " `$fieldname` char(100) NOT NULL default '$dfvalue';";
        $fields[1] = "char(100)";
    }else if($dtype=="checkbox"){
        $dfvalue = str_replace(',',"','",$dfvalue);
        $dfvalue = "'".$dfvalue."'";
        $fields[0] = " `$fieldname` SET($dfvalue) NULL;";
        $fields[1] = "SET($dfvalue)";
    }else if($dtype=="select" || $dtype=="radio"){
        $dfvalue = str_replace(',', "','", $dfvalue);
        $dfvalue = "'".$dfvalue."'";
        $fields[0] = " `$fieldname` enum($dfvalue) NULL;";
        $fields[1] = "enum($dfvalue)";
    }else{
        if(empty($dfvalue)){
            $dfvalue = '';
        }
        if(empty($mxlen)){
            $mxlen = 100;
        }
        if($mxlen > 255){
            $mxlen = 250;
        }
        $fields[0] = " `$fieldname` varchar($mxlen) NOT NULL default '$dfvalue';";
        $fields[1] = "varchar($mxlen)";
    }
    return $fields;
}

/**
 * 获取模型列表字段
 *
 * @access    public
 * @param     object  $dtp  模板引擎
 * @param     string  $oksetting  设置
 * @return    array
 */
function GetAddFieldList(&$dtp, &$oksetting)
{
    $oklist = '';
    $dtp->SetNameSpace("field","<",">");
    $dtp->LoadSource($oksetting);
    if(is_array($dtp->CTags))
    {
        foreach($dtp->CTags as $tagid=>$ctag)
        {
            if($ctag->GetAtt('islist')==1)
            {
                $oklist .= ($oklist=='' ? strtolower($ctag->GetName()) : ','.strtolower($ctag->GetName()) );
            }
        }
    }
    return $oklist;
}

function hashNameTime(){
    return time();
}

function IsHtmlArchives($ismake){
    if($ismake==1){
        return "已生成";
    }else if($ismake==-1){
        return "仅动态";
    }else{
        return "<font color='red'>未生成</font>";
    }
}

function IsBdLink($bd){
    if($bd==1){
        return "已提交";
    }else{
        return "<font color='red'>未提交</font>";
    }
}

function GetRankName($arcrank)
{
//    global $arcArray, $dsql;
//    if(!is_array($arcArray))
//    {
        //$dsql->SetQuery("SELECT * FROM `#@__arcrank` ");
        $data = \app\Admin\model\Arcrank::where(['rank'=>$arcrank])->find();
        return $data->membername;
//        $dsql->Execute();
//        while($row = $dsql->GetObject())
//        {
//            $arcArray[$row->rank]=$row->membername;
//        }
//    }
//    if(isset($arcArray[$arcrank]))
//    {
//        return $arcArray[$arcrank];
//    }
//    else
//    {
//        return "不限";
//    }
}



function getRealIP()
{
    $forwarded = request()->header("x-forwarded-for");
    if ($forwarded) {
        $ip = explode(',', $forwarded)[0];
    } else {
        $ip = request()->ip();
    }
    return $ip;
}

function fieldtype($str) {
    $arr = [
        'text' => '单行文本(varchar)',
        'textchar' => '单行文本(char)',
        'multitext' => '多行文本',
        'htmltext' => 'HTML文本',
        'textdata' => '文本保存HTML数据',
        'int' => '整数类型',
        'float' => '小数类型',
        'datetime' => '时间类型',
        'img' => '图片',
        'imgfile' => '图片(无格式)',
        'media' => '多媒体文件',
        'addon' => '附件类型',
        'select' => '使用select下拉框',
        'radio' => '使用radio选项卡',
        'checkbox' => 'checkbox多选框',
        'stepselect' => '联动类型',
    ];
    return isset($arr[$str]) ? $arr[$str] : '系统专用类型';
}

function autofield($str) {
    if ($str == '' || $str == 0){
        return  '固化字段';
    } else {
        return  '自动表单';
    }
}

function success($msg = '', $code = '200', $data = []): \think\response\Json
{
    return json(['code' => 200, 'msg' => $msg, 'data' => $data]);
}

function error($msg = '', $code = '400', $data = []): \think\response\Json
{
    return json(['code' => $code, 'msg' => $msg, 'data' => $data]);
}
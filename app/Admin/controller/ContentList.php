<?php
declare (strict_types=1);

namespace app\Admin\controller;

use think\facade\Db;
use think\facade\Request;
use think\facade\Session;
use think\facade\View;
use think\facade\Config;
use app\Admin\model\Feedback;
use app\Admin\model\Taglist;
use app\Admin\model\Admin;
use app\Admin\model\Arctiny;
use app\Admin\model\Arctype;
use app\Admin\model\Arcatt;
use app\Admin\model\Archives;
use app\Admin\model\Arcrank;
use app\Admin\model\Channeltype;
use app\Admin\model\Uploads;
use app\Admin\model\MemberStow;
use app\Admin\model\Erradd;

/**
 * [所有档案列表]
 * Class ContentList
 * @package app\Admin\controller
 */
class ContentList extends Base
{
    public function initialize()
    {
        parent::initialize();
        View::assign('_nav_this', 'ContentList_index');
        View::assign('nav', array(
            array('title' => '核心', 'url' => ''),
            array('title' => '所有档案列表', 'url' => ''),
        ));
        View::assign('_nav_itemed', 'hexing');
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $length  = 20;
        $map     = array();
        $arcrank = Request::param('arcrank');
        if (!empty($arcrank)) {
            View::assign('nav', array(
                array('title' => '核心', 'url' => ''),
                array('title' => '等审核的文档', 'url' => ''),
            ));
            View::assign('_nav_this', 'ContentList_index2');
            $map['arcrank'] = $arcrank;
        }
        $data = Archives::alias('A')
            ->leftjoin(Arctype::getTable() . " B ", "B.id=A.typeid")
            ->leftjoin(Admin::getTable() . " C ", "C.id=A.mid")
            ->field('A.*,B.typename as ctypename,C.userid')
            ->order('id desc')->paginate($length);
        View::assign('_data', $data);
        View::assign('arcrank', $arcrank);
        return View::fetch();
    }

    public function article_add()
    {
        if (Request::isPost()) {
            $param    = Request::param('');
            $pubdate  = GetMkTime($param['pubdate']);
            $sortrank = AddDay($pubdate, $param['sortup']);
            $senddate = time();
            $arcID    = GetIndexKey($param['arcrank'], $param['typeid'], $sortrank, $param['channelid'], $senddate, 1);
            $flag     = isset($param['flags']) ? join(',', $param['flags']) : '';
            $ismake   = $param['ishtml'] == 0 ? -1 : 0;

            $state = Archives::insert(array(
                'id'          => $arcID,
                'typeid'      => $param['typeid'],
                'typeid2'     => $param['typeid2'],
                'sortrank'    => $sortrank,
                'flag'        => $flag,
                'ismake'      => $ismake,
                'channel'     => $param['channelid'],
                'arcrank'     => $param['arcrank'],
                'click'       => $param['click'],
                'money'       => $param['money'],
                'title'       => $param['title'],
                'shorttitle'  => $param['shorttitle'],
                'color'       => $param['color'],
                'writer'      => $param['writer'],
                'source'      => $param['source'],
                'mid'         => 1,
                'voteid'      => 0,//投票
                'notpost'     => $param['notpost'],
                'description' => $param['description'],
                'keywords'    => $param['keywords'],
                'filename'    => $param['filename'],
                'dutyadmin'   => 1,
                'weight'      => $param['weight'],
                'senddate'    => $senddate,
            ));


            $cts      = Channeltype::where("id=" . $param['channelid'])->find();
            $addTable = trim($cts['addtable']);
            if (empty($addTable)) {
                Archives::where("id=" . $arcID)->delete();
                Arctiny::where("id=" . $arcID)->delete();
            }
            $inadd_f = $inadd_v = '';

            $userid  = Request::ip();
            $templet = empty($param['templet']) ? '' : $param['templet'];
            $query   = "INSERT INTO `{$addTable}`(aid,typeid,redirecturl,templet,userip,body{$inadd_f}) Values('$arcID','{$param['typeid']}','{$param['redirecturl']}','$templet','{$userid}','{$param['body']}'{$inadd_v})";
            Db::query($query);

            if ($state !== false) {
                return $this->success("提交成功", (string)url('index'));
            }
            return $this->error("提交失败");

            exit();
        }
        $channelid = Request::param('channelid');
        $cid       = Request::param('cid');
        $channelid = empty($channelid) ? 0 : intval($channelid);
        if ($cid > 0 && $channelid == 0) {
            $row       = Channeltype::where('id=' . $cid)->find();
            $channelid = $row['channeltype'];
        } else {
            if ($channelid == 0) {
                $channelid = 1;
            }
        }
        $cid    = empty($cid) ? 0 : intval($cid);
        $geturl = Request::param('geturl');
        if (empty($geturl)) $geturl = '';
        $ArcattAll           = Arcatt::where("")->order('sortid asc')->select();
        $ArchivesCount       = Archives::where("")->count();
        $cfg_need_typeid2    = Config::get('app.cfg_need_typeid2');
        $cfg_remote_site     = Config::get('app.cfg_remote_site');
        $cfg_arc_autokeyword = Config::get('app.cfg_arc_autokeyword');
        $cfg_rm_remote       = Config::get('app.cfg_rm_remote');
        $cfg_arc_dellink     = Config::get('app.cfg_arc_dellink');
        $cfg_arc_autopic     = Config::get('app.cfg_arc_autopic');
        $photo_markup        = Config::get('app.photo_markup');
        $cfg_arcautosp       = Config::get('app.cfg_arcautosp');
        $cfg_arcautosp_size  = Config::get('app.cfg_arcautosp_size');
        $cfg_feedback_forbid = Config::get('app.cfg_feedback_forbid');
        $cfg_arc_click       = Config::get('app.cfg_arc_click');
        $ArcrankAll          = Arcrank::where("")->select();
        $ArctypeAll          = Arctype::where("")->select();

        View::assign('channelid', $channelid);
        View::assign('cid', $cid);
        View::assign('geturl', $geturl);
        View::assign('title', '');
        View::assign('ArcattAll', $ArcattAll);
        View::assign('nowtime', time());
        View::assign('ArctypeAll', $ArctypeAll);
        View::assign('ArcrankAll', $ArcrankAll);
        View::assign('cfg_arc_click', $cfg_arc_click);
        View::assign('cfg_feedback_forbid', $cfg_feedback_forbid);
        View::assign('cfg_arcautosp_size', $cfg_arcautosp_size);
        View::assign('cfg_arcautosp', $cfg_arcautosp);
        View::assign('photo_markup', $photo_markup);
        View::assign('ArchivesCount', $ArchivesCount);
        View::assign('source', '');
        View::assign('writer', '');
        View::assign('cfg_need_typeid2', $cfg_need_typeid2);
        View::assign('cfg_remote_site', $cfg_remote_site);
        View::assign('cfg_arc_autokeyword', $cfg_arc_autokeyword);
        View::assign('cfg_rm_remote', $cfg_rm_remote);
        View::assign('cfg_arc_dellink', $cfg_arc_dellink);
        View::assign('cfg_arc_autopic', $cfg_arc_autopic);
        View::assign('keywords', '');
        View::assign('description', '');
        return View::fetch();
    }

    public function article_edit()
    {
        if (Request::isPost()) {
            $param = Request::param('');
            if (isset($param['typeid2'])) {
                $typeid2 = $param['typeid2'];
            } else {
                $typeid2 = 0;
            }

            $pubdate  = GetMkTime($param['pubdate']);
            $sortrank = AddDay($pubdate, $param['sortup']);
            $flag     = isset($flags) ? join(',', $flags) : '';
            $ismake   = $param['ishtml'] == 0 ? -1 : 0;

            if (empty($param['ddisremote'])) {
                $ddisremote = 0;
            } else {
                $ddisremote = 1;
            }

            // 处理缩略图
            //$litpic = GetDDImage('none', $param['picname'], $ddisremote);
            $litpic = '';
            //处理图片文档的自定义属性
            if ($litpic != '' && !preg_match("#p#", $flag)) {
                $flag = ($flag == '' ? 'p' : $flag . ',p');
            }
            if ($param['redirecturl'] != '' && !preg_match("#j#", $flag)) {
                $flag = ($flag == '' ? 'j' : $flag . ',j');
            }
            $param['ismake'] = $ismake == 0 ? -1 : 0;
            if (preg_match("#j#", $flag)) {
                $ismake = -1;
            }
            $arcrank = 0;


            $AdminUser = Session::get('AdminUser');
            $adminid   = $AdminUser->id;

            $state = Archives::update(array(
                'typeid'     => $param['typeid'],
                'typeid2'    => $typeid2, 'sortrank' => $sortrank, 'flag' => $flag, 'click' => $param['click'],
                'ismake'     => $ismake, 'arcrank' => $arcrank, 'money' => $param['money'], 'title' => $param['title'], 'color' => $param['color'],
                'writer'     => $param['writer'], 'source' => $param['source'], 'litpic' => $litpic, 'pubdate' => $pubdate, 'voteid' => $param['voteid'],
                'notpost'    => $param['notpost'], 'description' => $param['description'], 'keywords' => $param['keywords'],
                'shorttitle' => $param['shorttitle'], 'filename' => $param['filename'], 'dutyadmin' => $adminid, 'weight' => $param['weight'],
            ), array('id' => $param['id']));

            $cts = Channeltype::where("id", '=', $param['channelid'])->find();
            $addtable = trim($cts['addtable']);
            if ($addtable != '') {
                $useip   = Request::ip();
                $templet = empty($param['templet']) ? '' : $param['templet'];
                $query   = "UPDATE `$addtable` SET typeid='{$param['typeid']}',body='{$param['body']}',redirecturl='{$param['redirecturl']}',templet='$templet',userip='$useip' WHERE aid='{$param['id']}'";
                Db::query($query);
            }
            return $this->success('修改成功', (string)url('index') );
        }

        $channelid = Request::param('channelid');
        $cid       = Request::param('cid');
        $aid       = Request::param('aid');
        $geturl    = Request::param('geturl');
        $channelid = empty($channelid) ? 0 : intval($channelid);
        $cid       = empty($cid) ? 0 : intval($cid);
        if (empty($geturl)) $geturl = '';

        $ArcattAll = Arcatt::where("")->order('sortid asc')->select();

        $ArchivesCount = Archives::where("")->count();

        $arcRow = Archives::alias('A')
            ->leftJoin(Channeltype::getTable() . " B", "B.id=A.channel")
            ->leftJoin(Arcrank::getTable() . " C", "C.rank=A.arcrank")
            ->field('B.typename AS channelname,C.membername AS rankname,A.*')
            ->where("A.id=$aid")->find()->toArray();

        $cInfos   = Channeltype::where("id=" . $arcRow['channel'])->find();
        $addtable = $cInfos['addtable'];


        $addRow = Db::query("SELECT * FROM `$addtable` WHERE aid='$aid'");


        $trow             = Uploads::where("arcid =" . $addRow[0]['aid'])->select();
        $tags             = GetTags($aid);
        $user             = Session::get('AdminUser');
        $cfg_remote_site  = Config::get('app.cfg_remote_site');
        $cfg_need_typeid2 = Config::get('app.cfg_need_typeid2');
        $ArcrankAll       = Arcrank::where("")->select()->toArray();

        $ArctypeAll = Arctype::where("")->select();
        $channelid  = $arcRow['channel'];

        View::assign('ArctypeAll', $ArctypeAll);
        View::assign('arcRow', $arcRow);
        View::assign('addRow', $addRow[0]);
        View::assign('cfg_remote_site', $cfg_remote_site);
        View::assign('cfg_need_typeid2', $cfg_need_typeid2);
        View::assign('ArcattAll', $ArcattAll);
        View::assign('nowtime', time());
        View::assign('tags', $tags);
        View::assign('trow', $trow);
        View::assign('aid', $aid);
        View::assign('channelid', $channelid);
        View::assign('cid', $cid);
        View::assign('geturl', $geturl);
        View::assign('title', '');
        View::assign('ArcattAll', $ArcattAll);
        View::assign('ArchivesCount', $ArchivesCount);

        View::assign('nav', array(
            array('title' => '核心', 'url' => ''),
            array('title' => '所有档案列表', 'url' => ''),
            array('title' => '更改文章', 'url' => ''),
        ));
        return View::fetch();
    }

    public function article_delete()
    {
        if(Request::isPost()){
            $arr = Request::param('arr');
            $state = Archives::where("id in ($arr)")->delete();
            return json(['code'=>0,'msg'=>'删除成功']);


        }
    }

    /**
     * [档案回收箱]
     * @author Dave 178698695@qq.com
     * @return string
     */
    public function recycling()
    {
        $data = Archives::alias('arc')
            ->leftjoin(Arctype::getTable()." tp", "arc.typeid = tp.id")
            ->where("arc.arcrank = '-2'")->order('arc.id desc')->paginate();

        View::assign('_data', $data);
        return View::fetch('');
    }

    // 还原
    public function archives_do_return()
    {
        $aid = Request::param('aid');
        $qstr = Request::param('qstr');
        if( !empty($aid) && empty($qstr) ) $qstr = $aid;
        if($qstr ==''){
            return $this->error('参数无效');
        }
        $qstrs = explode("`", $qstr);
        foreach ($qstrs as $aid){
            Archives::update(['arcrank'=>'-1', 'ismake'=>0], ['id'=>$aid]);
            Arctiny::update(['arcrank'=>'-1'], ['id'=>$aid]);
        }
        return $this->success('还原成功', (string)url('recycling', ['cid'=>0]));
    }

    // 删除
    public function archives_do_del()
    {

        $qstr = Request::param('qstr');
        $aid = Request::param('aid');
        $recycle = Request::param('recycle');
        if(empty($fmdo)) $fmdo = '';
        $recycle = empty($recycle)? "" : $recycle;

        if( !empty($aid) && empty($qstr) ) $qstr = $aid;
        if ($qstr == '') {
            exit();
        }
        $qstrs = explode("`", $qstr);
        $okaids = Array();

        foreach($qstrs as $aid){
            if(!isset($okaids[$aid])){
                $this->DelArc($aid,"OK","",$recycle);
            }else{
                $okaids[$aid] = 1;
            }
        }
        ShowMsg("成功删除指定的文档！","recycling.php");
        exit();

    }
    /**
     *  删除文档信息
     *
     * @access    public
     * @param     string  $aid  文档ID
     * @param     string  $type  类型
     * @param     string  $onlyfile  删除数据库记录
     * @return    string
     */
    protected function DelArc($aid, $type='ON', $onlyfile=FALSE, $recycle=0)
    {
//        global $dsql,$cfg_cookie_encode,$cfg_multi_site,$cfg_medias_dir;
//        global $cuserLogin,$cfg_upload_switch,$cfg_delete,$cfg_basedir;
//        global $admin_catalogs, $cfg_admin_channel;
        $cfg_delete = 'Y';
        $cfg_admin_channel = 'all';
        /**********************/
        if($cfg_delete == 'N') $type = 'OK';
        if(empty($aid)) return ;
        $aid = preg_replace("#[^0-9]#i", '', $aid);
        $arctitle = $arcurl = '';
        if($recycle == 1) $whererecycle = "AND arcrank = '-2'";
        else $whererecycle = "";

        //查询表信息
        $query = "SELECT ch.maintable,ch.addtable,ch.nid,ch.issystem FROM ".Arctiny::getTable()." arc
                LEFT JOIN ".Arctype::getTable()." tp ON tp.id=arc.typeid
                LEFT JOIN ".Channeltype::getTable()." ch ON ch.id=arc.channel WHERE arc.id='$aid' ";
        // $row = $dsql->GetOne($query);
        $row = Db::query($query);
        $row = $row[0];

        $nid = $row['nid'];
        // '#@__archives'
        $maintable = (trim($row['maintable'])=='' ? Archives::getTable(): trim($row['maintable']));
        $addtable = trim($row['addtable']);
        $issystem = $row['issystem'];

        //查询档案信息
        if($issystem==-1){
            $arcQuery = "SELECT arc.*,tp.* from `$addtable` arc LEFT JOIN `#@__arctype` tp ON arc.typeid=tp.id WHERE arc.aid='$aid' ";
        }else{
            $arcQuery = "SELECT arc.*,tp.*,arc.id AS aid FROM `$maintable` arc LEFT JOIN `#@__arctype` tp ON arc.typeid=tp.id WHERE arc.id='$aid' ";
        }

        //$arcRow = $dsql->GetOne($arcQuery);
        $arcRow = Db::query($arcQuery);
        $arcRow = $arcRow[0];

        //检测权限
//        if(!TestPurview('a_Del,sys_ArcBatch'))
//        {
//            if(TestPurview('a_AccDel'))
//            {
//                if( !in_array($arcRow['typeid'], $admin_catalogs) && (count($admin_catalogs) != 0 || $cfg_admin_channel != 'all') )
//                {
//                    return FALSE;
//                }
//            }
//            else if(TestPurview('a_MyDel'))
//            {
//                if($arcRow['mid'] != $cuserLogin->getUserID())
//                {
//                    return FALSE;
//                }
//            }
//            else
//            {
//                return FALSE;
//            }
//        }

        //$issystem==-1 是单表模型，不使用回收站
        if($issystem == -1) $type = 'OK';
        if(!is_array($arcRow)) return FALSE;

        $cfg_basedir = app()->getRootPath();
        $cfg_upload_switch = 'Y';
        //$arctinyTable = Arctiny::getTable();

        /** 删除到回收站 **/
        if($cfg_delete == 'Y' && $type == 'ON')
        {
            Db::query("UPDATE `$maintable` SET arcrank='-2' WHERE id='$aid' ");
            //$dsql->ExecuteNoneQuery();
            //$dsql->ExecuteNoneQuery();
            Db::query("UPDATE ".Arctiny::getTable()." SET `arcrank` = '-2' WHERE id = '$aid'; ");
        }
        else
        {
            //删除数据库记录
            if(!$onlyfile)
            {
                $query = "Delete From ".Arctiny::getTable()." where id='$aid' $whererecycle";
                $state = Arctiny::hasWhere("id='$aid' $whererecycle")->delete();
                if($state){
                    /*
                    $dsql->ExecuteNoneQuery("Delete From `#@__feedback` where aid='$aid' ");
                    $dsql->ExecuteNoneQuery("Delete From `#@__taglist` where aid='$aid' ");
                    $dsql->ExecuteNoneQuery("Delete From `#@__erradd` where aid='$aid' ");
                    $dsql->ExecuteNoneQuery("Delete From `#@__member_stow` where aid='$aid' ");
                    */
                    Feedback::where(['aid'=>$aid])->delete();
                    MemberStow::where(['aid'=>$aid])->delete();
                    Taglist::where(['aid'=>$aid])->delete();
                    Erradd::where(['aid'=>$aid])->delete();


                    if($addtable != ''){
                        //$dsql->ExecuteNoneQuery("Delete From `$addtable` where aid='$aid'");
                        Db::query("Delete From `$addtable` where aid='$aid'");
                    }
                    if($issystem != -1)
                    {
                        //$dsql->ExecuteNoneQuery("Delete From `#@__archives` where id='$aid' $whererecycle");
                        Archives::where("id='$aid' $whererecycle")->delete();
                    }
                    //删除相关附件
                    if($cfg_upload_switch == 'Y')
                    {
//                        $dsql->Execute("me", "SELECT * FROM `#@__uploads` WHERE arcid = '$aid'");
//                        while($row = $dsql->GetArray('me'))
//                        {
//                            $addfile = $row['url'];
//                            $aid = $row['aid'];
//                            $dsql->ExecuteNoneQuery("Delete From `#@__uploads` where aid = '$aid' ");
//                            $upfile = $cfg_basedir.$addfile;
//                            if(@file_exists($upfile)) @unlink($upfile);
//                        }
                    }
                }
            }
            $cfg_cookie_encode = Config::get('app.cfg_cookie_encode');
            //删除文本数据
//            $filenameh = DEDEDATA."/textdata/".(ceil($aid/5000))."/{$aid}-".substr(md5($cfg_cookie_encode),0,16).".txt";
//            if(@is_file($filenameh)) @unlink($filenameh);

        }

        if(empty($arcRow['money'])) $arcRow['money'] = 0;
        if(empty($arcRow['ismake'])) $arcRow['ismake'] = 1;
        if(empty($arcRow['arcrank'])) $arcRow['arcrank'] = 0;
        if(empty($arcRow['filename'])) $arcRow['filename'] = '';

        //删除HTML
        if($arcRow['ismake']==-1 || $arcRow['arcrank']!=0 || $arcRow['typeid']==0 || $arcRow['money']>0){
            return TRUE;
        }

//        //强制转换非多站点模式，以便统一方式获得实际HTML文件
//        $GLOBALS['cfg_multi_site'] = 'N';
//        $arcurl = GetFileUrl($arcRow['aid'],$arcRow['typeid'],$arcRow['senddate'],$arcRow['title'],$arcRow['ismake'],
//            $arcRow['arcrank'],$arcRow['namerule'],$arcRow['typedir'],$arcRow['money'],$arcRow['filename']);
//        if(!preg_match("#\?#", $arcurl))
//        {
//            $htmlfile = GetTruePath().str_replace($GLOBALS['cfg_basehost'],'',$arcurl);
//            if(file_exists($htmlfile) && !is_dir($htmlfile))
//            {
//                @unlink($htmlfile);
//                $arcurls = explode(".", $htmlfile);
//                $sname = $arcurls[count($arcurls)-1];
//                $fname = preg_replace("#(\.$sname)$#", "", $htmlfile);
//                for($i=2; $i<=100; $i++)
//                {
//                    $htmlfile = $fname."_{$i}.".$sname;
//                    if( @file_exists($htmlfile) ) @unlink($htmlfile);
//                    else break;
//                }
//            }
//        }

        return true;
    }



}

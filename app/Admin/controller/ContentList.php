<?php
declare (strict_types=1);

namespace app\Admin\controller;

use app\Admin\model\Keywords;
use range\Image;
use think\facade\Db;
use think\facade\Request;
use think\facade\Route;
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
use Snoopy\Snoopy as SnoopyClass;

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
        $mid = Request::param('mid');
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
        if(!empty($mid)){
            $map['A.mid'] = $mid;
        }
        if(\request()->isPost()) {
            $page = \request()->post('page', 1);
            $limit = (int)\request()->post('limit', 10);
            $offset = ($page - 1) * $limit;
            $data = Archives::alias('A')
                ->leftjoin(Arctype::getTable() . " B ", "B.id=A.typeid")
                ->leftjoin(Admin::getTable() . " C ", "C.id=A.mid")
                ->field('A.*,B.typename as ctypename,C.userid,B.typedir')
                ->where($map)
                ->order('A.id desc')->limit($offset, $limit)->select();
            $count = Archives::alias('A')
                ->leftjoin(Arctype::getTable() . " B ", "B.id=A.typeid")
                ->leftjoin(Admin::getTable() . " C ", "C.id=A.mid")
                ->where($map)->count();
            return json(['code' => 0, 'msg'=>'', 'data'=>['count' => $count, 'list' => $data]], 200);
        }
        View::assign('arcrank', $arcrank);
        return View::fetch();
    }

    public function article_add()
    {
        if (Request::isPost()) {
            $param    = Request::post('');
            $color = \request()->post('color', '');
            $writer = \request()->post('writer', '');
            $source = \request()->post('source', '');
            $weight = \request()->post('weight', '');
            $typeid = \request()->post('typeid', '');
            $ishtml = \request()->post('ishtml', 0);
            $pubdate  = GetMkTime($param['pubdate']);
            $sortrank = AddDay($pubdate, $param['sortup']);
            $senddate = time();
            $arcID    = GetIndexKey($param['arcrank'], $typeid, $sortrank, $param['channelid'], $senddate, 1);
            $flag     = isset($param['flags']) ? join(',', $param['flags']) : '';
            $ismake   = $ishtml == 0 ? -1 : 0;

            $state = \think\Db::startTrans();
            try {

                Archives::insert(array(
                    'id'          => $arcID,
                    'typeid'      => $typeid,
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
                    'color'       => $color,
                    'writer'      => $writer,
                    'source'      => $source,
                    'mid'         => 1,
                    'voteid'      => 0,//投票
                    'notpost'     => $param['notpost'],
                    'description' => $param['description'],
                    'keywords'    => $param['keywords'],
                    'filename'    => $param['filename'],
                    'dutyadmin'   => 1,
                    'weight'      => $weight,
                    'senddate'    => $senddate,
                ));

                if($ishtml == 1){
                    $ArchivesInfo = Archives::where(['id' => $arcID])->find();
                    $arctypeInfo = Arctype::where("id=" . $typeid)->find();
                    $typedir = $arctypeInfo['typedir'];
                    $temparticle = str_replace('.html', '', $arctypeInfo['temparticle']);
                    View::assign('arctypeInfo', $arctypeInfo);
                    View::assign('archivesInfo', $ArchivesInfo);
                    $this->ViewAll();
                    $this->buildHtml($arcID, '.' . $typedir . '/', $temparticle);
                    Archives::where(['id' => $arcID])->save(['ismake' => 1]);
                }

                $cts      = Channeltype::where("id=" . $param['channelid'])->find();
                $addTable = trim($cts['addtable']);
                if (empty($addTable)) {
                    Archives::where("id=" . $arcID)->delete();
                    Arctiny::where("id=" . $arcID)->delete();
                }
                $inadd_f = $inadd_v = '';

                $userip  = Request::ip();
                $templet = empty($param['templet']) ? '' : $param['templet'];
                $query   = "INSERT INTO `{$addTable}`(aid,typeid,redirecturl,templet,userip,body{$inadd_f}) Values('$arcID','{$param['typeid']}','{$param['redirecturl']}','$templet','{$userip}','{$param['body']}'{$inadd_v})";
                Db::query($query);

                \think\Db::commit();
            } catch (\Exception $e) {
                $state = false;
                \think\Db::rollback();
            }

            if ($state !== false) {
                return success("提交成功");
            }
            return error("提交失败");
        }
        $channelid = Request::get('channelid');
        $cid       = Request::get('cid');
        $channelid = empty($channelid) ? 0 : intval($channelid);
        if ($cid > 0 && $channelid == 0) {
            $row       = Arctype::where('id=' . $cid)->find();
            $channelid = $row['channeltype'];
        } else {
            if ($channelid == 0) {
                $channelid = 1;
            }
        }
        $cInfos = Channeltype::where('id='.$channelid)->find();
        $fieldset = json_decode($cInfos['fieldset'], true);

        if (empty($geturl)) $geturl = '';
        $cid                 = empty($cid) ? 0 : intval($cid);
        $geturl              = Request::param('geturl');
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

        view::assign('cfg_tamplate_rand', \config('app.cfg_tamplate_rand'));
        view::assign('cfg_tamplate_arr', \config('app.cfg_tamplate_arr'));
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
        View::assign('fieldset', $fieldset);
        View::assign('nav', array(
            array('title' => '核心'),
            array('title' => '所有档案列表', 'url' => (string)url('ContentList/index')),
            array('title' => '添加文档'),
        ));
        return view('');
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


            $AdminUser = Session::get(config('app.uid_key'));
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

        $ArcattAll = Arcatt::where([])->order('sortid asc')->select();

        $ArchivesCount = Archives::where([])->count();

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
        $user             = Session::get(config('app.uid_key'));
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
            array('title' => '核心'),
            array('title' => '所有档案列表', 'url' => (string)url('ContentList/index')),
            array('title' => '更改文章'),
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
            ->where("arc.arcrank = '-2'")
            ->order('arc.id desc')
            ->field('arc.*, tp.typename')->paginate();
        View::assign('_data', $data);
        View::assign('nav', array(
            array('title' => '核心', 'url' => ''),
            array('title' => '所有档案列表', 'url' => (string)url('ContentList/index')),
            array('title' => '文档回收箱', 'url' => ''),
        ));
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
        return success('删除成功');

    }

    public function article_keywords_select()
    {
        $keywords = Request::param('keywords', '');
        $f = Request::param('f');
        $data = Keywords::where('')->order('rank desc')->paginate();
        View::assign('keywords', $keywords);
        View::assign('f', $f);
        View::assign('data', $data);
        return View::fetch('');
    }

    public function article_keywords_main()
    {
        if(Request::isPost()){
            $dopost = Request::param('dopost');
            if($dopost == 'add'){
                $keyword = Request::param('keyword');
                $rpurl = Request::param('rpurl');
                $rank = Request::param('rank');
                $state = (new Keywords())->replace()->insert(array(
                    'keyword' => $keyword,
                    'rpurl'   => $rpurl,
                    'sta'     => 1,
                    'rank'    => $rank,
                ));

                if($state !== false){
                    return $this->success("添加成功", (string)url('article_keywords_main'));
                }
                return $this->error("添加失败");
            }


        }

        $keyword = Request::param('keyword', '');
        $addquery = '';
        if(!empty($keyword)){
            $addquery = "keyword LIKE '%$keyword%' ";
        }

        $data = Keywords::where($addquery)->order('rank desc')->paginate();
        View::assign('data', $data);
        View::assign('keyword', $keyword);
        return View::fetch('');

    }

    public function article_keywords_make()
    {

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
            $arcQuery = "SELECT arc.*,tp.* from `$addtable` arc LEFT JOIN ".Arctype::getTable()." tp ON arc.typeid=tp.id WHERE arc.aid='$aid' ";
        }else{
            $arcQuery = "SELECT arc.*,tp.*,arc.id AS aid FROM `$maintable` arc LEFT JOIN ".Arctype::getTable()." tp ON arc.typeid=tp.id WHERE arc.id='$aid' ";
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
        }else{
            //删除数据库记录
            if(!$onlyfile)
            {
                $query = "Delete From ".Arctiny::getTable()." where id='$aid' $whererecycle";
                $state = Arctiny::where("id='$aid' $whererecycle")->delete();
                if($state){

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

    public function snoopy()
    {
        if(\request()->isPost()){
            $url = \request()->post('urls');

            $snoopy = new SnoopyClass();
            $snoopy->expandlinks = true;
            $snoopy->fetch($url);
            $results = $snoopy->results;
            $regex4 = "/<div class=\"mainLeft globalSectionContainer\".*?>.*?<div class=\"mainRight globalSectionContainer\">/ism";
            preg_match($regex4, $results, $matches);

            $pattern = "/[img|IMG].*?src=['|\"](.*?(?:[.gif|.jpg]))['|\"].*?[\/]?>/";
            preg_match_all($pattern, $matches[0], $match);
            // 去除空格
            $matche = trim( $matches[0]);

            foreach ($match[1] as $k => $v){
                if(strpos($v, 'http') !== false ) {
                    $d = (new Image())->RangeImage($v);
                    if($d) {
                        // 新图片 替换 原图片
                        $matche = str_replace($d['imgurl'], $d['url'], $matche);
                    }
                }
            }
            //$matche = preg_replace('# #','', $matche);
            //preg_match_all('&nbsp;', $matche, $matches);
            $matches = str_replace('&nbsp;', '', $matche);
            $name = (new Snoopy())->get_tag_data($matche, 'div', 'class', 'name')[0];
            $length = strpos($matche, '评价');
            $info = substr( $matche, 0, $length-150);
            return json([
                'code' => 0,
                'msg' => '成功',
                'data' => [
                    'name' => $name,
                    'info' => $info,
                ],
            ], 200);
        }
    }

    public function html()
    {
        $channelid = \request()->param('channelid');
        $cInfos = Channeltype::where('id='.$channelid)->find();
        $fieldset = json_decode($cInfos['fieldset'], true);
        $html = "";
        foreach ($fieldset as $key => $value){
            $value['itemname'] = $value['itemname'] . $value['type'];
            switch ($value['type']) {
                case 'text':
                    $html .= "<div class=\"layui-form-item\">";
                    $html .= "<label class=\"layui-form-label\">" . $value['itemname'] . "</label>";
                    $html .= "<div class=\"layui-input-block\">";
                    $html .= "<input type=\"text\" name=\"title\" lay-verify=\"title\" autocomplete=\"off\" placeholder=\"请输入\" class=\"layui-input\" />";
                    $html .= "</div>";
                    $html .= "</div>";
                    // 单行文本
                    break;
                case 'multitext':
                    $html .= "<div class=\"layui-form-item\">";
                    $html .= "<label class=\"layui-form-label\">" . $value['itemname'] . "</label>";
                    $html .= "<div class=\"layui-input-block\">";
                    $html .= "<textarea placeholder=\"请输入内容\" class=\"layui-textarea\"></textarea>";
                    $html .= "</div>";
                    $html .= "</div>";
                    // 多行文本
                    break;
                case 'htmltext':
                    // html 文本
                    break;
                case 'textdata':
                    // 文本保存html数据
                    break;
                case 'int':
                    $html .= "<div class=\"layui-form-item\">";
                    $html .= "<label class=\"layui-form-label\">" . $value['itemname'] . "</label>";
                    $html .= "<div class=\"layui-input-block\">";
                    $html .= "<input type=\"text\" name=\"title\" lay-verify=\"title\" autocomplete=\"off\" placeholder=\"请输入\" class=\"layui-input\" />";
                    $html .= "</div>";
                    $html .= "</div>";
                    // 整数类型
                    break;
                case 'float':
                    $html .= "<div class=\"layui-form-item\">";
                    $html .= "<label class=\"layui-form-label\">" . $value['itemname'] . "</label>";
                    $html .= "<div class=\"layui-input-block\">";
                    $html .= "<input type=\"text\" name=\"title\" lay-verify=\"title\" autocomplete=\"off\" placeholder=\"请输入\" class=\"layui-input\" />";
                    $html .= "</div>";
                    $html .= "</div>";
                    // 小数类型
                    break;
                case 'datetime':
                    $html .= "<div class=\"layui-form-item\">";
                    $html .= "<label class=\"layui-form-label\">" . $value['itemname'] . "</label>";
                    $html .= "<div class=\"layui-input-block\">";
                    $html .= "<input type=\"text\" name=\"title\" lay-verify=\"title\" autocomplete=\"off\" placeholder=\"请输入\" class=\"layui-input\" />";
                    $html .= "</div>";
                    $html .= "</div>";
                    // 时间类型
                    break;
                case 'img':
                    $html .= "<div class=\"layui-form-item\">";
                    $html .= "<label class=\"layui-form-label\">" . $value['itemname'] . "</label>";
                    $html .= '<button type="button" class="layui-btn" id="test1">上传图片</button>' .
                        '<div class="layui-upload-list">' .
                        '<img class="layui-upload-img" id="demo1">' .
                        '<p id="demoText"></p>' .
                        '</div>' .
                        '<div style="width: 95px;">' .
                        '<div class="layui-progress layui-progress-big" lay-showpercent="yes" lay-filter="demo">' .
                        '<div class="layui-progress-bar" lay-percent=""></div>' .
                        '</div>' .'</div>';
                    $html .= "</div>";
                    // 图片
                    break;
                case 'imgfile':
                    // 图片(仅网址)
                    $html .= "<div class=\"layui-form-item\">";
                    $html .= "<label class=\"layui-form-label\">" . $value['itemname'] . "</label>";
                    $html .= "<div class=\"layui-input-block\">";
                    $html .= "<input type=\"text\" name=\"title\" lay-verify=\"title\" autocomplete=\"off\" placeholder=\"请输入\" class=\"layui-input\" />";
                    $html .= "</div>";
                    $html .= "</div>";
                    break;
                case 'media':
                    // 多媒体文件
                    $html .= "<div class=\"layui-form-item\">";
                    $html .= "<label class=\"layui-form-label\">" . $value['itemname'] . "</label>";
                    $html .= '<button type="button" class="layui-btn" id="test1">上传图片</button>' .
                        '<div class="layui-upload-list">' .
                        '<img class="layui-upload-img" id="demo1">' .
                        '<p id="demoText"></p>' .
                        '</div>' .
                        '<div style="width: 95px;">' .
                        '<div class="layui-progress layui-progress-big" lay-showpercent="yes" lay-filter="demo">' .
                        '<div class="layui-progress-bar" lay-percent=""></div>' .
                        '</div>' . '</div>';
                    $html .= "</div>";
                    break;
                case 'addon':
                    // 附件类型
                    $html .= "<div class=\"layui-form-item\">";
                    $html .= "<label class=\"layui-form-label\">" . $value['itemname'] . "</label>";
                    $html .= '<button type="button" class="layui-btn" id="test1">上传图片</button>' .
                        '<div class="layui-upload-list">' .
                        '<img class="layui-upload-img" id="demo1">' .
                        '<p id="demoText"></p>' .
                        '</div>' .
                        '<div style="width: 95px;">' .
                        '<div class="layui-progress layui-progress-big" lay-showpercent="yes" lay-filter="demo">' .
                        '<div class="layui-progress-bar" lay-percent=""></div>' .
                        '</div>' .
                        '</div>';
                    $html .= "</div>";
                    break;
                case 'select':
                    // 使用
                    $html .= "<div class=\"layui-form-item\">";
                    $html .= "<label class=\"layui-form-label\">" . $value['itemname'] . "</label>";
                    $html .= '<div class="layui-input-inline">'.
                        '<select name="interest_' . $key . '" lay-filter="select">' .
                        '<option value="111">111</option>' .
                        '</select>' .
                        '</div>';
                    $html .= "</div>";
                    break;
                case 'radio':
                    // 使用radio选项卡
                    $html .= "<div class=\"layui-form-item\">";
                    $html .= "<label class=\"layui-form-label\">" . $value['itemname'] . "</label>";
                    $html .= '<div class="layui-input-block">' .
                        '<input type="radio" name="sex" value="男" title="男" checked="">' .
                        '</div>';
                    $html .= "</div>";
                    break;
                case 'checkbox':
                    // Checkbox多选框
                    $html .= "<div class=\"layui-form-item\">";
                    $html .= "<label class=\"layui-form-label\">" . $value['itemname'] . "</label>";
                    $html .= '<div class="layui-input-block">'
                            . '<input type="checkbox" name="like1[write]" lay-skin="primary" title="写作" checked="">'
                            . '</div>';
                    $html .= "</div>";
                    break;
                case 'stepselect':
                    // 联动类型
                    break;
            }

//            $html .= "<div>";
        }
        return array('code' => 0, 'data' => array('html' => $html));
    }





}

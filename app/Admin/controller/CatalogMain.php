<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use app\Admin\model\Archives;
use think\facade\Request;
use think\facade\View;
use think\facade\Config;
use app\Admin\model\Channeltype as ChanneltypeModel;
use app\Admin\model\Arctype as ArctypeModel;
use app\Admin\model\Arcrank;
use app\Admin\model\Arctype;
use app\Admin\model\Channeltype;
use app\Admin\model\SysEnum;
use pagehtml\Pagehtml;


/**
 * [网站栏目管理]
 * Class CatalogMain
 * @package app\Admin\controller
 */
class CatalogMain extends Base
{
    public function initialize()
    {
        parent::initialize();
        View::assign('_nav_this', 'CatalogMain_index');
        View::assign('nav', array(
            array('title'=>'核心', 'url'=>''),
            array('title'=>'网站栏目管理', 'url'=>''),
        ));
        $host = Config::get('app.app_host');
        View::assign('host', $host);
        View::assign('_nav_itemed', 'hexing');
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        if(\request()->isPost()) {
            $page = \request()->post('page', 1);
            $limit = (int)\request()->post('limit', 10);
            $map = [];
            $offset = ($page - 1) * $limit;
            $data = ArctypeModel::where($map)->limit($offset, $limit)->select();
            $count = ArctypeModel::where($map)->count();
            return json(['code' => 0, 'data'=>['count' => $count, 'list' => $data], 'msg'=>''], 200);
        }
        return View::fetch();
    }

    /**
     * [增加子类]
     * @author Dave 178698695@qq.com
     */
    public function catalog_add()
    {
        if(Request::isPost()){
            $param = Request::param('');
            $typename = Request::param('typename');
            $state = Arctype::insert(array(
                'reid'        => 0,
                'topid'       => 0,
                'sortrank'    => 0,
                'typename'    => $param['typename'],
                'typedir'     => $param['typedir'],
                'isdefault'   => $param['isdefault'],
                'defaultname' => $param['defaultname'],
                'issend'      => $param['issend'],
                'channeltype' => $param['channeltype'],
                'tempindex'   => $param['tempindex'],
                'templist'    => $param['templist'],
                'temparticle' => $param['temparticle'],
                'modname'     => 'default',
                'namerule'    => $param['namerule'],
                'namerule2'   => $param['namerule2'],
                'ispart'      => 0,
                'corank'      => 0,
                'description' => '',
                'keywords'    => '',
                'seotitle'    => $param['typename'],
                'moresite'    => 0,
                'siteurl'     => '',
                'sitepath'    => '',
                'ishidden'    => 0,
                'cross'       => 0,
                'content'     => '',
                'smalltypes'  => '',
            ));

            if($state !== false){
                return success('提交成功' );
            }
            return error('提交出错');
        }

        $id = Request::param('id', 0);
        if(empty($dopost)) $dopost = '';
        $nid       = 'article';
        $channelid = 1;
        $issend    = 1;
        $corank    = 0;
        $reid      = 0;
        $topid     = 0;
        $typedir   = '';
        $moresite  = 0;

        if($id>0){
            $myrow = ArctypeModel::hasWhere('profile', 'Arctype.id='.$id, 'Arctype.*,Channeltype.typename as ctypename', 'left')->find();
            View::assign('channelid', $myrow['channeltype']);
            View::assign('issennd', $myrow['issend']);
            View::assign('corank', $myrow['corank']);
            View::assign('topid', $myrow['topid']);
            View::assign('typedir', $myrow['typedir']);
        }

        //父栏目是否为二级站点
        $moresite = empty($myrow['moresite']) ? 0 : $myrow['moresite'];
        View::assign('moresite', $moresite);
        $row = ChanneltypeModel::where("id<>-1 AND isshow=1")->order("id asc")->select();
        $channelArray = array();
        foreach ($row as $k => $v){
            $channelArray[$v->id]['typename'] = $v->typename;
            $channelArray[$v->id]['nid'] = $v->nid;
            if($v->id == $channelid)
            {
                $nid = $v->nid;
            }
        }

        //Select * from `#@__arcrank` where rank >= 0
        $ArcrankAll = Arcrank::where("rank >= 0")->select();
        $SysEnumAll = SysEnum::where('')->order('disorder ASC, id DESC')->select();
        $cfg_templets_dir = '/templets';

        //文档的默认命名规则
        $art_shortname = $cfg_df_ext = '.html';
        $cfg_df_namerule = '{typedir}/{Y}/{M}{D}/{aid}'.$cfg_df_ext;
        View::assign('issend', $issend);
        View::assign('channelid', $channelid);
        View::assign('cfg_templets_dir', $cfg_templets_dir);
        View::assign('cfg_df_namerule', $cfg_df_namerule);
        View::assign('topid', $topid);
        View::assign('SysEnumAll', $SysEnumAll);
        View::assign('id', $id);
        View::assign('nid', $nid);
        View::assign('channelArray', $channelArray);
        View::assign('ArcrankAll', $ArcrankAll);
        View::assign('nav', array(
            array('title'=>'核心'),
            array('title'=>'网站栏目管理', 'url'=>(string)url('CatalogMain/index')),
            array('title'=>'增加顶级栏目组'),
        ));
        return view();
    }

    /**
     * 显示编辑资源表单页.
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $channel = ChanneltypeModel::where("id<>-1 AND isshow=1 ")->order('id asc')->select();
        View::assign('_channel', $channel);
        return View::fetch();
    }

    public function catalog_edit()
    {
        if(Request::isPost()){
            $param = Request::param('');

            $state = Arctype::update([
                'issend'      => $param['issend'],
                'sortrank'    => $param['sortrank'],
                'typedir'     => $param['typedir'],
                'typename'    => $param['typename'],
                'isdefault'   => $param['isdefault'],
                'defaultname' => $param['defaultname'],
                'ispart'      => $param['ispart'],
                'ishidden'    => $param['ishidden'],
                'channeltype' => $param['channeltype'],
                'tempindex'   => $param['tempindex'],
                'templist'    => $param['templist'],
                'temparticle' => $param['temparticle'],
                'namerule'    => $param['namerule'],
                'namerule2'   => $param['namerule2'],
                'description' => $param['description'],
                'keywords'    => $param['keywords'],
                'seotitle'    => $param['seotitle'],
                'moresite'    => $param['moresite'],
                'cross'       => $param['cross'],
                'crossid'     => $param['crossid'],


                //'corank' => $param['corank'],
            ], ['id'=>$param['id']]);

            //return json(['code'=>0,'msg'=>'成功']);
            if($state != false){
                return success('编辑成功');
            }else{
                return error('编辑失败');
            }
        }

        $id = Request::param('id');
        $channelid = 1;

        $ArcrankAll = Arcrank::where("rank >= 0")->select();
        View::assign('ArcrankAll', $ArcrankAll);

        $row = ChanneltypeModel::where("id<>-1 AND isshow=1")->order("id asc")->select();
        $channelArray = array();
        foreach ($row as $k => $v){
            $channelArray[$v->id]['typename'] = $v->typename;
            $channelArray[$v->id]['nid'] = $v->nid;
            if($v->id == $channelid)
            {
                $nid = $v->nid;
            }
        }

        $SysEnumAll = SysEnum::where('')->order('disorder ASC, id DESC')->select();

        $myrow = ArctypeModel::hasWhere('profile', 'Arctype.id='.$id, 'Arctype.*,Channeltype.typename as ctypename', 'left')->find();
        View::assign('myrow', $myrow);
        View::assign('channelid', $channelid);
        $cfg_templets_dir = '/templets';
        View::assign('cfg_templets_dir', $cfg_templets_dir);
        View::assign('SysEnumAll', $SysEnumAll);
        View::assign('channelArray', $channelArray);
        View::assign('id', $id);
        View::assign('nav', array(
            array('title'=>'核心'),
            array('title'=>'网站栏目管理', 'url'=>(string)url('CatalogMain/index')),
            array('title'=>'修改栏目'),
        ));
        return View::fetch();
    }

    public function catalog_del()
    {
        if(Request::isGet()){
            $id = Request::param('id');
            $state = Arctype::where("id=".$id)->delete();
            if($state !== false){
                return $this->success('删除成功', (string)url('index') );
            }
            return $this->error("删除失败");
        }
    }

    /**
     * [更新栏目组缓存]
     * @author Dave 178698695@qq.com
     */
    public function catalog_do()
    {

    }

    public function makehtml_list()
    {
        $ArctypeAll = Arctype::alias('A')->leftjoin(Channeltype::getTable()." B", 'B.id=A.channeltype')->field('A.*,B.typename as ctypename,B.addtable,B.issystem')->select();
        $cfg_remote_site = Config::get('app.cfg_remote_site');
        View::assign('cfg_remote_site', $cfg_remote_site);
        View::assign('ArctypeAll', $ArctypeAll);


        View::assign('nav', array(
            array('title'=>'核心', 'url'=>''),
            array('title'=>'网站栏目管理', 'url'=> (string)url('CatalogMain/index')),
            array('title'=>'更新栏目HTML'),
        ));
        return view();
    }

    public function makehtml_archives()
    {
        $ArctypeAll = Arctype::alias('A')->leftjoin(Channeltype::getTable()." B", 'B.id=A.channeltype')->field('A.*,B.typename as ctypename,B.addtable,B.issystem')->select();
        $cfg_remote_site = Config::get('app.cfg_remote_site');
        View::assign('cfg_remote_site', $cfg_remote_site);
        View::assign('ArctypeAll', $ArctypeAll);
        View::assign('nav', array(
            array('title'=>'核心', 'url'=>''),
            array('title'=>'网站栏目管理', 'url'=> (string)url('CatalogMain/index')),
            array('title'=>'更新文档HTML', 'url'=> ''),
        ));
        return view();
    }

    public function makehtml_list_action()
    {
        header("Content-type: text/html; charset=utf-8");

        $this->ViewAll();

        if(Request::isGet()){
            $param = Request::param('');

            // 游戏类型
            $typeid = $param['typeid'];
            // 最大创建个数
            $maxpagesize = $param['maxpagesize'];
            $arctypeInfo = Arctype::where("id=".$typeid)->find();
            View::assign('arctypeInfo', $arctypeInfo);

            if($param['uptype'] == 0){
                // PC


                $count = Archives::where("typeid=".$arctypeInfo['id'])->count();
                if($count == 0){
                    echo '此类没有数据';
                }

                $totalPage = ceil($count/20);
                $host = Config::get('app.app_host');

                for($i=1; $i<=$totalPage; $i++){
                    $arctypeInfo['tempindex'] = str_replace('.html', '', $arctypeInfo['tempindex']);
                    $arctypeInfo['defaultname'] = str_replace('.html', '', $arctypeInfo['defaultname']);

                    $page = new Pagehtml($count, 20, $host.$arctypeInfo['typedir'], $i);

                    $archivesAll = Archives::where("typeid=".$arctypeInfo['id'])->order('id desc')->limit($page->firstRow, (int)$page->listRows)->select();
                    View::assign('archivesAll', $archivesAll);
                    View::assign('page', $page->show());
                    $arctypeInfo['tempindex'] = str_replace('.html', '', $arctypeInfo['tempindex']);
                    $arctypeInfo['defaultname'] = str_replace('.html', '', $arctypeInfo['defaultname']);
                    if($i == 1){
                        $htmlfile = $arctypeInfo['defaultname'];
                    }else{
                        $htmlfile = 'list_'.$arctypeInfo['id'].'_'.$i;
                    }
                    echo "生成".$arctypeInfo['typedir'].'/'.$htmlfile.".html<br/>";

                    $this->buildHtml($htmlfile, '.'.$arctypeInfo['typedir'].'/', $arctypeInfo['tempindex']);
                    echo "静态页面$htmlfile.html已生成...<br>";
                }

            }elseif ($param['uptype'] == 'mkmobile'){
                //web


            }
            // 生成首页
            $this->buildHtml('index', './','/eyou/pc/index');

            echo '生成页面成功';
        }

    }

    public function makehtml_archives_action()
    {
        $this->ViewAll();

        if(Request::isGet()){
            $host    = Config::get('app.app_host');
            $param   = Request::param('');
            $startid = $param['startid'];
            $endid   = $param['endid'];
            $typeid  = $param['typeid'];
            $arctypeInfo = Arctype::where("id=".$typeid)->find();
            View::assign('arctypeInfo', $arctypeInfo);
            if($startid == 0 || $startid == ''){
                $start = Archives::where("")->field('id')->order('id asc')->find();
                $startid = $start['id'];
            }

            if($endid == 0 || $endid == ''){
                $end = Archives::where("")->field('id')->order('id desc')->find();
                $endid = $end['id'];
            }

            $ArchivesAll = Archives::where("typeid=".$arctypeInfo['id']." and id >= $startid AND id <= $endid ")->field('id ')->select();
            // 根目录
            $cfg_basedir = app()->getRootPath().'view/Admin';
            $typedir = $arctypeInfo['typedir'];
            if (!file_exists($cfg_basedir.'/'.$arctypeInfo['temparticle']) ){
                echo "模版不存在[".$cfg_basedir.'/'.$arctypeInfo['temparticle']."]<br/>";
                //continue;
                exit();
            }
            $temparticle = str_replace('.html', '', $arctypeInfo['temparticle']);
            $defaultname = str_replace('.html', '', $arctypeInfo['defaultname']);
            foreach ($ArchivesAll as $k=>$v){
                $archivesInfo = Archives::where("id=".$v['id'])->find();
                View::assign('archivesInfo', $archivesInfo);
                // 模版文件不存在
                echo '生成'.$arctypeInfo['typedir'].'/'.$v['id'].".html页面<br/>";

                $this->buildHtml($v['id'], '.'.$typedir.'/', $temparticle);
            }

            echo '页面生成完成';

            exit();
        }
    }

    /**
     * [模板管理器]
     * @author Dave 178698695@qq.com
     */
    public function select_templets()
    {
        $cfg_cmspath = '';
        $f = Request::param('f');
        $activepath = Request::param('activepath');
        if(!isset($activepath)) $activepath = $cfg_cmspath;
        // 根目录
        $cfg_basedir = app()->getRootPath().'view/Admin';
        $host = Config::get('app.app_host');
        //$cfg_templets_dir = '/templets';
        $cfg_templets_dir = '/';
        View::assign('cfg_templets_dir', $cfg_templets_dir);
        View::assign('cfg_basedir', $cfg_basedir);
        View::assign('activepath', $activepath);
        View::assign('f', $f);
        return View::fetch('');
    }






}

<?php
declare (strict_types = 1);

namespace app\Admin\controller;


use app\Admin\model\Archives;
use app\Admin\model\Flink;
use app\Admin\model\Sysconfig;
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
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $data = ArctypeModel::where(array())->select()->toArray();
        View::assign('_data', $data);
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

            var_dump($param);

            exit();

        }

        $id = Request::param('id', 0);
        if(empty($dopost)) $dopost = '';
        $nid = 'article';
        $channelid = 1;
        $issend = 1;
        $corank = 0;
        $reid = 0;
        $topid = 0;
        $typedir = '';
        $moresite = 0;

        if($id>0)
        {
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
        return View::fetch();
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
                $this->success('成功', (string)url('CatalogMain/index'));
            }else{
                $this->success('失败');
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
            array('title'=>'核心', 'url'=>''),
            array('title'=>'网站栏目管理', 'url'=>''),
            array('title'=>'修改栏目', 'url'=>''),
        ));
        return View::fetch();
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
            array('title'=>'网站栏目管理', 'url'=>''),
            array('title'=>'更新栏目HTML', 'url'=>''),
        ));
        return View::fetch();
    }


    public function makehtml_archives()
    {

        $ArctypeAll = Arctype::alias('A')->leftjoin(Channeltype::getTable()." B", 'B.id=A.channeltype')->field('A.*,B.typename as ctypename,B.addtable,B.issystem')->select();
        $cfg_remote_site = Config::get('app.cfg_remote_site');
        View::assign('cfg_remote_site', $cfg_remote_site);

        View::assign('ArctypeAll', $ArctypeAll);

        View::assign('nav', array(
            array('title'=>'核心', 'url'=>''),
            array('title'=>'网站栏目管理', 'url'=>''),
            array('title'=>'更新文档HTML', 'url'=>''),
        ));
        return View::fetch();
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

                    $this->buildHtml($htmlfile, '.'.$arctypeInfo['typedir'].'/', $arctypeInfo['tempindex']);
                    echo "静态页面$htmlfile.html已生成...<br>";
                }



//                var_dump($page->show());exit();
//                foreach ($archivesAll as $k => $v){
//                    $v['arcurl'] = '';
//                    $archivesAll[$k] = $v;
//                }
                //https://www.cnblogs.com/haiwei_sun/p/3409584.html


                //$arctypeInfo['tempindex'] = str_replace('{style}', $arctypeInfo['typedir'], $arctypeInfo['tempindex']);



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




            $ArchivesAll = Archives::where("typeid=".$arctypeInfo['id']." and id > $startid AND id < $endid ")->field('id ')->select();
            foreach ($ArchivesAll as $k=>$v){
                $archivesInfo = Archives::where("id=".$v['id'])->find();
                View::assign('archivesInfo', $archivesInfo);
                $arctypeInfo['tempindex'] = str_replace('.html', '', $arctypeInfo['tempindex']);
                $arctypeInfo['defaultname'] = str_replace('.html', '', $arctypeInfo['defaultname']);
                $this->buildHtml($v['id'], '.'.$arctypeInfo['typedir'].'/', $arctypeInfo['tempindex']);
            }



                //$page = new Pagehtml($count, 20, $host.$arctypeInfo['typedir'], $i);

//                $archivesAll = Archives::where("typeid=".$arctypeInfo['id'])->order('id desc')->limit($page->firstRow, (int)$page->listRows)->select();
//                View::assign('archivesAll', $archivesAll);
//                View::assign('page', $page->show());

            exit();
        }

    }

    protected function ViewAll()
    {
        $arctype = Arctype::alias('A')->leftjoin(Channeltype::getTable()." B", 'B.id=A.channeltype')->field('A.*,B.typename as ctypename,B.addtable,B.issystem')->select()->toArray();
        foreach ($arctype as $k => $v){
            $v['typeurl'] = $v['typedir'].'/'.$v['defaultname'];
            $arctype[$k] = $v;
        }
        View::assign('arctype', $arctype);

        $archives3 = Archives::where("typeid=3 or typeid=2")->order('id desc')->paginate(6);
        View::assign('archives3', $archives3);

        $config = Sysconfig::sele();
        View::assign('config', $config);

        $channel =  ChanneltypeModel::where("id", '>', 0)->limit(0, 10)->select();
        View::assign('channel', $channel);




        $arclist = Archives::where("typeid=1")->limit(6)->select();
        View::assign('arclist', $arclist);
        $arclist2 = Archives::where("typeid=2")->limit(6)->select();
        View::assign('arclist2', $arclist2);



        $arclist3 = Archives::where("typeid=3")->limit(6)->select();
        View::assign('arclist3', $arclist3);

        $flink = Flink::where("")->select();
        View::assign('flink', $flink);
    }






}

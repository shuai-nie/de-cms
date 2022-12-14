<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use app\Admin\model\Channeltype;
use app\Admin\model\Stepselect;
use tagparse\TagParse;
use think\facade\Config;
use think\facade\Db;
use think\facade\Request;
use think\facade\Session;
use think\facade\View;
use app\Admin\model\Channeltype as ChanneltypeModel;
use app\Admin\model\Arcrank as ArcrankModel;
use app\Admin\model\MemberModel ;

/**
 * [内容模型管理]
 * Class MyChannelMain
 * @package app\Admin\controller
 */
class MyChannelMain extends Base
{
    public function initialize()
    {
        parent::initialize();
        View::assign('nav', array(
            array('title'=>'核心', 'url'=>''),
            array('title'=>'内容模型管理', 'url'=>''),
        ));
        View::assign('_nav_this', 'MyChannelMain_index');
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
            $map = [];
            $page = \request()->post('page');
            $limit = (int)\request()->post('limit');
            $offset = ($page - 1) * $limit;
            $data = ChanneltypeModel::where($map)->limit($offset, $limit)->select();
            $count = ChanneltypeModel::where($map)->count();
            return json(['code' => 0, 'msg'=>'', 'data'=>['count' => $count, 'list' => $data]], 200);
        }
        View::assign('_data', []);
        return View::fetch();
    }

    public function mychannel_edit()
    {
        if(Request::isPost()){
            $addtable    = Request::post('addtable');
            $typename    = Request::post('typename');
            $addcon      = Request::post('addcon');
            $mancon      = Request::post('mancon');
            $editcon     = Request::post('editcon');
            $useraddcon  = Request::post('useraddcon');
            $usermancon  = Request::post('usermancon');
            $usereditcon = Request::post('usereditcon');
            $listfields  = Request::post('listfields');
//            $fieldset    = Request::post('fieldset');
            $issend      = Request::post('issend');
            $arcsta      = Request::post('arcsta');
            $usertype    = Request::post('usertype');
            $sendrank    = Request::post('sendrank');
            $needdes     = Request::post('needdes');
            $needpic     = Request::post('needpic');
            $titlename   = Request::post('titlename');
            $onlyone     = Request::post('onlyone');
            $dfcid       = Request::post('dfcid');
            $id          = Request::post('id');

            $exist = Db::query("show tables like '".$addtable."'");
            if(!$exist){
                return $this->error("系统找不到你所指定的表 {$addtable} ，请手工创建这个表！");
            }

            $state = Channeltype::update(array(
                'typename'    => $typename,
                'addtable'    => $addtable,
                'addcon'      => $addcon,
                'mancon'      => $mancon,
                'editcon'     => $editcon,
                'useraddcon'  => $useraddcon,
                'usermancon'  => $usermancon,
                'usereditcon' => $usereditcon,
                /*'fieldset'    => $fieldset,*/
                'listfields'  => $listfields,
                'issend'      => $issend,
                'arcsta'      => $arcsta,
                'usertype'    => $usertype,
                'sendrank'    => $sendrank,
                'needdes'     => $needdes,
                'needpic'     => $needpic,
                'titlename'   => $titlename,
                'onlyone'     => $onlyone,
                'dfcid'       => $dfcid,
            ), array(
                'id' => $id
            ));

            if($state !== false){
                return success('提交成功');
            }
            return error("提交失败");
        }
        $request = $this->request;
        $id = $request->get('id');
        $dopost = $request->get('dopost');
        $data = ChanneltypeModel::where(array('id'=>$id))->find()->toArray();
        $data['fieldset'] = !empty($data['fieldset']) ? json_decode($data['fieldset'], true) : array();
        View::assign('data', $data);
        $ArcrankAll = ArcrankModel::where("rank>=10")->select()->toArray();
        View::assign('ArcrankAll', $ArcrankAll);
        $MemberModelAll = MemberModel::where('')->select()->toArray();
        View::assign('MemberModelAll', $MemberModelAll);
        View::assign('id', $id);


        View::assign('nav', array(
            array('title'=>'核心'),
            array('title'=>'内容模型管理', 'url'=>(string)url('MyChannelMain/index')),
            array('title'=>'编辑-内容模型'),
        ));
        return view();
    }

    protected function TagParseArr($data)
    {

        $ds = file("storage/inc/fieldtype.txt");
        foreach ($ds as $d) {
            $dds = explode(',', trim($d));
            $fieldtypes[$dds[0]] = $dds[1];
        }
        $fieldset = $data['fieldset'];
        $dtp      = new TagParse();
        $dtp->SetNameSpace("field", "<", ">");
        $dtp->LoadSource($fieldset);
        $html = "";
        if (is_array($dtp->CTags)) {
            foreach ($dtp->CTags as $ctag) {
                $html .= "<tr align=\"center\" bgcolor=\"#FFFFFF\" height=\"26\" align=\"center\" onMouseMove=\"javascript:this.bgColor='#FCFDEE';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\" height=\"24\"><td>";

                $itname = $ctag->GetAtt('itemname');
                if ($itname == ''){
                    $html .= "没指定";
                } else {
                    $html .= $itname ;
                }


                $html .= "</td><td>" . $ctag->GetTagName() . "</td>";
                $html .= "<td>";

                $ft = $ctag->GetAtt('type');
                    if (isset($fieldtypes[$ft])) $html .= $fieldtypes[$ft];
                    else  $html .= "系统专用类型";

                $html .= "</td><td>";
                $ft   = $ctag->GetAtt('autofield');
                if ($ft == '' || $ft == 0) {
                    $html .= "固化字段";
                } else {
                    $html .= "自动表单";
                }

                $html .= "</td>";
                $html .= "<td>";

                if ($ft == 1) {

                    $html .= "<a href='mychannel_field_edit.php?id=&fname=&issystem='></a>";
                    if ($data['issystem'] != 1) {
                        $html .= "<a href='#' onClick=\"javascript:DelNote('mychannel_field_edit.php?id=&fname=&action=delete\");'></a>";
                    } else {
                        $html .= "禁止修改";
                    }
                    $html .= "</td></tr>";
                }
            }
        }
        View::assign('TagParse_html', $html);
    }

    public function mychannel_field_add()
    {
        if (Request::isPost()) {
            $param = Request::post('');
            $vdefault = \request()->post('vdefault', '');
            $isnull = \request()->post('isnull', '');
            $maxlength = \request()->post('maxlength', '');
            $fieldname = \request()->post('fieldname', '');
            $itemname = \request()->post('itemname', '');
            $autofield = \request()->post('autofield');
            $notsend = \request()->post('notsend');
            $dtype = \request()->post('dtype');
            $islist = \request()->post('islist');
            $spage = \request()->post('spage');
            $dfvalue = trim($vdefault);
            $isnull = $isnull == 1 ? 'true' : "false";
            $mxlen = $maxlength;
            $fieldstring = \request()->post('fieldstring', '');

            if(preg_match("#^(select|radio|checkbox)$#i", $param['dtype'])){
                if(!preg_match("#,#", $dfvalue)){
                    return $this->error("你设定了字段为 {$param['dtype']} 类型，必须在默认值中指定元素列表，如：'a,b,c' ");
                }
            }

            if ($param['dtype'] == 'stepselect') {
                $arr = Stepselect::where("egroup=" . $fieldname)->find();
                if(!is_array($arr)){
                    return $this->error("你设定了字段为联动类型，但系统中没找到与你定义的字段名相同的联动组名!");
                }
            }

            $row = Channeltype::where("id=".$param['id'])->field("fieldset,addtable,issystem")->find();
            $fieldset = $row['fieldset']; // 存储字段
            $trueTable = $row["addtable"]; // 表名

            $fieldinfos = GetFieldMake($dtype, $fieldname, $dfvalue, $mxlen);
            $ntabsql = $fieldinfos[0];
            $buideType = $fieldinfos[1];

            $alter = " ALTER TABLE `".$trueTable."` ADD ".$ntabsql;
            $rs = Db::query($alter);

            if(!$rs){
                return $this->error("保存节点配置出错!");
            }
            $fieldset = json_decode($fieldset, true);
            array_push($fieldset, [
                'tagname' => $fieldname,
                'itemname' => $itemname,
                'autofield' => $autofield,
                'notsend' => $notsend,
                'type' => $dtype,
                'isnull' => $isnull,
                'islist' => $islist,
                'default' => $vdefault,
                'maxlength' => $maxlength,
                'page' => $spage,
            ]);
            Channeltype::update([
                'fieldset' => json_encode($fieldset, JSON_UNESCAPED_UNICODE),
            ], ['id' => $param['id']]);

            return $this->success("成功增加一个字段", (string)url('mychannel_edit', ['id'=>$param['id'], 'dopost'=>'edit', 'openfield'=>1]));
            exit();
        }

        $id = Request::param('id');
        View::assign('id', $id);
        return View::fetch();
    }

    public function ddd($str)
    {
        $str = str_replace('\\', '', $str);
        $str = str_replace('"', '', $str);
        return $str;
    }

    public function cc()
    {
        $map = [];
        $data = ChanneltypeModel::where($map)->select();
        $dtp = new TagParse();
        $dtp->SetNameSpace("field", "<", ">");
        foreach ($data as $k => $v) {
            $fieldset = $v['fieldset'];
            if (!empty($fieldset)) {
                $dtp->LoadSource($fieldset);
                $arr = [];
                foreach ($dtp->CTags as $k1 => $v1) {

                    $items = $v1->CAttribute->Items;
                    if($items){
                        // var_dump($items);exit();
                        $tagname = $this->ddd($items['tagname'] );
                        $itemname = isset($items['itemname']) ? $this->ddd($items['itemname']) : '';
                        $autofield = isset($items['autofield']) ? $this->ddd($items['autofield']) : '';
                        $notsend = isset($items['notsend']) ?  $this->ddd($items['notsend']) : '';
                        $type = $this->ddd($items['type']);
                        $isnull = $this->ddd($items['isnull']);
                        $islist = isset($items['islist']) ? $this->ddd($items['islist']) : '' ;
                        $default = $this->ddd($items['default']);
                        $maxlength = isset($items['maxlength']) ? $this->ddd($items['maxlength']) : '';
                        $page = isset($items['page']) ? $this->ddd($items['page']) : '';

                        $arr[] = [
                            'tagname' => $tagname,
                            'itemname' => $itemname,
                            'autofield' => $autofield,
                            'notsend' => $notsend,
                            'type' => $type,
                            'isnull' => $isnull,
                            'islist' => $islist,
                            'default' => $default,
                            'maxlength' => $maxlength,
                            'page' => $page,
                        ];
                    }
                }
                //var_dump($v->id);
                $fieldset = json_encode($arr, JSON_UNESCAPED_UNICODE);
                ChanneltypeModel::update(['fieldset'=>$fieldset], ['id'=>$v->id]);
            }
        }
    }

    public function object_array($object)
    {
        if(is_object($object))
        {
            $array = (array)$object;
        }
        if(is_array($object))
        {
            foreach($object as $key=>$value)
            {
                $array[$key] =$this->object_array($value);
            }
        }
        return $array;
    }

    public function mychannel_add()
    {
        if(Request::isPost()){
            $param          = Request::param('');
            $addtable    = Request::param('addtable');
            $typename    = Request::param('typename');
            $addcon      = Request::param('addcon');
            $mancon      = Request::param('mancon');
            $editcon     = Request::param('editcon');
            $useraddcon  = Request::param('useraddcon');
            $usermancon  = Request::param('usermancon');
            $usereditcon = Request::param('usereditcon');
            $listfields  = Request::param('listfields');
            $fieldset    = Request::param('fieldset');
            $issend      = Request::param('issend');
            $arcsta      = Request::param('arcsta');
            $usertype    = Request::param('usertype');
            $sendrank    = Request::param('sendrank');
            $needdes     = Request::param('needdes');
            $needpic     = Request::param('needpic');
            $titlename   = Request::param('titlename');
            $onlyone     = Request::param('onlyone');
            $dfcid       = Request::param('dfcid');
            $id       = Request::param('id');
            $nid = Request::param('nid');

            $version        = Db::query("SELECT VERSION() as version;");
            $mysql_versions = explode(".", trim($version[0]['version']));
            $mysql_version  = number_format((float)($mysql_versions[0] . "." . $mysql_versions[1]), 2);
            $charset        = Config::get('database.connections.mysql.charset');

            $count = Channeltype::where("id=" . $id . " or nid like '{$nid}' or addtable like '{$addtable}' ")->count();
            if($count >= 1){
                return $this->error('可能‘频道id’、‘频道名称标识’、‘附加表名称’在数据库已存在，不能重复使用！');
            }

            if($addtable != ''){
                $exist = Db::query("show tables like '".$addtable."'");

                if(!$exist){
                    Db::query("DROP TABLE IF EXISTS `{$param['addtable']}`;");
                    if($param['issystem'] != 1){
                        $tabsql = "CREATE TABLE `{$param['addtable']}`(
                            `aid` int(11) NOT NULL default '0',
                            `typeid` int(11) NOT NULL default '0',
                            `redirecturl` varchar(255) NOT NULL default '',
                            `templet` varchar(30) NOT NULL default '',
                            `userip` char(15) NOT NULL default '',";
                    }else{
                        $tabsql = "CREATE TABLE `{$param['addtable']}`(
                            `aid` int(11) NOT NULL default '0',
                            `typeid` int(11) NOT NULL default '0',
                            `channel` SMALLINT NOT NULL DEFAULT '0',
                            `arcrank` SMALLINT NOT NULL DEFAULT '0',
                            `mid` MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT '0',
                            `click` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0',
                            `title` varchar(60) NOT NULL default '',
                            `senddate` int(11) NOT NULL default '0',
                            `flag` set('c','h','p','f','s','j','a','b') default NULL,
                            `litpic` varchar(60) NOT NULL default '',
                            `userip` char(15) NOT NULL default '',
                            `lastpost` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0',
                            `scores` MEDIUMINT( 8 ) NOT NULL DEFAULT '0',
                            `goodpost` MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT '0',
                            `badpost` MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT '0',";
                    }

                    if($mysql_version < 4.1){
                        $tabsql .= "    PRIMARY KEY  (`aid`), KEY `typeid` (`typeid`)\r\n) TYPE=MyISAM; ";
                    }else{
                        $tabsql .= "    PRIMARY KEY  (`aid`), KEY `typeid` (`typeid`)\r\n) ENGINE=MyISAM DEFAULT CHARSET=".$charset."; ";
                    }

                    Db::query($tabsql);
                }
            }
            $listfields = $fieldset = '';
            $state = Channeltype::insert(array(
                'id'          => $param['id'],
                'nid'         => $param['nid'],
                'typename'    => $param['typename'],
                'addtable'    => $param['addtable'],
                'addcon'      => $param['addcon'],
                'mancon'      => $param['mancon'],
                'editcon'     => $param['editcon'],
                'useraddcon'  => $param['useraddcon'],
                'usermancon'  => $param['usermancon'],
                'usereditcon' => $param['usereditcon'],
                'fieldset'    => $fieldset,
                'listfields'  => $listfields,
                'issystem'    => $param['issystem'],
                'issend'      => $param['issend'],
                'arcsta'      => $param['arcsta'],
                'usertype'    => $param['usertype'],
                'sendrank'    => $param['sendrank'],
                'needdes'     => $param['needdes'],
                'needpic'     => $param['needpic'],
                'titlename'   => $param['titlename'],
                'onlyone'     => $param['onlyone'],
                'dfcid'       => $param['dfcid'],
            ));

            if($state !== false){
                return $this->success('提交成功', (string)url('index'));
            }
            return $this->error('提交失败');
            exit();
        }
        $row = ChanneltypeModel::where(array())->order('id desc')->find();
        $newid = $row['id'] + 1;
        if($newid < 10) $newid = $newid+10;
        View::assign('cfg_dbprefix', Config::get('database.connections')['mysql']['prefix'] );
        View::assign('newid', $newid);
        $usertype = Session::get(config('app.uid_key'))->usertype;

        $ArcrankAll = ArcrankModel::where("adminrank<='$usertype' And rank>=10")->select();
        $MemberModelAll = MemberModel::where("")->select();
        View::assign('ArcrankAll', $ArcrankAll);
        View::assign('MemberModelAll', $MemberModelAll);
        View::assign('nav', array(
            array('title'=>'核心'),
            array('title'=>'内容模型管理', 'url'=>(string)url('MyChannelMain/index')),
            array('title'=>'新建-内容模型'),
        ));
        return \view();
    }

    public function mychannel_delete()
    {
        if(Request::isGet()){
            $param = Request::param('');
            $state = Channeltype::where("id=".$param['id'])->delete();
            if($state !== false){
                return $this->success("删除成功", (string)url('index'));
            }
            return $this->error("删除失败");
            exit();
        }
    }

    /**
     * [内容模型-禁用]
     * User: yesheng35@126.com
     * DateTime 2021/11/23 22:06
     */
    public function mychannel_edit_hide()
    {
        $id = Request::param('id');
        $state = Channeltype::update(['isshow'=>0], ['id'=>$id]);
        if($state !== false){
            return $this->success('修改成功', (string)url('index'));
        }
        return $this->error("修改失败");

    }

    /**
     * [内容模型-启用]
     * User: yesheng35@126.com
     * DateTime 2021/11/23 22:07
     */
    public function mychannel_edit_show()
    {
        $id = Request::param('id');
        $state = Channeltype::update(['isshow'=>1], ['id'=>$id]);
        if($state !== false){
            return $this->success('修改成功', (string)url('index'));
        }
        return $this->error("修改失败");

    }

}

<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use app\Admin\model\Channeltype;
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
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $data = ChanneltypeModel::where(array())->select()->toArray();
        View::assign('_data', $data);
        return View::fetch();
    }


    public function mychannel_edit()
    {
        if(Request::isPost()){
            $param = Request::param('');
            $exist = Db::query("show tables like '".$param['addtable']."'");
            if(!$exist){
                return $this->error("系统找不到你所指定的表 {$param['addtable']} ，请手工创建这个表！");
            }

            $state = Channeltype::update(array(
                'typename'    => $param['typename'],
                'addtable'    => $param['addtable'],
                'addcon'      => $param['addcon'],
                'mancon'      => $param['mancon'],
                'editcon'     => $param['editcon'],
                'useraddcon'  => $param['useraddcon'],
                'usermancon'  => $param['usermancon'],
                'usereditcon' => $param['usereditcon'],
                'fieldset'    => $param['fieldset'],
                'listfields'  => $param['listfields'],
                'issend'      => $param['issend'],
                'arcsta'      => $param['arcsta'],
                'usertype'    => $param['usertype'],
                'sendrank'    => $param['sendrank'],
                'needdes'     => $param['needdes'],
                'needpic'     => $param['needpic'],
                'titlename'   => $param['titlename'],
                'onlyone'     => $param['onlyone'],
                'dfcid'       => $param['dfcid'],
            ), array(
                'id' => $param['id']
            ));

            if($state !== false){
                return $this->success('提交成功', (string)url('index'));
            }
            return $this->error("提交失败");

            exit();
        }

        $request = $this->request;
        $id = $request->param('id');
        $dopost = $request->param('dopost');
        $data = ChanneltypeModel::where(array('id'=>$id))->find();
        View::assign('data', $data);
        $ArcrankAll = ArcrankModel::where("rank>=10")->select()->toArray();
        View::assign('ArcrankAll', $ArcrankAll);
        $MemberModelAll = MemberModel::where('')->select()->toArray();
        View::assign('MemberModelAll', $MemberModelAll);
        View::assign('id', $id);
        return View::fetch();
    }

    public function mychannel_field_add()
    {
        $id = Request::param('id');
        View::assign('id', $id);
        return View::fetch();
    }

    public function mychannel_add()
    {
        if(Request::isPost()){
            $param          = Request::param('');
            $version        = Db::query("SELECT VERSION() as version;");
            $mysql_versions = explode(".", trim($version[0]['version']));
            $mysql_version  = number_format((float)($mysql_versions[0] . "." . $mysql_versions[1]), 2);
            $charset        = Config::get('database.connections.mysql.charset');

            $count = Channeltype::where("id=" . $param['id'] . " or nid like '{$param['nid']}' or addtable like '{$param['addtable']}' ")->count();
            if($count >= 1){
                return $this->error('可能‘频道id’、‘频道名称标识’、‘附加表名称’在数据库已存在，不能重复使用！');
            }

            if($param['addtable'] != ''){
                $exist = Db::query("show tables like '".$param['addtable']."'");

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
        $usertype = Session::get('AdminUser')->usertype;

        $ArcrankAll = ArcrankModel::where("adminrank<='$usertype' And rank>=10")->select();
        View::assign('ArcrankAll', $ArcrankAll);
        $MemberModelAll = MemberModel::where("")->select();
        View::assign('MemberModelAll', $MemberModelAll);
        return View::fetch();
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

}

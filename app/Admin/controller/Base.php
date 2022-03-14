<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use app\Admin\model\Archives;
use app\Admin\model\Arctype;
use app\Admin\model\Channeltype;
use app\Admin\model\Channeltype as ChanneltypeModel;
use app\Admin\model\Flink;
use app\Admin\model\Sysconfig;
use app\BaseController;
use think\App;
use think\Request;
use think\facade\View;
use think\template\driver\File;
use think\facade\Session;
use liliuwei\think\Jump;
use think\Validate;


class Base extends BaseController
{

    public function initialize()
    {
        $this->isLogin();
        View::assign('_nav_this', Request()->controller().'_'.Request()->action());
        View::assign('nav', array());
        View::assign('_nav_itemed', '');
    }

    protected function isLogin()
    {
        $stat = Session::has(config('app.uid_key'));
        if(!$stat){
            redirect( (string)url('Login/index') )->send();
        }
        $user = Session::get(config('app.uid_key'));
        View::assign('user', $user);
    }

    /**
     * @author Dave 178698695@qq.com
     * @param string $htmlfile  新文件名
     * @param string $htmlpath  存放路径
     * @param string $templateFile  模版
     * @return string
     * @throws \Exception
     * $this->buildHtml('index', HTTP_PATH."index/", $tempSrc);
     * $this->buildHtml('index32', "h5/", 'tags_main\index');
     */
    public function buildHtml($htmlfile='', $htmlpath='', $templateFile)
    {
        $content = View::fetch($templateFile);
        $htmlpath = !empty($htmlpath) ? $htmlpath : './appTemplate';
        $htmlfile = $htmlpath . $htmlfile . '.' . config('view.view_suffix');
        $File = new File();
        $File->write($htmlfile, $content);
        return $content;
    }

    protected function ViewAll(){
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


        //常见问题
        $arclist3 = Archives::where("typeid=3")->limit(6)->select();
        View::assign('arclist3', $arclist3);

        $flink = Flink::where("")->select();
        View::assign('flink', $flink);
    }






}

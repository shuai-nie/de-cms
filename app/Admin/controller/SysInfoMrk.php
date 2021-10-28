<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use think\facade\Request;
use think\facade\View;

/**
 * [水印设置]
 * Class SysInfoMrk
 * @package app\Admin\controller
 */
class SysInfoMrk extends Base
{

    public function initialize()
    {
        parent::initialize();
        View::assign('nav', array(
            array('title'=>'系统', 'url'=>''),
            array('title'=>'图片水印设置', 'url'=>''),
        ));
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $ImageWaterConfigFile = "storage/mark/inc_photowatermark_config.php";
        if(Request::isPost()){
            $param = Request::param('');

            $vars = array(
                'photo_markup', 'photo_markdown', 'photo_marktype', 'photo_wwidth',
                'photo_wheight', 'photo_waterpos', 'photo_watertext', 'photo_fontsize',
                'photo_fontcolor', 'photo_marktrans', 'photo_diaphaneity'
            );
            $configstr = $shortname = "";

            foreach($vars as $v)
            {
                $$v = str_replace("'", "", $param["get_$v"] );
                $configstr .= "\${$v} = '".${$v}."';\r\n";
            }

            $configstr .= "\$photo_markimg = '{$param["get_photo_markimg"]}';\r\n";
            $configstr = "<"."?php\r\n".$configstr."?".">\r\n";
            $fp = fopen($ImageWaterConfigFile,"w") or die("写入文件 $ImageWaterConfigFile 失败，请检查权限！");
            fwrite($fp,$configstr);
            fclose($fp);
            $this->success('修改成功', (string)url('index'));
        }
        View::assign('ImageWaterConfigFile', $ImageWaterConfigFile);
        return View::fetch();
    }



}

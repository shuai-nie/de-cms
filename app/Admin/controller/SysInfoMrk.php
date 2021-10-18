<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use think\Request;
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


        return View::fetch();
    }

    public function dd()
    {
        $allow_mark_types = array(
            'image/gif',
            'image/xpng',
            'image/png',
        );

        $ImageWaterConfigFile = "/mark/inc_photowatermark_config.php";
        $vars = array('photo_markup','photo_markdown','photo_marktype','photo_wwidth','photo_wheight','photo_waterpos','photo_watertext','photo_fontsize','photo_fontcolor','photo_marktrans','photo_diaphaneity');
        $configstr = $shortname = "";
        foreach($vars as $v)
        {
            ${$v} = str_replace("'", "", ${'get_'.$v});
            $configstr .= "\${$v} = '".${$v}."';\r\n";
        }
        if(is_uploaded_file($newimg))
        {
            $imgfile_type = strtolower(trim($newimg_type));

            if(!in_array($imgfile_type, $allow_mark_types))
            {
                ShowMsg("上传的图片格式错误，请使用 gif、png格式的其中一种！","-1");
                exit();
            }
            if($imgfile_type=='image/xpng' || $imgfile_type=='image/png')
            {
                $shortname = ".png";
            }
            else if($imgfile_type=='image/gif')
            {
                $shortname = ".gif";
            }
            else
            {
                ShowMsg("水印图片仅支持gif、png格式的其中一种！","-1");
                exit;
            }
            $photo_markimg = 'mark'.$shortname;
            @move_uploaded_file($newimg,DEDEDATA."/mark/".$photo_markimg);
        }
        $configstr .= "\$photo_markimg = '{$photo_markimg}';\r\n";
        $configstr = "<"."?php\r\n".$configstr."?".">\r\n";
        $fp = fopen($ImageWaterConfigFile,"w") or die("写入文件 $ImageWaterConfigFile 失败，请检查权限！");
        fwrite($fp, $configstr);
        fclose($fp);
        echo "<script>alert('修改配置成功！');</script>\r\n";
    }

}

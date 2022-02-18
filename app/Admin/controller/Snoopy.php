<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use think\Request;
use Snoopy\Snoopy as SnoopyClass;

class Snoopy
{
    /**
     * 显示资源列表
     * @return \think\Response
     */
    public function index()
    {
        $snoopy = new SnoopyClass();
        $snoopy = new \Snoopy\Snoopy();
        $url = "https://www.milu.com/sysjttwzc/";
        $snoopy->fetch($url);
        $results = $snoopy->results;

        $regex4 = "/<div class=\"mainLeft globalSectionContainer\".*?>.*?<\/div>/ism";

        preg_match_all($regex4, $results, $matches);
        var_dump($matches);
        exit();

        $preg = '/<a .*?href="(.*?)".*?>/is';
        preg_match_all($preg, $str, $array2);
        for ($i = 0; $i < count($array2[1]); $i++) {
            echo $array2[1][$i] . "<br />";
        }

        /*$pattern = "/[img|IMG].*?src=['|\"](.*?(?:[.gif|.jpg]))['|\"].*?[\/]?>/";
        preg_match_all($pattern, $results, $match);
        var_dump($match[1]);*/





    }


    public function cc()
    {
        $imgUrl = "https://static.app.985sy.com/attachment/syapp/logo/202202091644376607.jpg";
        // 文件保存目录路径
        $save_path = $_SERVER ['DOCUMENT_ROOT'] . '/storage/image/';
        // 文件保存目录URL
        $save_url = '/storage/image/';
        $save_path = realpath ( $save_path ) . '/';
        // 图片存储目录
        $imgPath = $save_path . date( "Ymd" );
        $saveUrl = $save_url . date( "Ymd" );
        // 创建文件夹
        if (! is_dir ( $imgPath )) {
            @mkdir ( $imgPath, 0777 );
        }
        set_time_limit ( 0 );
        $value = trim ( $imgUrl );
        // 读取远程图片
        $get_file = @file_get_contents ( $value );
        // 保存到本地图片名称
        $imgname = date ( "YmdHis" ) . '_' . rand ( 10000, 99999 ) . "." . substr ( $value, - 3, 3 );
        // 保存到本地的实际文件地址（包含路径和名称）
        $fileName = $imgPath . '/' . $imgname;
        // 文件写入
        if ($get_file) {
            $fp = @fopen ( $fileName, "w" );
            @fwrite ( $fp, $get_file );
            @fclose ( $fp );
        }
        return json(['imgurl' => $imgUrl, 'url' => $this->get_http_type() . $_SERVER['HTTP_HOST'] . $saveUrl . '/' . $imgname]);
    }


    public function get_http_type()
    {
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        return $http_type;
    }




}

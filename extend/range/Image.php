<?php
namespace range;

/**
 * 远程图片下载到本地
 * Class Image
 * @author Lucius yesheng35@126.com
 * @package range
 */
class Image {

    /**
     * @param $imgUrl
     * @return \think\response\Json
     * @author Lucius yesheng35@126.com
     */
    public function RangeImage($imgUrl)
    {
        //$imgUrl = "https://static.app.985sy.com/attachment/syapp/logo/202202091644376607.jpg";
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
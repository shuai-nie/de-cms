<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use range\Image;
use think\Request;
use Snoopy\Snoopy as SnoopyClass;
use voku\helper\HtmlDomParser;

class Snoopy
{
    /**
     * 显示资源列表
     * @return \think\Response
     */
    public function index()
    {
        $snoopy = new SnoopyClass();
        //$snoopy = new \Snoopy\Snoopy();
        $url = "https://www.milu.com/sysjttwzc/";
        $snoopy->expandlinks = true;
        $snoopy->fetch($url);
        $results = $snoopy->results;
        $regex4 = "/<div class=\"mainLeft globalSectionContainer\".*?>.*?<div class=\"mainRight globalSectionContainer\">/ism";
        preg_match($regex4, $results, $matches);

        $pattern = "/[img|IMG].*?src=['|\"](.*?(?:[.gif|.jpg]))['|\"].*?[\/]?>/";
        preg_match_all($pattern, $matches[0], $match);
        $matche = $matches[0];
        foreach ($match[1] as $k => $v){
            if(strpos($v, 'http') !== false ) {
                $d = (new Image())->RangeImage($v);
                if($d) {
                    // 新图片 替换 原图片
                    $matche = str_replace($d['imgurl'], $d['url'], $matche);
                }
            }
        }

//        $dom = HtmlDomParser::file_get_html($matche);
//        $name = $dom->findOne('.name');
//        var_dump( $name);
//        $info = $dom->findOne('.info');
//        var_dump($info);
//
//        $i2 = '<div class="gameDetail">([\s\S]*?(<div[^>]*>((?1)|[\s\S])*<\/div>)*[\s\S]*?)*<\/div>';
//        $matches2 = '';
//        preg_match_all($i2, $matche, $matches2);
//        var_dump($matches2);

        var_dump($this->get_tag_data($matche, 'div', 'class', 'name')[0]);

        var_dump($this->get_tag_data($matche, 'div', 'id', 'info'));
        $length = strpos($matche, '评价');
        $info = substr( $matche, 0, $length-150);
        var_dump($info);
      //  var_dump($this->getTagValue($matche, 'div'));
//        var_dump($matche);


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

    /**
     * @param $string 需要匹配的字符串
     * @param $tag html标签
     */
    public function getTagValue($string, $tag){
        $pattern = "/<{$tag}>(.*?)<\/{$tag}>/s";
        preg_match_all($pattern, $string, $matches);
        return isset($matches[1]) ? $matches[1] : '';
    }

    /**
     * @param $html
     * @param $tag
     * @param $class
     * @param $value
     * @return mixed
     * @author Lucius yesheng35@126.com
     * get_tag_data($temp,"div","class","num");
     */
    public function get_tag_data($html, $tag, $class, $value)
    {
        //$value 为空，则获取class=$class的所有内容
        $regex = $value ? "/<$tag.*?$class=\"$value\".*?>(.*?)<\/$tag>/is" : "/<$tag.*?$class=\".*?$value.*?\".*?>(.*?)<\/$tag>/is";
        preg_match_all($regex, $html, $matches, PREG_PATTERN_ORDER);
        return $matches[1];//返回值为数组 ,查找到的标签内的内容
    }

    /**
     * @param $html
     * @param $rule
     * @return string
     */
    public function processing($html, $rule)
    {
        $outHtml = "";
        $pattern = '])>(.*)])>/U'; // 0为带标签的数据 1前标签 2为不带标签的文本内容 3后标签

        preg_match_all($pattern, $html, $data);
        foreach($data[3] as $k => $v){
            if(isset($rule[$v])){
                $len = mb_strlen($data[2][$k], 'utf8');
                if($len > $rule[$v]){
                    $start = 0;
                    $end = $len;
                    do{
                        $subText = mb_substr($data[2][$k], $start, $rule[$v], 'UTF-8');
                        $outHtml .= "{$subText}{$data[3][$k]}>";
                        $len -= $rule[$v];
                        $start += $rule[$v];
                        if($len<0) {
                            $len = 0;
                            $start = $end;
                        }
                    }while($len);
                    continue;
                }
            }
            $outHtml .= $data[0][$k];
        }
        return $outHtml;
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
        return [
            'imgurl' => $imgUrl,
            'url'    => $this->get_http_type() . $_SERVER['HTTP_HOST'] . $saveUrl . '/' . $imgname,
        ];
    }


    public function get_http_type()
    {
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        return $http_type;
    }




}

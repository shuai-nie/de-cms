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




}

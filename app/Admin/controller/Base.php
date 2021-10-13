<?php
declare (strict_types = 1);

namespace app\Admin\controller;

use app\BaseController;
use think\App;
use think\Request;
use think\facade\View;
use think\template\driver\File;

class Base extends BaseController
{
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;

        self::initialize();

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




}

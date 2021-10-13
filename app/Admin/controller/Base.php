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
     * @param string $htmlfile
     * @param string $htmlpath
     * @param $templateFile
     * @return string
     * @throws \Exception
     */
    public function buildHtml($htmlfile='', $htmlpath='', $templateFile)
    {
        $content = View::fetch($templateFile);
        $htmlpath = !empty($htmlpath) ? $htmlpath : './appTemplate';
        $htmlfile = $htmlpath . $htmlfile . '.' . config('view.view_suffi');
        $File = new File();
        $File->write($htmlfile, $content);
        return $content;
    }




}

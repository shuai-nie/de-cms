<?php
// +----------------------------------------------------------------------
// | 应用设置
// +----------------------------------------------------------------------

return [
    // 应用地址
    'app_host'         => env('app.host', ''),
    // 应用的命名空间
    'app_namespace'    => '',
    // 是否启用路由
    'with_route'       => false,
    // 默认应用
    'default_app'      => 'Admin',
    // 默认时区
    'default_timezone' => 'Asia/Shanghai',

    // 应用映射（自动多应用模式有效）
    'app_map'          => [
//        'admin' => 'Admin'
    ],
    // 域名绑定（自动多应用模式有效）
    'domain_bind'      => [],
    // 禁止URL访问的应用列表（自动多应用模式有效）
    'deny_app_list'    => [],

    // 异常页面的模板文件
    'exception_tmpl'   => app()->getThinkPath() . 'tpl/think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message'    => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'   => false,

    'cfg_cookie_encode' => '7g2vbkvLtFtT6r51Y2r5HwWYTC7q',
    'auto_multi_app'   => true,
    'cfg_remote_site' => 'N',
    'cfg_need_typeid2' => 'N',
    'cfg_arc_autokeyword' => 'Y',
    'cfg_rm_remote' => 'Y',
    'cfg_arc_dellink' => 'N',
    'cfg_arc_autopic' => 'Y',
    'photo_markup' => '0',
    'cfg_arcautosp' => 'N',
    'cfg_arcautosp_size'=> 5,
    'cfg_feedback_forbid'=> 'N',
    'cfg_arc_click' => -1,

    'cfg_tplcache_dir' => '/data/tplcache',
    'cfg_tplcache' => 'Y',
    'cfg_df_style' => 'default',
    'cfg_disable_funs' => 'phpinfo,eval,exec,passthru,shell_exec,system,proc_open,popen,curl_exec,curl_multi_exec,parse_ini_file,show_source,file_put_contents',




];

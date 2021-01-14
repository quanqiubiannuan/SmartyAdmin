<?php

namespace application\admin\controller;

class Index extends Backend
{
    /**
     * 后台首页
     */
    public function home()
    {
        $this->assign('mysmartyVersion',MYSMARTY_VERSION);
        $this->assign('phpVersion',PHP_VERSION);
        $this->assign('iniPath',php_ini_loaded_file());
        $this->assign('smartyAdminVersion',config('app.smarty_admin_version'));
        $this->display();
    }
}
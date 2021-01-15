<?php

namespace application\admin\controller;

use application\admin\model\LoginLog;
use library\mysmarty\Route;

#[Route('/admin')]
class Index extends Backend
{
    /**
     * 后台首页
     */
    public function home()
    {
        $this->assign('mysmartyVersion', MYSMARTY_VERSION);
        $this->assign('phpVersion', PHP_VERSION);
        $this->assign('iniPath', php_ini_loaded_file());
        $this->assign('smartyAdminVersion', config('app.smarty_admin_version'));
        $loginLog = new LoginLog();
        $logs = $loginLog->eq('admin_id', $this->smartyAdmin['id'])
            ->order('id', 'desc')
            ->eq('status', 1)
            ->field('ip,create_time')
            ->limit(10)
            ->select();
        $this->assign('logs', $logs);
        $this->assign('extensions', implode('<br>', get_loaded_extensions()));
        $this->display();
    }

    /**
     * 输出PHP信息
     */
    public function phpinfo()
    {
        phpinfo();
    }
}
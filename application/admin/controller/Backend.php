<?php

namespace application\admin\controller;

use library\mysmarty\Controller;

/**
 * 后台基础控制器
 * @package application\admin\controller
 */
class Backend extends Controller
{
    // 关闭缓存
    protected bool $myCache = false;
    // 当前登录用户信息
    protected array $smartyAdmin;

    public function __construct()
    {
        parent::__construct();
        // 未登录跳转到登录页面
        $smartyAdmin = getSession('smartyAdmin');
        if (empty($smartyAdmin)) {
            redirect('/admin/admin/login');
        }
        $this->smartyAdmin = $smartyAdmin;
    }
}
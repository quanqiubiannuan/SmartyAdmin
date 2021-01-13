<?php

namespace application\admin\controller;

use library\mysmarty\Controller;

/**
 * 后台基础控制器
 * @package application\admin\controller
 */
class Backend extends Controller
{
    public function __construct()
    {
        parent::__construct();
        // 未登录跳转到登录页面
        if (empty(getSession('smartyAdmin'))) {
            redirect('/admin/admin/login');
        }
    }
}
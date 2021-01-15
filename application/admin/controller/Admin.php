<?php

namespace application\admin\controller;

use application\admin\model\LoginLog;
use library\mysmarty\Captcha;
use library\mysmarty\Controller;
use library\mysmarty\Route;

class Admin extends Controller
{
    // 关闭缓存
    protected bool $myCache = false;

    /**
     * 用户登录
     */
    #[Route('/login')]
    public function login()
    {
        deleteSession(config('app.smarty_admin_session', 'smartyAdmin'));
        if (isPost()) {
            $code = getPostString('code');
            if (empty($code)) {
                $this->error('验证码不能为空');
            }
            $name = getPostString('name');
            if (empty($name)) {
                $this->error('账号不能为空');
            }
            $password = getPostString('password');
            if (empty($password)) {
                $this->error('密码不能为空');
            }
            if (!Captcha::check($code)) {
                $this->error('验证码错误');
            }
            $loginLog = new LoginLog();
            // 判断账号当前60分钟内的失败次数
            $startTime = date('Y-m-d H:i:s', time() - 3600);
            if ($loginLog->eq('ip', getIp())
                    ->egt('create_time', $startTime)
                    ->count() >= 3) {
                $this->error('登录失败');
            }
            $admin = (new \application\admin\model\Admin())->eq('name', $name)->find();
            if (empty($admin)) {
                $this->error('账号或密码错误');
            }
            if (!password_verify($password, $admin['password'])) {
                $loginLog->addLoginLog($admin['id'], 2);
                $this->error('账号或密码错误');
            }
            if (1 != $admin['status']) {
                $loginLog->addLoginLog($admin['id'], 2);
                $this->error('账号已停用');
            }
            $loginLog->addLoginLog($admin['id'], 1);
            unset($admin['password']);
            setSession(config('app.smarty_admin_session', 'smartyAdmin'), $admin);
            redirect('/');
        }
        $this->display();
    }

    /**
     * 验证码
     */
    #[Route('/captcha')]
    public function code()
    {
        // 输出验证码图片
        Captcha::code()->output();
    }
}
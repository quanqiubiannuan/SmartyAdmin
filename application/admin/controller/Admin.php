<?php

namespace application\admin\controller;

use application\admin\model\LoginLog;
use library\mysmarty\Route;
use library\mysmarty\Session;
use library\mysmarty\Upload;

#[Route('/admin')]
class Admin extends BackendCurd
{
    protected array $searchCondition = ['name', 'gender' => '='];
    protected array $joinCondition = ['auth_group', 'auth_group.id=admin.auth_group_id'];
    protected string $field = 'admin.*,auth_group.name as auth_group_name';
    protected bool $allowAddMethod = true;
    protected bool $allowEditMethod = true;
    protected bool $allowDeleteMethod = true;
    protected int $dataType = 3;

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

    /**
     * 个人资料
     */
    public function profile()
    {
        if ($this->isSuperAdmin) {
            $groupName = '超级管理员';
        } else {
            $groupName = $this->authGroup['name'];
        }
        $this->assign('groupName', $groupName);
        $this->display();
    }

    /**
     * 更新用户资料
     */
    public function updateProfile()
    {
        if (isPost()) {
            $updateData = [];
            $name = getPostString('name');
            if (empty($name)) {
                $this->error('用户名不能为空');
            }
            $updateData['name'] = $name;
            $password = getPostString('password');
            if (!empty($password)) {
                $len = mb_strlen($password, 'utf-8');
                if (preg_match('/[^a-z0-9]/i', $password) || $len < 6 || $len > 20) {
                    $this->error('密码由6-20位字母或数字组成');
                }
                $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
            }
            $gender = getPostString('gender');
            if (!in_array($gender, [1, 2])) {
                $this->error('性别错误');
            }
            $updateData['gender'] = $gender;
            $avatar = Upload::getInstance()->move('avatar');
            if (!empty($avatar)) {
                $updateData['avatar'] = $avatar;
            }
            $admin = new \application\admin\model\Admin();
            if ($admin->eq('id', $this->smartyAdmin['id'])->update($updateData)) {
                Session::getInstance()->clear();
                $this->success('更新成功', '/login');
            }
            $this->error('更新失败');
        }
        $this->display();
    }

    /**
     * 添加
     */
    public function add()
    {
        $authGroups = $this->getLevelAuthGroup();
        if (isPost()) {
            $data = $_POST;
            $validate = new \application\admin\validate\Admin();
            if ($validate->scene('add')->check($data) === false) {
                $this->error($validate->getError());
            }
            if (!$this->isSuperAdmin && !in_array($data['auth_group_id'], array_column($authGroups, 'id'))) {
                $this->error('您没有权限设置此角色组');
            }
            $avatar = Upload::getInstance()->move('avatar');
            if (empty($avatar)) {
                $avatar = '';
            }
            $data['avatar'] = $avatar;
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            $admin = new \application\admin\model\Admin();
            $num = $admin->allowField(true)->add($data);
            if ($num > 0) {
                $this->success('添加成功', getAbsoluteUrl() . '/' . $this->currentMenu['url']);
            }
            $this->error('添加失败');
        }
        $this->assign('authGroups', $authGroups);
        $this->display();
    }
}
<?php

namespace application\admin\controller;

use application\admin\model\AuthGroup;
use application\admin\model\AuthRule;
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
    protected array $smartyAdmin = [];
    // 菜单组
    protected array $smartyMenu = [];
    // 分组
    protected array $authGroup = [];
    // 菜单规则
    protected array $authRule = [];
    // 是否为超级管理员
    protected bool $isSuperAdmin = false;
    // 当前访问路径
    protected string $currentPath = '';
    // 当前访问的菜单
    protected array $currentMenu = [
        'name' => '未知',
        'icon' => 'fas fa-exclamation-triangle'
    ];

    public function __construct()
    {
        parent::__construct();
        // 未登录跳转到登录页面
        $smartyAdmin = getSession(config('app.smarty_admin_session', 'smartyAdmin'));
        if (empty($smartyAdmin)) {
            redirect('/login');
        }
        // 初始化当前登录用户信息
        $this->smartyAdmin = $smartyAdmin;
        if (0 === (int)$this->smartyAdmin['auth_group_id']) {
            $this->isSuperAdmin = true;
        }
        // 初始化当前用户组
        if (!$this->isSuperAdmin) {
            $authGroup = new AuthGroup();
            $this->authGroup = $authGroup->eq('id', $this->smartyAdmin['auth_group_id'])
                ->eq('status', 1)
                ->find();
        }
        // 当前访问路径
        $currentPath = getPath();
        if (empty($currentPath)) {
            $currentPath = ROUTE['home']['uri'];
        }
        $this->currentPath = $currentPath;
        // 初始化菜单
        $this->smartyMenu = $this->getSmartyMenu();
        if (empty($this->smartyMenu)) {
            $this->error('您无权访问此页面');
        }
        // 判断用户是否有权限
        if (!$this->isSuperAdmin) {
            if (!in_array($this->currentPath, array_column($this->authRule, 'url'), true)) {
                $this->error('您无权访问此页面');
            }
        }
        $this->assign('smartyMenu', $this->smartyMenu);
        $this->assign('currentMenu', $this->currentMenu);
        $this->assign('smartyAdmin', $this->smartyAdmin);
    }

    /**
     * 获取当前用户所有的权限菜单规则
     * @return array
     */
    private function getSmartyMenu(): array
    {
        $smartyMenu = [];
        $authRule = new AuthRule();
        if ($this->isSuperAdmin) {
            // 超级管理员
            $authRuleData = $authRule->field('id,url,name,icon,pid')
                ->order('sort_num', 'asc')
                ->eq('status', 1)
                ->eq('is_menu', 1)
                ->select();
        } else {
            if (!empty($this->authGroup)) {
                // 分组用户
                $authRuleData = $authRule->in('id', $this->authGroup['rules'])
                    ->field('id,url,name,icon,pid')
                    ->order('sort_num', 'asc')
                    ->eq('status', 1)
                    ->eq('is_menu', 1)
                    ->select();
            } else {
                // 其它用户
                return [];
            }
        }
        if (!empty($authRuleData)) {
            $this->authRule = $authRuleData;
            $newAuthRuleData = [];
            foreach ($authRuleData as $v) {
                if ($this->currentPath === $v['url']) {
                    $this->currentMenu = $v;
                    $v['active'] = true;
                } else {
                    $v['active'] = false;
                }
                $newAuthRuleData[$v['pid']][] = $v;
            }
            $smartyMenu = $this->generateTree($newAuthRuleData, $newAuthRuleData[0]);
        }
        return $smartyMenu;
    }

    /**
     * 将数组数据转为树形结构
     * @param array $list 原数组数据
     * @param array $parent 顶级原数组数据
     * @return array
     */
    private function generateTree(array &$list, array $parent): array
    {
        $tree = [];
        foreach ($parent as $k => $v) {
            if (isset($list[$v['id']])) {
                $v['expanded'] = false;
                if (!empty($this->currentMenu['pid']) && ($this->currentMenu['pid'] === $v['id'])) {
                    $v['expanded'] = true;
                }
                $v['children'] = $this->generateTree($list, $list[$v['id']]);
            }
            $tree[] = $v;
        }
        return $tree;
    }
}
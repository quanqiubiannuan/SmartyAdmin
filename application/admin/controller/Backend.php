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
    protected array $smartyMenu = [];
    protected array $authGroup = [];

    public function __construct()
    {
        parent::__construct();
        // 未登录跳转到登录页面
        $smartyAdmin = getSession('smartyAdmin');
        if (empty($smartyAdmin)) {
            redirect('/admin/admin/login');
        }
        // 初始化当前登录用户信息
        $this->smartyAdmin = $smartyAdmin;
        // 初始化当前用户组
        $authGroup = new AuthGroup();
        if ($this->smartyAdmin['auth_group_id'] != 0) {
            $this->authGroup = $authGroup->eq('id', $this->smartyAdmin['auth_group_id'])
                ->eq('status', 1)
                ->find();
        }
        // 初始化菜单
        $this->smartyMenu = $this->getSmartyMenu();
    }

    /**
     * 获取当前用户所有的权限菜单规则
     * @return array
     */
    private function getSmartyMenu(): array
    {
        $smartyMenu = [];
        $authRule = new AuthRule();
        if (0 == $this->smartyAdmin['auth_group_id']) {
            // 超级管理员
            $authRuleData = $authRule->order('sort_num', 'asc')
                ->eq('status', 1)
                ->select();
        } else if (!empty($this->authGroup)) {
            // 分组用户
            $authRuleData = $authRule->in('id', $this->authGroup['rules'])
                ->order('sort_num', 'asc')
                ->eq('status', 1)
                ->select();
        } else {
            // 其它用户
            return [];
        }
        if (!empty($authRuleData)) {
            $newAuthRuleData = [];
            foreach ($authRuleData as $v) {
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
                $v['children'] = $this->generateTree($list, $list[$v['id']]);
            }
            $tree[] = $v;
        }
        return $tree;
    }
}
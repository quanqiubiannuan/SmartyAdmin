<?php

namespace application\admin\controller;

use library\mysmarty\Route;

#[Route('/auth_rule')]
class AuthRule extends BackendCurd
{
    protected int $dataType = 3;

    /**
     * 首页查询
     */
    public function index()
    {
        $list = $this->dealLevelData($this->getAuthRuleData('*', [1, 2]));
        $this->assign('list', $list);
        $this->display();
    }
}
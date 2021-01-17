<?php

namespace application\admin\controller;

use library\mysmarty\Route;

#[Route('/log')]
class LoginLog extends BackendCurd
{
    protected int $totalNum = 3;
    protected string $field = 'id';
    protected array $joinCondition = ['admin','admin.id=login_log.admin_id'];
}
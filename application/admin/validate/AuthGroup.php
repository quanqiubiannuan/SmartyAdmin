<?php

namespace application\admin\validate;

use library\mysmarty\Validate;

class AuthGroup extends Validate
{
    protected array $rule = [
        'name@角色名' => 'required',
        'status@状态' => 'required|integer|in:1,2',
    ];

    protected array $scene = [
        'add' => 'name',
        'edit' => 'name,status'
    ];
}

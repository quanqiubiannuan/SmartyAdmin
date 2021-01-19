<?php

namespace application\admin\validate;

use library\mysmarty\Validate;

class Admin extends Validate
{
    protected array $rule = [
        'id' => 'required|integer',
        'name@用户名' => 'required',
        'password@密码' => 'required|length:6,20|alphaNum',
        'gender@性别' => 'required|integer|in:1,2',
    ];

    protected array $scene = [
        'add' => 'name,password,gender',
        'edit' => 'id,name,password,gender'
    ];
}

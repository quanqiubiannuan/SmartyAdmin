<?php

namespace application\admin\validate;

use library\mysmarty\Validate;

class Admin extends Validate
{
    protected array $rule = [
        'id' => 'required|integer',
        'name@用户名' => 'required',
        'password@密码' => 'required',
        'avatar@头像' => 'required',
        'gender@性别' => 'required|integer|in:1,2',
    ];
}

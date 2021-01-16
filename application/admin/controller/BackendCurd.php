<?php

namespace application\admin\controller;

/**
 * 具有查询，添加，删除，编辑操作的权限控制类
 * @package application\admin\controller
 */
class BackendCurd extends Backend
{
    // 主表名
    protected string $table = '';
    // 查询的字段名
    protected string $field = '';
    // 主表主键名
    protected string $primaryKey = 'id';
    // 关联条件
    protected array $joinCondition = [];
    public function __construct()
    {
        parent::__construct();
        if (empty($this->table)) {
            $class = substr(static::class, strrpos(static::class, '\\') + 1);
            $this->table = toDivideName($class);
        }
    }

    /**
     * 列表
     */
    public function index()
    {
        var_dump($this->table);
        exit();
        $this->display();
    }
}
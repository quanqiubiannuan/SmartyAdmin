<?php

namespace application\admin\controller;

use library\mysmarty\Model;

/**
 * 具有查询，添加，删除，编辑操作的权限控制类
 * @package application\admin\controller
 */
class BackendCurd extends Backend
{
    // 主数据库名
    protected string $database = '';
    // 主表名
    protected string $table = '';
    // 查询的字段名
    protected string $field = '';
    // 主表主键名
    protected string $primaryKey = 'id';
    // 排序
    protected string $order = 'id desc';
    // 每页多少条数据
    protected int $size = 10;
    // 分页变量
    protected string $varPage = 'page';
    // 假的总统计数据，大于0的时候将不在统计真实的总数据，加快列表查询
    protected int $totalNum = 0;
    // 关联条件
    protected array $joinCondition = [];
    // 是否允许执行列表方法
    protected bool $allowIndexMethod = true;
    // 是否允许执行添加方法
    protected bool $allowAddMethod = false;
    // 是否允许执行编辑方法
    protected bool $allowEditMethod = false;
    // 是否允许执行删除方法
    protected bool $allowDeleteMethod = false;

    public function __construct()
    {
        parent::__construct();
        if (empty($this->database)) {
            $this->database = config('database.mysql.database');
        }
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
        if (!$this->allowIndexMethod) {
            $this->error('您无权访问此页面');
        }
        // 处理查询字段
        if (empty($this->field)) {
            $this->field = '*';
        }
        $model = Model::getInstance()->setDatabase($this->database)
            ->setTable($this->table)
            ->field($this->field)
            ->order($this->order);
        // 处理关联查询
        if (!empty($this->joinCondition)) {
            $joinConditionNum = count($this->joinCondition);
            if ($joinConditionNum === count($this->joinCondition, COUNT_RECURSIVE)) {
                // 一维数组
                switch ($joinConditionNum) {
                    case 2:
                        // 使用left join
                        $model->leftJoin($this->joinCondition[0], $this->joinCondition[1]);
                        break;
                    case 3:
                        // 使用join
                        $model->join($this->joinCondition[0], $this->joinCondition[1], $this->joinCondition[2]);
                        break;
                }
            } else {
                // 二维数组

            }
        }
        if ($this->totalNum > 0) {
            $list = $model->paginateByCount($this->totalNum, $this->size, false, 5, $this->varPage);
        } else {
            $list = $model->paginate($this->size, false, 5, $this->varPage);
        }

        var_dump($list);
        exit();
        $this->display();
    }

    /**
     * 添加
     */
    public function add()
    {
        if (!$this->allowAddMethod) {
            $this->error('您无权访问此页面');
        }
    }

    /**
     * 编辑
     */
    public function edit()
    {
        if (!$this->allowEditMethod) {
            $this->error('您无权访问此页面');
        }
    }

    /**
     * 删除
     */
    public function delete()
    {
        if (!$this->allowDeleteMethod) {
            $this->error('您无权访问此页面');
        }
    }
}
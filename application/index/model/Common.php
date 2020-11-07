<?php

namespace app\index\model;

use think\Db;

trait Common
{

    /**
     * 获取列表
     * @param array $where
     * @param string $field
     * @param int $page
     * @param int $pageSize
     * @param string $orderBy
     * @param string $groupBy
     * @return array|false|mixed|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList($where = [], $field = '*', $page = 0, $pageSize = 0, $orderBy = '', $groupBy = '')
    {
        /**
         * @var Db $query
         */
        $query = self::field($field)->where($where);
        if (!empty($orderBy)) {
            $query = $query->order($orderBy);
        }
        if (!empty($groupBy)) {
            $query = $query->group($groupBy);
        }
        if ($page != 0 && $pageSize != 0) {
            $query = $query->limit($pageSize)->page($page);
        }
        return $query->select();

    }

    /**
     * 获取数据总和
     * @param array $where
     * @return float|int|string
     */
    public function getListCount($where = [],$groupBy=[])
    {
        $query = self::where($where);
        if (!empty($groupBy)) {
            $query = $query->group($groupBy);
        }
        return $query->count();
    }
}
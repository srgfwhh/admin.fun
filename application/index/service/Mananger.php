<?php

namespace app\index\service;

use app\index\model\AuthGroup;
use app\index\model\AuthRule;

class Mananger
{


    /**
     * 角色菜单
     * @param int $group_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function groupMenue(int $group_id)
    {
        $groupModel = new AuthGroup();
        $rules = $groupModel->where("id", $group_id)->value("rules");

        $ruleModel = new AuthRule();
        $menues = $ruleModel->where(["id" => ["in", $rules], 'status' => 1, 'is_menu' => 1])
            ->field("id,title,parentId,href,icon")
            ->order("sort asc")
            ->select();
        foreach ($menues as $k => $v){
            if (trim($v['href']) && !strpos($v['href'],'http://') && !strpos($v['href'],'https://')){
                $menues[$k]['href'] = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/' . $v['href'];//iframe后台打路由进入，会拼接相对地址然后就无限循环页面，索性给他加上http域名
            }
        }
        return $this->packageArray($menues->toArray());
    }

    /**
     * 所有节点,前端需要的形式
     * @return mixed
     * @throws \think\exception\DbException
     */
    public static function viewRules($scenes = '')
    {
        $ruleModel = new AuthRule();
        $viewRules = $ruleModel->order("sort asc")->select()->toArray();
        $count = count($viewRules);
        //设置根节点
        if ($scenes == 'spread'){
            $viewRules[$count]['id'] = 0;
            $viewRules[$count]['parentId'] = null;
            $viewRules[$count]['title'] = "根节点";
            $viewRules[$count]['status'] = 1;
        }
        //print_r($viewRules);die;
        foreach ($viewRules as $k => $v){//处理一下节点数据,变为前端需要的数据
            if ($v['status'] === 0) $viewRules[$k]['disabled'] = true;
            if ($scenes == 'spread') $viewRules[$k]['spread'] = true;
        }
        return $viewRules;
    }


    /**
     * 将数据变为树状结构
     * @param $list
     * @param string $pk
     * @param string $pid
     * @param string $child
     * @param int $root
     * @return array
     */
    public static function packageArray($list, $pk = 'id', $pid = 'parentId', $child = 'children', $root = 0)
    {

        // 创建Tree
        $tree = array();
        if (is_array($list)) {
            // 创建基于主键的数组引用
            $refer = array();
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] =& $list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[$pid];
                if ($root === $parentId) {
                    $tree[] =& $list[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent =& $refer[$parentId];
                        $parent[$child][] =& $list[$key];
                    }
                }

            }
        }
        return $tree;
    }

    /**
     * 树还原成列表
     * @param array $tree 原来的树
     * @param string $child 孩子节点的键
     * @param string $order 排序显示的键，一般是主键 升序排列
     * @param array $list 过渡用的中间数组，
     * @return array        返回排过序的列表数组
     */
    public static function UnsealArray($tree, $child = 'children', $order = 'id', &$list = array())
    {
        if (is_array($tree)) {
            $refer = array();
            foreach ($tree as $key => $value) {
                $reffer = $value;
                if (isset($reffer[$child])) {
                    unset($reffer[$child]);
                    self::UnsealMenues($value[$child], $child, $order, $list);
                }
                $list[] = $reffer;
            }
        }
        return $list;
    }

}
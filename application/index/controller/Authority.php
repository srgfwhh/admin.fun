<?php


namespace app\index\controller;

use app\classes\helper\Register;
use app\index\service\Mananger;
use app\index\validate\RuleValidate;
use think\Db;
use think\exception\PDOException;

/**
 * 节点管理
 * Class Authority
 * @package app\index\controller
 */
class Authority extends Common
{

    public function index()
    {

        //得到前端形式的节点数组
        $viewRules = Mananger::viewRules('spread');
        $viewRules = Mananger::packageArray($viewRules, 'id', 'parentId', 'children', null);
        $viewRules = json_encode($viewRules);
        $this->assign("viewRules", $viewRules);
        return $this->fetch();
    }

    //设置节点
    public function setRule()
    {
        $validate = new RuleValidate();
        $params = request()->param();
        if (isset($params['id'])) {
            $rule = "editRule";
        } else {
            $rule = "addRule";
        }
        $result = $validate->scene($rule)->check($params);
        if (!$result) return errJson($validate->getError());

        $params['href'] = trim(strtolower($params['href']));
        $ruleModel = Register::get("rule");
        try {
            if (isset($params['id'])) {
                $field = ['href', 'title', 'icon', 'status', 'is_menu'];
                if ($params['sort']) $field[] = 'sort';
                $result = $ruleModel->allowField($field)->save($params, ['id' => $params['id']]);
            } else {
                if (!$params['sort']) {
                    $parentId = isset($params['parentId']) ? $params['parentId'] : 0;
                    $params['sort'] = $ruleModel->where(['parentId' => $parentId])->max("sort") + 1;

                }
                $result = $ruleModel->allowField(true)->save($params);
            }
        } catch (PDOException $e) {
            if (strstr($e->getMessage(), 'title')) {
                return errJson("节点名称已经存在");
            } else {
                return errJson("未知异常");
            }
        }
        if ($result !== false) return sucJson();
        return errJson("设置失败");
    }


    //删除
    public function delRule()
    {
        $validate = new RuleValidate();
        $params = request()->param();
        $result = $validate->scene("delRule")->check($params);
        if (!$result) return errJson($validate->getError());
        $ruleModel = Register::get("rule");
        $id = $params['id'];
        $sonId = $ruleModel->where("parentId", $id)->value("id");
        if ($sonId) return errJson("删除失败，该节点还存在子节点");
        //删除节点同时删除权限
        $groupModel = Register::get('group');
        $groups = $groupModel->where("find_in_set({$id},rules)")->column('id,rules');

        Db::startTrans();
        try {
            $ruleModel->where("id", $id)->delete();
            foreach ($groups as $k => $v) {
                $v = explode(",", $v);
                if ($index = array_search($id, $v)) unset($v[$index]);
                $groups[$k] = implode(',', $v);
                $groupModel->where("id", $k)->setField("rules", $groups[$k]);
            }
            Db::commit();
            return sucJson();
        } catch (\Exception $e) {
            Db::rollback();
            return errJson("删除失败，异常：" . $e->getMessage());
        }
    }
}
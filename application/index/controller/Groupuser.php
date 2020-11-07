<?php


namespace app\index\controller;

use app\classes\helper\Register;
use app\index\service\Mananger;
use app\index\validate\GroupValidate;
use think\exception\PDOException;

/**
 * 用户组
 * Class GroupUser
 * @package app\index\controller
 */
class Groupuser extends Common
{
    //角色列表
    public function index()
    {
        $viewRules = Mananger::viewRules();
        $viewRules = Mananger::packageArray($viewRules);
        $viewRules = json_encode($viewRules);
        $this->assign("viewRules", $viewRules);
        return $this->fetch();
    }


    public function groups()
    {
        $groupModel = Register::get('group');
        $groupUsers = $groupModel->select()->toArray();
        $viewRules = Mananger::viewRules();
        $authModel = Register::get('rule');
        foreach ($groupUsers as $k => $group) {
            $rulesIds = explode(",", $group['rules']);
            $parentIds = $authModel->where(['parentId' => ['in', $rulesIds]])->column("parentId");
            $itemViewRules = $viewRules;
            foreach ($itemViewRules as $key => $rule) {

                if (in_array($rule['id'], $rulesIds) && !in_array($rule['id'], $parentIds)) {
                    $itemViewRules[$key]['checked'] = true;
                } else {
                    $itemViewRules[$key]['checked'] = false;
                }
            }

            $itemViewRules = Mananger::packageArray($itemViewRules);
            $groupUsers[$k]['viewRules'] = json_encode($itemViewRules);
        }
        $count = $groupModel->count();
        return sucJson($groupUsers, $count);
    }

    //修改角色
    public function setGroup()
    {
        $validate = new GroupValidate();
        $params = request()->param();
        if (isset($params['id'])) {
            $rule = "editGroup";
        } else {
            $rule = "addGroup";
        }
        $result = $validate->scene($rule)->check($params);
        if (!$result) return errJson($validate->getError());

        $ruleModel = Register::get("rule");
        $rules = $ruleModel->where(["id" => ['in', $params['rules']]])->column("id");
        $params['rules'] = implode(",", $rules);
        $groupModel = Register::get('group');
        $params['home_page'] = trim($params['home_page']);
        try {
            if (isset($params['id'])) {
                $result = $groupModel->allowField(['title', 'status', 'rules','home_page'])->save($params, ['id' => $params['id']]);
            } else {
                $params['nickname'] = trim($params['nickname']);
                $result = $groupModel->allowField(true)->save($params);
            }
        } catch (PDOException $e) {
            if (strstr($e->getMessage(), 'nickname')) {
                return errJson("角色别名重复，设置失败");
            } elseif (strstr($e->getMessage(), 'title')) {
                return errJson("角色名称重复，设置失败");
            } else {
                return errJson("未知异常");
            }
        }
        if ($result !== false) return sucJson();
        return errJson("设置失败");
    }

    public function delGroup()
    {
        $validate = new GroupValidate();
        $params = request()->param();
        $result = $validate->scene("delGroup")->check($params);
        if (!$result) return errJson($validate->getError());
        $nickname = Register::get("group")->where("id", $params['id'])->value("nickname");
        if ($nickname == "ADMIN") return errJson("无权限删除管理员");
        //查询角色有无用户
        $userModel = new \app\index\model\ManagerUser();
        $haveIn = $userModel->where("group_id", $params['id'])->find();
        if ($haveIn) return errJson("删除失败，角色下存在用户");
        $result = Register::get("group")->where("id", $params['id'])->delete();
        if ($result !== false) return sucJson();
        return errJson("删除失败");
    }
}
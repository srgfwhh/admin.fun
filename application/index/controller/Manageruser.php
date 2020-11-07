<?php


namespace app\index\controller;

//后台用户
use app\classes\helper\Register;
use app\index\validate\UserValidate;

class Manageruser extends Common
{
    //管理员列表
    public function index()
    {
        //查询角色
        $groupModel = Register::get('group');
        $groups = $groupModel->field("id,title,nickname")->select()->toArray();

        $map = array_column($groups, null, 'nickname');
        $userModel = new \app\index\model\ManagerUser();
        $oms = $userModel->where("group_id", $map['OM']['id'])->field("id,nickname")->select();
        $this->assign("oms", $oms);
        $this->assign("groups", $groups);
        return $this->fetch();
    }

    //用户列表
    public function users()
    {
        $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $limit = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 10;
        $keyword = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : '';
        $group_id = isset($_REQUEST['group_id']) ? $_REQUEST['group_id'] : 0;
        $userModel = new \app\index\model\ManagerUser();
        $where = [];
        if ($group_id) $where['group_id'] = $group_id;
        if ($keyword) $where['account|nickname'] = ['like', "%{$keyword}%"];
        $users = $userModel->where($where)->order("id desc")->page($page, $limit)->select();
        $groupModel = Register::get('group');
        $groups = $groupModel->column("id,nickname,title");
        foreach ($users as $k => $user) {
            $users[$k]['group_nickname'] = $groups[$user['group_id']]['nickname'];
            $users[$k]['group_title'] = $groups[$user['group_id']]['title'];
        }
        $count = $userModel->where($where)->count();
        return sucJson($users, $count);
    }

    public function setUser()
    {
        $validate = new UserValidate();
        $params = request()->param();
        if (isset($params['id'])) {
            $rule = "editUser";
        } else {
            $rule = "addUser";
        }
        $result = $validate->scene($rule)->check($params);
        if (!$result) return errJson($validate->getError());
        $userModel = new \app\index\model\ManagerUser();
        if (isset($params['id'])) {
            //查询角色是否为运营
            $user = $userModel->where("id", $params['id'])->field("id,group_id")->find();
            if (!$user) return errJson("操作失败，无此用户");
            $group_name = Register::get("group")->where("id", $user['group_id'])->value("nickname");

            if ($group_name == "OPS") {
                if ($params['parent_id'] <= 0) return errJson("修改运营需选择主管");
                $parentId = $userModel->where("id", $params['parent_id'])->value("id");
                if (!$parentId) return errJson("主管不存在");
                $result = $userModel->allowField("parent_id,nickname,status")->save($params, ['id' => $params['id']]);
            } else {
                $result = $userModel->allowField("nickname,status")->save($params, ['id' => $params['id']]);
            }
        } else {
            $params['account'] = trim($params['account']);
            $group_name = Register::get("group")->where("id", $params['group_id'])->value("nickname");
            $params['password'] = md5("jd123456");
            if ($group_name == "OPS") {
                if ($params['parent_id'] <= 0) return errJson("添加运营需选择主管");
                $parentId = $userModel->where("id", $params['parent_id'])->value("id");
                if (!$parentId) return errJson("主管不存在");
                $result = $userModel->allowField("group_id,parent_id,account,nickname,password,status,password")->save($params);
            } else {
                $result = $userModel->allowField("group_id,account,nickname,password,status,password")->save($params);
            }
        }
        if ($result !== false) return sucJson();
        return errJson("保存失败");
    }


    public function delUser(){
        $validate = new UserValidate();
        $params = request()->param();
        $result = $validate->scene("onlyId")->check($params);
        if (!$result) return errJson($validate->getError());
        $userModel = new \app\index\model\ManagerUser();

        $user = $userModel->where("id", $params['id'])->field("id,group_id")->find();
        if (!$user) return errJson("操作失败，无此用户");
        $group_name = Register::get("group")->where("id", $user['group_id'])->value("nickname");

        if ($group_name == "ADMIN") {
            return errJson("删除失败");
        }
        if ($group_name == "OM") {
            $son = $userModel->where("parent_id", $params['id'])->value("id");
            if ($son) return errJson("删除失败，此账号存在子账号");
        }
        $result = $userModel->where("id",$params['id'])->delete();
        if ($result !== false) return sucJson();
        return errJson("删除失败");
    }

    public function resetPsw(){
        $validate = new UserValidate();
        $params = request()->param();
        $result = $validate->scene("onlyId")->check($params);
        if (!$result) return errJson($validate->getError());
        $userModel = new \app\index\model\ManagerUser();
        $result = $userModel->save(['password' => md5("jd123456")],["id" => $params['id']]);
        if ($result !== false) return sucJson();
        return errJson("重置失败");
    }


    public function upInfo()
    {
        $userModel = new \app\index\model\ManagerUser();
        if ($this->request->isGet()) {
            $nickname = $userModel->where("id",$this->userInfo['id'])->value("nickname");
            $this->assign("nickname", $nickname);
            $groupModel = Register::get('group');
            $group = $groupModel->where("nickname",$this->userInfo['group_name'])->field("id,title,nickname")->find();
            $this->assign("group", $group);
            if ($this->userInfo['group_name'] == 'OPS'){
                $om = $userModel->where(['parent_id' => $this->userInfo['parent_id']])->field("id,nickname")->find();
                $this->assign("om", $om);
            }
            return $this->fetch();
        }else{

            $validate = new UserValidate();
            $params = request()->param();
            $result = $validate->scene("upInfo")->check($params);
            if (!$result) return errJson($validate->getError());
            $params['nickname'] = trim($params['nickname']);
            $result = $userModel->allowField("nickname")->save($params,['id' => $this->userInfo['id']]);
            if ($result !== false) return sucJson();
            return errJson("修改失败");
        }
    }

    public function changePsw()
    {
        if ($this->request->isGet()) {
            return $this->fetch();
        }
        $validate = new UserValidate();
        $params = request()->param();
        $result = $validate->scene("changePsw")->check($params);
        if (!$result) return errJson($validate->getError());

        $userModel = new \app\index\model\ManagerUser();
        $user = $userModel->where(['id' => $this->userInfo['id']])->field("id,password")->find();
        if (!$user) return errJson("用户已被删除");
        if ($user->password != md5($params['oldPassword'])) return errJson("当前密码错误，修改失败");
        $result = $userModel->allowField("password")->save(['password' => md5($params['password'])],['id' =>  $this->userInfo['id']]);
        if ($result !== false) return sucJson();
        return errJson("修改失败");
    }

}
<?php


namespace app\index\controller;

use app\index\model\AuthGroup;
use app\index\model\ManagerUser;
use app\index\validate\UserValidate;
use think\Controller;

class Login extends Controller
{

    public function index()
    {
        if ($this->request->isGet()) {
            $userInfo = session('userInfo');
            if ($userInfo) {
                $this->redirect('index/index');
            }
            return $this->fetch();
        }
        $validate = new UserValidate();
        $params = request()->param();
        $params['l_account'] = trim($params['l_account']);
        $params['l_password'] = trim($params['l_password']);
        $result = $validate->scene('login')->check($params);
        if (!$result) return errJson($validate->getError());

        $model = new ManagerUser();
        $user = $model->where(["account" => $params['l_account'], 'password' => \md5($params['l_password'])])->find();
        if (!$user) return errJson("账号或密码错误");
        if ($user['status'] != 1) return errJson("账号已被封禁");

        //查询组信息
        $group = AuthGroup::get(function ($query) use ($user) {
            $query->field("id,status,nickname")->where('id', $user->group_id);
        });
        if (!$group) return errJson("还未分配角色不能登陆");
        if ($group->status != 1) return errJson("角色已被禁用，请联系管理员");
        $userInfo = $user->toArray();
        $userInfo['group_name'] = $group->nickname;
        session("userInfo", $userInfo);
        return sucJson();
    }

    public function logout()
    {
        session('userInfo',null);
        $this->redirect('index/index');
    }

}
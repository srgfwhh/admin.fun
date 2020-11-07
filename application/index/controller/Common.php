<?php


namespace app\index\controller;

use app\classes\helper\Register;
use app\index\model\AuthGroup;
use app\index\model\AuthRule;
use think\Controller;

class Common extends Controller
{
    protected $userInfo;

    protected function _initialize()
    {
        //判断有无登陆
        $this->userInfo = session("userInfo");
        if (!$this->userInfo) {
            if ($this->request->isAjax()) {
                errJson("请重新登陆", 101)->send();
                die;
            } else {
                $this->redirect('/index/login/index');
                die;
            }
        }
        /**
         * 权限判断
         * 得到模块，控制器和方法名称
         */
        $accessPath = strtolower($this->request->module());
        $accessPath .= "/" . strtolower($this->request->controller());
        $accessPath .= "/" . strtolower($this->request->action());
        $ruleModel = new AuthRule();

        Register::set("rule", $ruleModel);//这个模型比较常用，就先放注册器中
        $node = $ruleModel->where("href", $accessPath)->field("id,status,title")->find();
        if (!$node) {
            errJson("没有权限")->send();
            die;
        }
        if ($node->status != 1) {
            errJson("操作被禁用")->send();
            die;
        }
        $groupModel = new AuthGroup();
        Register::set("group", $groupModel);//这个模型比较常用，就先放注册器中
        $groupIds = $groupModel->where("find_in_set({$node->id},rules)")->column('id');
        if (!in_array($this->userInfo['group_id'], $groupIds)) {
            errJson("{$this->userInfo['group_name']}无此权限")->send();
            die;
        }
        $this->assign("common_title", $node['title']);
    }
}
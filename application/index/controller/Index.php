<?php

namespace app\index\controller;

use app\classes\helper\Register;
use app\index\service\Mananger;

class Index extends Common
{
    //默认路口页面
    public function index()
    {
        $home_page = Register::get("group")->where("nickname",$this->userInfo['group_name'])->value("home_page");
        $this->assign("home_page", $home_page);
        $userModel = new \app\index\model\ManagerUser();
        $nickname = $userModel->where("id",$this->userInfo['id'])->value("nickname");
        $this->assign("nickname", $nickname);
        return $this->fetch();
    }

    public function getMenu()
    {
        $service = new Mananger();
        $menues = $service->groupMenue($this->userInfo['group_id']);
        return sucJson($menues);
    }
}
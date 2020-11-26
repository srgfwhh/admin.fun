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
        $this->assign("home_page", $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/' . $home_page);//iframe后台打路由进入，会拼接相对地址然后就无限循环页面，索性给他加上http域名
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
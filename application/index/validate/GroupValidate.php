<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/18
 * Time: 15:54
 */
namespace app\index\validate;

use think\Validate;

class GroupValidate extends Validate
{
    protected $rule = [
        'id' => 'require|number|gt:0',

        'title' => 'require|max:15|min:1',
        'nickname' => 'require|max:15|min:1|alphaDash',
        'status' => 'require|in:0,1',
        'home_page' => 'require|max:80|min:1',
    ];

    protected $message = [
        'title.require' => '角色名称不能为空',
        'title.max' => '角色名称长度错误',
        'title.min' => '角色名称长度错误',

        'nickname.alphaDash' => '别名只能为字母和数字，下划线_及破折号-',
        'nickname.require' => '别名不能为空',
        'nickname.max' => '别名长度错误',
        'nickname.min' => '别名长度错误',

    ];

    protected $scene = [
        'addGroup' => ['title', 'nickname', 'status','home_page'],
        'editGroup' => ['title', 'status','id','home_page'],
        'delGroup'  => ['id']
    ];

}
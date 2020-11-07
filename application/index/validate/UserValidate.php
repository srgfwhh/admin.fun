<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/18
 * Time: 15:54
 */
namespace app\index\validate;

use think\Validate;

class UserValidate extends Validate
{
    protected $rule = [
        'l_account' => 'require',
        'l_password' => 'require',


        'account' => 'require|alphaDash|max:20|min:3',
        'password' => 'require|alphaDash|max:30|min:3',
        'captcha' => 'require|length:4',

        'nickname' => 'require|max:15|min:1',
        'sex' => 'require|in:1,2',
        'group_id' => 'require|number|gt:0',
        'officialIds' => 'array',
        'status'    => 'require|number|in:0,1',

        'id' => 'require|number|gt:0',
        'parent_id' => 'number|gt:0',

        'oldPassword' => 'require',
        'repassword' => 'require|confirm:password',

        'admin_id' => 'number|gt:0',
    ];

    protected $message = [
        'l_account.require' => '账号不能为空',
        'l_password.require' => '密码不能为空',

        'account.require' => '账号不能为空',
        'account.alphaDash' => '账号只能为字母和数字，下划线_及破折号-',
        'account.max' => '账号最多不能超过20个字符',
        'account.min' => '账号必须超过2个字符',

        'password.require' => '密码不能为空',
        'password.alphaDash' => '密码只能为字母和数字，下划线_及破折号-',
        'password.max' => '密码最多不能超过30个字符',
        'password.min' => '密码必须超过2个字符',

        'captcha.require' => '验证码不能为空',
        'captcha.length' => '验证码长度错误',

        'nickname.require' => '姓名不能为空',
        'nickname.alphaDash' => '姓名只能为字母和数字，下划线_及破折号-',
        'nickname.max' => '姓名长度错误',
        'nickname.min' => '姓名长度错误',


        'repassword.confirm' => '两次输入密码不一致',

    ];

    protected $scene = [
        'login' => ['l_account', 'l_password'],
        'addUser' => ['account','group_id','nickname','parent_id'],
        'editUser' => ['id', 'parentId', 'nickname', 'status'],
        'onlyId' => ['id'],
        'upInfo' => ['nickname'],
        'changePsw' => ['oldPassword|password|repassword'],
    ];

}
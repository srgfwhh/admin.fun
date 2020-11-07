<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/18
 * Time: 15:54
 */
namespace app\index\validate;

use think\Validate;

class RuleValidate extends Validate
{
    protected $rule = [
        'id' => 'require|number|gt:0',
        'parentId' => 'require|number|egt:0',

        'title' => 'require|max:15|min:1',
        'href' => 'require|max:50|min:1',
        'icon' => 'max:50|min:1',
        'status' => 'require|in:0,1',
        'is_menu' => 'require|in:0,1',
        'sort' => 'number|egt:0',

    ];

    protected $message = [
    ];

    protected $scene = [
        'addRule' => ['parentId', 'title', 'icon', 'status', 'is_menu', 'sort'],
        'editRule' => ['id', 'title', 'icon', 'status', 'is_menu', 'sort'],
        'delRule' => ['id'],
    ];

}
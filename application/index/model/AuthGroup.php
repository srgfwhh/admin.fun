<?php

namespace app\index\model;

use think\Model;

class AuthGroup extends Model
{
    use Common;
    protected $table = 'auth_group';
    protected $resultSetType = 'collection';

}
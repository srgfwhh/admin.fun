<?php

namespace app\index\model;

use think\Model;

class AuthRule extends Model
{
    use Common;
    protected $table = 'auth_rule';
    protected $resultSetType = 'collection';
    protected $autoWriteTimestamp = 'datetime';

}
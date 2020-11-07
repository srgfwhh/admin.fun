<?php

namespace app\index\model;

use think\Model;

class ManagerUser extends Model
{
    use Common;
    protected $table = 'manager_user';
    protected $resultSetType = 'collection';
    protected $autoWriteTimestamp = 'datetime';

}
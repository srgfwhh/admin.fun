<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function errJson($msg,$code = 103)
{
    return json(['code' => $code, 'msg' => $msg]);
}

function sucJson($data = [],$count = null){
    if ($count === null){
        return json(['code' => 0, 'msg' => 'ok', 'data' => $data]);
    }
    return json(['code' => 0, 'msg' => 'ok','count' => $count, 'data' => $data]);
}

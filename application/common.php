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

/**
 * [jsonOut json格式化输出]
 * @param  string  $msg    [消息]
 * @param  integer $status [状态]
 * @param  string  $url    [跳转连接]
 * @return [type]          [输出并退出]
 */
function jsonOut($msg ='',$status = 0, $url = '',$data='')
{
    echo json_encode(array('status'=>$status,'msg'=>$msg,'url'=>$url,'data'=>$data));
    exit();
}

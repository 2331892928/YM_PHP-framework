
<?php
/*
 * @作者：AMEN
 * @官网：https://www.ymypay.cn/
 * @博客：https://blog.ymypay.cn/
 * 湮灭网络工作室
 */

//全局函数/类引入区
//框架比引入不可删除
require_once __webSite__.'bin/Global.php';
//框架内置引入完毕
//路由
App::use('/',__routes__.'/index/index.php');//严格示例，只能/到达路由，一般做接口
App::use('/user',__routes__.'/index/index.php',false);
//不严格示例，可以到达/users/xxx 路由xxx为泛，可以/users/index.html,/users/login.html,/users/index/login,通过内部$request->params()获取params参数

//为空则是：/
//填写：index 则是：index,/index,/index/






//禁止删除
App::use(null,error(404,"页面不存在"));
//禁止删除
function error($response_code,$result){
    http_response_code ($response_code);
    $debug = '';
    if(Config['DE_BUG']){
        $debug = 'Error: '.ErrorCode[$response_code].'</br>';
        $debug_arr = debug_backtrace();
        foreach($debug_arr as $key => $val){
            $debug.= "&nbsp &nbsp &nbsp at ".$val['class'].$val['type'].$val['function']."&nbsp &nbsp(".$val['file'].':'. $val['line'].')</br>';
            $details = json_encode($val['args'],JSON_UNESCAPED_UNICODE);
            $details = str_replace("[","",$details);
            $details = str_replace("]","",$details);
            $details = str_replace(",","······",$details);
            $details = strip_tags($details);
            if($details=="{}"){
                continue;
            }
            $debug.="&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp|&nbsp &nbsp &nbsp".$details.'</br>';
        }
    }
    exit('</br>'.$response_code.'  '.ErrorCode[$response_code].'</br>'.'</br>'.$result.'</br>'.'</br>'.$debug.'</br>'.'「YM框架——湮灭网络工作室 by AMEN」'.'</br>Dev：'.Config['DEV']);
}
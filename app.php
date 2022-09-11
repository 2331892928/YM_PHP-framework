
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

//自带waf，不用可以注释，也可以自定义waf规则,推荐第二种
//使用$waf->check()判断自己去处理时，请注意页面必须是500或其他状态码，千万不能是200
$waf = new Waf();
//第一种
//if(!$waf->check()){
//http_response_code (500);
//    echo '非法请求';
//    die;
//}

//第二种
$waf = new Waf();
$waf->run();

//第三种
//$rules = ['\.\./', //禁用包含 ../ 的参数
//        ];
//$waf = new Waf($rules);
//$waf->run();

//第四种
//$rules = ['\.\./', //禁用包含 ../ 的参数
//        ];
//$waf = new Waf($rules);
//if(!$waf->check()){
//http_response_code (500);
//    echo '非法请求';
//    die;
//}
//waf结束

//路由,从小至大，如果根路由在最上边，且不严格，第二个路由是/admin的话，浏览器输入/admin将会被定义到跟路由
App::use('/',__routes__.'/index/index.php');//严格示例，只能/到达路由，一般做接口
App::use('/user',__routes__.'/index/index.php',false);
//不严格示例，可以到达/users/xxx 路由xxx为泛，可以/users/index.html,/users/login.html,/users/index/login,通过内部$request->params()获取params参数

//为空则是：/
//填写：index 则是：index,/index,/index/






//禁止删除,如果觉得不好看可自行替换，此句话意思是无任何路由定义,但必须保证是404页面，且有明显提示：路由不存在或页面不存在。可以通过YM_Request类获取日志，也就是debug
App::use(null,error(404,"路由不存在"));
//禁止删除
function error($response_code,$result){
    http_response_code ($response_code);
    $debug = '';
    if(Config['DE_BUG']){
        $debug = 'Error: '.ErrorCode[$response_code].'</br>';
        $debug_arr = debug_backtrace();
        foreach($debug_arr as $key => $val){
            $class = array_key_exists("class",$val) ? $val['class'] : "";
            $type = array_key_exists("type",$val) ? $val['type'] : "";
            $function = array_key_exists("function",$val) ? $val['function'] : "";
            $file = array_key_exists("file",$val) ? $val['file'] : "";
            $line = array_key_exists("line",$val) ? $val['line'] : "";

            $debug.= "&nbsp &nbsp &nbsp at ".$class.$type.$function."&nbsp &nbsp(".$file.':'. $line.')</br>';
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
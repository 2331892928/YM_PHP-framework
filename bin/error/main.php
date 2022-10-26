<?php
/*
 * @作者：AMEN
 * @官网：https://www.ymypay.cn/
 * @博客：https://blog.ymypay.cn/
 * 湮灭网络工作室
 */

function customError($errno, $errstr, $error_file, $error_line)
{
    $response_code = 400;
    http_response_code ($response_code);
    $debug = '';

    if(Config['DE_BUG']){
        echo "<b>Error:</b> [$errno] $errstr".'</br>';
        echo "&nbsp &nbsp &nbsp &nbsp at (".$error_file.":".$error_line.")";
        $debug = 'Error: '.'更多信息：'.'</br>';
        $debug_arr = debug_backtrace();
        foreach($debug_arr as $key => $val){
            $class = array_key_exists("class",$val) ? $val['class'] : "";
            $type = array_key_exists("type",$val) ? $val['type'] : "";
            $function = array_key_exists("function",$val) ? $val['function'] : "";
            $file = array_key_exists("file",$val) ? $val['file'] : "";
            $line = array_key_exists("line",$val) ? $val['line'] : "";

            $debug.= "&nbsp &nbsp &nbsp at ".$class.$type.$function."&nbsp &nbsp(".$file.':'. $line.')</br>';                $details = json_encode($val['args'],JSON_UNESCAPED_UNICODE);
            $details = str_replace("[","",$details);
            $details = str_replace("]","",$details);
            $details = str_replace(",","······",$details);
            $details = strip_tags($details);
            if($details=="{}"){
                continue;
            }
            $debug.="&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp|&nbsp &nbsp &nbsp".$details.'</br>';
        }
    }else{
        echo "程序出现错误，具体错误请打开debug";
    }
    exit('</br>'.'</br>'.'</br>'.'</br>'.$debug.'</br>'.'「YM框架——湮灭网络工作室 by AMEN」'.'</br>Dev：'.Config['DEV']);
}

// 设置错误处理函数
set_error_handler("customError",Config['ERROR_LEVELS']);




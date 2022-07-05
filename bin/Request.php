
<?php
/*
 * @作者：AMEN
 * @官网：https://www.ymypay.cn/
 * @博客：https://blog.ymypay.cn/
 * 湮灭网络工作室
 */
class YM_request{



    public function post($name){
        $_POST    && $this->SafeFilter($_POST);
        return $_POST[$name];
    }
    public function get($name){
        $_GET     && $this->SafeFilter($_GET);
        return $_GET[$name];
    }
    public function params(): array
    {
        $uri = $_SERVER['REQUEST_URI'];
        $uri = str_replace("?","/",$uri);
        $uri = str_replace("&","/",$uri);
        $arr_query = explode('/',$uri);
        array_splice($arr_query,0,1);
        return $arr_query;
    }
    public function request($name){
        return $_REQUEST[$name];
    }
    public function whetherGet(): bool
    {
        if($_SERVER['REQUEST_METHOD']==='GET'){
            return true;
        }
        return false;
    }
    public function requestType(): String
    {
        return $_SERVER['REQUEST_METHOD'];
    }
    public function error($response_code,$result){
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
    public function body(){
        //        $postStr = $GLOBALS['HTTP_ROW_POST_DATA'];
        return file_get_contents("php://input");
    }
    public function header(){
        $header = getallheaders();
        if(!$header){
            return apache_request_headers();
        }
        return $header;
    }
    public function send($msg){
        print_r($msg);
    }
    public function sendFile($path){
        if(!file_exists($path)){
            error(404,'页面文件不存在');
        }
        $msg = file_get_contents($path);
        //全局变量,先替换__website
        $msg = str_replace('{{__webSite__}}',__webSite__,$msg);
        //再替换其他全局变量
        foreach (Config['PUBLIC_VARIABLE'] as $key => $value){
            $msg = str_replace('{{'.$key.'}}',$value,$msg);
        }
        //替换静态文件变量
        $msg = str_replace('{{__stylesheets__}}',__stylesheets__,$msg);
        $msg = str_replace('{{__javascripts__}}',__javascripts__,$msg);
        $msg = str_replace('{{__images__}}',__images__,$msg);
        $msg = str_replace('{{__fonts__}}',__fonts__,$msg);
        $msg = str_replace('{{__data__}}',__data__,$msg);
        //判断文件类型
        $Mime = require __webSite__.'bin\config\Mime.php';
        $fileSuffix = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $Mime_type = $Mime[$fileSuffix];
        if($Mime_type==null){
            error(405, '页面文件类型未知');
        }
        header('Content-type: '.$Mime_type);
        ob_clean();
        print_r($msg);
    }
    public function render(string $path,array $options){
        if(!file_exists($path)){
            error(404,'页面文件不存在');
        }
        $msg = file_get_contents($path);
//        $msg = $this->myTrim($msg);
        foreach ($options as $key => $value){
            $msg = str_replace('{{'.$key.'}}',$value,$msg);
        }
        //全局变量,先替换__website
        $msg = str_replace('{{__webSite__}}',__webSite__,$msg);
        //再替换其他全局变量
        foreach (Config['PUBLIC_VARIABLE'] as $key => $value){
            $msg = str_replace('{{'.$key.'}}',$value,$msg);
        }
        //替换静态文件变量
        $msg = str_replace('{{__stylesheets__}}',__stylesheets__,$msg);
        $msg = str_replace('{{__javascripts__}}',__javascripts__,$msg);
        $msg = str_replace('{{__images__}}',__images__,$msg);
        $msg = str_replace('{{__fonts__}}',__fonts__,$msg);
        $msg = str_replace('{{__data__}}',__data__,$msg);
        //判断文件类型
        $Mime = require __webSite__.'bin\config\Mime.php';
        $fileSuffix = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $Mime_type = $Mime[$fileSuffix];
        if($Mime_type==null){
            error(405, '页面文件类型未知');
        }
        header('Content-type: '.$Mime_type);
        ob_clean();
        print_r($msg);
    }
    public function header_cookies(): array
    {
        $_COOKIE  && $this->SafeFilter($_COOKIE);
        $cookies = $this->header()['Cookie'];
        $arr_cookies = explode(";",$cookies);
        $arrs_cookies = [];
        foreach($arr_cookies as $value){
            $arr_ls = explode("=",$value);
            $arrs_cookies[$arr_ls[0]]=$arr_ls[1];
        }
        return $arrs_cookies;
    }
    public function cookies(): array
    {
        $_COOKIE  && $this->SafeFilter($_COOKIE);
        return $_COOKIE;
    }
    public function ip(){
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) {
            $ip = getenv("HTTP_CLIENT_IP");
        } else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
            $ip = getenv("REMOTE_ADDR");
        else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
            $ip = $_SERVER['REMOTE_ADDR'];
        else
            $ip = "unknown";
        return ($ip);
    }
    public function purge($string,$trim = true,$filter = true,$force = 0, $strip = FALSE) {//递归addslashes  对参数进行净化
        $encode = mb_detect_encoding($string,array("ASCII","UTF-8","GB2312","GBK","BIG5"));
        if($encode != 'UTF-8'){
            $string = iconv($encode,'UTF-8',$string);
        }
        if($trim){
            $string = trim($string);
        }
        if($filter){
            $farr = array(
                "/<(\\/?)(script|i?frame|style|html|body|title|link|meta|object|\\?|\\%)([^>]*?)>/isU",
                "/(<[^>]*)on[a-zA-Z]+\s*=([^>]*>)/isU",
                "/select|insert|and|or|create|update|delete|alter|count|\'|\/\*|\*|\.\.\/|\.\/|\^|union|into|load_file|outfile|dump/is"
            );
            $string = preg_replace($farr,'',$string);
        }
        !defined('MAGIC_QUOTES_GPC') && define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
        if(!MAGIC_QUOTES_GPC || $force) {
            if(is_array($string)) {
                foreach($string as $key => $val) {
                    $string[$key] = $this->purge($val, $force, $strip);
                }
            } else {
                $string = addslashes($strip ? stripslashes($string) : $string);
            }
        }

        return $string;
    }
    private function myTrim($str)

    {

        $search = array(" ","　","\n","\r","\t");

        $replace = array("","","","","");

        return str_replace($search, $replace, $str);

    }
    private function SafeFilter (&$arr)
    {
        $ra=Array('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/','/script/','/javascript/','/vbscript/','/expression/','/applet/'
        ,'/meta/','/xml/','/blink/','/link/','/style/','/embed/','/object/','/frame/','/layer/','/title/','/bgsound/'
        ,'/base/','/onload/','/onunload/','/onchange/','/onsubmit/','/onreset/','/onselect/','/onblur/','/onfocus/',
            '/onabort/','/onkeydown/','/onkeypress/','/onkeyup/','/onclick/','/ondblclick/','/onmousedown/','/onmousemove/'
        ,'/onmouseout/','/onmouseover/','/onmouseup/','/onunload/');

        if (is_array($arr))
        {
            foreach ($arr as $key => $value)
            {
                if (!is_array($value))
                {
                    if (!get_magic_quotes_gpc())  //不对magic_quotes_gpc转义过的字符使用addslashes(),避免双重转义。
                    {
                        $value  = addslashes($value); //给单引号（'）、双引号（"）、反斜线（\）与 NUL（NULL 字符）  加上反斜线转义
                    }
                    $value       = preg_replace($ra,'',$value);     //删除非打印字符，粗暴式过滤xss可疑字符串
                    $arr[$key]     = htmlentities(strip_tags($value)); //去除 HTML 和 PHP 标记并转换为 HTML 实体
                }
                else
                {
                    $this->SafeFilter($arr[$key]);
                }
            }
        }
    }
}
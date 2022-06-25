<!--/*
 * @作者：AMEN
 * @官网：https://www.ymypay.cn/
 * @博客：https://blog.ymypay.cn/
 * 湮灭网络工作室
 */-->
<?php
/*
 * @作者：AMEN
 * @官网：https://www.ymypay.cn/
 * @博客：https://blog.ymypay.cn/
 * 湮灭网络工作室
 */
class YM_request{



    public function body_post($name){
        $_POST    && $this->SafeFilter($_POST);

        return $_POST[$name];
    }
    public function query_get($name){
        $_GET     && $this->SafeFilter($_GET);
        return $_GET[$name];
    }
    public function request($name){
        return $_REQUEST[$name];
    }
    public function is_get(){
        if($_SERVER['REQUEST_METHOD']==='GET'){
            return true;
        }
        return false;
    }
    public function error($response_code,$result){
        http_response_code ($response_code);
        exit($response_code.'  '.ErrorCode[$response_code].'</br>'.$result.'</br>'.'「YM框架——湮灭网络工作室 by AMEN」'.'</br>Dev：'.Config['DEV']);
    }
    public function body(){
        $postStr = file_get_contents("php://input");
//        $postStr = $GLOBALS['HTTP_ROW_POST_DATA'];
        return $postStr;
    }
    public function header(){
        $header = getallheaders();
        if($header==false){
            return apache_request_headers();
        }
        return $header;
    }
    public function send($msg){
        print_r($msg);
        print_r('</br>');
    }
    public function sendFile($path){
        $msg = file_get_contents($path);
        print_r($msg);
    }
    public function render(string $path,array $options){
        $msg = file_get_contents($path);
        $msg = $this->myTrim($msg);
        foreach ($options as $key => $value){
            $msg = str_replace('{{'.$key.'}}',$value,$msg);
        }
        //全局变量,先替换__website
        $msg = str_replace('{{__webSite__}}',__webSite__,$msg);
        //再替换其他全局变量
        foreach (Config['PUBLIC_VARIABLE'] as $key => $value){
            $msg = str_replace('{{'.$key.'}}',$value,$msg);
        }
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
//    public function get_uri(){
//        $uriV2 = $_SERVER['REQUEST_URI'];
//        $uri = $uriV2;
//        if(stripos(strrev($uriV2),'/')){//颠倒过来好判断 //未发现/结尾，加上,完整uri，
//            $uri = $uriV2.'/';
//        }
//        //运行目录前边对比将完整uri，
////        $pathV2 = $path;
////        if(stripos(strrev($path),'/')){//颠倒过来好判断 //未发现/结尾，加上
////            $pathV2 = $path.'/';
////        }
//    }
//    public function get($path,$class,$function,YM_request $request){
//        $class->$function($request);
//    }
}

<?php
/*
 * @作者：AMEN
 * @官网：https://www.ymypay.cn/
 * @博客：https://blog.ymypay.cn/
 * 湮灭网络工作室
 */
class App{
    /**
     * 定义路由
     * path 路径
     * route 路由
     */
    static function use($path,$route,$strict=true){
        if($path===null){
            require_once $route;
            exit();
        }

        if(Config['DE_BUG']){
            if(!file_exists($route)){
                error(404,'路由：'.$path.'  路由文件：'.$route.' 不存在');
            }
        }
        //浏览器路由1
        $uri = $_SERVER['REQUEST_URI'];
        $uri = urldecode($uri);

        //浏览器uri
        $flag_get = false;
        $whetherGetParameter = count($_GET);
        if($whetherGetParameter>0){//有get参数
            $flag_get = true;
            $questionMarkPosition = stripos($uri,'?'); //第一个问号位置
            $annotationSymbols = substr($uri,$questionMarkPosition-1,1);
            if($annotationSymbols=="/"){//问号前有/符号，删除它
                $uri = substr_replace($uri,"",$questionMarkPosition-1,1);
            }
        }else{
            //找到最后一个/符号，删除它
            $annotationSymbols = substr($uri,strlen($uri)-1,1);
            if($annotationSymbols=="/"){//问号前有/符号，删除它
                $uri = substr_replace($uri,"",strlen($uri)-1,1);
            }
        }
        //用户uri
        $pathV2 = $path;
        if($pathV2==''){
            $pathV2 = "/";
        }
        if(substr($pathV2,0,1)!='/'){//开头未发现/，加上
            $pathV2 = '/'.$path;
        }
        if(substr(strrev($pathV2),0,1)=='/'){//颠倒过来好判断 结尾如果有/，删除，方便判断路由，浏览器uri做了此处理，删除了结尾/
            $pathV2 = substr_replace($pathV2,"",strlen($pathV2)-1,1);
        }





        if(in_array(substr_replace($pathV2,"",0,1),Config['SYSTEM_ROUTES'])){
            error(404,'路由：'.$path.'  是系统级别路由，不可声明，请更换');
        }
        $system_uri = str_replace("?","/",$uri);
        $arr_query = explode('/', $system_uri);
        array_splice($arr_query, 0, 1);

        $first_query = count($arr_query)==0 ? "" : $arr_query[0];
        if(in_array($first_query,Config['SYSTEM_ROUTES'])){//是系统路由
            $arr_query2 = $arr_query;
            array_splice($arr_query2, 0, 1);
            //把问号后边的删除，避免因为get参数而找不到文件
            if($flag_get){
                array_splice($arr_query,count($arr_query)-1,1);
            }
            $file = __public__ . implode("/", $arr_query);
            if (!file_exists($file)) {
                error(404, '静态文件不存在'.$file);
            }
            $Mime = require __webSite__.'bin/config/Mime.php';
            $msg = file_get_contents($file);
            $fileSuffix = strtolower(pathinfo($file, PATHINFO_EXTENSION));

            if(array_key_exists($fileSuffix,$Mime)){
                $Mime_type = $Mime[$fileSuffix];
            }else{
                $Mime_type = "text/html;";
                error(405, '静态类型未知');
            }
            header('Content-type: '.$Mime_type);
            ob_clean();
            print_r($msg);
            exit();
        }

        $flag = false;
        if(!$strict){//不严格，只匹配前缀,
            //print_r($uri);
//            if(gettype(stripos($uri,$pathV2))=='integer' && stripos($uri,$pathV2)==0){//匹配
//                $flag = true;
//
//            }
            $yurl = $uri."/";
            $ypathV2 = $pathV2."/";
            if(gettype(stripos($yurl,$ypathV2))=='integer' && stripos($yurl,$ypathV2)==0){//匹配

                $flag = true;

            }
        }else{//严格模式，匹配除get以外的是否相等
            $questionMarkPosition = stripos($uri,'?'); //第一个问号位置
            $uriV1 = $uri;
            if($flag_get){//有get参数
                //删除get参数方便判断
                $uriV1 = substr($uriV1,0,$questionMarkPosition);
            }
            if($uriV1==$pathV2){
                $flag = true;
            }
        }
//        print_r($uriV1);
//        print_r('</br>');
//        print_r($pathV2);
        if($flag){//根路由
            require_once $route;
            require_once __webSite__ . 'bin/Request.php';
            $requests = new YM_request();
            $index = new Index();
            $index->start($requests);
            exit();
        }
    }
}
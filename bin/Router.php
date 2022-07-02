
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
        $uriV2 = $_SERVER['REQUEST_URI'];
        $uri = $uriV2;
//        $uri = $uriV2.'/';

        if(gettype(stripos(strrev($uriV2),'/'))=='integer' && stripos(strrev($uriV2),'/')!=0){//颠倒过来好判断 未发现/结尾，加上
            $uri = $uriV2.'/';

        }

        //parm get参数uri
        $whwz = stripos($uri,'?'); //第一个问号位置
        if(gettype($whwz)=='integer'){
            $uri = substr($uri,0,$whwz);
            if(stripos(strrev($uri),'/')!=0){

                $uri = $uri.'/';
            }
        }

        //浏览器路由1

        //用户设置路由1
        $pathV2 = $path;
        if(gettype(stripos(strrev($path),'/'))=='integer'){//颠倒过来好判断 //未发现/结尾，加上
            $pathV2 = $path.'/';
        }

        if($pathV2==''){
            $pathV2 = "/";
        }

        //用户设置路由1




        if(in_array($pathV2,Config['SYSTEM_ROUTES'])){
            error(404,'路由：'.$path.'  是系统级别路由，不可声明，请更换');
        }
        $arr_query = explode('/', $uri);
        array_splice($arr_query, 0, 1);
        array_splice($arr_query, count($arr_query) - 1, 1);
        $first_query = $arr_query[0];
        if(in_array($first_query,Config['SYSTEM_ROUTES'])){//是系统路由
            $arr_query2 = $arr_query;
            array_splice($arr_query2, 0, 1);
            $file = __public__ . $first_query . '\\' . implode("/", $arr_query2);
            if (!file_exists($file)) {
                error(404, '静态文件不存在');
            }
            $msg = file_get_contents($file);
            $file_exists = strtolower(get_extension($file));

            switch ($file_exists) {
                case "css":
                    header('Content-type: text/css');
                    print_r($msg);
                    break;
                case "js":
                    header('Content-type: text/javascript');
                    print_r($msg);
                    break;
                case "jpg":
                case "jpeg":
                    header('Content-type: image/jpeg');
                    ob_clean();
                    $image = imagecreatefromjpeg($file);
                    imagejpeg($image);
                    break;
                case "png":
                    header('Content-type: image/png');
                    ob_clean();
                    $image = imagecreatefrompng($file);
                    imagepng($image);
                    break;
                case "webp":
                    header('Content-type: image/webp');
                    ob_clean();
                    $image = imagecreatefromwbmp($file);
                    imagewebp($image);
                    break;
                case "ico":
                    header('Accept-Ranges:bytes');
                    header('Content-Length: '.filesize($file));
                    header('Content-type: image/x-icon');
                    ob_clean();
                    print_r($msg);
                    break;
                case "gif":
                    header('Content-type: image/gif');
                    ob_clean();
                    $image = imagecreatefromgif($file);
                    imagegif($image);
                    break;
                case "json":
                    header('Content-type: application/json');
                    print_r($msg);
                    break;
                case "pdf":
                    header('Content-type: application/pdf');
                    print_r($msg);
                    break;
                case "RSS":
                    header('Content-Type: application/rss+xml; charset=ISO-8859-1');
                    print_r($msg);
                    break;
                case "xml":
                    header('Content-type: text/xml');
                    print_r($msg);
                    break;
                case "plan":
                    header('Content-type: text/plain');
                    print_r($msg);
                    break;
                case "atom":
                    header('Content-type: application/atom+xml');
                    ob_clean();
                    print_r($msg);
                    break;
                case "svg":
                    header('image/svg+xmz');
                    ob_clean();
                    print_r($msg);
                    break;
                case "tff":
                case "woff":
                case "woff2":
                    header('Accept-Ranges:bytes');
                    header('Content-Length: '.filesize($file));
                    header('Content-type:  application/x-font-woff');
                    ob_clean();
                    print_r($msg);
                    break;
                default:
                    printf($msg);
                    break;
            }
            exit();
        }
//是否严格，不严格可以获取query参数

        if(!$strict){
            $llqdkh = stripos($uri,$pathV2);
            if($llqdkh==0){
                $uri = substr($uri,0,strlen($pathV2));
            }
        }

        if($uri==$pathV2){//根路由
            require_once $route;
            require_once __webSite__ . 'bin\Request.php';
            $requests = new YM_request();
            $index = new Index();
            $index->start($requests);
            exit();
        }
    }
}
function get_extension($file){
    return pathinfo($file, PATHINFO_EXTENSION);

}
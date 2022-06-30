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
class App{
    /**
     * 定义路由
     * path 路径
     * route 路由
     */
    static function use($path,$route){
        if($path===null){
            require_once $route;
            exit();
        }

        if(Config['DE_BUG']){
            if(!file_exists($route)){
                error(404,'路由：'.$path.'  路由文件：'.$route.' 不存在');
            }
        }

        $uriV2 = $_SERVER['REQUEST_URI'];
        $uri = $uriV2;
//        $uri = $uriV2.'/';
        if(stripos(strrev($uriV2),'/')){//颠倒过来好判断 //未发现/结尾，加上
            $uri = $uriV2.'/';

        }
        $pathV2 = $path;
        if(stripos(strrev($path),'/')){//颠倒过来好判断 //未发现/结尾，加上
            $pathV2 = $path.'/';
        }
        if($pathV2===''){
            $pathV2 = "/";
        }
        //判断是否是系统级别路由
//        foreach(Config['SYSTEM_ROUTES'] as $items){
//            if($items===$pathV2){
//                error(404,'路由：'.$path.'  是系统级别路由，不可声明，请更换');
//                break;
//            }
//        }
        if(in_array($pathV2,Config['SYSTEM_ROUTES'])){
            error(404,'路由：'.$path.'  是系统级别路由，不可声明，请更换');
        }

        if($uri===$pathV2){//根路由
            require_once $route;
            require_once __webSite__ . 'bin\Request.php';
            $requests = new YM_request();
            $index = new Index();
            $index->start($requests);
            exit();
        }else{//判断是否子目录运行,或路由不匹配或系统路由
            //判断是否是系统级别路由
            //获取第一个与第二个query
            $arr_query = explode('/',$uri);
            array_splice($arr_query,0,1);
            array_splice($arr_query,count($arr_query)-1,1);
            if(count($arr_query)>=3){
                //session_start();
                $first_query = $arr_query[0];
                $second_query = $arr_query[1]; //也是now_user_strict_session
                //$YM_Class = new YM_Class();
                //$new_user_strict_session = $YM_Class->getRandom();
                $user_strict_session = $_SESSION['user_strict_session'];
                //是系统路由
                if(in_array($first_query,Config['SYSTEM_ROUTES'])){
                    //防止特殊情况晚一秒，一般是同时，同一秒
                    if($user_strict_session===$second_query || md5('ym-php-framework'.(time()+1).'ym-php-framework')===$second_query){//能匹配session
                        //刷新session
                        unset($_SESSION['user_strict_session']);
                        session_destroy();
                        //$_SESSION['user_strict_session'] = $new_user_strict_session;
                        $arr_query2 = $arr_query;
                        array_splice($arr_query2,0,2);
                        $file = __public__.$first_query.'\\'.implode("/",$arr_query2);

                        if(!file_exists($file)){
                            error(404,'静态文件不存在');
                        }
                        $msg = file_get_contents($file);
                        print_r($msg);
                        exit();
                    }else{
                        //刷新session
                        unset($_SESSION['user_strict_session']);
                        session_destroy();
                        error(404,"静态文件非法");
                    }
                }
//                foreach(Config['SYSTEM_ROUTES'] as $items){
//                    if($items===$first_query){//是系统路由
//
//                        break;
//                    }
//                }
                //session_destroy();
            }

            //$now_user_strict_session = $YM_Class->txt_zhong($uri,"");



            //有bug，不是子目录运行也会出现，只要目录与此项目名称对即可
//            $urilen = strlen($uri);
//            $pathlen = strlen($pathV2);
//            //如果path比uri短，那就是子目录
//            //不存在一样长，一样长是根路由
//            if($pathlen<$urilen){
//                //取出子目录
//                $flag = strpos($uri,$pathV2);
//                if($flag){
//                    //子路由目录名称
//                    //substr($uri,0,$flag+1)
//                    $zml = substr($uri,0,$flag+1);
//                    //子目录下的uri
//                    $uriname = substr($uri,$flag,$urilen-$flag);
//                    if($uriname===$pathV2){
//                        require_once $route;
//                        require_once __webSite__ . 'bin\Request.php';
//                        $requests = new YM_request();
//                        $index = new Index();
//                        $index->start($requests);
//                        exit();
//                    }
//                }
//            }
        }

//        //判断是否是子目录运行，并且排除
//        //获取当前目录最后一个文件夹名称
//        $d1 = strripos(__webSiteV1__,'\\'); //最后一个文件夹的第一个斜杠位置
//        $d2 = strlen(__webSite__);//总长度
//        $site = substr(__webSite__,$d1+1,$d2-$d1-2);
//        //获取URI第一个目录，是否与当前目录最后一个文件夹名称，如果是则是子目录运行
//        //去除第一个斜杠
//        $uriV1 = substr($uri,1,strlen($uri));
//        //取出第二个斜杠位置
//        $d1 = stripos($uriV1,'/');
//        //取出第一个query
//        $uri_site = substr($uriV1,0,$d1);
//        //两个取出完毕，对比是否是子目录运行
//        //判断uri是否是/后缀，如果不是，加上
//
//        if($uri_site==$site){//子目录运行，除了第一个query后边都是路由，比如：http://qq.com/www/index   www为本程序路径，/index为路由
//            //子目录运行，需去除第一个query
//            $uriV3 = substr($uriV1,$d1,strlen($uriV1)-$d1);
//            if($strict){//是否严格，如果是严格必须是/index 或/index/ 才可到达，否则/index/qq/不可到达
//                if($uriV3==$pathV2){
//                    $index->start($requests);
//                }else{
//                    exit('404');
//                }
////                print_r($uriV3);
////                print_r('</br>');
////                print_r($pathV2);
//                exit();
//            }else{//不是严格
//                //取出第二个query，与pathV2的第一个query比较，相同则执行
//            }
//        }else{//根目录运行，全部都是路由
//
//        }

    }
}
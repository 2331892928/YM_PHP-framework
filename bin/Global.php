
<?php
/*
 * @作者：AMEN
 * @官网：https://www.ymypay.cn/
 * @博客：https://blog.ymypay.cn/
 * 湮灭网络工作室
 */
define("ErrorCode", require_once __webSite__ . 'bin/config/ErrorCode.php');
define("Config", require_once __webSite__ . 'bin/config/Config.php');
foreach (Config['PUBLIC_VARIABLE'] as $key => $value) {
    define($key, $value);
}


//'__images__'=>__webSite__.'public' . '\\images',
//        '__javascripts__'=>__webSite__.'public' . '\\javascripts',
//        '__stylesheets__'=>__webSite__.'public' . '\\stylesheets',
require_once __webSite__.'bin/Router.php';
require_once __webSite__.'bin/Map.php';
require_once __webSite__.'bin/db.class.php';
require_once __webSite__.'bin/YM_Class.php';
//$host = '//'.$_SERVER['HTTP_HOST'].':'.$_SERVER["SERVER_PORT"];
$host = '//'.$_SERVER['HTTP_HOST'];
foreach (Config['SYSTEM_ROUTES'] as $key) {
    define('__'.$key.'__', $host.'/'.$key.'/');
}
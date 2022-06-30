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

//全局函数/类引入区
//框架比引入不可删除
require_once __webSite__.'bin/Global.php';
//框架内置引入完毕
//路由
App::use('',__routes__.'/index/index.php');
//为空则是：/
//填写：index 则是：index,/index,/index/







App::use(null,error(404,"页面不存在"));
function error($response_code,$result){
    http_response_code ($response_code);

    exit($response_code.'  '.ErrorCode[$response_code].'</br>'.$result.'</br>'.'「YM框架——湮灭网络工作室 by AMEN」'.'</br>Dev：'.Config['DEV']);
}
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
define("ErrorCode", require_once __webSite__ . 'bin/config/ErrorCode.php');
define("Config", require_once __webSite__ . 'bin/config/Config.php');
foreach (Config['PUBLIC_VARIABLE'] as $key => $value) {
    define($key, $value);
}
require_once __webSite__.'bin/Router.php';
require_once __webSite__.'bin/Map.php';
require_once __webSite__.'bin/YM_Class.php';
require_once __webSite__.'bin/db.class.php';
$YM_Class = new YM_Class();

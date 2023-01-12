
<?php
/*
 * @作者：AMEN
 * @官网：https://www.ymypay.cn/
 * @博客：https://blog.ymypay.cn/
 * 湮灭网络工作室
 */
return [
    //是否debug
    'DE_BUG'=>true,
    /**
     * 值	常量	描述
        2	E_WARNING	非致命的 run-time 错误。不暂停脚本执行。
        8	E_NOTICE	run-time 通知。在脚本发现可能有错误时发生，但也可能在脚本正常运行时发生。
        256	E_USER_ERROR	致命的用户生成的错误。这类似于程序员使用 PHP 函数 trigger_error() 设置的 E_ERROR。
        512	E_USER_WARNING	非致命的用户生成的警告。这类似于程序员使用 PHP 函数 trigger_error() 设置的 E_WARNING。
        1024	E_USER_NOTICE	用户生成的通知。这类似于程序员使用 PHP 函数 trigger_error() 设置的 E_NOTICE。
        4096	E_RECOVERABLE_ERROR	可捕获的致命错误。类似 E_ERROR，但可被用户定义的处理程序捕获。（参见 set_error_handler()）
        8191	E_ALL	所有错误和警告。（在 PHP 5.4 中，E_STRICT 成为 E_ALL 的一部分）
     */
    'ERROR_LEVELS'=>8191,
    //框架内部配置项,勿修改↓
    'PUBLIC_VARIABLE'=>[
        '__routes__'=>__webSite__.'routes',
        '__views__'=>__webSite__.'views',
        '__public__'=>__webSite__.'public' . '/',
        '__includes__'=>__webSite__.'public' . '/includes',
    ],
    'SYSTEM_ROUTES'=>[
        'stylesheets',
        'javascripts',
        'images',
        'data',
        'fonts',
    ],
    'DEV'=>'0.8'
    //框架内部配置项↑
];
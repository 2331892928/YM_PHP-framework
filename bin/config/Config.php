
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
    //框架内部配置项,勿修改↓
    'PUBLIC_VARIABLE'=>[
        '__routes__'=>__webSite__.'routes',
        '__views__'=>__webSite__.'views',
        '__public__'=>__webSite__.'public' . '\\',
        '__includes__'=>__webSite__.'public' . '\\includes',
    ],
    'SYSTEM_ROUTES'=>[
        'stylesheets',
        'javascripts',
        'images',
        'data',
        'fonts',
    ],
    'DEV'=>'0.4'
    //框架内部配置项↑
];
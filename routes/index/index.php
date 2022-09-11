
<?php
/*
 * @作者：AMEN
 * @官网：https://www.ymypay.cn/
 * @博客：https://blog.ymypay.cn/
 * 湮灭网络工作室
 */
//示例路由，类名必须为Index，文件名无所谓
//第一个方法必须为start，且为public，因为他是入口
//其余最好是private，保护
//此框架可以在任意地方引入类以及函数，但如果是为了全局使用，请在根目录app.php引入
//如果是局部引入，使用前引入即可
class Index{
    public function start(YM_request $request){
        //通过这种方法当前请求是get还是post，这样就可以单独拦截，若post和get都接收，直接写即可
        //获取参数：$request->query_get()/body_post,已过滤xss
//        if($request->whetherGet()){
//            $this->get($request);
//        }else{
////            $this->post($request);
//            $request->error(405,'not post');
//        }
        //文件输出示例
        $request -> statusPage(404,"F:/phpProjects/YM_PHP-framework/views/404.html");
//        $this->text($request);
        //更多帮助请查看：https://ym-php.rkru.cn
    }
    private function get(YM_request $request){
        $request->send('现在是get');
        $request->send('cookies:');
        $request->send($request->cookies());
        $request->send('访问者ip：');
        $request->send($request->ip());

    }
    private function post(YM_request $request){
        $request->send('现在是post');
        $request->send('headar:');
        $request->send($request->header());
        $request->send('访问者ip：');
        $request->send($request->ip()); //若此ip返回不满意，可直接用 php原生获取ip

    }
    private function text(YM_request $request){
        //直接输出文件
//        $request->sendFile(__webSite__.'views/index.html');
        //模板输出文件
        //输出不会覆盖
        $request->render(__webSite__.'views/index.html',['title'=>'示例文件','msg'=>'变量用{{}}包起来,不能有任何空白字符']);
        //输出params参数
        $request->send($request->params());

    }
}
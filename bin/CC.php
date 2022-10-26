<?php
/*
 * @作者：AMEN
 * @官网：https://www.ymypay.cn/
 * @博客：https://blog.ymypay.cn/
 * 湮灭网络工作室
 */
class CC{
    private $model = 0;
    private $second = 120;
    private $frequency = 600;
    private $blockingTime = 300;
    private $ip_model = 1;
    private $whetherToOpen = true;
    private $redis = NULL;
    private $fw_key = NULL;
    private $dq_time = NULL;
    private $last_time = NULL;

    /**
     * @param string $redis_host redis连接参数 host
     * @param int $redis_port redis连接参数 port
     * @param string $redis_pass redis连接参数 password
     * @param int $model 请求类型，0:URL带参数;1:URL不带参数;2:IP,3:IP+UA
     * @param int $second 周期，不得大于等于86400秒
     * @param int $frequency 频率
     * @param int $blockingTime 封锁时间，不得大于等于86400秒,不得低于周期
     * @param int $ip_model 取ip模式，默认3 0HTTP_CLIENT_IP，1HTTP_X_FORWARDED_FOR，2REMOTE_ADDR，3REMOTE_ADDR
     * @param bool $whetherToOpen 是否开启
     */
    public function __construct($redis_host = "127.0.0.1", $redis_port = 6379, $redis_pass = "", $model = 1,$second = 120,$frequency = 600,$blockingTime = 300,$ip_model = 3,$whetherToOpen = true){
        $this->model = $model;
        $this->second = $second >= 86400 ? 86300 : $second;
        $this->frequency = $frequency;
        $this->blockingTime = ($blockingTime >= 86400 ? $second+100 : $blockingTime) <= $second ? $second+100 : $blockingTime;
        $this->ip_model = $ip_model;
        $this->whetherToOpen = $whetherToOpen;
//        get_loaded_extensions
        if(!extension_loaded("redis"))$this->whetherToOpen = false;
        if(!$this->whetherToOpen)return;
        $redis = new Redis();
        $this->redis = $redis;
        $dq_time = time();
        $this->dq_time = $dq_time;
        $redis->pconnect($redis_host,$redis_port,1);
        $redis->auth($redis_pass);

        $redis->select(15);
        $redis->setOption(Redis::OPT_PREFIX,'ym_php_');

        // 初始化模式
        $model = $redis->get("model");

        if ($model==NULL or $model != $this->model){

            $redis->flushAll();
            $redis->set("model",$this->model);
            $model = $this->model;
        }
        // 默认统计时间为24小时
        $time = $redis->get('time');

        // 初始化时间
        if ($time == NULL){$redis->set('time',$dq_time);$time = $dq_time;}
        // 再次初始化时间
        if ($dq_time - $time >= 86400){$redis->set('time',$dq_time);$time = $dq_time;}

        //计算频率
        $accessTime = NULL;
        $fw_key = NULL;

        switch($model){
            case 0:
                //计入访问日志,url带参数
                $fw_key = $_SERVER['REQUEST_URI'];
                //不需要解码
                break;
            case 1:
                //计入访问日志,url不带参数
                $fw_key = $_SERVER['REQUEST_URI'];

                //不需要解码
                $wz = stripos($fw_key,"?");

                if(gettype($wz) == "integer"){
                    $fw_key = substr($fw_key,0,$wz);
                }
                break;
            case 3:
                $fw_key = $this->ipV2($this->ip_model);
                $UA = $_SERVER['HTTP_USER_AGENT'];
                $fw_key = $fw_key.$UA;
                break;
            case 2:
            default:
                //计入访问日志
            $fw_key = $this->ipV2($this->ip_model);


        }
        $this->fw_key = $fw_key;
        // 如果已经在黑名单，不判断了，不记录了
        if($this->status(false))return;


        //放入日志
        $accessTime = $redis->hget('logs',$fw_key);
        $redis->hset('logs',$fw_key,$accessTime == NULL ? $dq_time : $accessTime ."----YM_PHP----". $dq_time);
        $accessTime = $redis->hget('logs',$fw_key);
        //计算频率
        if($accessTime!=NULL){
            $accessTime = explode("----YM_PHP----",$accessTime);
            rsort($accessTime);
            //计算频率
            $pl = 0;
            foreach($accessTime as $value){
                //周期
                $sc = $dq_time - $value;
                if($sc >= $this->second){
                    break;
                }else{
                    $pl = $pl + 1;
                }
            }
//                print_r($pl);
//                print_r($this->frequency);
            if ($pl >= $this->frequency){
                // 进入封锁，进入数据库
                $this->redis->hset('hmd',$fw_key,$dq_time);
            }
        }




//        $redis->close();
    }

    /**
     * @param bool $close 无需设置此参数
     * @return bool
     */
    public function status($close=true){
        $status = $this->redis->hget('hmd',$this->fw_key);
        if($status==NULL) return false;
        $this->last_time = $status;
        if($close)$this->redis->close();
        if($this->dq_time-$status < $this->blockingTime)return true;else{//清除此key黑名单

            $this->redis->hdel('key',$this->fw_key);
            return false;
        }
    }
    public function getAlert()
    {
        $html = <<<str
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="initial-scale=1,minimum-scale=1,width=device-width">
    <title>安全拦截</title>
    <style>
        body {
            font-size: 100%;
            background-color: #550000;
            color: #fff;
            margin: 15px;
        }
        h1 {
            font-size: 3em;
            line-height: 1.5em;
            margin-bottom: 26px;
            font-weight: bolder;
        }
        .wrapper {
            border: 10px solid #ee4444;
            background:yellow;
            color:red;
            margin: 20vh auto 0;
            padding: 20px 5px 40px 5px;
            max-width: 500px;
            text-align:center;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h1>YM-PHP框架</h1><h1>网站防火墙</h1>
        <p>系统已拦截本次请求</p>
        <p>可能的原因是触发了CC防御</p>
        <p>{ms}</p>
        <p>请在:<b>【{fw}】</b>秒后访问</p>
        <p>您的IP:<b>【{ip}】</b>已被记录</p>
    </div>
</body>
</html>
str;
        switch($this->model){
            case 0:
                $ms = "当前模式为：uri,此uri已被禁止访问，不带参数";
                break;
            case 1:
                $ms = "当前模式为：uri,此uri已被禁止访问，带参数";
                break;
            case 3:
                $ms = "当前模式为：IP+UA,此IP+UA已被禁止访问";
                break;
            case 2:
            default:
                $ms = "当前模式为：IP,此IP已被禁止访问";

        }
        http_response_code(400);
        return str_ireplace(['{ip}','{fw}','{ms}'], [$this->ipV2($this->ip_model),date("Y-m-d H:i:s",$this->last_time+$this->blockingTime),$ms], $html);
//        date("Y-m-d H:i:s",$this->last_time+time())
    }
    /**
     * 获取ipv2自定义版
     * @param int $type 类型，默认0HTTP_CLIENT_IP，1HTTP_X_FORWARDED_FOR，2REMOTE_ADDR，3REMOTE_ADDR，4自定义，需要在参数二给出
     * @param string $ipServer
     */
    private function ipV2($type = 0,$ipServer = NULL){
        switch ($type){
            case 0:
                $ip = getenv("HTTP_CLIENT_IP");
                break;
            case 1:
                $ip = getenv("HTTP_X_FORWARDED_FOR");
                break;
            case 2:
                $ip = getenv("REMOTE_ADDR");
                break;
            case 3:
                $ip = $_SERVER['REMOTE_ADDR'];
                break;
            default:
                $ip = $_SERVER[$ipServer];
        }
        return ($ip);
    }
}
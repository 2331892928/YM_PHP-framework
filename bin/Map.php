
<?php
/*
 * @作者：AMEN
 * @官网：https://www.ymypay.cn/
 * @博客：https://blog.ymypay.cn/
 * 湮灭网络工作室
 */
class Map{
    private $arr_map = [];
    public function get($key){
        $res = $this->arr_map[$key];
        if($res==null){
            return null;
        }
        return $res;
    }
    public function put($key,$name): bool
    {
        $res = $this->arr_map[$key];
        if($res==null){
            $this->arr_map[$key] = $name;
            return true;
        }
        return false;
    }
}
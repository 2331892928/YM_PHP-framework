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

class YM_Class
{
    public function send_mail($host, $port, $user, $pass, $to, $content = NULL, $title = 'YM框架邮件系统', $type = 'TXT', $debug = false)
    {
        // require 'class/email/mail.php';
//        require_once 'class/email/class.phpmailer.php';
        require_once __webSite__ . 'bin/email/class.phpmailer.php';
        require_once __webSite__ . 'bin/email/class.smtp.php';
//        include("class/email/class.smtp.php");
        //$to 表示收件人地址 $subject 表示邮件标题 $body表示邮件正文
        //error_reporting(E_ALL);
        error_reporting(E_STRICT);
        date_default_timezone_set("Asia/Shanghai");//设定时区东八区
        $mail = new PHPMailer(); //new一个PHPMailer对象出来
        // $body             = eregi_replace("[\]",'',$content); //对邮件内容进行必要的过滤
        $body = $content;
        $mail->CharSet = "UTF-8";//设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
        $mail->IsSMTP(); // 设定使用SMTP服务
        $mail->SMTPDebug = 1;                     // 启用SMTP调试功能
        // 1 = errors and messages
        // 2 = messages only
        $mail->SMTPAuth = true;                  // 启用 SMTP 验证功能
        $mail->Host = $host;      // SMTP 服务器
        if ($port == 465) {
            $mail->SMTPSecure = "ssl";// 安全协议
        }
        $mail->Port = $port;                   // SMTP服务器的端口号
        $mail->Username = $user;  // SMTP服务器用户名
        $mail->Password = $pass;            // SMTP服务器密码
        $mail->SetFrom($user, $title);
        $mail->AddReplyTo($user, $title);
        $mail->Subject = $title;
        $mail->AltBody = "To view the message, please use an HTML compatible email viewer! - From www.86daigou.com"; // optional, comment out and test
        $mail->MsgHTML($body);
        $address = $to;
        $mail->AddAddress($address, $to);
        //$mail->WordWrap = 50; // set word wrap 换行字数
        //$mail->AddAttachment("images/phpmailer.gif");      // 附件
        //$mail->AddAttachment("images/phpmailer_mini.gif"); // 附件
        if (!$mail->Send()) {
            return ("Mailer Error: " . $mail->ErrorInfo);
        } else {
            return (1);
        }

    }

    public function txt_zhong($str, $leftStr, $rightStr)
    {//取文本中间
        $left = strpos($str, $leftStr);
        //echo '左边:'.$left;
        $right = strpos($str, $rightStr, $left);
        //echo '<br>右边:'.$right;
        if ($left < 0 or $right < $left) return '';
        return substr($str, $left + strlen($leftStr), $right - $left - strlen($leftStr));
    }

    public function txt_you($str, $leftStr)
    {//取文本右边
        $left = strpos($str, $leftStr);
        return substr($str, $left + strlen($leftStr));
    }

    public function txt_zuo($str, $rightStr)
    {//取文本左边
        $right = strpos($str, $rightStr);
        return substr($str, 0, $right);
    }

    public function mi_rc4($data, $pwd, $t = 0)
    {//t=0加密，1=解密
        $cipher = '';
        $key[] = "";
        $box[] = "";
        $pwd = $this->mi_rc4_encode($pwd);
        $data = $this->mi_rc4_encode($data);
        $pwd_length = strlen($pwd);
        if ($t == 1) {
            $data = hex2bin($data);
        }
        $data_length = strlen($data);
        for ($i = 0; $i < 256; $i++) {
            $key[$i] = ord($pwd[$i % $pwd_length]);
            $box[$i] = $i;
        }
        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $key[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for ($a = $j = $i = 0; $i < $data_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $k = $box[(($box[$a] + $box[$j]) % 256)];
            $cipher .= chr(ord($data[$i]) ^ $k);
        }
        if ($t == 1) {
            return $cipher;
        } else {
            return bin2hex($cipher);
        }
    }

    public function RSA_GMI($data, $key, $t = 0)
    {//RSA公钥加解密
        require_once __webSite__.'bin/Rsa.php';//引入RSA加解密类
        if ($t == 0) {
            $mi_data = Rsa::publicEncrypt($data, $key);//使用公钥将数据加密
        } else {
            $mi_data = Rsa::publicDecrypt($data, $key);//使用公钥将数据解密
        }
        return $mi_data;
    }

    public function RSA_SMI($data, $key, $t = 0)
    {//RSA私钥加解密
        require_once __webSite__.'bin/Rsa.php';//引入RSA加解密类
        if ($t == 0) {
            $mi_data = Rsa::privateEncrypt($data, $key);//使用私钥将数据加密
        } else {
            $mi_data = Rsa::privateDecrypt($data, $key);//使用私钥将数据解密
        }
        return $mi_data;
    }
    public function getRandom($number=32): string
    {
        $str = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ" ;
        $key  =  "" ;
        for ( $i =0; $i < $number ; $i ++) {
            $key  .=  $str{mt_rand(0,32)};
        }
        return $key ;
    }
    public function getMillisecond(): float
    {

        list($t1, $t2) = explode(' ', microtime());

        return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);

    }

    private function mi_rc4_encode($str, $turn = 0)
    {//turn=0,utf8转gbk,1=gbk转utf8
        if (is_array($str)) {
            foreach ($str as $k => $v) {
                $str[$k] = array_iconv($v);
            }
            return $str;
        } else {
            if (is_string($str) && $turn == 0) {
                return mb_convert_encoding($str, 'GBK', 'UTF-8');
            } elseif (is_string($str) && $turn == 1) {
                return mb_convert_encoding($str, 'UTF-8', 'GBK');
            } else {
                return $str;
            }
        }
    }
}

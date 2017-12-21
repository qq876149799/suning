<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
//导入Config
use think\Config;
// 应用公共文件
use Org\Ucpaas;
use think\Session;
// use think\vendor\captcha;

//导入验证码类
function check_verify($code, $id = ''){
    $captcha = new \think\vendor\captcha();
    return $captcha->check($code, $id);
}
//发送短信
function sendSms($phone)
{
	
	//初始化必填
	$options['accountsid']='f99485ad2a3e5e3d62738039929411b9';
	$options['token']='8def021b4e9d6e845287971dbe7a553e';


	//初始化 $options必填
	$ucpass = new Ucpaas($options);

	//开发者账号信息查询默认为json或xml
	header("Content-Type:text/html;charset=utf-8");

	//短信验证码（模板短信）,默认以65个汉字（同65个英文）为一条（可容纳字数受您应用名称占用字符影响），超过长度短信平台将会自动分割为多条发送。分割后的多条短信将按照具体占用条数计费。
	$appId = "1c6f208e933a48a0bb8bd9c7fc208a7a";
	$to = $phone;
	$templateId = "227634";
	//验证码
	$param=mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9);
	//存入session
	session('code',$param);

	$ss=json_decode($ucpass->templateSMS($appId,$to,$templateId,$param),true);
	$a[]=$ss;
	return json_encode($a);

}
//测试
function test()
{
	echo 111111;
}
//发送邮件
//$to(接收方) $title(邮件主题) $content(发送内容)
function sendmail($to,$title,$content){
	//导入三方类库
	$mail=new \Org\Util\PHPMailer();
	// var_dump($mail);
	//设置字符集
	$mail->CharSet="utf-8";
	//设置采用SMTP方式发送邮件
	$mail->IsSMTP();
	//设置邮件服务器地址
	$mail->Host=Config::get('mailarr.smtp');//C 获取配置文件信息 
	//设置邮件服务器的端口 默认的是25  如果需要谷歌邮箱的话 443 端口号
	$mail->Port=25;
	//设置发件人的邮箱地址
	$mail->From=Config::get('mailarr.username'); //
	// $mail->FromName='我';//
	//设置SMTP是否需要密码验证
	$mail->SMTPAuth=true;
	//发送方
	$mail->Username=Config::get('mailarr.username');
	$mail->Password=Config::get('mailarr.password');//C客户端的授权密码
	//发送邮件的主题
	$mail->Subject=$title;
	//内容类型 文本型
	$mail->AltBody="text/html";
	//发送的内容
	$mail->Body=$content;
	//设置内容是否为html格式
	$mail->IsHTML(true);
	//设置接收方
	$mail->AddAddress(trim($to));
	if(!$mail->Send()){
		return false;
		// echo "失败".$mail->ErrorInfo;
	}else{
		return true;
	}
}
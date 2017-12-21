<?php
namespace app\admin\controller;
//导入模板引擎
use think\Controller;
//导入Db类
use think\Db;
//导入session
use think\Session;

class Allow extends controller
{
   //初始化方法
	public function _initialize(){
		// 判断是否登录
		if (!session('?islogin')) {
			return $this->error('请登录','/admin/login',-1,2);
		}
		// //判断权限
		// $request = request();
		// //获取当前访问路径
		// $url =  $request->baseUrl();
		// // echo $request->pathinfo();
		// // echo $url;
		// //获取session中的权限信息
		// $list = session('islogin.qx');
		// // var_dump($list);
		// //判断是否具有权限
		// if (empty($list) || !in_array($url,$list)) {
		// 	echo '抱歉您没有该权限,请联系超级管理员';die;
		// }
	}
    
}

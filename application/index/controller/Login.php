<?php 
namespace app\index\controller;
//导入模板引擎
use think\Controller;
//导入Db类
use think\Db;
//导入session类
use think\Session;

class Login extends Controller{
	public function login(){
		//加载模板
		return $this->fetch("/login");
		
	}

	//执行登录
	public function dologin(){
		// echo 11111;
		//创建请求对象
		$request=request();
		//获取值
		$username=$request->param('username');
		$pwd=$request->param('password');
		// echo $username;
		//获取数据库数据
		$info=Db::name("users")->where('email',$username)->whereor('phone',$username)->whereor('username',$username)->where('is_del',0)->find();
		// var_dump($info);exit;
		//判断用户名是否存在
		   if(empty($info)){
		   		echo 2;exit;
		   		//判断密码是否正确
		   	}elseif($info['password']!=md5($pwd)){
		   		echo 3;exit;
		   	}else{
		   		//判断邮箱是否可登录
				if ($username == $info['email']) {
					if ($info['status'] != 2) {
						echo 4;
						exit;
					}
				}
				//判断是否封号
				if ($info['status'] == 0) {
					echo 6;
					exit;
				}
				//把用户的信息存储在session里
				if (empty($info['username'])) {
					$data['username'] = $username;
				}else{
					$data['username'] = $info['username'];
				}
				$data['id'] = $info['id'];
		   		session('user',$data);
		   		echo 1;
		   	}
	}

	//退出
	public function logout(){
		//销毁session
		session('user',null);
		return redirect('/');
	}
}



 ?>
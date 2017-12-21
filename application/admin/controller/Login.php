<?php
namespace app\admin\controller;
//导入模板引擎
use think\Controller;
//导入Db类
use think\Db;
//导入session类
use think\Session;
	/**
	* 后台登录
	*/
	class Login extends controller
	{
		public function index()
		{	//加载模板
			return $this->fetch('/login');

		}
		//执行登录
		public function dologin()
		{
			//创建对象
			$request=request();
			$fcode=$request->param('fcode');
			$pwd = $request->param('password');
			$uname = $request->param('username');
			// var_dump($request->param());exit;
			// var_dump($fcode);
			//检测验证码
			if(!captcha_check($fcode)){
				$this->error("验证码错误","/admin/login");
			}else{
				//获取数据库数据
				$info=Db::name("admins")->where('username',$uname)->find();
				// var_dump($info);
				if(!empty($info)){
					//检测密码
					if(md5($pwd)==$info['password']){
						//记录登录信息
			            $arr = array(
			                'last_login_time'=>time(),
			                'last_login_ip'=>$_SERVER['REMOTE_ADDR'],
			                'login_num'=>$info['login_num']+1
			            );
			            db('admins')->where('id',$info['id'])->update($arr);
						//把用户的信息存储在session里
						//获取现有权限列表
						// var_dump($info);
						$role = explode(',',$info['role_id']);
						$mrole = '';
						foreach ($role as $value) {
							$mrole .= Db::name('node_list')->where('role_id',$value)->find()['nid'].',';
						}
						$mrole = array_unique(explode(',',$mrole));
						array_pop($mrole);
						foreach ($mrole as $node_id) {
							$res[] = Db::name('node')->where('id',$node_id)->field('url')->find()['url'];
						}
						//现有权限url
						$res[] = '/admin/welcome.html';
						$res[] = '/admin/index';
						//增加执行权限
						foreach ($res as $url) {
							if (strpos($url,'add')) {
								$cname = substr($url,0,strripos($url,'/'));
								$res[] = $cname.'/doadd';
							}else if(strpos($url,'edit')){
								$cname = substr($url,0,strripos($url,'/'));
								$res[] = $cname.'/doedit';
							}
						}
						$info['qx'] = $res;
						// var_dump($info);exit;
						session("islogin",$info);
						session('islogin.login_num',session('islogin.login_num')+1);
						$this->success("登录成功","/admin/index");
					}else{
						$this->error("登录失败","/admin/login");
					}
				}
			}
		}
		//退出登录
		public function logout()
		{	//销毁session
			Session::delete('islogin');
			$this->success("退出登录成功","/admin/login");
		}
		
	}
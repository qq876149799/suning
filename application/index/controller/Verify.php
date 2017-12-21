<?php
namespace app\index\controller;
use think\Db;
use think\Controller;
use think\Session;
	/**
	* 	前台验证
	*/
	class Verify extends Controller
	{
		
		function getVerify()
		{
			$request = request();
			$action = $request->get('action');
			// var_dump($action);exit;
			//找回密码验证
			if ($action == 'password') {
				$info=$request->only(['phone','code']);
				if(!captcha_check($info['code'])){
						$this->error("验证码错误","/forget/index");
				}
				if(!Db::table('suning_users')->where('phone',$info['phone'])->find()){
					$this->redirect('/forget/index');
				}
				$pattern = "/(\d{3})\d{4}(\d{4})/";
				$replacement = "\$1****\$2";
				preg_replace($pattern, $replacement, $info['phone']);
				$flag = 're';
			}else if($action == 'email'){
				//绑定邮箱验证
				$flag = 'bind';
				$phone = $request->param('phone');
				$info = ['phone'=>$phone];
			}else if($action == 'uppwd'){
				//修改密码
				if(!Session::get('user')){
					return redirect('/index/login');
				}
				$flag= 'uppwd';
				$info=session::get('user');
				$phone=Db::table('suning_users')->where('id',$info['id'])->field('phone')->find();
				$email=Db::table('suning_users')->where('id',$info['id'])->field('email')->find();
				$info['phone']=$phone['phone'];
				$info['email']=$email['email'];
			}
			// var_dump($info);exit;
			return view('/member_safe',['flag'=>$flag,'info'=>$info]);
		}
	}
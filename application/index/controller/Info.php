<?php
namespace app\index\controller;

use think\Controller;
use think\Session;
use think\Db;

	//个人信息
	class Info extends controller
	{
		
		function getIndex()
		{	
			//检查是否登录
			if(!session("?user")){
				return $this->redirect('/index/login');
			}
			//查询用户信息
			$info = Db::name('user_info')->where('uid',session('user.id'))->find();
			$user = Db::name('users')->where('id',session('user.id'))->field('username,email,phone')->find();
			// var_dump($user);
			
			$info['birthday'] = date("Y/m/d",$info['birthday']);
			// var_dump($info);
			return view('/member_info',['info'=>$info,'user'=>$user]);
		}

		//执行修改
		public function postDoedit()
		{
			// echo 'sadjkasjdas';exit;
			$request = request();
			//获取数据
			$data = $request->param('');
			// var_dump($data);exit;
			$id = session('user.id');
			// echo $data['birthday'];
			$data['birthday'] = strtotime($data['birthday']);
			$users = [
				'id'=>$id,
				'username'=>$data['username']
			];
			$info = [
				'fullname'=>$data['fullname'],
				'nickname'=>$data['nickname'],
				'sex'=>$data['sex'],
				'birthday'=>$data['birthday'],
				'address'=>$data['address']
			];
			//保存数据
			$old = Db::name('users')->where('id',$id)->find();
			$oldinfo = Db::name('user_info')->where('uid',$id)->field('fullname,nickname,sex,birthday,address')->find();
			if ($old['username'] != $users['username']) {
				$res = Db::name('users')->update($users);
			}else{
				$res = true;
			}
			if($res){
				if($oldinfo != $info){
					Db::name('user_info')->where('uid',$id)->update($info);
					//全部成功
					echo 1;
				}else{
					//插入info表失败
					echo 2;
				}
			}else{
				//插入user表失败
				echo 3;
			}
		}

		//城市级联数据
		public function getCity()
		{
			$request = request();
			$upid = $request->param('upid');
			$city = Db::table('district')->where('upid',$upid)->select();
			$city = json_encode($city);
			echo $city;
		}

		//Ajax检查数据
		public function getCheck()
		{
			$request = request();
			$data = $request->param('');
			if (isset($data['username'])) {
				//用户名查重
				$res = Db::name('users')->where('username',$data['username'])->find();
				if (empty($res)) {
					//用户名可用
					echo 1;
				}else{
					//用户名重复
					echo 2;
				}
			}
			if (isset($data['code'])) {
				//校验验证码
				if(captcha_check($data['code'])){
					//验证码通过
					echo 1;
				}else{
					//验证码不通过
					echo 2;
				}
			}
		}

		//绑定邮箱
		public function getBind(){
			$request = request();

			$user = Db::name('users')->where('id',session('user.id'))->field('username,email,phone')->find();
			// var_dump($user);exit;
			return view('/member_safe',['user'=>$user]);
		}
		public function getBindd(){
			$request = request();
			$code = $request->param('code');
			$phone = $request->param('phone');
			// $email=$request->param('email');
			// var_dump($phone);exit;
			$user = Db::name('users')->where('id',session('user.id'))->field('username,email,phone')->find();
			// var_dump($user);exit;
			$res = Db::name('users')->where('phone',$phone)->find();
			$res = sendSms($phone);
			$fcode = session('code');
			 // var_dump($res);exit;							

			
			// var_dump($res);exit;

			//加载模板
			return view('/member_mobile_check',['user'=>$user]);

		}
		public function getBindds(){
			$request=request();

			// $fcode=$request->param('fcode');
			// var_dump($fcode);exit;

			return view('/member_mobile_bind');
		}
		
		 public function getBinddss(){
		 	$request=request();
		 	$email=$request->param('email');
		 	$info=Session::get('user');
		 	// var_dump($info);
		 	// var_dump($email);
		 	$s=sendmail($email,'测试',"<a href='http://object.cn/info/Binddd?id={$info['id']}&email={$email}'>成功</a>");
		 	// var_dump($s);
		 	if($s){
		 		$this->success("发送成功请去邮箱激活","https://mail.163.com");
		 	}else{
		 		$this->error("未成功");
		 	}
				
		  }		
		  public function getBinddd(){
		  		$id=$_GET['id'];
		  		$data['email']=$_GET['email'];
		  		$data['token']=rand(1,10000);
		  		$data['status']=0;
		  		$data['status']=2;
		  		$cc=Db::name('users')->where('id',$id)->update($data);
		  		if($cc){
		  			$this->success("成功","/info/bindok/");
		  		}else{
		  			$this->error("失败");
		  		}
		  }

		  public function getBindok(){
		  	return view('/youxiang_success');
		  }
		  public function getverify(){
		  	$request=request();
            $info=$request->param();
            // var_dump($info);exit;
            return view('/findpwd',['phone'=>$_GET['phone']]);
		  }
		  //检查
	        public function postCheck()
	        {
	            $phone=$_POST['p'];
	            $res=sendSms($phone);
	            echo $res;
	        }
	        //找回密码
	        public function postFind(){
	            $request=request();
	            return view('/member_pwd',['phone'=>$request->param('phone')]);
	        }
	        //Reset方法中判断错误进入的方法
	        public function getFind(){
	            $request=request();
	            return view('/member_pwd',['phone'=>$request->param('phone')]);
	        }
	        //处理修改的密码
	        public function postReset(){
	            $request=request();
	            $oldpwd=$request->param('oldpwd');
	            $newpwd=$request->param('newpwd');
	            $repwd=$request->param('repwd');
	            $phone=$request->param('loginName');
	            $user=Session::get('user');
	            $info=Db::table('suning_users')->where('id',$user['id'])->find();
	            if($info['password']!=md5($oldpwd)){
	                $this->error('旧密码输入错误',"/info/find?phone={$request->param('loginName')}");
	            }
	            if(md5($oldpwd)==md5($newpwd)){
	            	$this->error('新密码不能与旧密码一致',"/info/find?phone={$request->param('loginName')}");
	            }
	            if(md5($newpwd)!=md5($repwd)){
	            	$this->error('密码输入不一致，请确认',"/info/find?phone={$request->param('loginName')}");
	            }
	            // echo 111;
	            if(Db::table('suning_users')->where('id',$user['id'])->update(['password'=>md5($newpwd)])){
	                return view('/password_success');
	                // echo 111;
	            }
	        }

	      //上传头像
	      public function postupload(){
    		// 获取表单上传文件 
	      		$request = request();

	      		// var_dump($request);exit;
	      		 // var_dump($request->param(''));exit;
			    $file = request()->file('image');
			    // var_dump($file);exit;
		    // 移动到框架应用根目录/uploads/head 目录下
		    	if($file){
		        $info = $file->move(ROOT_PATH . 'public' . DS . 'head');
		        // var_dump($info);exit;
		        if($info){
		          
		            $files=$info->getSaveName();
		           	// echo $files;
		            $files='/head/'.$files;
		            // var_dump($files);exit;	
		           	
		        }else{
		            // 上传失败获取错误信息
		            echo $file->getError();
		        }

		        $data['head']=$files;
		        $id=session::get("user.id");
		        // var_dump($files);exit;
		        // echo $id;exit;
		        Db::table("suning_user_info")->where("uid",$id)->update($data);
		    }
		 	 $this->redirect('/info/index');

}
	}
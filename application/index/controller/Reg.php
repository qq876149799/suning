<?php 
namespace app\index\controller;
//导入模板引擎
use think\Controller;
//导入Db类
use think\Db;
//导入session类
use think\Session;

	class Reg extends Controller{
		public function getIndex(){
			//加载模板
			return $this->fetch("/reg");
		}
	

	//处理注册
	public function postDoreg(){
		// echo 23213213123;
		$request = request();
		$code = $request->param('code');
		$phone = $request->param('phone');
		$pwd = $request->param('password');
		// echo $phone;exit;
		//判断验证码是否正确
		$fcode = session('code');
		if ($fcode != $code) {
			echo 2;
		}else{
			//插入数据库
			$data = ['phone'=>$phone,'password'=>md5($pwd),'addtime'=>time(),'status'=>1,'is_del'=>0];
			$id = Db::name('users')->insertGetId($data);
			// echo db('suning_users')->getlastsql();exit;
			if($id){
				//向info表插入空数据
				Db::name('user_info')->insert(['uid'=>$id,'head'=>'/static/index/images/0000000000_01_100x100.jpg']);
				session(null,'code');
				$p = '';
				for ($i=0; $i < strlen($phone); $i++) { 
					if ($i>=3 && $i<9) {
						$phone[$i] = '*';
					}
					$p .=$phone[$i];
				}
				session('phone',$p);
				echo 1;
			}
		}
	}

	//检查
	public function postCheck()
	{
		$request = request();
		$phone = $request->param('phone');
		//判断是否已注册
		$res = Db::name('users')->where('phone',$phone)->find();
		if (!empty($res)) {
			echo 2;
		}else{
			//发送验证码
			$res = sendSms($phone);
			echo $res;
		}
	}

	public function getRegok()
	{
		return view('/reg_success');
	}
}

<?php 
namespace app\index\controller;
//导入模板引擎
use think\Controller;
//导入Db类
use think\Db;
//导入session类
use think\Session;
    class Forget extends Controller{
        //进入忘记密码页面
        public function getIndex(){
            return view('/forget');
        }
        //处理第一步传的值
        public function getNext(){
            $request=request();
            $info=$request->only(['userName','code']);
            //将用户名存进session中
            session('username',$info['userName']);
            $username=$info['userName'];
            $pattern = "/(\d{3})\d{4}(\d{4})/";
            $replacement = "\$1****\$2";
            $phone=preg_replace($pattern, $replacement, $username);
            //判断userName是否存在
            if(!Db::table('suning_users')->where('phone',$info['userName'])->find()){
                $this->redirect('/forget/index');
            }else{
                if(!captcha_check($info['code'])){
                    $this->error("验证码错误","/forget/index");
                }
                return view('/member_safe',['phone'=>$phone]);
            }
        }
        public function getVerify(){
            $request=request();
            // var_dump($request);exit;
            $info=$request->param();
            // var_dump($info);exit;
            return view('/verify',['phone'=>$_GET['phone']]);
        }
            //检查
        public function postCheck()
        {
            $phone=$_POST['p'];
            $res=sendSms($phone);
            echo $res;
        }
        public function postFind(){
            $request=request();
            // var_dump($request->param());exit;   
            return view('/find_password',['phone'=>$request->param('phone')]);
        }
        public function postReset(){
            $request=request();
            // var_dump($request->param());exit;
            $pwd=$request->param('pwd');
            $repwd=$request->param('repwd');
            // $email=$request->param('email');
            // var_dump($request);exit;
            $info=Db::table('suning_users')->where('phone',$request->param('phone'))->find();
            // var_dump($info);exit;
            // $phone=$info['email'];
            // var_dump($email);exit;
            // $email=$info(['email']);
            // echo $email;exit;
            // if()
            // var_dump(Db::table('suning_users')->where('phone',$request->param('phone'))->update(['password'=>md5($pwd)]));
            if($info['password']==md5($pwd)){
                return view('/pwd_success');
            }
            if(Db::table('suning_users')->where('phone',$request->param('phone'))->update(['password'=>md5($pwd)])){
                return view('/pwd_success');
            }
        }

    }
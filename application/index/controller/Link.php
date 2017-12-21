<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
//前台友情链接
class Link extends Controller
{
	//前台遍历
    public function getIndex()
    {
    	$info=Db::table('suning_link')->where('status',1)->select();
        return view('/link',['info'=>$info]);
    }
    //申请友情链接
    public function postDolink(){
    	$request=request();
		$fcode=$request->param('fcode');
    	if(!captcha_check($fcode)){
			$this->error("验证码错误","/link/index");
		}else{
			$data=$request->only(['name','url']);
			if(!$data['name']){
				$this->error("连接名不能为空","/link/index");
			}else{
				if($data['url']=='http://'){
					$this->error("链接地址不能为空","/link/index");
				}
			}
			$data['status']=2;
			$s=Db::table("suning_link")->insert($data);
		   	if($s){
		   		$this->success("添加成功","/link/index");
		   	}else{
		   		$this->error("添加失败","/link/index");
		   	}
		}
    }
}

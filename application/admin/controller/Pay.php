<?php 
namespace app\admin\controller;

//导入Controller
use think\Controller;
use think\Db;
	class Pay extends Allow
	{
		//充值列表
		public function getIndex(){
			$pay=Db::table('suning_pay')->select();
			// var_dump($pay);exit;
			$data=[];
			foreach($pay as $key=>$value){
				$info=Db::table('suning_users')->where('id',$value['uid'])->find();
				$data[$key]['name']=$info['phone'];
				$data[$key]['money']=$value['money'];
				$data[$key]['createtime']=$value['createtime'];
				$data[$key]['status']=$value['status'];
				$data[$key]['id']=$value['id'];
			}
			return view('/pay-list',['data'=>$data]);
		}
		//处理充值
		public function getStatus(){
			$request=request();
			$id=$request->param('id');
			$money=$request->param('momey');
			//更新状态
			$pay=Db::table('suning_pay')->where('id',$id)->update(['status'=>1]);
			// var_dump($pay);
			$pays=Db::table('suning_pay')->where('id',$id)->find();
			if($pays){
				$uid=Db::table('suning_users')->where('id',$pays['uid'])->field('id')->find()['id'];
				// var_dump($uid);exit;				
				$old=Db::table('suning_user_info')->where('uid',$uid)->field('money')->find()['money'];
				$old=(float)$old;
				$new=$old+$money;
				Db::table('suning_user_info')->where('uid',$uid)->update(['money'=>$new]);
				$this->redirect('/admin-pay/index');
			}
		}
		//删除充值记录
	}
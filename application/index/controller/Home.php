<?php
namespace app\index\controller;

use think\Session;
use think\Db;
use think\Controller;

	class Home extends Controller
	{
		//我的易购
		public function getIndex(){
			//判断是否登录
			if(!Session::get('user')){
				return redirect('/index/login');
			}
			//我的收藏
			$info=Db::table('suning_collect')->select();
			$data=[];
			foreach($info as $key=>$value){
				$infos=Db::table('suning_goods')->where('id',$value['gid'])->find();
				//获取商品ID
				$data[$key]['gid']=$infos['id'];
				//获取商品名称
				$data[$key]['name']=$infos['name'];
				//获取商品图品信息
				$data[$key]['img'] = explode(',',$infos['img'])[0];
				$infoss=Db::table('suning_goods_attr')->where('gid',$value['gid'])->find();
				$data[$key]['price']=$infoss['price'];
				
			}
			$count=count($data);
			//加载购物车
			$user=Session::get('user');
			$id=$user['id'];
			$info=Db::table('suning_cart')->where("uid","{$id}")->select();
			// var_dump($info);
			//查商品信息
			$res = [];
			//组合页面需要的数据
			foreach ($info as $key => $value) {
				$goods = Db::name('goods')->where('id',$value['gid'])->find();
				$res[$key]['gid'] = $goods['id'];
				$res[$key]['name'] = $goods['name'];
				$res[$key]['img'] = explode(',',$goods['img'])[0];
				//查询属性信息
				$attr = Db::name('goods_attr')->where('id',$value['aid'])->find();
				// var_dump($attr);
				$res[$key]['price'] = $attr['price'];
				// 小计
				$res[$key]['subtotal'] = $attr['price']*$value['gnum'];	
			}
			$lengh=count($res);
			$userinfo=Db::table('suning_user_info')->where('uid',$user['id'])->find();

			//查历史记录
			$history = Db::name('history')->where('uid',session('user.id'))->select();
			//通过商品id 查商品图 和名字
			foreach ($history as $key => $value) {
				$ress = Db::name('goods')->where('id',$value['gid'])->field('name,img')->find();
				$history[$key]['img'] = explode(',',$ress['img'])[0];
				$history[$key]['name'] = $ress['name'];
			}
			
			return view('/member_collect_goods',['data'=>$data,'count'=>$count,'res'=>$res,'lengh'=>$lengh,'userinfo'=>$userinfo,'history'=>$history]);
		}
		//我的收藏
		public function getCollect(){
			//判断是否登录
			if(!Session::get('user')){
				return redirect('/index/login');
			}
			$info=Db::table('suning_collect')->select();
			$data=[];
			foreach($info as $key=>$value){
				$infos=Db::table('suning_goods')->where('id',$value['gid'])->find();
				//获取商品ID
				$data[$key]['gid']=$infos['id'];
				//获取商品名称
				$data[$key]['name']=$infos['name'];
				//获取商品图品信息
				$data[$key]['img'] = explode(',',$infos['img'])[0];
				$infoss=Db::table('suning_goods_attr')->where('gid',$value['gid'])->find();
				$data[$key]['price']=$infoss['price'];
				
			}
			$count=count($data);
			return view('/member_collect',['data'=>$data,'count'=>$count]);
		}
		//添加收藏
		public function getAddcollect(){
			//判断是否登录
			if(!Session::get('user')){
				return redirect('/index/login');
			}
			$user=Session::get('user');
			$data=[];
			//获取用户id
			$data['uid']=$user['id'];
			$request=request();
			//获取商品id
			$gid=$request->param('id');
			$data['gid']=$gid;
			$data['time']=time();
			//判断是否收藏过该商品
			if(!Db::table('suning_collect')->where('gid',$gid)->select()){
				if(Db::table('suning_collect')->insert($data)){
					echo 1;
				}else{
					echo 0;
				}
			}else{
				echo 2;
			}
		}
		//取消收藏
		public function getDelete(){
			$request=request();
			$gid=$request->param('id');
			Db::table('suning_collect')->where('gid',$gid)->delete();
			//重定向
        	$this->redirect("/home/collect");
		}
		//充值
		public function getPay(){
			//判断是否登录
			if(!Session::get('user')){
				return redirect('/index/login');
			}

			$user=Session::get('user');
			$money=Db::table('suning_user_info')->where('uid',$user['id'])->find()['money'];
			// var_dump($money);exit;
			return view('/member_pay',['user'=>$user,'money'=>$money]);
		}
		//处理充值
		public function postDopay(){
			$request=request();
			$user=Session::get('user');
			$data=[];
			$data['money']=$request->param('money');
			$data['uid']=$user['id'];
			$data['createtime']=time();
			$data['status']=0;
			// var_dump($data);
			Db::table('suning_pay')->insert($data);
			return view('/pay_success');
		}
	}
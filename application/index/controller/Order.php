<?php
namespace app\index\Controller;

use think\Controller;
use think\Db;
use think\Session;

	/**
	* 订单生成
	*/
	class Order extends controller
	{

		public function postCreate()
		{
			$request = request();
			// $cids = $request->post('cids/a');
			// echo json_encode($cids);exit;
			// echo 21312381;exit;
			// echo json_encode($request->post(''));exit;
			//组合order表数据
			//1.生成订单号
			$order_num = date('Ymd').mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9);
			// echo $order_num;
			//2.获得用户id
			$uid = session('user.id');
			//3.获得地址id
			$add_id = $request->post('addid');
			//4.获得总价
			$total = $request->post('total');
			//5.下单时间
			$time = time();
			//6.状态，未支付
			$data1 = [
				'order_num'=>$order_num,
				'uid'=>$uid,
				'add_id'=>$add_id,
				'total'=>$total,
				'create_time'=>$time,
				'status'=>0
			];
			// echo json_encode($data1);
			$oid = Db::name('orders')->insertGetId($data1);
			// $oid = 1;
			//存入orders表成功
			if ($oid) {
				//组合order_info数据
				$cids = $request->post('cids/a');
				// echo json_encode($cids);exit;
				$data2 = [];
				foreach ($cids as $value) {
					//查购物车信息
					$cart = Db::name('cart')->where('id',$value)->find();
					//查商品信息
					$goods = Db::name('goods')->where('id',$cart['gid'])->find();
					//查商品属性
					$attr = Db::name('goods_attr')->where('id',$cart['aid'])->find();
					$data2['oid'] = $oid;
					$data2['gid'] = $cart['gid'];
					$data2['gname'] = $goods['name'];
					$data2['gnum'] = $cart['gnum'];
					$data2['attr'] = $attr['attr1'].','.$attr['attr2'].','.$attr['attr3'];
					$data2['img'] = explode(',',$goods['img'])[0];
					$data2['price'] = $attr['price'];
					//存入order_info表
					$res = Db::name('order_info')->insert($data2);
				}		
			}else{
				echo 0;exit;
			}
			//成功信息
			if($res){
				// 成功返回订单id  删除购物车商品
				foreach ($cids as $value) {
					Db::name('cart')->where('uid',session('user.id'))->where('id',$value)->delete();
				}
				echo $oid;
			}else{
				//删除order表数据
				Db::name('orders')->where('id',$oid)->delete();
				echo 0;
			}
		}

		//加载支付模板
		public function getTopay()
		{
			$request = request();
			$oid = $request->param('id');
			//查订单总金额
			$order = Db::name('orders')->where('id',$oid)->find();
			// var_dump($order);
			//查订单有几个商品
			$goods = Db::name('order_info')->where('oid',$order['id'])->select();
			// var_dump($goods);
			$sum = count($goods);
			//查用户余额
			$money = Db::name('user_info')->where('uid',session('user.id'))->field('money')->find()['money'];
			return view('/pay',['sum'=>$sum,'money'=>$money,'total'=>$order['total'],'oid'=>$oid]);
		}

		//支付扣款
		public function postDopay()
		{
			$request = request();
			//获取当前用户id
			$uid = session('user.id');
			//获取订单id
			$oid = $request->post('oid');
			//获取订单金额
			$money = $request->post('money');
			//查询用户金额
			$u_money = Db::name('user_info')->where('uid',$uid)->field('money')->find()['money'];
			//减去金额
			$new = $u_money-(float)$money;
			//数据库操作
			if (Db::name('user_info')->where('uid',session('user.id'))->update(['money'=>$new])) {
				//更改订单状态
				if(Db::name('orders')->where('id',$oid)->update(['status'=>1])){
					//减库存
					//查到所有的商品
					$goods = Db::name('order_info')->where('oid',$oid)->select();

			foreach ($goods as $key => $value) {
				$attrs = explode(',',$value['attr']);
				$where['gid'] = $value['gid'];
				$where['attr1'] = $attrs[0];
				$where['attr2'] = $attrs[1];
				$where['attr3'] = $attrs[2];
				$g = Db::name('goods_attr')->where($where)->find();
				$new = $g['num']-$value['gnum'];
				// echo $new;exit;
				//减库存
				Db::name('goods_attr')->where('id',$g['id'])->update(['gid'=>$value['gid'],'num'=>$new]);
			}	
					echo 1;
				}else{
					echo 0;
				}
				
			}else{
				echo 0;
			}
		}

		//获取城市级联数据
		public function getAddress()
		{
				$request=request();
				$upid=$request->param("upid");
				// var_dump($upid);exit;
				//获取数据
				$data=Db::table("district")->where("upid","{$upid}")->select();
				echo json_encode($data);
		}

		// 添加新地址
		public function postInsert()
		{
			$adds=[];
			$arr=$_POST['arr'];
			$adds['huo']=implode("-",$arr);
			$adds['phone']=$_POST['phone'];
			$adds['adds']=$_POST['ads'];
			$adds['name']=$_POST['peo'];
			$adds['uid']=session("user.id");
			// echo json_encode($adds);exit;
			// Db::table("suning_address")->insert($adds);
			// echo "<pre>";
			// print_r($adds);
			//执行添加操作
			if(Db::table("suning_address")->insert($adds)){
				echo 1;
			}else{
				echo 0;
				}
		}
		public function getSend(){
			
		}
	}
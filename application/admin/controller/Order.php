<?php
namespace app\admin\Controller;

use think\Controller;
use think\Db;

	/**
	* 订单管理
	*/
	class Order extends Allow
	{
		//列表
		function getIndex()
		{
			$data = Db::name('orders')->select();
			$status = [0=>'买家未支付',1=>'买家已支付',2=>'等待买家收货',3=>'订单完成'];
			//修改数字状态为汉字
			foreach ($data as $key => $value) {
				$data[$key]['state'] = $status[$data[$key]['status']];
			}
			
			// var_dump($data);
			foreach ($data as $key => $value) {
				//查用户名
				$users = Db::name('users')->where('id',$value['uid'])->find();
				// var_dump($users);
				if (empty($users['username'])) {
					if (empty($users['email'])) {
						$data[$key]['user'] = $users['phone'];
					}else{
						$data[$key]['user'] = $users['email'];
					}
				}else{
					$data[$key]['user'] = $users['username'];
				}
				//查收货信息
				$address = Db::name('address')->where('id',$value['add_id'])->find();
				// var_dump($address);
				$data[$key]['linkman'] = $address['name'];
				$data[$key]['address'] = $address['huo'].' '.$address['adds'];
				$data[$key]['phone'] = $address['phone'];
			}
			// var_dump($data);
			return view('/orders-list',['data'=>$data]);
		}

		//发货
		public function getSend()
		{
			$request = request();
			$id = $request->param('id');
			//修改数据库
			$data = [
				'id'=>$id,
				'status'=>2
			];
			if(Db::name('orders')->update($data)){
				echo 1;
			}else{
				echo '失败';
			}
		}

		//订单详情
		public function getInfo()
		{
			$request = request();
			$id = $request->param('id');
			$data = Db::name('order_info')->where('oid',$id)->select();
			// var_dump($data);
			return view('/order-info',['data'=>$data]);
		}
	}
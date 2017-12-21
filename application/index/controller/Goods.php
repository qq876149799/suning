<?php
namespace app\index\Controller;

use think\Controller;
use think\Db;
use think\Session;
	
	/**
	* 商品详情
	*/
	class Goods extends controller
	{
		
		function getIndex()
		{
			$request = request();
			$id = $request->get('id');
			$goods = Db::name('goods')->where('id',$id)->find();
			$goods['img'] = explode(',',$goods['img']);
			$mid = $goods['mid'];
			//通过mid查询该商品所含有属性
			$m = Db::name('type')->where('m_id',$mid)->select();
			//处理属性 数组
			foreach ($m as $key => $value) {
				$m[$key]['value'] = explode(',',$value['value']);
			}
			// var_dump($m);
			// var_dump($goods);
			// 查商品的评价信息
			$pingjia = Db::name('appraise')->where('gid',$id)->select();
			// var_dump($pingjia);
			// 查用户头像和名称
			if (!empty($pingjia)) {

				foreach ($pingjia as $key => $value) {
					$info = Db::name('user_info')->where('uid',$value['uid'])->find();
					// var_dump($info);
						$account = Db::name('users')->where('id',$value['uid'])->find();
					if (empty($info['nickname'])) {
						
						if (empty($account['username'])) {
							if (empty($account['email'])) {
								$pingjia[$key]['uname'] = $account['phone'];
							}else{
								$pingjia[$key]['uname'] = $account['username'];
							}
						}else{
							$pingjia[$key]['uname'] = $account['username'];
						}
					}else{
						$pingjia[$key]['uname'] = $account['phone'];
					}
					$pingjia[$key]['head'] = $info['head'];
				}
			}else{
				$pingjia = '';
			}

			//历史记录
			if (Session::has('user')) {
				// echo '<script>alert("1111");</script>';
				$history = [
					'gid'=>$goods['id'],
					'uid'=>session('user.id')
				];

				//查原来是否就有
				$you = Db::name('history')->where('gid',$goods['id'])->find();
				if($you){
					//有  删除
					Db::name('history')->where('id',$you['id'])->delete();
				}
				Db::name('history')->insert($history);
			}else{
				// echo '<script>alert("2222");</script>';
			}
			
			
			// var_dump($pingjia);
			return view('/goods_info',['goods'=>$goods,'m'=>$m,'pingjia'=>$pingjia]);
		}

		//Ajax查库存
		public function getSearch()
		{
			$request = request();
			$gid = $request->get('id');
			$attrs = $request->except(['id','action']);
			// var_dump($attrs);exit;
			if ($attrs['attr2']=='null') {
				// echo 1;
				$where['gid'] = $gid;
				$where['attr1'] = $attrs['attr1'];

			}else if ($attrs['attr3']=='null') {
				// echo 2;
				$where['gid'] = $gid;
				$where['attr1'] = $attrs['attr1'];
				$where['attr2'] = $attrs['attr2'];
			}else{
				// echo 3;
				$where['gid'] = $gid;
				$where['attr1'] = $attrs['attr1'];
				$where['attr2'] = $attrs['attr2'];
				$where['attr3'] = $attrs['attr3'];
			}
			$res = Db::name('goods_attr')->where('gid',$gid)->where($where)->find();

			// var_dump($attrs);exit;
			// where();
			// $res = Db::name('goods_attr')->where($where)->find();
			// echo db('suning_goods_attr')->getlastsql();
			// var_dump($request->param('attr3'));
			
			// echo $sum;
			// var_dump($request->get(''));
			// echo 1;
			//查询属性表的库存
			
			// echo json_encode($res);
			echo json_encode($res);
		}

		//获取单独一条库存
		public function getNum()
		{
			$request = request();
			$gid = $request->param('gid');
			$data = Db::name('goods_attr')->where('gid',$gid)->find();
			// var_dump($data);
			// echo 1;
			echo json_encode($data);
		}
		
	}
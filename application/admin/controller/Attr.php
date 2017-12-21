<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;

	/**
	* 后台属性列表
	*/
	class Attr extends Allow
	{
		//获取属性
		public function getAttr($mid)
		{
			// $attrs = Db::name('type_item')->where('type_id',$type_id)->select();
			$data = Db::name('type')->where('m_id',$mid)->select();
			// $attrs = [];
			foreach ($data as $key => $value) {
				$res = Db::name('type_item')->where('type_id',$value['id'])->select();
				// var_dump($res);
				foreach ($res as $key => $value) {
					$data[$value['id']]['item'] = $value['item'];
				}
			}
			// return $attrs;
			// var_dump($data);
		}

		//列表
		public function getIndex()
		{
			$request = request();
			$id = $request->param('id');
			//根据mid查询属性列表
			$data = Db::name('type')->where('m_id',$id)->select();

			return view('/attr-list',['data'=>$data]);
		}

		//添加
		public function getAdd()
		{
			$request = request();
			$mid = $request->param('mid');
			// var_dump($mid);
			return view('/attr-add',['mid'=>$request->param('mid')]);
		}

		//执行添加属性
		public function postDoadd()
		{
			$request = request();
			$data = [
				'm_id'=>$request->param('mid'),
				'name'=>$request->param('name'),
			];
			// var_dump($data);exit;
			// 检查是否够3个属性
			$check = Db::name('type')->where('m_id',$data['m_id'])->select();
			// var_dump(count($check));exit;
			if (count($check)==3) {
				echo '该模型已有3个属性，无法继续添加';exit;
			}
			//查询是否原来就有这个属性
			$result = Db::name('type')->where('name',$data['name'])->where('m_id',$data['m_id'])->find();
			// var_dump($result);exit;
			if (empty($result)) {
				//原来没有这个属性
				// echo 'mei';
				$data['value'] = $request->post('val');
				// var_dump($data); 
				// 插入数据库
				if(Db::name('type')->insert($data)){
					echo 1;
				}else{
					echo 0;
				}
			}else{
				//原来就有
				//取id
				$id = $result['id'];
				// echo 'you';
				//获取输入的属性值
				$Nattr = $request->param('val');
				// var_dump($attr);
				//拆分查询
				$a1 = explode(',',$Nattr);
				$a2 = explode(',',$result['value']);
				//拼接，去重
				$data1 = array_unique(array_merge($a1,$a2));
				//组成字符串
				$data1 = implode(',',$data1);
				// var_dump($data1);exit;
				//插入数据
				if(Db::name('type')->where('id',$id)->update(['value'=>$data1])){
					echo 1;
				}else{
					echo 0;
				}
			}
		}

		//执行修改属性
		public function postDoedit()
		{
			$request = request();
			// var_dump($request->post(''));
			$data = [
				'id'=>$request->post('id'),
				'value'=>$request->post('value')
			];
			//判断是否与原值相同
			$old = Db::name('type')->where('id',$data['id'])->find();
			if ($old['value'] == $data['value']) {
				// echo '相同';
				echo 1;
			}else{
				// echo '不同';
				if(Db::name('type')->update($data)){
					echo 1;
				}else{
					echo 0;
				}
			}
		}

		//执行删除属性
		public function getDelete()
		{
			$request = request();
			// var_dump($request->get('id'));
			$id = $request->get('id');
			//查询是否有商品正在使用这个属性
			//拿到该属性的值 成数组
			$type = Db::name('type')->where('id',$id)->find();
			// var_dump($type);
			$type = explode(',',$type['value']);
			// var_dump($type);

			// $where = ['attr1'=>];
			// $res = 0;
			// 遍历该属性所有值
			foreach ($type as $val) {
				// 判断是否有商品在用这个属性
				if (!Db::name('goods_attr')->where('attr1',$val)->select()) {
					if(!Db::name('goods_attr')->where('attr2',$val)->select()){
						if(!Db::name('goods_attr')->where('attr3',$val)->select()){
							//没有 返回1
							$res = 1;
							// return $res;							
						}else{
							// 有 返回2 阻止继续循环
							$res = 2;
							return $res;
						}
					}else{
						$res = 2;
						return $res;
						
					}
				}else{
					$res = 2;
					return $res;

				}
				

			}
			if ($res == 1) {
				//可以删
				if(Db::name('type')->where('id',$id)->delete()){
					echo 1;
				}else{
					echo 0;
				}

			}else if($res == 2){
				//不能删
				echo 2;
			}
		}

		//执行修改属性名
		public function postDoeditName()
		{
			$request = request();
			// var_dump($request->post(''));
			$data = [
				'id'=>$request->post('id'),
				'name'=>$request->post('name')
			];
			//判断是否与原值相同
			$old = Db::name('type')->where('id',$data['id'])->find();
			if ($old['name'] == $data['name']) {
				// echo '相同';
				echo 1;
			}else{
				// echo '不同';
				if(Db::name('type')->update($data)){
					echo 1;
				}else{
					echo 0;
				}
			}
		}

	}
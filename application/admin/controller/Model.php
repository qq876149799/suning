<?php
namespace app\admin\controller;

use think\Db;
use think\Controller;

	/**
	* 商品模型控制器
	*/
	class Model extends Allow
	{
		//模型列表
		function getIndex()
		{
			$data = Db::name('model')->select();
			return view('/model-list',['data'=>$data]);
		}

		//添加模型
		public function getAdd()
		{
			return view('/model-add');
		}

		//执行添加
		public function postDoadd()
		{
			$request = request();
			$name = $request->post('name');
			// echo $name;
			
			if(Db::name('model')->insert(['name'=>$name])){
				echo 1;
			}
		}

		//修改模型
		public function getEdit()
		{
			$request = request();
			// var_dump($request->get(''));
			$id = $request->get('id');
			//查询
			$data = Db::name('model')->where('id',$id)->find();
			return view('/model-edit',['data'=>$data]);
		}

		//执行修改
		public function postDoedit()
		{
			$request = request();
			// var_dump($request->post(''));
			$data = [
				'id'=>$request->post('id'),
				'name'=>$request->post('name')
			];
			//判断是否与原数据相同
			$old = Db::name('model')->where('id',$data['id'])->find();
			// var_dump($old);
			if ($old['name'] == $data['name']) {
				//相同
				echo 1;
			}else{
				if(Db::name('model')->update($data)){
					echo 1;
				}else{
					echo 0;
				}
			}
		}

		//删除模型
		public function getDelete()
		{
			$request = request();
			$id = $request->get('id');
			// var_dump($id);
			// 先查询该模型下是否有商品
			$res = Db::name('goods')->where('mid',$id)->select();
			// var_dump($res);exit;
			if (!empty($res)) {
				echo '该模型下还有商品，无法删除！';exit;
			}
			if(Db::name('model')->where('id',$id)->delete()){
				echo 1;
			}else{
				echo 0;
			}
		}
	}
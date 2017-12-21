<?php
namespace app\admin\controller;

use think\Db;

	/**
	* 后台批量删除
	*/
	class Delete
	{
		
		function getAction()
		{
			$request = request();
			$ids = $request->param('id/a');
			$table = $request->param('table');
			// var_dump($ids,$table);
			switch ($table) {
				case 'users':
					//回收会员
					return $this->remember($ids);
					break;
				case '':
					break;
				default:
					# code...
					break;
			}
		}

		//执行回收会员
		public function remember($ids)
		{
			// return $ids;
			$res = array();
			foreach ($ids as $val) {
				$data = ['id'=>$val,'is_del'=>1];
				if(Db::name('users')->where('id',$val)->update($data)){
					$res[] = $val;
				}
			}
			return $res;
		}
	}
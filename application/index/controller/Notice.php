<?php 
namespace app\index\controller;

use think\Session;
use think\Db;
use think\Controller;

class Notice extends Controller
{
	//加载模板
	public function getIndex(){
				$request=request();	

		        $id=$request->only('id');
		        // var_dump($id);exit;
        		$info=Db::name('notice')->where($id)->find();
        		// var_dump($info);exit;
				return view('/gonggao',['info'=>$info]);
	}
}

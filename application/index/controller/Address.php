<?php 
namespace app\index\controller;

use think\Controller;
use think\Session;
use think\Db;
	//地址管理
	class Address extends controller
	{
		public function getalladdress($id){
			$aa= array($id);
			// var_dump($aa);exit;
			$cc=implode(" ",$id);
			// var_dump($cc);exit;
			$list=Db::name("address")->where("uid","{$aa[0]['id']}")->select();
			// var_dump($list);exit;
			return $list;
		}
		public function getIndex(){
				$userid=Session::get('user');
				$address=$this->getalladdress($userid);
				// var_dump($userid);exit;
			//加载模板
			return view('/address',['address'=>$address]);
		}
		//获取城市级联数据
		public function getAddress(){
				$request=request();
				$upid=$request->param("upid");
				//获取数据
				$data=Db::table("district")->where("upid","{$upid}")->select();
				echo json_encode($data);
		}

		//地址添加
		public function postInsertaddress(){
			$request=request();
			
			$data['adds']=$request->param('adds');//街道
			// $data['city']=$request->param('city');
			$data['uid']=Session::get("user")['id'];
			$data['phone']=$request->param('phone');
			$data['name']=$request->param('name');
			// var_dump($data);exit;
			$data['huo']=$request->param('address');
			if(Db::name("address")->insert($data)){
				$this->redirect('/address/index');
			}
		}

		//地址删除
		public function getDelete(){
			// echo "111";
			$request=request();
			$id=$request->param('id');
			// var_dump($id);
			if(Db::name("address")->where("id",$id)->delete()){
				$this->redirect('/address/index');
			}else{
				$this->error("删除失败");
			}
		}

		//地址修改
		public function getEdit(){
			$request=request();
			$id=$request->param('id');
			// var_dump($id);
			$info=Db::name('address')->where('id',$id)->find();
			// var_dump($info);exit;
			return view("/addressedit",['info'=>$info]);
		}
		public function postDoedit(){
			$request=request();
			// var_dump($request);exit;
			$edit=$request->only(['name','adds','phone','huo']);
			// var_dump($edit);
			$update=Db::name('address')->where('id',$request->param('id'))->find();
			// var_dump($update);
			if($edit['name']!=$update['name']||$update['adds']!=$edit['adds']||$update['huo']!=$edit['huo']){
            if(Db::name('address')->where('id',$request->param('id'))->update($edit)){
                $this->success("修改成功","/address/index");
            }else{
                $this->error_log(message)or("修改失败");
            }
        }else{
            	$this->success("数据无修改","/address/index");
        }
	}	
}	

 ?>
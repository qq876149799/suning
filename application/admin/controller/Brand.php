<?php  
namespace app\admin\controller;
use think\Controller;
use think\Db;

//品牌管理
class Brand extends Allow
{
	public function getIndex(){
		$list=Db::name('brand')->select();
		// var_dump($list);
		//加载模板
		return view('/product-brand',['list'=>$list]);
	}
	//添加品牌
	public function getAdd(){
		return view('/brandd-add');
	}
	//执行添加
	public function postDoadd(){
		$request=request();
		$lists=$request->only(['name']);
		// var_dump($lists);
		$file = request()->file('pic');
        $info = $file->validate(['size'=>156788888,'ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'pinpai');
		if($info){
			// echo $info->getExtension();
		}else{
			echo $file->getError();
		}
		$lists['pic']=$info->getSaveName();
		// var_dump($lists);
		if(Db::name('brand')->insert($lists)){
			$this->success('添加成功','/admin-brand/index');
		}else{
			$this->error('添加失败');
		}
	}
	//执行删除
	public function getDelete(){
		$request=request();
		$id=$request->param('id');
		$list=Db::name('brand')->where('id',$id)->find();
		// var_dump($list);
		if(Db::name('brand')->where('id',$id)->delete()){
			unlink("pinpai/{$list['pic']}");
			$this->success("删除成功","/admin-brand/index");
		}else{
			$this->error("删除失败","/admin-brand/index");
		}
	}
	//修改
	public function getEdit(){
		$request=request();
		$id=$request->param('id');
		$info=Db::name('brand')->where('id',$id)->find();
		// var_dump($info);exit;

		return view('/brandd-edit',['info'=>$info]);
	}
	 //执行修改
    public function postDoedit(){

    	$request=request();
    	// var_dump($request);exit;
    	$l=$request->only(['id','name']);
    	$file=$request->file('pic');
    	// var_dump($file);
    	$id=$request->param('id');
    	// var_dump($id);
    	$oldinfo=Db::name('brand')->where('id',$id)->find();
    	// var_dump($oldinfo);
    	if($file==''){
    		//判断图片是否修改过
    		if($l['name']!=$oldinfo['name']){
    			if(Db::table('suning_brand')->where('id',$request->param('id'))->update($l)){
    				$this->success('修改成功','/admin-brand/index');
    			}else{
    				$this->error("修改失败");
    			}
    		}else{
    			$this->success('数据无修改','/admin-brand/index');
    		}
    	}else{
    		
    		$info=Db::table('suning_brand')->where('id',$id)->find();
    		// var_dump($info);exit;
    		//旧的图片的路径
    		$oldname=$info['pic'];
    		// var_dump($oldname);exit;
    		//上传图片
    		$info = $file->validate(['size'=>15678888,'ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS .'uploads');
    		// var_dump($info);
    		if(!$info){
    			//失败的错误信息
    			echo $file->getError();
    		}
    		//新的路径
    		$l['pic']=$info->getSaveName();
    		// var_dump($l['pic']);exit;
    		if($l['name']!=$oldinfo['name']||$l['pic']!=$oldinfo['pic']){
    			$a=Db::table('suning_brand')->where('id',$id)->update($l);
    			if($a){
    				//删除旧的
    				// unlink("uploads/{$oldname}");
    				$this->success('修改成功','/admin-brand/index');
    			}else{
    				$this->error('修改失败');
    			}
    		}else{
    			$this->success("数据无修改",'/admin-brand/index');
    		}
    	}
    }

}

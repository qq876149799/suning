<?php 
namespace app\admin\controller;
use think\Controller;
use think\Db;
//广告
class Slide extends controller{
    //加载轮播图列表
    public function getIndex(){
        $list=Db::table('suning_lunbo')->select();
        // var_dump($list);exit;
        return view('/slide-list',['list'=>$list]);
    }

    //处理状态
    public function getstatus(){
        $request=request();
        $l=$request->param();
        if($l['status']==1){
            // echo '启用';
            Db::table('suning_lunbo')->where('id',$request->param('id'))->update(['status'=>0]);
        }else{
            Db::table('suning_lunbo')->where('id',$request->param('id'))->update(['status'=>1]);
        }
        $this->redirect('/admin-slide/index');
    }

    //添加广告
    public function getAdd(){
        return view('/slide-add');
    }
    //执行添加
    public function postDoadd(){
        $request=request();
        $l=$request->only(['name','url','status']);
        // var_dump($l);
         // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('pic');
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate(['size'=>1567888,'ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'lunbo');
        if($info){
            // 成功上传后 获取上传信息
            // 输出 jpg
            // echo $info->getExtension();
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            // echo $info->getSaveName();
            // 输出 42a79759f284b767dfcb2a0197904287.jpg
            // echo $info->getFilename();
            // echo '上传成功'; 
        }else{
            // 上传失败获取错误信息
            echo $file->getError();
        }
        $l['img']=$info->getSaveName();
        // var_dump($l); 
        if(Db::table('suning_lunbo')->insert($l)){
            $this->success('添加成功','/admin-slide/index');
        }else{
            $this->error('添加失败');
        }
    }
    //执行删除
    public function getDelete(){
        $request=request();
        $id=$request->param('id');
        $list=Db::table('suning_lunbo')->where('id',$id)->find();
        if(Db::table('suning_lunbo')->where('id',$id)->delete()){
            unlink("lunbo/{$list['img']}");
           $this->success("删除成功","/admin-slide/index");
        }else{
           $this->error("删除失败","/admin-slide/index");
        }
    }

    //修改广告信息
    public function getEdit(){
        $request=request();
        $id=$request->param('id');
        $info=Db::table('suning_lunbo')->where('id',$id)->find();
        // var_dump($info);exit;
        return view('/slide-edit',['info'=>$info]);
    }

    //执行修改
    public function postDoedit(){
        $request=request();
        $l=$request->only(['name','url','status']);
        $file=$request->file('pic');
        $id=$request->param('id');
        $oldinfo=Db::name('lunbo')->where('id',$id)->find();
        // var_dump($l);
        // var_dump($file);
        if($file==''){
            //判断图片是否修改过
            if($l['name']!=$oldinfo['name']||$l['url']!=$oldinfo['url']||$l['status']!=$oldinfo['status']){
                if(Db::table('suning_lunbo')->where('id',$request->param('id'))->update($l)){
                    $this->success('修改成功','/admin-slide/index');
                }else{
                    $this->error('修改失败');
                }       
            }else{
                $this->success('数据无修改','/admin-slide/index');
            }   
        }else{
            // echo '222';
            $info=Db::table('suning_lunbo')->where('id',$id)->find();
            //旧图片路径
            $oldname=$info['img'];
            // echo $oldname;
            //上传文件(图片)
            $info = $file->validate(['size'=>15678,'ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');
            if(!$info){
                // 上传失败获取错误信息
                echo $file->getError();
            }
            //新图片路径         
            $l['img']=$info->getSaveName();
            if($l['name']!=$oldinfo['name']||$l['url']!=$oldinfo['url']||$l['status']!=$oldinfo['status']||$l['img']!=$oldinfo['img']){
                 $a=Db::table('suning_lunbo')->where('id',$id)->update($l);
                if($a){
                    //删除旧图片
                    unlink("uploads/{$oldname}");
                    $this->success('修改成功','/admin-slide/index');
                }else{
                    $this->error('修改失败');
                }       
            }else{
                $this->success('数据无修改','/admin-slide/index');
            }   
           
            
        }
    }
}
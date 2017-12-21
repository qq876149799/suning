<?php 
namespace app\admin\controller;
//导入Controller
use think\Controller;
use think\Db;

    class Article extends Allow
    {
        //公告列表
        public function getindex(){
            $list=Db::table('suning_notice')->select();
            //加载模板
            return $this->fetch("/notice-list",['list'=>$list]);
        }

        //添加列表
        public function getAdd(){
            return view('/notice-add');
        }

        //执行添加
        public function postDoadd(){
            $request=request();
            $data=$request->only(['title','content']);
            // var_dump($data);exit;
            if(Db::name('notice')->insert($data)){
                $this->success("添加成功","/admin-article/index");
            }else{
                $this->error("添加失败");
            }
        }
        //执行删除
        public function getDelete(){
            $request=request();
            
            $id=$request->param('id');
           
            $lists=Db::name('notice')->where('id',$id)->find();

            if(Db::name('notice')->where('id',$id)->delete()){
                $this->success("删除成功","/admin-article/index");
            }else{
                $this->error("删除失败","/admin-article/index");
            }
        }
    //修改公告
    public function getEdit(){

        
        $request=request();
        $id=$request->param('id');
        $info=Db::name('notice')->where('id',$id)->find();
        // var_dump($info);
        return view('/notice-edit',['info'=>$info]);
    }
    //执行修改
    public function postDoedit(){
        $request=request();
        $edit=$request->only(['title','content']);
        // var_dump($edit);
        $update=Db::name('notice')->where('id',$request->param('id'))->find();
        // var_dump($update);
        if($edit['title']!=$update['title']||$update['content']!=$edit['content']){
            if(Db::name('notice')->where('id',$request->param('id'))->update($edit)){
                $this->success("修改成功","/admin-article/index");
            }else{
                $this->error("修改失败");
            }
        }else{
            $this->success("数据无修改","/admin-article/index");
        }
    }
 }
 
<?php 
namespace app\admin\controller;

use think\Controller;
use think\Db;

    //栏目
    class Category extends controller{
        //加载栏目列表
        public function getIndex(){
            $list=Db::table('suning_nav')->select();
            // var_dump($list);exit;
            return view('/system-category',['list'=>$list]);
        }

        //处理状态
        public function getstatus(){
            $request=request();
            $l=$request->param();
            if($l['status']==1){
                // echo '启用';
                Db::table('suning_nav')->where('id',$request->param('id'))->update(['status'=>0]);
            }else{
                Db::table('suning_nav')->where('id',$request->param('id'))->update(['status'=>1]);
            }
            $this->redirect('/system-category/index');
        }

        //添加栏目
        public function getAdd(){
            return view('/system-category-add');
        }

        //执行添加
        public function postDoadd(){
            $request=request();
            $l=$request->only(['name','url','status']);
            // var_dump($l);
            if($request->param('name')==''){
                $this->error('栏目名不能为空','/system-category/add');
            }
            if($request->param('url')==''){
                    $this->error('栏目地址不能为空','/system-category/add');
                }
            if(Db::table('suning_nav')->insert($l)){
                $this->success('添加成功','/system-category/index');
            }else{
                $this->error('添加失败');
            }
        }

        //执行删除
        public function getDelete(){
            $request=request();
            $id=$request->param('id');
            if(Db::table('suning_nav')->where('id',$id)->delete()){
               $this->success("删除成功","/system-category/index");
            }else{
               $this->error("删除失败","/system-category/index");
            }
        }

        //修改广告信息
        public function getEdit(){
            $request=request();
            $id=$request->param('id');
            $info=Db::table('suning_nav')->where('id',$id)->find();
            // var_dump($info);exit;
            return view('/system-category-edit',['info'=>$info]);
        }

        //执行修改
        public function postDoedit(){
            $request=request();
            $l=$request->only(['name','url','status']);
            $oldinfo=Db::name('nav')->where('id',$request->param('id'))->find();
            if($l['name']!=$oldinfo['name']||$l['url']!=$oldinfo['url']||$l['status']!=$oldinfo['status']){
                if(Db::table('suning_nav')->where('id',$request->param('id'))->update($l)){
                    $this->success('修改成功','/system-category/index');
                }else{
                    $this->error('修改失败');
                }       
            }else{
                $this->success('数据无修改','/system-category/index');
            }
        }
    }
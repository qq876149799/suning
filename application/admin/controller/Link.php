<?php
namespace app\admin\controller;
use think\Db;
use think\Controller;
    /**
    * 后台友情链接
    */
    class Link extends Allow
    {
        //友情链接列表
        public function getIndex()
        {
            $info=Db::table('suning_link')->select();
        return view('/link',['info'=>$info]);
        }

        //添加链接
        public function getAdd(){
            return view('/link-add');
        }

        //执行添加
        public function postDoadd(){
            $request=request();
            $data=$request->only(['name','url']);
            if(!$data['name']){
                $this->error("连接名不能为空","/admin-link/add");
            }else{
                if($data['url']=='http://'){
                    $this->error("链接地址不能为空","/admin-link/add");
                }
            }
            $data['status']=2;
            $s=Db::table("suning_link")->insert($data);
            if($s){
                $this->success("添加成功","/admin-link/index");
            }else{
                $this->error("添加失败","/admin-link/add");
            }
        }

        //跳转至修改页
        public function getLink_edit(){
            return view('/link_edit');
        }

        //修改
        public function getEdit(){
            $request=request();
            $id=$request->param('id');
            $info=Db::table('suning_link')->where('id',$id)->find();
            //var_dump($info);exit;
            return view('/link_edit',['info'=>$info]);
        }

        //执行修改
        public function postDoedit(){
            $request=request();
            $oldinfo=Db::name('link')->where('id',$request->param('id'))->find();
            $info=$request->only(['name','url','status']);
            if($info['name']!=$oldinfo['name']||$info['url']!=$oldinfo['url']||$info['status']!=$oldinfo['status']){
                if(Db::table('suning_link')->where('id',$request->param('id'))->update($info)){
                    $this->success("修改成功","/admin-link/index");
                }else{
                    $this->error("修改失败");
                }       
            }else{
                $this->success('数据无修改','/admin-link/index');
            }
        }

        //执行删除
        public function getDelete(){
              $request=request();
              $id=$request->param('id');
              //var_dump(Db::table('suning_link')->where('id',$id)->delete());exit;
              if(Db::table('suning_link')->where('id',$id)->delete()){
                 $this->success("删除成功","/admin-link/index");
              }else{
                 $this->error("删除失败","/admin-link/index");
              }
        }
    }
<?php 
namespace app\admin\controller;
use think\Controller;
use think\Db;   
class Admin extends Allow{
    //管理员列表
    public function getIndex(){
        $info=Db::table('suning_admins')->select();
        return view('/admin-list',['info'=>$info]);
    }

    //添加管理员
    public function getadd(){
        //查询角色表
        $role = Db::name('role')->field('id,name')->select();
        // var_dump($role);
        return view('/admin_add',['role'=>$role]);
    }

    //执行添加
    public function postdoAdd(){
        $request=request();
        $info=$request->except(['password2','action']);
        $info['addtime']=time();
        //处理角色
        if (empty($info['role_id'])) {
            $info['role_id'] = '';
        }else{
            $info['role_id'] = implode(',',$info['role_id']);
        }
        // var_dump($info);exit;
        //判断内容不为空
        if(!$info['username']){
                $this->error("管理员名不能为空","/admin-index/add");
            }else{
                if($info['password']==''){
                    $this->error("密码不能为空","/admin-index/add");
                }
                if($info['password']!=$request->param('password2')){
                    $this->error('两次密码不一致','/admin-index/add');
                }
            }
            $info['password']=md5($info['password']);
        $s=Db::table("suning_admins")->insertGetId($info);
        //role_list数据
        $data = [
            'uid'=>$s,
            'role_id'=>$info['role_id']
        ];
        if($s){
            //插入role_list表
            if (Db::name('role_list')->insert($data)) {
                $this->success("添加成功","/admin-index/index");
            }else{
                Db::name('admins')->where('id',$s)->delete();
                $this->error("添加失败","/admin-index/add");
            }
           
        }else{
           $this->error("添加失败","/admin-index/add");
        }
    }

    //修改管理员信息
    public function getEdit(){
        $request=request();
        $id=$request->param('id');
        $info=Db::table('suning_admins')->where('id',$id)->find();
        return view('/admin_edit',['info'=>$info]);
    }

    //执行修改
    public function postDoedit(){
        $request=request();
        $info=$request->only(['password','newpwd','renewpwd','id']);
        $oldinfo=Db::name('admins')->where('id',$request->param('id'))->find();
        $info['password'] = md5($request->param('password'));
        if($oldinfo['password']!=$info['password']){
            $this->error('原密码输入错误，请重新输入');
        }
        if($info['newpwd']!=$info['renewpwd']){
            $this->error('两次密码不一致，请重新输入');
        }
        var_dump($info);exit;
        if($info['password']!=$oldinfo['newpwd']){
                if(Db::table('suning_admins')->where('id',$request->param('id'))->update($info)){
               $this->success("修改成功","/admin-index/index");
            }else{
               $this->error("修改失败");
            }       
        }else{
            $this->success('密码无修改','/admin-index/index');
        }   
    }

    //执行删除
    public function getDelete(){
        $request=request();
        $id=$request->param('id');
        if(Db::table('suning_admins')->where('id',$id)->delete()){
           $this->success("删除成功","/admin-index/index");
        }else{
           $this->error("删除失败","/admin-index/index");
        }
    }
}


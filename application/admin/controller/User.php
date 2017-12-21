<?php 
namespace app\admin\controller;

//导入Controller
use think\Controller;
use think\Db;

    class User extends Allow
    {
        //列表
        public function getindex(){
            $request=request();
            //获取数据
            $keywords=$request->get('keywords');
            // var_dump($keywords);exit;

            $user=Db::name('users')->where('is_del',0)->paginate(6);
            // var_dump($user);
            //加载模板
            return $this->fetch("/member-list",['user'=>$user,'request'=>$request->param(),'keywords'=>$keywords]);
        }

        //执行删除
        public function getdelete(){
            //创建请求对象
            $request=request();
            $id=$request->param('id');
            // var_dump($id);exit;
            //删除
            if(Db::name("users")->where("id",$id)->delete()){
                $this->success("删除成功","/admin-user/dels");
            }else{
                $this->error("删除失败");
            }
        }

        //状态修改
        public function getstatus(){
            // 创建请求对象
            $request=request();
            $list=$request->param();
            // var_dump($list);
            if($list['status']==1){
                // echo '启用';
                Db::table('suning_users')->where('id',$request->param('id'))->update(['status'=>0]);
            }else{
                Db::table('suning_users')->where('id',$request->param('id'))->update(['status'=>1]);
            }
            $this->redirect('/admin-user/index');

        }

        //假删除方法
        public function getIs_del(){
            // 创建请求对象
            $request=request();
            $id = $request->param('id');
            //单个删除
            //查原有数据
            $info = Db::name('users')->where('id',$id)->field('is_del')->find();
            if($info['is_del']==1){
                Db::name('users')->where('id',$id)->update(['is_del'=>0]);
                $this->success("还原成功","/admin-user/dels");
            }else{
                Db::name('users')->where('id',$id)->update(['is_del'=>1]);
                $this->success("删除成功","/admin-user/index");
            }
        }

        //查看会员信息
        public function getInfo()
        {
            $request = request();
            $id = $request->param('id');
            $user = Db::name('users')->where('id',$id)->find();
            $info = Db::name('user_info')->where('uid',$id)->find();
            return view('/member-show',['info'=>$info,'user'=>$user]);
        }

        //查看已删除的列表
        public function getDels()
        {
            $data = Db::name('users')->where('is_del',1)->select();
            // var_dump($data);
            // $data = 1;
            return view('/member-del',['data'=>$data]);
        }
        //查看地址
        public function getAddress(){
        $list = Db::table('suning_users users, suning_address address')->where('users.id = 

        address.uid')->field('address.id as id,address.uid as uid ,address.name as name,address.phone as 

        phone,address.huo as huo')->order('address.id desc' )->select();
           // echo"<pre>";
           //  var_dump($list);
            return view('/member-address',['list'=>$list]);
        }

        
    }
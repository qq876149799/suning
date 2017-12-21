<?php
namespace app\admin\controller;

use think\Db;
use think\Controller;


    //后台无限分类
    class Cates extends Allow
    {   
        //列表页
        public function getIndex()
        {
            // $res = $this->getCatess();
            // var_dump($res);exit;
            return view('/category-list');
        }

        //获取类别信息 调整形式
        public function cates()
        {
            $res = Db::query("select * from suning_cates order by concat(path,',',id)");
            foreach ($res as $key => $value) {
                //获取path
                $path=$value['path'];
                //把path 转换为数组
                $arr=explode(",",$path);
                // echo "<pre>";
                // var_dump($arr);
                //获取逗号个数
                $len=count($arr)-1;
                // echo $len."<br>";
                //str_repeat 重复字符串
                $res[$key]['name']=str_repeat("|--",$len).$value['name'];
            }
            return $res;
        }

        //添加页面
        public function getAdd()
        {
            //查询现有分类
            // $res = Db::name('cates')->select();
            $res = $this->cates();
            return view('/category-add',['data'=>$res]);
        }

        //执行添加
        public function postDoadd()
        {
            $request = request();
            //判断是否为空
            if ($request->param('name') == '') {
                $this->error('分类名称不能为空','/admin-cates/add');
            }
            $data = $request->only(['name','pid']);
            $pid = $request->param('pid');
            // echo $pid;exit;
            //判断是否为一级分类
            if ($pid == 0) {
                $data['path'] = 0;
            }else{
                //获取父类信息
                $info = Db::name('cates')->where('id',$pid)->find();
                $data['path'] = $info['path'].','.$pid.',';
            }
            
            // var_dump($data);exit;
            $res = Db::name('cates')->insert($data);
            // var_dump($res);
            if ($res) {
                $this->success('添加分类成功，请刷新页面查看效果','/admin-cates/index',-1,2);
            }else{

            }$this->error('添加分类失败','/admin-cates/add',-1,2);
        }

        //执行删除
        public function getDelete()
        {
            $request = request();
            $id = $request->param('id');
            // echo $id;
            // 判断是否有父级
            $res = Db::name('cates')->where('pid',$id)->select();
            if (empty($res)) {
                //说明没有下级
                if(Db::name('cates')->where('id',$id)->delete()){
                    $this->success('删除分类成功，请刷新页面查看效果','/admin-cates/index');
                }else{
                    $this->error('删除分类失败','/admin-cates/index');
                }
            }else{
                $this->error('该分类下还有子类，请先删除子类','/admin-cates/index');
            }
        }

        //修改页面
        public function getEdit()
        {
            $request = request();
            $id = $request->param('id');
            //需要修改的数据
            $info = Db::name('cates')->where('id',$id)->find();
            // var_dump($info);exit;
            //类别信息
            $res = Db::query("select * from suning_cates order by concat(path,',',id)");

            return view('/category-edit',['data'=>$res,'info'=>$info]);
        }

        //执行修改
        public function postDoedit()
        {
            $request = request();
            $data = array(
                'name'=>$request->param('name'),
                'id'=>$request->param('id')
            );

            if(Db::name('cates')->update($data)){
                $this->success('修改成功，请刷新页面查看效果');
            }else{
                $this->error('修改失败');
            }
        }

        //获取分类数据
        public function getCatess()
        {
            $res = Db::query("select id,pid,name from suning_cates");
            echo json_encode($res);
        }  

        //Ajax跳转
        public function getJump()
        {
            $request = request();
            if (!empty($request->param('code'))) {
                $code = $request->param('code');
                switch ($code) {
                    case 1 :
                        $this->success('删除分类成功','/admin-cates/index');
                        break;
                    case 2:
                        $this->error('删除分类失败','/admin-cates/index');
                        break;
                    case 3:
                        $this->error('该分类下还有子类，请先删除子类','/admin-cates/index');
                        break;
                }
            }
            if (!empty($request->param('id'))) {
                $id = $request->param('id');
                echo '/admin-cates/edit?id='.$id;
            }
            
        }
    }
<?php 
namespace app\admin\controller;

//导入Controller
use think\Controller;
//导入Db
use think\Db;

    class System extends Allow
    {
        public function getindex(){
            $request=request();
            $title=$request->param('id');
            $info=Db::name("system")->find();
            // var_dump($info);
            //加载模板
            return $this->fetch("/system-base",['info'=>$info]);
        }

        //修改
        public function postDoedit(){
            $request=request();
            //获取参数
            $data=$request->only(['title','copyight','ICP','code']);
            // var_dump($data);
            $id=$request->param('id');
            $table=Db::name("system")->where("id",1)->find();
            // var_dump($table);exit;
            if($data['title'] == $table['title'] || $data['copyight'] == $table['copyight'] || $data['ICP'] == $table['ICP'] || $data['code'] == $table['code']){
                $this->success("数据无修改","/system-base/index");
            }else{
                if(Db::name("system")->where("id",1)->update($data)){
                    $this->success("修改成功","/system-base/index");
                }else{
                    $this->error("修改失败","/system-base/index");
                }
            }
        }
    }
?>
<?php
namespace app\admin\controller;

use think\Db;
use think\Controller;

    /**
    * 后台商品
    */
    class Goods extends Allow
    {
        //列表
        function getIndex()
        {
            $data = Db::name('goods')->select();
            foreach ($data as $key => $value) {
                $data[$key]['img'] = explode(',',$value['img'])[0];
                $data[$key]['name'] = $value['name'];
                $data[$key]['description'] = strip_tags(mb_substr($value['description'],0,20));
            }
            return view('/goods-list',['data'=>$data]);
        }

        //获取分类数据
        public function getCates()
        {
            //分类
            $data = Db::query("select * from suning_cates order by concat(path,id)");
            //处理name
            foreach ($data as $key => $value) {
                //获取path
                $path=$value['path'];
                //把path 转换为数组
                $arr=explode(",",$path);
                // echo "<pre>";
                // var_dump($path);
                //获取逗号个数
                $len=count($arr)-1;
                // echo $len."<br>";
                //str_repeat 重复字符串
                $data[$key]['name']=str_repeat("|--",$len).$value['name'];
            }
            return $data;
        }

        //获得模型数据
        public function getModel()
        {
            $model = Db::name('model')->select();
            return $model;
        }

        //添加页面
        public function getAdd()
        {
            //获取属性字段个数
            $fields = Db::query("DESC suning_goods_attr");
            $attrs = count($fields)-4;
            // var_dump($attrs);
            //获取必须数据
            $data = $this->getCates();
            $model = $this->getModel();
            //加载模版
            return view('/goods-add',['data'=>$data,'model'=>$model,'attrs'=>$attrs]);
        }

        //Ajax获取属性名
        public function getAttr($id = '')
        {
            if ($id == '') {
                $request = request();
                $id = $request->param('id');
            }else{
                $id = $id;
            }
            $data = Db::name('type')->where('m_id',$id)->select();
            // var_dump($data);
            // $value = [];
            foreach ($data as $k=>$val) {
                // $value = ;
                $vals = explode(',',$val['value']);
                // var_dump($vals);
                $data[$k]['value'] = $vals;
            }
            // var_dump($data);
            return $data;
        }

        //执行添加
        public function postDoadd()
        {
            $request = request();
            // 获取表单上传文件
            $files = $request->file('file');
            // var_dump($request->post(''));exit;
            if (!empty($files)) {
                // var_dump($files);exit;
                $path = ROOT_PATH.'public'.DS.'uploads'. DS .'goods';
                foreach($files as $file){
                    //添加验证规则  validate 验证方法
                    $result=$this->validate(['file2'=>$file],['file2'=>'require|image|fileSize:20480000'],['file2.image'=>'非法图像文件','file2.fileSize'=>'图像大小超出']);
                    //对比
                    if(true!==$result){
                        $this->error($result,"/admin-goods/add");
                    }
                    // 移动到框架应用根目录/public/uploads/ 目录下
                    $info = $file->validate(['size'=>20480000,'ext'=>'jpg,png,gif,bmp,jpeg'])->move($path);
                    // var_dump($info);exit;
                    if($info){
                        // 成功上传后 获取上传信息
                        $names[] = '/uploads/goods/'.$info->getSaveName();
                        // echo '上传成功';
                        // var_dump($names);
                        
                    }else{
                        // 上传失败获取错误信息
                        echo $file->getError();exit;
                    }
                   
                }
                //拆分数组,使用逗号组合所有图片名称
                $names = implode(',',$names);
            }else{
                $names = '';
            }
            // var_dump($names);exit;
            // var_dump($request->post(''));
            $data = [
                'name'=>$request->post('name'),
                'subtitle'=>$request->post('subtitle'),
                'cate_id'=>$request->post('cate'),
                'img'=>$names,
                'description'=>$request->post('description'),
                'is_sale'=>$request->post('is_sale'),
                'mid'=>$request->post('model')
            ];
            // var_dump($data);
            $gid = Db::name('goods')->insertGetId($data);
            if($gid){
                //处理attr字段
                $arr = $request->except(['name','subtitle','model','description','cate','action','is_sale']);
                // var_dump($arr);
                //插入商品副表s
                foreach ($arr as $key => $value) {
                    // $keys = substr($key,0,strlen($key)-1);
                    $arr1[] = $value;
                }
                // var_dump($arr1);
                $sum = substr($key,-1);
                
                $sum = intval($sum);
                $sum+=1;
                
                // var_dump($sum);
                // var_dump(count($arr));
                $i = count($arr)/$sum;
                // var_dump($i);
                
                for ($j=1; $j <= $sum; $j++) {
                    $x =($j-1)*4+$j-1;
                    $y = $j*4+$j;
                    $arr2 = array_slice($arr1,$x,$y);
                    // var_dump($arr2);exit;
                    // $arr2['gid'] = $gid;
                    if(Db::query("insert into suning_goods_attr (gid,attr1,attr2,attr3,num,price) values({$gid},'".$arr2[0]."','".$arr2[1]."','".$arr2[2]."','".$arr2[3]."','".$arr2[4]."')")){
                        $this->error('商品添加失败');
                        // echo '失败';
                    }else{
                        $this->success('商品添加成功','/admin-goods/index',-1,1);
                        // echo '成功';
                    }
                    // var_dump($arr2);
                }
            }else{
                $this->error('商品添加失败',-1,1);
            }
        }

        //下架
        public function getAction()
        {
            $request = request();
            Db::name('goods')->where('id',$request->get('id'))->update(['is_sale'=>0]);

        }

        //上架
        public function getAction1()
        {
            $request = request();
            $data = ['id'=>$request->param('id'),'is_sale'=>1];
            Db::name('goods')->where('id',$request->get('id'))->update(['is_sale'=>1]);
        }

        //删除
        public function getDelete()
        {
            $request = request();
            $id = $request->get('id');
            $data = Db::name('goods')->where('id',$id)->field('img')->find();
            $img = explode(',',$data['img']);
            // var_dump($img);
            foreach ($img as $val) {
                if (file_exists('.'.$val)) {
                    unlink('.'.$val);
                }
            }
            if(Db::name('goods')->where('id',$id)->delete()){
                if(Db::name('goods_attr')->where('gid',$id)->delete()){
                    $this->success('删除成功','/admin-goods/index',-1,1);
                }else{$this->error('删除失败',-1,1);}
            }else{$this->error('删除失败',-1,1);}
        }

        //编辑主表
        public function getEdit()
        {
            $cates = $this->getCates();
            $request = request();
            $id = $request->get('id');
            // var_dump($id);
            //查询原信息
            $data = Db::name('goods')->where('id',$id)->find();
            //处理图片地址
            // $data['img'] = explode(',',$data['img']);
            // var_dump($data);
            return view('/goods-edit',['data'=>$data,'cates'=>$cates]);
        }

        //执行编辑主表
        public function postDoedit()
        {
            //只可以修改
            // name subtitle cate_id img is_sale description
            $request = request();
            // var_dump($request->post(''));
            $id = $request->post('id');
            //判断是否修改图片
            $files = $request->file('file');
            if (empty($files)) {
                // echo '不修改图片';
                $data = $request->only(['name','subtitle','cate_id','description','is_sale']);
                // $data['cate'] = intval($data['cate']);
                // var_dump($data);
                //防止未修改提交
                $old = Db::name('goods')->where('id',$id)->field('name,subtitle,cate_id,description,is_sale')->find();
                // var_dump($old);
                if ($old == $data) {
                    // echo '相同';
                    $this->success('数据无修改','/admin-goods/index',-1,1);
                }
            }else{
                echo '修改图片';
                // var_dump($files);exit;
                $path = ROOT_PATH.'public'.DS.'uploads'. DS .'goods';
                foreach($files as $file){
                    //添加验证规则  validate 验证方法
                    $result=$this->validate(['file2'=>$file],['file2'=>'require|image|fileSize:20480000'],['file2.image'=>'非法图像文件','file2.fileSize'=>'图像大小超出']);
                    //对比
                    if(true!==$result){
                        $this->error($result);
                    }
                    // 移动到框架应用根目录/public/uploads/ 目录下
                    $info = $file->validate(['size'=>20480000,'ext'=>'jpg,png,gif,bmp,jpeg'])->move($path);
                    // var_dump($info);exit;
                    if($info){
                        // 成功上传后 获取上传信息
                        $names[] = '/uploads/goods/'.$info->getSaveName();
                        echo '上传成功';
                        //拆分数组,使用逗号组合所有图片名称
                        $names = implode(',',$names);
                        //删除旧图片
                        $oldfile = explode(',', $request->param('oldfile'));
                        foreach ($oldfile as $value) {
                            if (file_exists('.'.$value)) {
                                unlink('.'.$value);
                            }
                        }
                    }else{
                        // 上传失败获取错误信息
                        echo $file->getError();exit;
                    }
                }
                
                $data = $request->only(['name','subtitle','cate_id','description','is_sale']);
                $data['img'] = $names;
                // var_dump($data);exit;
            }
            if(Db::name('goods')->where('id',$id)->update($data)){
                $this->success('修改商品成功');
            }else{
                $this->error('修改商品失败');
            }
        }

        //修改商品属性
        public function getEditAttr()
        {
            $request = request();
            $id = $request->get('id');
            // $data = Db::name('goods_attr')->where('gid',$id)->select();
            // var_dump($data);
            //获取mid
            $m = Db::name('goods')->where('id',$id)->field('mid')->find();
            $mid = $m['mid'];
            //通过mid查询该商品有几个属性
            $attrs = Db::name('type')->where('m_id',$mid)->select();
            // $th = count($attrs);
            // var_dump($attrs);
            //查询该商品属性
            $data = Db::name('goods_attr')->where('gid',$id)->select();
            // var_dump($data);
            return view('/goods-attr-edit',['data'=>$data,'attrs'=>$attrs,'id'=>$id,'mid'=>$mid]);
        }

        //执行修改属性
        public function postDoeditAttr()
        {
            $request = request();
            // $price = $request->post('price');
            // echo $price;
            if (empty($request->post('price'))) {
                //说明是修改库存
                $data = $request->only(['id','num']);
            }else{
                $data = $request->only(['id','price']);
            }
            if(Db::name('goods_attr')->update($data)){
                //修改成功
                echo 1;
            }else{
                //修改失败
                echo 2;
            }
        }

        //添加属性
        public function getAddattr()
        {
            $request = request();
            $gid = $request->get('id');
            //需要获取到可以添加哪些属性
            $info = Db::name('goods')->where('id',$gid)->field('mid,name')->find();
            // var_dump($mid);
            $mid = $info['mid'];
            // $gname = ;
            // var_dump($gname);
            //查询该model有哪些属性值
            $attrs = $this->getAttr($mid);
            //一共需要的select框个数
            $fields = Db::query("DESC suning_goods_attr");
            $sum = count($fields)-4;
            return view('/goods-attr-add',['attrs'=>$attrs,'gname'=>$info['name'],'sum'=>$sum,'gid'=>$gid]);
        }

        //执行添加属性
        public function postDoaddattr()
        {
            $request = request();
            // var_dump($request->post(''));
            $data = $request->post('');
            $data1 = [];
            foreach ($data as $key => $value) {
                $data1[] = $value;
            }
            //首先查询该规格是否存在
            $result = Db::name('goods_attr')->where("attr1='".$data1[1]."'and attr2='".$data1[2]."'and attr3='".$data1[3]."' and gid='".$data1[0]."'")->find();
            if (!empty($result)) {
                echo '<script>alert("该种规格已存在，请直接进行修改");parent.location.reload();</script>';
                
            }
            // var_dump($result);exit;
            $res = Db::name('goods_attr')->insert(['gid'=>$data1[0],'attr1'=>$data1[1],'attr2'=>$data1[2],'attr3'=>$data1[3]]);
            // var_dump($res);
            if ($res) {
                echo '<script>parent.location.reload();</script>';
            }else{
                echo '添加规格失败！';
            }
        }

        

        //删除属性
        public function getDeleteAttr()
        {
            $request = request();
            $id = $request->get('id');
            $gid = $request->get('gid');
            // echo $id;
            // 查询这个商品是否只有这一条规格
            $data = Db::name('goods_attr')->where('gid',$gid)->select();
            if (count($data)==1) {
                //最后一条属性
                echo 2;exit;
            }
            if(Db::name('goods_attr')->where('id',$id)->delete()){
                // echo '删除成功！';
                echo 1;
            }
        }

        //推荐
        public function getTui()
        {
            $request = request();
            $id = $request->param('id');
                //查商品状态
                $data = Db::name('goods')->where('id',$id)->find();
                // var_dump($data);
                if ($data['tui'] == 1) {
                    Db::name('goods')->where('id',$id)->update(['tui'=>0]);
                }else{
                    Db::name('goods')->where('id',$id)->update(['tui'=>1]);
                }
            
            $this->redirect('/admin-goods/index');
   
        }

        //好货
        public function getHao()
        {
            $request = request();
            $id = $request->param('id');
                 //查商品状态
                $data = Db::name('goods')->where('id',$id)->find();
                // var_dump($data);
                if ($data['hao'] == 1) {
                    Db::name('goods')->where('id',$id)->update(['hao'=>0]);
                }else{
                    Db::name('goods')->where('id',$id)->update(['hao'=>1]);
                }
            
           
            $this->redirect('/admin-goods/index');
   
        }

    }

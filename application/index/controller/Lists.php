<?php 
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Session;
class Lists extends Controller
{
	//商品列表
	public function getIndex(){
		$request=request();
		$id=$request->param('id');
		$info=Db::table('suning_cates')->where('id',$id)->find();
		$path=$info['path'];
		// var_dump($path);
		$list=Db::query("SELECT * FROM suning_goods WHERE is_sale = 1 AND (cate_id in(SELECT id FROM suning_cates WHERE `path` like '%,{$id},%') or id = {$id})");
		
		// var_dump($list);
		//处理商品数据
		$data = [];
		$i = 0;	//循环变量
		foreach ($list as $vals) {
			//循环查询每个商品的信息
			// name price img c_num 
			$res = Db::name('goods_attr')->where('gid',$vals['id'])->find();
			// var_dump($res);
			$data[$i]['gid'] = $vals['id'];
			$data[$i]['name'] = $vals['name'];
			$data[$i]['pic'] = explode(',',$vals['img'])[0];
			$data[$i]['c_num'] = $vals['c_num'];
			$data[$i]['price'] = $res['price'];
			$i++;
		}
		// var_dump($data);
		// exit;
		//品牌遍历
		$brand=Db::table('suning_brand')->select();
		//销量排名
		$d=[];
		$i=0;
		$infos=Db::table('suning_goods')->where('is_sale',1)->order('sales','desc')->limit(5)->select();
		foreach($infos as $val){
			$id=$val['id'];
			$infoss=Db::table('suning_goods_attr')->where('gid',$id)->find();
			$d[$i]['id']=$val['id'];
			$d[$i]['name']=$val['name'];
			$d[$i]['pic']=explode(',',$val['img'])[0];
			$d[$i]['price']=$infoss['price'];
			$i++;
		}
		return view('/goods_detail',['data'=>$data,'brand'=>$brand,'d'=>$d]);
	}
}
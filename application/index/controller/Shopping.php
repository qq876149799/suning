<?php 
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Session;

class Shopping extends Controller
{
	public function postIndex(){
        
        $request = request();
        // var_dump($request->post(''));
        //查收货信息
        $uid = session('user.id');
        $address = Db::name('address')->where('uid',session('user.id'))->select();
        //要购买的商品id数组
        $cids = $request->post('ids/a');
        //查询商品信息
        $data = [];
        foreach ($cids as $key=>$val) {
            //购物车信息
            $carts = Db::name('cart')->where('id',$val)->find();
            // var_dump($carts);
            //商品信息
            $goods = Db::name('goods')->where('id',$carts['gid'])->find();
            //商品属性
            $attr = Db::name('goods_attr')->where('id',$carts['aid'])->where('gid',$carts['gid'])->find();
            // var_dump($attr);
            $data[$key]['gid'] = $goods['id'];
            $data[$key]['name'] = $goods['name'];
            $data[$key]['img'] = explode(',',$goods['img'])[0];
            $data[$key]['attr']['attr1'] = $attr['attr1'];
            $data[$key]['attr']['attr2'] = $attr['attr2'];
            $data[$key]['attr']['attr3'] = $attr['attr3'];
            $data[$key]['price'] = $attr['price'];
            $data[$key]['gnum'] = $carts['gnum'];
            $data[$key]['subtotal'] = $attr['price']*$carts['gnum'];
            $data[$key]['cid'] = $val;
        }
        // var_dump($data);
        //结算页面需要的数据：收货信息 商品图片，名称，单价，购买数量，小计，总计
        return view('/order',['address'=>$address,'data'=>$data]);
	}
}
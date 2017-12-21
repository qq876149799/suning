<?php 
namespace app\index\controller;

use think\Controller;
use think\Session;
use think\Db;
//个人中心订单
class Orders extends controller
{
	public function getIndex(){
		$id=Session::get('user')['id'];
		//查orders表
		$orders = Db::name('orders')->where('uid',$id)->select();
		// var_dump($orders);
		//通过orders表查上商品信息组合数组
		$data = [];
		$i = 0;
		$status = [0=>'未支付',1=>'已支付',2=>'待收货',3=>'已收货'];
		$btn = [
			0=>'<a href="javascript:;" class="bnt bnt-orange" id="topay">去支付</a>',
			1=>'<font color="orange" size="2" style="font-weight:bold">待发货</font>',
			2=>'<a href="javascript:;" class="bnt bnt-orange" id="shouhuo">点击收货</a>',
			3=>'<font color="green" size="2" style="font-weight:bold">订单完成</font>'
		];
		foreach ($orders as $key => $value) {
			$orders[$key]['state'] = $status[$value['status']];
			$orders[$key]['btn'] = $btn[$value['status']];
			$orders[$key]['goods'] = Db::name('order_info')->where('oid',$value['id'])->select();
			$i++;
		}
		// var_dump($orders);
		return view('/member_order',['orders'=>$orders]);
	}

	//订单详情
    public function getOrderinfo(){
    	$request=request();
    	$id=$request->param('id');
    	//查询订单信息
    	$order=Db::table('suning_orders')->where('id',$id)->find();
    	//查询地址
    	$addss=Db::table("suning_address")->where('id',$order['add_id'])->find();
    	$add=$addss['adds'];
    	$adds=$addss['huo'];
    	$name=$addss['name'];
    	$phone=$addss['phone'];
    	$address=$adds.$add;
    	//组合数据
    	$data['id']=$order['id'];
    	$data['status']=$order['status'];
    	$data['order_num']=$order['order_num'];
    	$data['time']=date('Y-m-d H:i:s',$order['create_time']);
    	$data['address']=$address;
    	$data['name']=$name;
    	$data['phone']=$phone;
    	$data['total']=$order['total'];
    	//查询订单详细信息
    	// var_dump($data);exit;
    	$orderinfo=Db::table('suning_order_info')->where('oid',$id)->select();
    	// var_dump($orderinfo);exit;
    	return view('/member_order_detail',['data'=>$data,'orderinfo'=>$orderinfo]);
    }

    //收货
    public function getSend()
    {
        $request = request();
        $id = $request->param('id');
        //查状态
        $res = Db::name('orders')->where('id',$id)->find();
        // var_dump($res);
        if ($res['status'] == 2) {
            //修改状态
            if(Db::name('orders')->where('id',$id)->update(['status'=>3])){
                $this->redirect('/member-order/index');
            }
        }
    }

    //评价
    public function getAppraise()
    {
        $request = request();
        $id = $request->param('id');
        // echo $id;
        //查询订单下的商品信息
        $info = Db::name('order_info')->where('id',$id)->select();
        // var_dump($info);
        return view('/member-appraise',['info'=>$info]);
    }

    //执行评价
    public function postAppraise()
    {
        $request = request();
        // var_dump($request->request(''));
        //获取gid
        $gid = $request->param('gid');
        //当前用户id
        $id = session('user.id');
        //获取content
        $content = $request->param('content');
        //组合数据
        $data = [
            'gid'=>$gid,
            'uid'=>$id,
            'content'=>$content,
            'addtime'=>time()
        ];
        //插入数据库
        if(Db::name('appraise')->insert($data)){
            
            echo '<script>alert("评价成功！");location="/member-order/index";</script>';
        }
    }
}
<?php 
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Session;
class Cart extends Controller
{	
	//加入购物车
	public function getAdd(){
		//判断是否登录
		if(!Session::get('user')){
			return redirect('/index/login');
		}
		//拿到用户id
		$user=Session::get('user');
		$data['uid']=$user['id'];
		//拿到商品id
		$request=request();
		$data['gid']=$request->param('id');
		//判断是否为列表页添加
		if (empty($request->param('gnum'))) {
			//只有商品id
			//拿到默认属性id
			$attr = Db::name('goods_attr')->where('gid',$data['gid'])->find();
			$data['aid'] = $attr['id'];
			//先检查表中是否有这个商品
			$old = Db::name('cart')->where('uid',$data['uid'])->where('aid',$data['aid'])->find();
			// var_dump($old);exit;
			if (!empty($old)) {
				//判断库存
				if ($old['gnum']+1>$attr['num']) {
					$old['gnum'] = $attr['num'];
				}else{
					$old['gnum'] = $old['gnum']+1;
				}
				//修改购物车表
				Db::name('cart')->update($old);
			}else{
				$data['gnum'] = 1;
				// var_dump($data);exit;
				//插入购物车表
				Db::name('cart')->insert($data);
			}
		}else{
			//商品详情页添加
			//有商品id 购买数量
			//获取购买数量
			$where['gid'] = $data['gid'];
			$gnum = $request->param('gnum');
			//获取属性id
			if ($request->param('attr2')=='null') {
				// echo 1;exit;
				$where['attr1'] = $request->param('attr1');

			}else if ($request->param('attr3')=='null') {
				// echo 2;exit;
				$where['attr1'] = $request->param('attr1');
				$where['attr2'] = $request->param('attr2');
			}else{
				// echo 3;exit;
				// var_dump($request->param(''));exit;
				$where['attr1'] = $request->param('attr1');
				$where['attr2'] = $request->param('attr2');
				$where['attr3'] = $request->param('attr3');
			}
			// echo json_encode($where);exit;
			$attr = Db::name('goods_attr')->where($where)->find();
			$aid = $attr['id'];
			// echo $aid;exit;
			//查原来是否就有
			$old = Db::name('cart')->where('uid',$data['uid'])->where('aid',$aid)->find();
			
			if (!empty($old)) {
				//判断库存
				// var_dump($old);exit;
				if ($old['gnum']+1>$attr['num']) {
					$old['gnum'] = $attr['num'];
				}else{
					$old['gnum'] = $old['gnum']+1;
				}
				//修改购物车表
				if(Db::name('cart')->update($old)){
					echo 1;exit;
				}else{
					echo 2;exit;
				}
			}else{
				//组合数据
				$data = [
					'gid'=>$request->param('id'),
					'aid'=>$aid,
					'uid'=>$user['id'],
					'gnum'=>$gnum
				];
				// echo json_encode($data);
				// exit;
				//存表
				if(Db::name('cart')->insert($data)){
					echo 1;exit;
				}else{
					echo 2;exit;
				}
			}	
		}
		//跳转页面
		return view('/cart_add');
	}

	//ajax添加购物车成功
	public function getCartok()
	{
		return view('/cart_add');
	}

	public function getIndex(){
		if(session('?user')){
	    	// echo "已登录";exit;
	    	$data = '<div class="ng-bar-node-box username-handle" style="display: block;" id="islogin"><a href="#" rel="nofollow" class="ng-bar-node username-bar-node-noside"><span id="usernameHtml02">'.session('user.username').'</span><em>∨</em></a><div class="ng-d-box ng-down-box ng-username-slide" style="display:none;"><a href="/info/index" class="ng-vip-union" target="_blank" name="public0_none_denglu_zhanghao" rel="nofollow">账号管理</a><a href="/logout" rel="nofollow" name="public0_none_denglu_tuichu">退出登录</a></div></div>';
	    }else{
	    	// echo "未登录";exit;
	    	$data = '<div class="ng-bar-node reg-bar-node" id="reg-bar-node" style="display: block;"><a href="/index/login" name="public0_none_denglu_denglu" rel="nofollow" class="login">请登录</a><a href="/reg/index" target="_blank" class="login reg-bbb" rel="nofollow" name="public0_none_denglu_zhuce">注册有礼</a></div>';
	    }
		if(!Session::get('user')){
			return redirect('/index/login');
		}
		$user=Session::get('user');
		$id=$user['id'];
		// echo session('user.id');
		$info=Db::table('suning_cart')->where("uid",session('user.id'))->select();
		// echo Db::name('suning_cart')->getlastsql();
		// var_dump($info);
		//查商品信息
		$res = [];
		//组合页面需要的数据
		foreach ($info as $key => $value) {
			$goods = Db::name('goods')->where('id',$value['gid'])->find();
			$res[$key]['gid'] = $goods['id'];
			$res[$key]['name'] = $goods['name'];
			$res[$key]['img'] = explode(',',$goods['img'])[0];
			$res[$key]['gnum'] = $value['gnum'];
			//查询属性信息
			$attr = Db::name('goods_attr')->where('id',$value['aid'])->find();
			// var_dump($attr);
			$res[$key]['attr']['attr1'] = $attr['attr1'];
			$res[$key]['attr']['attr2'] = $attr['attr2'];
			$res[$key]['attr']['attr3'] = $attr['attr3'];
			$res[$key]['price'] = $attr['price'];
			$res[$key]['cid'] = $value['id'];
			$res[$key]['aid'] = $value['aid'];
			// var_dump($goods);
			// $res[$key] = $goods;
			// 小计
			$res[$key]['subtotal'] = $attr['price']*$value['gnum'];	
		}
		// var_dump($res);
		//加载模版
		return view('/cart',['data'=>$res,'user'=>$user]);
	}

    //执行删除
    public function getDelete(){
        $request=request();
        $id=$request->param('id');
        Db::table("suning_cart")->where("id","{$id}")->delete();
        //重定向
        $this->redirect("/cart/index");
    }
    //数量减
    public function getJian(){
    	$request=request();
        $cid = $request->param('cid');
        //获取购买数量
        $gnum = Db::name('cart')->where('id',$cid)->field('gnum')->find()['gnum'];
        // echo $gnum;exit;
        if ($gnum-1<=0) {
        	$gnum = 1;
        }else{
        	$gnum-=1;
        }
        // echo $gnum;exit;
        //存入      
        if(Db::name("cart")->where('id',$cid)->update(['gnum'=>$gnum])){
        	echo $gnum;
        }else{
        	echo $gnum;
        }
    }
    //数量加
    public function getJia(){
    	$request=request();
        $aid=$request->param('aid');
        $cid = $request->param('cid');
        //获取库存
        $num=Db::name('goods_attr')->where('id',$aid)->field('num')->find()['num'];
        //获取购买数量
        $gnum = Db::name('cart')->where('id',$cid)->field('gnum')->find()['gnum'];
        // echo $gnum;exit;
        if ($gnum+1>$num) {
        	$gnum = $num;
        	echo $gnum;exit;
        }else{
        	$gnum+=1;
        }
        // echo $gnum;exit;
        //存入      
        if(Db::name("cart")->where('id',$cid)->update(['gnum'=>$gnum])){
        	echo $gnum;
        }else{
        	echo $gnum;
        }
    }
    //数量修改
    public function getChange()
    {
    	$request = request();
    	$aid=$request->param('aid');
        $cid = $request->param('cid');
        $v = $request->param('v');
        //获取库存
        $num=Db::name('goods_attr')->where('id',$aid)->field('num')->find()['num'];
        //获取购买数量
        $gnum = Db::name('cart')->where('id',$cid)->field('gnum')->find()['gnum'];
        
        if ($v>=$num) {
        	$v = $num;
        }
        if ($v<=0) {
        	$v = 1;
        }
        if ($v==$gnum) {
        	echo $v;exit;
        }
        // echo $v;exit;
        //存入      
        if(Db::name("cart")->where('id',$cid)->update(['gnum'=>$v])){
        	echo $v;
        }else{
        	echo $v;
        }
    }

    //批量删除
    public function getDel(){
    	$request = request();
    	$ids = $request->get('ids/a');
    	// echo 1;
    	//循环遍历
    	foreach ($ids as $v) {
    		if(Db::name('cart')->where('id',$v)->delete()){
    			echo 1;
    		}else{
    			echo 0;
    		}
    	}
    }

    //单个删除
    public function getDels()
    {
    	$request = request();
    	$id = $request->param('id');
    	// echo $id;
    	if(Db::name('cart')->where('id',$id)->delete()){
    		echo 1;
    	}else{
    		echo 2;
    	}
    }
}

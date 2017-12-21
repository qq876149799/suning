<?php
namespace app\index\controller;

use think\Session;
use think\Db;
use think\Controller;

	class Index extends controller
	{
		//获取分类
		public function getCateByPid($pid)
		{
			$cate = Db::name('cates')->where('pid',$pid)->select();
			// return $cate;
			$data = [];
			foreach ($cate as $key => $value) {
				$value['cates']=$this->getCateByPid($value['id']);
				$data[] = $value;
			}
			// var_dump($data);
			return $data;
		}
		
		
		//加载首页模板
	    public function index()
	    {
	    	// var_dump(session(''));
	    	//判断是否登录
	    	if(session('?user')){
	    		// echo "已登录";exit;
	    		$data = '<div class="ng-bar-node-box username-handle" style="display: block;" id="islogin"><a href="/home/index" rel="nofollow" class="ng-bar-node username-bar-node-noside"><span id="usernameHtml02">'.session('user.username').'</span><em>∨</em></a><div class="ng-d-box ng-down-box ng-username-slide" style="display:none;"><a href="/info/index" class="ng-vip-union" target="_blank" name="public0_none_denglu_zhanghao" rel="nofollow">账号管理</a><a href="/logout" rel="nofollow" name="public0_none_denglu_tuichu">退出登录</a></div></div>';
	    		$tip = session('user.username');
	    		$btn = '<p class="level" style="display: block;"><a href="#" name="index3_homepage1_newUser_tankuang" style="color:#F60;">新人有礼</a></p>';
	    	}else{
	    		// echo "未登录";exit;
	    		$data = '<div class="ng-bar-node reg-bar-node" id="reg-bar-node" style="display: block;"><a href="/index/login" name="public0_none_denglu_denglu" rel="nofollow" class="login">请登录</a><a href="/reg/index" target="_blank" class="login reg-bbb" rel="nofollow" name="public0_none_denglu_zhuce">注册有礼</a></div>';
	    		$tip = '你好';
	    		$btn = '<div class="btn"><a href="/index/login" class="login" name="index3_homepage1_denglu_button">登录</a><a href="/reg/index" class="register" name="index3_homepage1_zhuce_button">新人有礼</a></div>';
	    	}
	    		
	    	$list=Db::name('notice')->limit(2)->select();
	    	// var_dump($list);exit;
	    	$lists=Db::name('system')->select();
	    	// var_dump($lists);exit;	
	    	$cate = $this->getCateByPid(0);
	    	//获取栏目
	    	$nav=Db::table('suning_nav')->where('status',1)->select();
	    	//获取轮播图
	    	$slide=Db::table('suning_lunbo')->where('status',1)->limit(6)->select();
	    	//获取广告1
	    	$ad1=Db::table('suning_ad')->where(['status'=>1,'position'=>1])->limit(0,3)->select();
	    	//获取广告2
	    	$ad2=Db::table('suning_ad')->where(['status'=>1,'position'=>1])->limit(3,6)->select();
	    	// var_dump($ad1);
	    	// var_dump($ad2);exit;
	    	// var_dump($cate);
	    	// 获取部分商品  图片 名字 id 默认价格
	    	$goods = Db::name('goods')->limit('6')->where('is_sale',1)->order('sales DESC')->select();
	    	//查需要的信息
	    	foreach ($goods as $key => $value) {
	    		$info = Db::name('goods_attr')->where('gid',$value['id'])->find();
	    		$goods[$key]['price']  = $info['price'];
	    		$goods[$key]['img'] = explode(',',$value['img'])[0];
	   	    }
	    	// var_dump($goods);
	    	// 查好货
	    	$hao = Db::name('goods')->where('is_sale',1)->where('hao',1)->limit('6')->select();
	    	//查需要的信息
	    	foreach ($hao as $key => $value) {
	    		$info = Db::name('goods_attr')->where('gid',$value['id'])->find();
	    		$hao[$key]['price']  = $info['price'];
	    		$hao[$key]['img'] = explode(',',$value['img'])[0];
	   	    }
	    	// 查热卖
	    	$mai = Db::name('goods')->where('is_sale',1)->where('hao',1)->limit('4')->select();
	    	//查需要的信息
	    	foreach ($mai as $key => $value) {
	    		$info = Db::name('goods_attr')->where('gid',$value['id'])->find();
	    		$mai[$key]['price']  = $info['price'];
	    		$mai[$key]['img'] = explode(',',$value['img'])[0];
	   	    }
	        return $this->fetch('/index',['cate'=>$cate,'data'=>$data,'nav'=>$nav,'list'=>$list,'slide'=>$slide,'lists'=>$lists,'tip'=>$tip,'btn'=>$btn,'ad1'=>$ad1,'ad2'=>$ad2,'goods'=>$goods,'hao'=>$hao,'mai'=>$mai]);
		}
	}

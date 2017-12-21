<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;
 //前台首页
Route::rule('/','index/Index/index');
//前台登录
Route::rule('index/login','index/Login/login');
//执行登录
Route::rule('index/dologin','index/Login/dologin');
//前台退出登录
Route::rule('logout','index/Login/logout');
//前台注册
Route::controller('/reg',"index/Reg");
//前台友情链接
Route::controller('link','index/Link');
//前台个人中心
Route::controller('info','index/Info');
//前台公告
Route::controller('notice','index/Notice');
//前台找回密码
Route::controller('forget','index/Forget');
//前台验证
Route::controller('/verify','index/Verify');
//前台地址管理
Route::controller('address','index/Address');
//前台购物车
Route::controller('cart','index/Cart');
//前台个人中心订单
Route::controller('member-order','index/Orders');
//前台结算支付
Route::controller('shopping','index/Shopping');
//前台个人首页
Route::controller('home','index/Home');
//前台商品列表页
Route::controller('list','index/Lists');
//前台商品详情
Route::controller('goods','index/Goods');
//前台订单生成
Route::controller('order','index/Order');

//后台首页
Route::controller('admin','admin/Index');
//后台管理员
Route::controller('/admin-index','admin/Admin');
//后台登录
Route::rule('/admin/login','admin/Login/index');
//后台执行登录
Route::rule('/admin/dologin','admin/Login/dologin');
//后台退出
Route::rule('/admin/logout','admin/Login/logout');
//后台无限分类
Route::controller('/admin-cates','admin/Cates');
//后台友情链接
Route::controller('admin-link','admin/Link');
//后台用户管理
Route::controller('admin-user','admin/User');
//后台广告管理
Route::controller('admin-ad','admin/Ad');
//后台栏目管理
Route::controller('system-category','admin/Category');
//后台角色管理
Route::controller('admin-role','admin/Role');
//后台系统设置
Route::controller('system-base','admin/System');
//后台公告管理
Route::controller('admin-article','admin/Article');
//后台轮播图管理
Route::controller('admin-slide','admin/Slide');

//后台品牌管理
Route::controller('admin-brand','admin/Brand');

//批量删除
Route::controller('admin-delete','admin/Delete');
//后台商品管理
Route::controller('admin-goods','admin/Goods');
//后台商品模型
Route::controller('admin-model','admin/Model');
//后台属性管理
Route::controller('goods-attr','admin/Attr');
//后台地址管理
Route::controller('admin-address','admin/Address');
//后台订单管理
Route::controller('admin-orders','admin/Order');
//后台充值
Route::controller('admin-pay','admin/Pay');

// return [
//     '__pattern__' => [
//         'name' => '\w+',
//     ],
//     '[hello]'     => [
//         ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
//         ':name' => ['index/hello', ['method' => 'post']],
//     ],

// ];
